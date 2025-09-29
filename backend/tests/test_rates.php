<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../config/constants.php";

// ====== Test Payload ======
$payload = [
    "Unit Name" => "Ocean View Suite",
    "Arrival" => "26/09/2025",
    "Departure" => "27/09/2025",
    "Occupants" => 2,
    "Ages" => [22, 20] // Adjust ages as needed
];

// Map to Gondwana API format
$unitTypeId = UNIT_TYPE_IDS[$payload["Unit Name"]] ?? null;
if (!$unitTypeId) {
    die("Unknown Unit Name");
}

$apiPayload = [
    "Unit Type ID" => $unitTypeId,
    "Arrival" => date("Y-m-d", strtotime(str_replace("/", "-", $payload["Arrival"]))),
    "Departure" => date("Y-m-d", strtotime(str_replace("/", "-", $payload["Departure"]))),
    "Guests" => array_map(function($age){
        return [
            "Age Group" => ($age >= 13 ? "Adult" : "Child"),
            "Age" => $age
        ];
    }, $payload["Ages"])
];

// ====== Send POST request to Gondwana API ======
$ch = curl_init(GONDWANA_API);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ====== Debug ======
file_put_contents(__DIR__ . "/debug_gondwana.json", $response);

if ($httpcode !== 200 || !$response) {
    die("Failed to fetch rates from Gondwana. HTTP code: $httpcode");
}

// Decode JSON
$data = json_decode($response, true);
if (!$data) {
    die("Invalid JSON response from Gondwana API");
}

// Extract first leg
$leg = $data["Legs"][0] ?? null;
if (!$leg) {
    die("No rates returned from Gondwana API");
}

// ====== Calculate Rate ======
$rate = 0;

// Prefer Effective Average Daily Rate
if (isset($leg["Effective Average Daily Rate"]) && $leg["Effective Average Daily Rate"] > 0) {
    $rate = $leg["Effective Average Daily Rate"];
} elseif (isset($data["Total Charge"]) && $data["Total Charge"] > 0) {
    // Fallback: divide Total Charge by nights
    $arrival = new DateTime(str_replace("/", "-", $payload["Arrival"]));
    $departure = new DateTime(str_replace("/", "-", $payload["Departure"]));
    $nights = $departure->diff($arrival)->days ?: 1;
    $rate = $data["Total Charge"] / $nights;
}

// ====== Capture Error Message if exists ======
$errorMsg = $leg["Guests"][0]["Error Message"] ?? "";

// ====== Prepare Result ======
$result = [
    "Unit Name" => $payload["Unit Name"],
    "Date Range" => $payload["Arrival"] . " → " . $payload["Departure"],
    "Rate" => $rate,
    "Error Message" => $errorMsg,
    "Guests" => $leg["Guests"] ?? [],
    "Available" => $leg["Error Code"] === 0
];

// Save simplified result
file_put_contents(__DIR__ . "/rates_data.json", json_encode($result, JSON_PRETTY_PRINT));

// ====== Output for debugging ======
echo "Payload sent to Gondwana API:\n";
print_r($apiPayload);

echo "\n\nRaw Response received:\n";
print_r($data);

echo "\n\nSimplified result saved to rates_data.json:\n";
print_r($result);
