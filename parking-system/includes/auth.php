<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        $baseUrl = getBaseUrl();
        header("Location: {$baseUrl}/admin/login.php");
        exit;
    }
}

function getLoggedInAdminUsername() {
    return $_SESSION['admin_username'] ?? 'Admin';
}
