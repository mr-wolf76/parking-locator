<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Parking Areas in Kathmandu | Parking Locator';
$areas = getParkingAreas();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="h3 fw-bold text-dark mb-1">Kathmandu Parking Study Areas</h1>
        <p class="text-muted mb-0 small">
            Major commercial hubs, heritage corridors, and transit junctions monitored in this study.
        </p>
    </div>
</div>

<div class="container py-4 flex-grow-1">
    <div class="row g-4">
        <?php foreach ($areas as $area): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-3 bg-white h-100 p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($area['area']); ?></h5>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Kathmandu Valley
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-7">
                            <?php echo (int)$area['total_facilities']; ?> Facilities
                        </span>
                    </div>

                    <div class="p-3 bg-light rounded-2 border mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Paid Parking Facilities:</span>
                            <span class="fw-semibold text-dark"><?php echo (int)$area['paid_count']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Free Customer/Public Bays:</span>
                            <span class="fw-semibold text-success"><?php echo (int)$area['free_count']; ?></span>
                        </div>
                    </div>

                    <div class="mt-auto d-flex gap-2">
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=<?php echo urlencode($area['area']); ?>" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-search me-1"></i> Find Parking
                        </a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?area=<?php echo urlencode($area['area']); ?>" class="btn btn-outline-secondary btn-sm">
                            View All
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
