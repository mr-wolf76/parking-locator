<?php
/**
 * Simple Authentication Helper for Admin
 * Uses PHP Sessions (standard beginner-friendly approach for XAMPP)
 */
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is currently logged in via session
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Restrict access to admin pages; redirect to login if not authenticated
function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        $baseUrl = getBaseUrl();
        header("Location: {$baseUrl}/admin/login.php");
        exit();
    }
}

// Get the current logged in admin username
function getLoggedInAdminUsername() {
    return $_SESSION['admin_username'] ?? 'admin';
}

// Helper to generate admin URLs
function adminUrl($path) {
    $baseUrl = getBaseUrl();
    return $baseUrl . $path;
}
