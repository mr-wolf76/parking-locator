<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/includes/functions.php';

$query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$landmarks = getKathmanduLandmarks();
$destinations = [];

foreach ($landmarks as $key => $data) {
    if (!empty($query)) {
        if (strpos(strtolower($data['name']), $query) === false && strpos($key, $query) === false) {
            continue;
        }
    }
    $destinations[] = [
        'id' => $key,
        'name' => $data['name'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude']
    ];
}

echo json_encode([
    'status' => 'success',
    'count' => count($destinations),
    'destinations' => $destinations
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
