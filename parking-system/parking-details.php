<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$facility = getParkingById($id);

if (!$facility || $facility['status'] !== 'active') {
    $pageTitle = 'Parking Facility Not Found | Parking Locator';
    require_once __DIR__ . '/includes/header.php';
    require_once __DIR__ . '/includes/navbar.php';
    ?>
    <div class="container py-5 text-center flex-grow-1">
        <div class="py-5">
            <div class="stat-icon-wrapper bg-light text-muted mx-auto mb-3">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Parking Facility Not Found</h3>
            <p class="text-muted mb-4">The requested parking facility is either inactive, removed, or does not exist.</p>
            <a href="<?php echo $baseUrl; ?>/find-parking.php" class="btn btn-primary px-4">
                Back to Find Parking
            </a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = htmlspecialchars($facility['name']) . ' - Details | Parking Locator';
$directionsUrl = "https://www.google.com/maps/dir/?api=1&destination=" . $facility['latitude'] . "," . $facility['longitude'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?php echo $baseUrl; ?>/index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $baseUrl; ?>/find-parking.php" class="text-decoration-none">Find Parking</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $baseUrl; ?>/find-parking.php?area=<?php echo urlencode($facility['area']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($facility['area']); ?></a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 250px;"><?php echo htmlspecialchars($facility['name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4 flex-grow-1">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border rounded-3 bg-white p-4 p-md-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <?php if ($facility['payment_type'] === 'free'): ?>
                            <span class="badge badge-payment-free px-3 py-2 fs-7">Free Parking</span>
                        <?php else: ?>
                            <span class="badge badge-payment-paid px-3 py-2 fs-7">Paid Facility</span>
                        <?php endif; ?>

                        <?php if ($facility['vehicle_type'] === 'car'): ?>
                            <span class="badge badge-vehicle px-3 py-2 fs-7"><i class="bi bi-car-front me-1"></i>Car Only</span>
                        <?php elseif ($facility['vehicle_type'] === 'motorcycle'): ?>
                            <span class="badge badge-vehicle px-3 py-2 fs-7"><i class="bi bi-bicycle me-1"></i>Motorcycle / Scooter</span>
                        <?php else: ?>
                            <span class="badge badge-vehicle px-3 py-2 fs-7"><i class="bi bi-car-front me-1"></i>Car &amp; Motorcycle</span>
                        <?php endif; ?>

                        <span class="badge bg-light text-dark border px-3 py-2 fs-7">
                            <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($facility['area']); ?>
                        </span>
                    </div>

                    <div class="text-muted small">
                        <i class="bi bi-clock-history me-1"></i> Verified: <?php echo formatDate($facility['last_verified']); ?>
                    </div>
                </div>

                <h2 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($facility['name']); ?></h2>
                <p class="text-muted mb-4 fs-6">
                    <i class="bi bi-pin-map-fill text-danger me-1"></i> <?php echo htmlspecialchars($facility['address']); ?>
                </p>

                <?php if (!empty($facility['image'])): ?>
                    <div class="mb-4 rounded-3 overflow-hidden border">
                        <img src="<?php echo $baseUrl; ?>/uploads/parking/<?php echo htmlspecialchars($facility['image']); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" class="img-fluid w-100" style="max-height: 380px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-2">About This Facility</h5>
                    <p class="text-secondary leading-relaxed mb-0">
                        <?php echo !empty($facility['description']) ? nl2br(htmlspecialchars($facility['description'])) : 'No additional descriptive details have been registered for this parking facility yet.'; ?>
                    </p>
                </div>

                <div class="row g-3 py-3 border-top border-bottom mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small mb-1">Pricing Structure</div>
                        <div class="fw-bold text-dark">
                            <?php echo !empty($facility['parking_fee']) ? htmlspecialchars($facility['parking_fee']) : ($facility['payment_type'] === 'free' ? 'Free of charge' : 'Standard local rate'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small mb-1">Operating Hours</div>
                        <div class="fw-bold text-dark">
                            <?php echo formatTime($facility['opening_time']); ?> – <?php echo formatTime($facility['closing_time']); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small mb-1">Allowed Vehicles</div>
                        <div class="fw-bold text-dark text-capitalize">
                            <?php echo htmlspecialchars($facility['vehicle_type']); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small mb-1">Contact Phone</div>
                        <div class="fw-bold text-dark">
                            <?php echo !empty($facility['contact']) ? htmlspecialchars($facility['contact']) : 'Not listed'; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo $directionsUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary px-4 py-2" id="get-directions-btn">
                        <i class="bi bi-arrow-up-right me-1"></i> Get Directions
                    </a>
                    <a href="<?php echo $baseUrl; ?>/report.php?parking_id=<?php echo $facility['id']; ?>" class="btn btn-outline-secondary px-3 py-2" id="report-issue-btn">
                        <i class="bi bi-flag me-1"></i> Report Incorrect Info
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border rounded-3 bg-white p-3 mb-4 sticky-top" style="top: 80px;">
                <h5 class="fw-bold text-dark mb-3">Facility Location Map</h5>
                <div id="detail-map" class="detail-map-container mb-3"></div>

                <div class="p-3 bg-light rounded-2 border mb-3 small text-muted">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Latitude:</span>
                        <span class="font-monospace text-dark"><?php echo number_format($facility['latitude'], 6); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Longitude:</span>
                        <span class="font-monospace text-dark"><?php echo number_format($facility['longitude'], 6); ?></span>
                    </div>
                </div>

                <a href="<?php echo $directionsUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-compass me-1"></i> Start GPS Navigation
                </a>
                <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=<?php echo urlencode($facility['area']); ?>" class="btn btn-outline-secondary w-100">
                    Find More in <?php echo htmlspecialchars($facility['area']); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/assets/js/map.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDetailMap(
        <?php echo (float)$facility['latitude']; ?>,
        <?php echo (float)$facility['longitude']; ?>,
        <?php echo json_encode($facility['name']); ?>,
        <?php echo json_encode($facility['address']); ?>
    );
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
