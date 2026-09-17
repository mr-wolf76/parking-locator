<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireAdminAuth();

if (!isset($adminTitle)) {
    $adminTitle = 'Admin Panel | Parking Locator';
}
$baseUrl = getBaseUrl();
$currentAdminScript = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function() {
            try {
                var currentFetch = window.fetch;
                var proto = Object.getPrototypeOf(window);
                var protoDesc = proto ? Object.getOwnPropertyDescriptor(proto, 'fetch') : null;
                if (protoDesc && !protoDesc.set && protoDesc.configurable) {
                    Object.defineProperty(proto, 'fetch', {
                        get: function() { return currentFetch; },
                        set: function(fn) { currentFetch = fn; },
                        configurable: true,
                        enumerable: true
                    });
                }
            } catch (e) {}

            try {
                var _f = window.fetch ? window.fetch.bind(window) : null;
                Object.defineProperty(window, 'fetch', {
                    get: function() { return _f; },
                    set: function(fn) { _f = fn; },
                    configurable: true,
                    enumerable: true
                });
            } catch (e) {}
        })();
    </script>
    <title><?php echo htmlspecialchars($adminTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white fs-6" href="<?php echo adminUrl('/admin/index.php'); ?>">
            <span class="badge bg-primary text-white p-2">
                <i class="bi bi-shield-lock-fill"></i>
            </span>
            <span>PARKING LOCATOR ADMIN</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo $baseUrl; ?>/index.php" target="_blank" class="btn btn-outline-light btn-sm" title="Open Public Website">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Site
            </a>
            <span class="text-white-50 small d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars(getLoggedInAdminUsername()); ?>
            </span>
            <a href="<?php echo adminUrl('/admin/logout.php'); ?>" class="btn btn-danger btn-sm" title="Log Out">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-lg-2 col-md-3 admin-sidebar p-3 border-end">
            <div class="d-flex flex-column gap-1">
                <div class="text-uppercase text-muted fw-bold px-3 py-2" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    Navigation
                </div>
                <a href="<?php echo adminUrl('/admin/index.php'); ?>" class="admin-nav-link <?php echo ($currentAdminScript === 'index.php') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="admin-nav-link <?php echo ($currentAdminScript === 'parking.php' || $currentAdminScript === 'edit-parking.php') ? 'active' : ''; ?>">
                    <i class="bi bi-p-square"></i> Facilities List
                </a>
                <a href="<?php echo adminUrl('/admin/add-parking.php'); ?>" class="admin-nav-link <?php echo ($currentAdminScript === 'add-parking.php') ? 'active' : ''; ?>">
                    <i class="bi bi-plus-circle"></i> Add Facility
                </a>
                <a href="<?php echo adminUrl('/admin/reports.php'); ?>" class="admin-nav-link <?php echo ($currentAdminScript === 'reports.php') ? 'active' : ''; ?>">
                    <i class="bi bi-flag"></i> User Reports
                </a>
                <div class="border-top my-3"></div>
                <div class="text-uppercase text-muted fw-bold px-3 py-2" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                    System Information
                </div>
                <div class="px-3 text-muted small">
                    <div>BIM Summer Project</div>
                    <div>PHP + SQLite/MySQL</div>
                    <div class="mt-2 text-dark fw-medium">Ver. 1.0 (2026)</div>
                </div>
            </div>
        </div>
        <div class="col-lg-10 col-md-9 p-3 p-lg-4">
