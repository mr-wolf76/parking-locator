<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/functions.php';

$destinationQuery = trim($_GET['destination'] ?? $_GET['q'] ?? '');
$latitude = isset($_GET['lat']) && is_numeric($_GET['lat']) ? (float)$_GET['lat'] : null;
$longitude = isset($_GET['lng']) && is_numeric($_GET['lng']) ? (float)$_GET['lng'] : null;
$vehicleFilter = strtolower(trim($_GET['vehicle'] ?? $_GET['vehicle_type'] ?? 'all'));
$paymentFilter = strtolower(trim($_GET['payment'] ?? $_GET['payment_type'] ?? 'all'));
$sortFilter = strtolower(trim($_GET['sort'] ?? 'nearest'));

$destinationData = null;
if ($latitude !== null && $longitude !== null) {
    $destinationData = [
        'name' => !empty($destinationQuery) ? $destinationQuery : 'Selected Destination',
        'latitude' => $latitude,
        'longitude' => $longitude
    ];
} elseif (!empty($destinationQuery)) {
    $matched = resolveDestinationCoordinates($destinationQuery);
    if ($matched) {
        $destinationData = $matched;
    }
}

$filters = [
    'payment_type' => $paymentFilter,
    'vehicle_type' => $vehicleFilter,
    'sort' => $sortFilter
];

if ($destinationData) {
    $facilities = getNearbyParking($destinationData['latitude'], $destinationData['longitude'], $filters);
} else {
    $facilities = getActiveParking($filters);
    foreach ($facilities as &$f) {
        $f['distance_meters'] = null;
        $f['distance_formatted'] = null;
    }
    unset($f);
}

foreach ($facilities as &$facility) {
    $facility['parking_id'] = (int)$facility['id'];
}
unset($facility);

echo json_encode([
    'status' => 'success',
    'destination' => $destinationData,
    'total_facilities' => count($facilities),
    'filters' => [
        'vehicle' => $vehicleFilter,
        'payment' => $paymentFilter,
        'sort' => $sortFilter
    ],
    'facilities' => $facilities
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
