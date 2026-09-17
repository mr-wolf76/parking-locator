<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $facility = getParkingById($id);
    if (!$facility) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Parking facility not found'
        ]);
        exit;
    }

    $facility['parking_id'] = (int)$facility['id'];
    echo json_encode([
        'status' => 'success',
        'facility' => $facility
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    $all = getAllActiveParking();
    foreach ($all as &$item) {
        $item['parking_id'] = (int)$item['id'];
    }
    unset($item);

    echo json_encode([
        'status' => 'success',
        'count' => count($all),
        'facilities' => $all
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
