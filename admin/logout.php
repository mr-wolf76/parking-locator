<?php
require_once dirname(__DIR__) . '/includes/functions.php';
$baseUrl = getBaseUrl();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

header("Location: {$baseUrl}/admin/login.php?logged_out=1");
exit();
