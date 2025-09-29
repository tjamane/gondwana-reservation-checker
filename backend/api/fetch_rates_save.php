<?php
require_once __DIR__ . "/../config/constants.php";

// Example test payload
$payload = [
    "Unit Name" => "Mountain Cabin",
    "Arrival" => "26/09/2025",
    "Departure" => "28/09/2025",
    "Occupants" => 1,
    "Ages" => [30]
];

// Convert to Gondwana API format
$unitTypeId = UNIT_TYPE_IDS[$payload["Unit Name"]] ?? null;
if (!$unitTypeId) {
    die("Unknown Unit Name");
}

$apiPayload = [
    "Unit Type ID" => $unitTypeId,
    "Arrival" => date("Y-m-d", strtotime(str_replace("/", "-", $payload["Arrival"]))),
    "Departure" => date("Y-m-d", strtotime(str_replace("/", "-", $payload["Departure"]))),
    "Guests" => array_map(function($age){
        return ["Age Group" => ($age >= 13 ? "Adult" : "Child")];
    }, $payload["Ages"])
];

// Send POST request to Gondwana API
$ch = curl_init(GONDWANA_API);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200 || !$response) {
    die("Failed to fetch rates from Gondwana");
}

// Decode response
$data = json_decode($response, true);
if (!$data) {
    die("Invalid JSON response from Gondwana");
}

// Extract date range, name, and rate
$leg = $data["Legs"][0] ?? null;
if (!$leg) {
    die("No rates found");
}

$rateData = [
    "Unit Name" => $payload["Unit Name"],
    "Date Range" => $payload["Arrival"] . " → " . $payload["Departure"],
    "Rate" => $leg["Effective Average Daily Rate"] ?? null
];

// Save to JSON file
file_put_contents(__DIR__ . "/rates_data.json", json_encode($rateData, JSON_PRETTY_PRINT));

echo "Data saved to rates_data.json:\n";
print_r($rateData);
