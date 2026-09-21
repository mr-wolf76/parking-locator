<?php
/**
 * Real-Time Parking Availability API Endpoint
 * Project: Parking Information and Locator System (Kathmandu)
 * 
 * Provides live parking space status via JSON for frontend AJAX polling
 * and handles gate sensor/simulation entry & exit updates.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request: Return live availability data
if ($method === 'GET') {
    $facilityId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($facilityId) {
        $facility = getParkingById($facilityId);
        if (!$facility) {
            echo json_encode(['success' => false, 'error' => 'Parking facility not found']);
            exit;
        }
        echo json_encode([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
            'facility' => formatRealtimeSpacesData($facility)
        ]);
        exit;
    }

    // Return all facilities live data
    $allFacilities = getAllRealtimeFacilities();
    echo json_encode([
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'count' => count($allFacilities),
        'facilities' => $allFacilities
    ]);
    exit;
}

// Handle POST request: Gate operations, adjustments, or simulations
if ($method === 'POST') {
    // Support JSON payload or standard form POST
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';

    // Action 1: Simulate random gate traffic (useful for teacher viva/demo)
    if ($action === 'simulate') {
        $result = simulateRandomTraffic();
        echo json_encode($result);
        exit;
    }

    // Action 2: Vehicle entry or exit adjustment
    if ($action === 'adjust') {
        $facilityId = (int)($input['facility_id'] ?? 0);
        $vehicleType = $input['vehicle_type'] ?? 'both';
        $direction = $input['direction'] ?? 'entry';

        if (!$facilityId) {
            echo json_encode(['success' => false, 'error' => 'Missing facility ID']);
            exit;
        }

        $result = adjustFacilitySpace($facilityId, $vehicleType, $direction);
        echo json_encode($result);
        exit;
    }

    // Action 3: Set exact spaces for a facility (attendant manual adjustment)
    if ($action === 'set') {
        $facilityId = (int)($input['facility_id'] ?? 0);
        $availableBike = isset($input['available_bike']) ? (int)$input['available_bike'] : 0;
        $availableCar = isset($input['available_car']) ? (int)$input['available_car'] : 0;

        if (!$facilityId) {
            echo json_encode(['success' => false, 'error' => 'Missing facility ID']);
            exit;
        }

        $result = setFacilitySpaces($facilityId, $availableBike, $availableCar);
        echo json_encode($result);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid action specified']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unsupported request method']);
