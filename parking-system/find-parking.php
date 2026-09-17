<?php
require_once __DIR__ . '/includes/functions.php';

$destination = isset($_GET['destination']) ? sanitizeInput($_GET['destination']) : '';
$paymentFilter = isset($_GET['payment']) ? sanitizeInput($_GET['payment']) : 'all';
$vehicleFilter = isset($_GET['vehicle']) ? sanitizeInput($_GET['vehicle']) : 'all';
$sortFilter = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'nearest';
$areaFilter = isset($_GET['area']) ? sanitizeInput($_GET['area']) : '';

$destCoords = null;
$destinationNotFound = false;

if (!empty($destination)) {
    $destCoords = resolveDestinationCoordinates($destination);
    if (!$destCoords) {
        $destinationNotFound = true;
    }
}

$filters = [
    'payment_type' => $paymentFilter,
    'vehicle_type' => $vehicleFilter,
    'sort' => $sortFilter,
    'area' => $areaFilter
];

if ($destCoords) {
    $parkingFacilities = getNearbyParking($destCoords['latitude'], $destCoords['longitude'], $filters);
} else {
    if (!empty($areaFilter)) {
        $filters['area'] = $areaFilter;
    }
    $parkingFacilities = getActiveParking($filters);
    foreach ($parkingFacilities as &$facility) {
        $facility['distance_formatted'] = null;
        $facility['distance_meters'] = null;
    }
    unset($facility);
}

$pageTitle = !empty($destination) ? 
    'Parking near ' . htmlspecialchars($destination) . ' | Parking Locator' : 
    'Find Parking in Kathmandu | Parking Locator';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container-fluid px-lg-4">
        <form action="<?php echo $baseUrl; ?>/find-parking.php" method="GET" id="search-filter-form">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                        </span>
                        <input type="text" name="destination" class="form-control border-start-0" placeholder="Destination in Kathmandu..." value="<?php echo htmlspecialchars($destination); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="payment" class="form-select" id="filter-payment">
                        <option value="all" <?php echo ($paymentFilter === 'all') ? 'selected' : ''; ?>>All Fees</option>
                        <option value="free" <?php echo ($paymentFilter === 'free') ? 'selected' : ''; ?>>Free Only</option>
                        <option value="paid" <?php echo ($paymentFilter === 'paid') ? 'selected' : ''; ?>>Paid Only</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-6">
                    <select name="vehicle" class="form-select" id="filter-vehicle">
                        <option value="all" <?php echo ($vehicleFilter === 'all') ? 'selected' : ''; ?>>All Vehicles</option>
                        <option value="car" <?php echo ($vehicleFilter === 'car') ? 'selected' : ''; ?>>Car</option>
                        <option value="motorcycle" <?php echo ($vehicleFilter === 'motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                        <option value="both" <?php echo ($vehicleFilter === 'both') ? 'selected' : ''; ?>>Car &amp; Bike</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-6">
                    <select name="sort" class="form-select" id="filter-sort">
                        <option value="nearest" <?php echo ($sortFilter === 'nearest') ? 'selected' : ''; ?>>Nearest First</option>
                        <option value="farthest" <?php echo ($sortFilter === 'farthest') ? 'selected' : ''; ?>>Farthest</option>
                        <option value="fee_asc" <?php echo ($sortFilter === 'fee_asc') ? 'selected' : ''; ?>>Lowest Fee</option>
                        <option value="fee_desc" <?php echo ($sortFilter === 'fee_desc') ? 'selected' : ''; ?>>Highest Fee</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 col-6 text-lg-end">
                    <a href="<?php echo $baseUrl; ?>/find-parking.php" class="btn btn-outline-secondary w-100 w-lg-auto" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container-fluid px-lg-4 py-3 flex-grow-1">
    <?php if ($destinationNotFound): ?>
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                We couldn't find that destination. Try searching with a nearby area or landmark such as <strong>Thamel</strong>, <strong>New Road</strong>, <strong>Baneshwor</strong>, or <strong>Patan</strong>. Showing all registered facilities below.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-5 col-xl-5 order-2 order-lg-1">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <?php if ($destCoords): ?>
                            Parking near "<?php echo htmlspecialchars($destCoords['name']); ?>"
                        <?php elseif (!empty($areaFilter)): ?>
                            Parking in "<?php echo htmlspecialchars($areaFilter); ?>"
                        <?php else: ?>
                            All Parking Facilities
                        <?php endif; ?>
                    </h5>
                    <div class="text-muted small">
                        Showing <span id="results-count-badge" class="fw-semibold text-dark"><?php echo count($parkingFacilities); ?></span> verified facilities
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <input type="text" id="filter-keyword" class="form-control form-control-sm" placeholder="Quick filter list...">
                </div>
            </div>

            <div id="empty-results-state" class="<?php echo count($parkingFacilities) === 0 ? '' : 'd-none'; ?> p-5 text-center bg-white border rounded-3 my-3">
                <div class="stat-icon-wrapper bg-light text-muted mx-auto mb-3">
                    <i class="bi bi-slash-circle"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">No parking facilities found</h5>
                <p class="text-muted small mb-3">
                    Try another destination or change your payment and vehicle filters.
                </p>
                <a href="<?php echo $baseUrl; ?>/find-parking.php" class="btn btn-outline-primary btn-sm">
                    Clear All Filters
                </a>
            </div>

            <div class="parking-results-scroll pe-lg-1" id="parking-results-container" style="max-height: calc(100vh - 230px); overflow-y: auto;">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($parkingFacilities as $facility): ?>
                        <?php
                            $jsonAttr = htmlspecialchars(json_encode([
                                'id' => (int)$facility['id'],
                                'name' => $facility['name'],
                                'address' => $facility['address'],
                                'area' => $facility['area'],
                                'latitude' => (float)$facility['latitude'],
                                'longitude' => (float)$facility['longitude'],
                                'payment_type' => $facility['payment_type'],
                                'parking_fee' => $facility['parking_fee'],
                                'vehicle_type' => $facility['vehicle_type'],
                                'distance_formatted' => $facility['distance_formatted'] ?? null
                            ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="parking-card parking-card-item p-3 cursor-pointer" 
                             id="parking-card-<?php echo $facility['id']; ?>"
                             data-id="<?php echo $facility['id']; ?>"
                             data-payment="<?php echo htmlspecialchars($facility['payment_type']); ?>"
                             data-vehicle="<?php echo htmlspecialchars($facility['vehicle_type']); ?>"
                             data-name="<?php echo htmlspecialchars($facility['name']); ?>"
                             data-address="<?php echo htmlspecialchars($facility['address']); ?>"
                             data-area="<?php echo htmlspecialchars($facility['area']); ?>"
                             data-json="<?php echo $jsonAttr; ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="d-flex flex-wrap gap-1 align-items-center mb-1">
                                        <?php if ($facility['payment_type'] === 'free'): ?>
                                            <span class="badge badge-payment-free">Free Parking</span>
                                        <?php else: ?>
                                            <span class="badge badge-payment-paid">Paid</span>
                                        <?php endif; ?>

                                        <?php if ($facility['vehicle_type'] === 'car'): ?>
                                            <span class="badge badge-vehicle"><i class="bi bi-car-front me-1"></i>Car</span>
                                        <?php elseif ($facility['vehicle_type'] === 'motorcycle'): ?>
                                            <span class="badge badge-vehicle"><i class="bi bi-bicycle me-1"></i>Bike</span>
                                        <?php else: ?>
                                            <span class="badge badge-vehicle"><i class="bi bi-car-front me-1"></i>Car &amp; Bike</span>
                                        <?php endif; ?>

                                        <span class="badge bg-light text-muted border"><?php echo htmlspecialchars($facility['area']); ?></span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($facility['name']); ?></h6>
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($facility['address']); ?>
                                    </div>
                                </div>
                                <?php if (!empty($facility['distance_formatted'])): ?>
                                    <div class="text-end ps-2">
                                        <span class="badge bg-light text-primary border border-primary-subtle fs-7 fw-semibold">
                                            <i class="bi bi-signpost-2 me-1"></i><?php echo htmlspecialchars($facility['distance_formatted']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 mt-2 border-top gap-2">
                                <div class="small text-muted">
                                    <span class="fw-semibold text-dark">
                                        <?php echo !empty($facility['parking_fee']) ? htmlspecialchars($facility['parking_fee']) : ($facility['payment_type'] === 'free' ? 'Free of charge' : 'Standard rate'); ?>
                                    </span>
                                    <span class="mx-1">•</span>
                                    <span><?php echo formatTime($facility['opening_time']); ?> – <?php echo formatTime($facility['closing_time']); ?></span>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo $baseUrl; ?>/parking-details.php?id=<?php echo $facility['id']; ?>" class="btn btn-sm btn-outline-primary px-3">
                                        Details
                                    </a>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $facility['latitude']; ?>,<?php echo $facility['longitude']; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary px-3" title="Open GPS directions in Google Maps">
                                        <i class="bi bi-arrow-up-right me-1"></i> Directions
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-xl-7 order-1 order-lg-2">
            <div class="sticky-top" style="top: 80px;">
                <div id="finder-map" class="map-container shadow-sm"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/assets/js/map.js"></script>
<script src="<?php echo $baseUrl; ?>/assets/js/parking.js"></script>
<script>
window.appBaseUrl = <?php echo json_encode($baseUrl); ?>;
document.addEventListener('DOMContentLoaded', function() {
    var destData = <?php echo json_encode($destCoords); ?>;
    var facilities = <?php echo json_encode(array_values($parkingFacilities)); ?>;
    initFinderMap(destData, facilities, window.appBaseUrl);
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
