<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/constants.php"; // <-- Unit Type IDs are defined here

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Only POST method allowed"]);
    exit;
}

// Get JSON body
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// Extract values
$unitName      = $input["Unit Name"] ?? null;
$arrivalDate   = $input["Arrival"] ?? null;
$departureDate = $input["Departure"] ?? null;
$ages          = $input["Ages"] ?? [];

if (!$unitName || !$arrivalDate || !$departureDate) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Map Unit Name to Unit Type ID from constants
$unitTypeId = UNIT_TYPE_IDS[$unitName] ?? null;
if (!$unitTypeId) {
    http_response_code(400);
    echo json_encode(["error" => "Unknown Unit Name"]);
    exit;
}

// Convert dd/mm/yyyy to yyyy-mm-dd
function convertDate($date) {
    $parts = explode("/", $date);
    if (count($parts) === 3) {
        return $parts[2] . "-" . $parts[1] . "-" . $parts[0];
    }
    return null;
}
$arrival = convertDate($arrivalDate);
$departure = convertDate($departureDate);

// Convert ages to Guests array
$guests = array_map(function($age) {
    return ["Age Group" => ($age >= 13 ? "Adult" : "Child"), "Age" => $age];
}, $ages);

// Build Gondwana payload
$payload = [
    "Unit Type ID" => $unitTypeId,
    "Arrival"      => $arrival,
    "Departure"    => $departure,
    "Guests"       => $guests
];

// Call Gondwana API
$remoteAPI = "https://dev.gondwana-collection.com/Web-Store/Rates/Rates.php";
$ch = curl_init($remoteAPI);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Save debug
file_put_contents(__DIR__ . "/debug_gondwana.json", $response);

if ($httpcode !== 200 || !$response) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch rates from Gondwana"]);
    exit;
}

// Return raw Gondwana API response to frontend
echo $response;
exit;
