<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once dirname(__DIR__) . '/includes/functions.php';

$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];

$parkingId = (int)($_POST['parking_id'] ?? $jsonBody['parking_id'] ?? 0);
$issueType = trim((string)($_POST['issue_type'] ?? $_POST['report_type'] ?? $jsonBody['issue_type'] ?? $jsonBody['report_type'] ?? ''));
$message = trim((string)($_POST['message'] ?? $jsonBody['message'] ?? ''));
$contact = trim((string)($_POST['reporter_contact'] ?? $jsonBody['reporter_contact'] ?? ''));

if ($parkingId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Valid parking facility ID is required.'
    ]);
    exit;
}

if (empty($issueType)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select the type of issue.'
    ]);
    exit;
}

if (empty($message)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please provide details about the issue.'
    ]);
    exit;
}

$facility = getParkingById($parkingId);
if (!$facility) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Referenced parking facility does not exist.'
    ]);
    exit;
}

$saved = createReport($parkingId, $issueType, $message, $contact);

if ($saved) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you. Your report has been submitted for review.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to save report at this time. Please try again later.'
    ]);
}
