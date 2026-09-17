<?php
require_once __DIR__ . '/functions.php';

define('AUTH_TOKEN_SECRET', 'kathmandu_parking_system_auth_secret_token_2026');

if (session_status() === PHP_SESSION_NONE) {
    if (isset($_REQUEST['sid']) && preg_match('/^[a-zA-Z0-9,-]+$/', $_REQUEST['sid'])) {
        session_id($_REQUEST['sid']);
    }
    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_secure', '1');
    session_set_cookie_params([
        'lifetime' => 86400 * 7,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'None'
    ]);
    session_start();
}

function generateAdminToken($adminId, $username) {
    return hash_hmac('sha256', $adminId . '|' . $username, AUTH_TOKEN_SECRET);
}

function verifyAdminToken($token) {
    if (empty($token) || !is_string($token)) {
        return false;
    }
    try {
        $db = getDbConnection();
        $stmt = $db->query("SELECT id, username FROM admins LIMIT 1");
        $admin = $stmt->fetch();
        if (!$admin) {
            return false;
        }
        $expected = generateAdminToken($admin['id'], $admin['username']);
        if (hash_equals($expected, $token)) {
            return $admin;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

function getActiveAdminToken() {
    if (isset($_REQUEST['admin_token']) && !empty($_REQUEST['admin_token'])) {
        return $_REQUEST['admin_token'];
    }
    if (isset($_COOKIE['admin_token']) && !empty($_COOKIE['admin_token'])) {
        return $_COOKIE['admin_token'];
    }
    if (isset($_SESSION['admin_id'], $_SESSION['admin_username'])) {
        return generateAdminToken($_SESSION['admin_id'], $_SESSION['admin_username']);
    }
    return '';
}

function isAdminLoggedIn() {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return true;
    }

    $token = $_REQUEST['admin_token'] ?? $_COOKIE['admin_token'] ?? '';
    if (!empty($token)) {
        $admin = verifyAdminToken($token);
        if ($admin) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
    }

    return false;
}

function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        $baseUrl = getBaseUrl();
        header("Location: {$baseUrl}/admin/login.php");
        exit;
    }
}

function getLoggedInAdminUsername() {
    return $_SESSION['admin_username'] ?? 'admin';
}

function adminUrl($path) {
    $baseUrl = getBaseUrl();
    $fullPath = $baseUrl . $path;
    $params = [];
    $token = getActiveAdminToken();
    if (!empty($token)) {
        $params['admin_token'] = $token;
    }
    if (session_id()) {
        $params['sid'] = session_id();
    }
    if (!empty($params)) {
        $sep = (strpos($fullPath, '?') === false) ? '?' : '&';
        return $fullPath . $sep . http_build_query($params);
    }
    return $fullPath;
}

function logoutAdmin() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    setcookie('admin_token', '', time() - 42000, '/', '', true, true);
    session_destroy();
}
