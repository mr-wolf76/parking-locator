<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

if (!isset($pageTitle)) {
    $pageTitle = 'Parking Information and Locator System | Kathmandu';
}
$baseUrl = getBaseUrl();
$currentScript = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="A Web-Based System for Finding Parking Facilities Near Selected Destinations in Kathmandu">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2" id="main-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="<?php echo $baseUrl; ?>/index.php" id="nav-brand-link">
            <span class="brand-icon-box text-white bg-primary rounded d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-p-square-fill"></i>
            </span>
            <span class="brand-title">PARKING LOCATOR</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent" aria-controls="navbarMainContent" aria-expanded="false" aria-label="Toggle navigation" id="nav-toggle-btn">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMainContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($currentScript === 'index.php') ? 'active fw-semibold text-primary' : ''; ?>" href="<?php echo $baseUrl; ?>/index.php" id="nav-link-home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($currentScript === 'find-parking.php') ? 'active fw-semibold text-primary' : ''; ?>" href="<?php echo $baseUrl; ?>/find-parking.php" id="nav-link-find">Find Parking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($currentScript === 'areas.php') ? 'active fw-semibold text-primary' : ''; ?>" href="<?php echo $baseUrl; ?>/areas.php" id="nav-link-areas">Parking Areas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 <?php echo ($currentScript === 'about.php') ? 'active fw-semibold text-primary' : ''; ?>" href="<?php echo $baseUrl; ?>/about.php" id="nav-link-about">About</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if (isAdminLoggedIn()): ?>
                    <a href="<?php echo $baseUrl; ?>/admin/index.php" class="btn btn-outline-primary btn-sm px-3" id="nav-btn-admin-dash">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    <a href="<?php echo $baseUrl; ?>/admin/logout.php" class="btn btn-light btn-sm text-secondary px-2" id="nav-btn-admin-logout" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $baseUrl; ?>/admin/login.php" class="btn btn-outline-secondary btn-sm px-3" id="nav-btn-admin-login">
                        <i class="bi bi-lock me-1"></i> Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
