<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Parking Information and Locator System | Kathmandu';
$areas = getParkingAreas();
$recentFacilities = getActiveParking(['sort' => 'latest']);
$recentFacilities = array_slice($recentFacilities, 0, 4);

require_once __DIR__ . '/includes/header.php';
?>

<div class="hero-section py-5">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-medium mb-3">
                    <i class="bi bi-geo-alt-fill me-1"></i> Kathmandu Valley Parking Information
                </span>
                <h1 class="display-5 fw-bold text-dark mb-3">Find Parking Near Your Destination</h1>
                <p class="lead text-muted mb-4 mx-auto" style="max-width: 680px;">
                    Search your destination and discover nearby parking facilities with location, pricing, operating hours and directions.
                </p>

                <div class="search-box-card rounded-3 p-3 p-md-4 mb-4 text-start">
                    <form action="<?php echo $baseUrl; ?>/find-parking.php" method="GET" class="row g-2 align-items-center" id="home-search-form">
                        <div class="col-md-9">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                                    <i class="bi bi-search text-primary"></i>
                                </span>
                                <input type="text" name="destination" id="home-search-input" class="form-control border-start-0 ps-2" placeholder="Where are you going? (e.g. Thamel, New Road, Baneshwor...)" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold" id="home-search-submit">
                                Find Parking
                            </button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top">
                        <span class="small text-muted fw-medium me-1">Popular destinations:</span>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Thamel" class="destination-chip quick-destination-chip" data-destination="Thamel">Thamel</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=New+Road" class="destination-chip quick-destination-chip" data-destination="New Road">New Road</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Baneshwor" class="destination-chip quick-destination-chip" data-destination="Baneshwor">Baneshwor</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Putalisadak" class="destination-chip quick-destination-chip" data-destination="Putalisadak">Putalisadak</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Kalanki" class="destination-chip quick-destination-chip" data-destination="Kalanki">Kalanki</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Patan" class="destination-chip quick-destination-chip" data-destination="Patan">Patan</a>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=Maharajgunj" class="destination-chip quick-destination-chip" data-destination="Maharajgunj">Maharajgunj</a>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-3 text-muted small">
                    <span><i class="bi bi-shield-check text-success me-1"></i> Verified information</span>
                    <span>•</span>
                    <span><i class="bi bi-geo me-1"></i> Distance calculation</span>
                    <span>•</span>
                    <span><i class="bi bi-map me-1"></i> Interactive maps</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">How The System Works</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">
            A practical web-based utility to eliminate roadside congestion by directing drivers to designated parking spaces.
        </p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border p-3 rounded-3 bg-white" id="step-card-1">
                <div class="card-body">
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-pin-map-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">1. Enter Destination</h5>
                    <p class="text-muted small mb-0">
                        Type the landmark, market, hospital, or commercial hub in Kathmandu where you are heading. The system identifies precise coordinates.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border p-3 rounded-3 bg-white" id="step-card-2">
                <div class="card-body">
                    <div class="stat-icon-wrapper bg-success-subtle text-success mb-3">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">2. Compare Options</h5>
                    <p class="text-muted small mb-0">
                        Review parking facilities sorted by walking distance, filter by fee structure (Free or Paid), and select suitable vehicle categories.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border p-3 rounded-3 bg-white" id="step-card-3">
                <div class="card-body">
                    <div class="stat-icon-wrapper bg-info-subtle text-info mb-3">
                        <i class="bi bi-compass-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">3. Navigate & Park</h5>
                    <p class="text-muted small mb-0">
                        Inspect verified gate timings, rates, contact details, and trigger one-click turn-by-turn navigation directly to the facility.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Explore Parking by Area</h3>
            <p class="text-muted small mb-0">Select a major area in Kathmandu to see all registered facilities.</p>
        </div>
        <a href="<?php echo $baseUrl; ?>/areas.php" class="btn btn-outline-primary btn-sm px-3" id="view-all-areas-link">
            All Areas <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-3 mb-5">
        <?php foreach (array_slice($areas, 0, 6) as $area): ?>
            <div class="col-md-4 col-sm-6">
                <a href="<?php echo $baseUrl; ?>/find-parking.php?destination=<?php echo urlencode($area['area']); ?>" class="card text-decoration-none text-dark border p-3 rounded-3 bg-white h-100 hover-shadow transition">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold text-dark fs-6 mb-1"><?php echo htmlspecialchars($area['area']); ?></div>
                            <div class="text-muted small">
                                <?php echo (int)$area['total_facilities']; ?> verified facilities
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary border">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card border rounded-3 bg-white p-4 p-md-5 mb-4" id="academic-note-card">
        <div class="row align-items-center gy-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-secondary text-white">Project Scope & Reliability</span>
                    <span class="text-muted small">BIM 6th Semester Summer Project</span>
                </div>
                <h4 class="fw-bold text-dark mb-2">Data Transparency & System Notice</h4>
                <p class="text-muted small mb-0">
                    This system provides verified facility specifications, operational hours, and fee policies. The system does not claim to track real-time empty spot availability as IoT sensor infrastructure is not deployed. Each facility record displays its last verification date.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo $baseUrl; ?>/about.php" class="btn btn-outline-secondary px-4 py-2" id="learn-more-link">
                    Read Project Details
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
