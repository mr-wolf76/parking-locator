<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Project | Parking Locator';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-secondary text-white mb-2">BIM 6th Semester Summer Project</span>
                <h1 class="h2 fw-bold text-dark mb-1">About Parking Locator</h1>
                <p class="text-muted mb-0 small">
                    A Web-Based System for Finding Parking Facilities Near Selected Destinations in Kathmandu
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container py-4 flex-grow-1">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border rounded-3 bg-white p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3">1. Background &amp; Problem Statement</h4>
                <p class="text-secondary leading-relaxed mb-3">
                    Kathmandu Valley has experienced an exponential rise in vehicle ownership, particularly two-wheelers and private automobiles. However, urban infrastructure expansion has not matched this vehicular surge. Commercial centers such as New Road, Thamel, Putalisadak, and New Baneshwor suffer from acute parking shortages.
                </p>
                <p class="text-secondary leading-relaxed mb-0">
                    Drivers frequently cruise corridors searching for parking, causing severe traffic jams, fuel wastage, and air pollution. In many instances, off-street parking facilities or designated municipal lots exist just a few hundred meters away, but drivers are unaware of their location, fee structure, or operating hours.
                </p>
            </div>

            <div class="card border rounded-3 bg-white p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3">2. System Purpose &amp; Methodology</h4>
                <p class="text-secondary leading-relaxed mb-3">
                    The primary objective of this project is to bridge this information gap. Instead of searching exclusively for a parking lot name that the user might not know, the user inputs their intended destination (e.g. where they want to shop, eat, or attend a meeting).
                </p>
                <p class="text-secondary leading-relaxed mb-3">
                    The system then computes straight-line distance using the mathematical Haversine formula based on geospatial coordinates (latitude and longitude) and displays nearby available facilities ranked by proximity.
                </p>
                <div class="p-3 bg-light rounded-2 border small mb-0">
                    <div class="fw-semibold text-dark mb-1">Haversine Distance Formulation:</div>
                    <div class="text-muted font-monospace mb-2">
                        a = sin²(Δφ/2) + cos(φ1) · cos(φ2) · sin²(Δλ/2)<br>
                        c = 2 · atan2(√a, √(1−a))<br>
                        d = R · c (where R = 6,371,000 meters)
                    </div>
                    <div class="text-muted">
                        This formula computes precise spatial distance across spherical coordinates to render human-readable distances (e.g. 450 m away or 1.2 km away).
                    </div>
                </div>
            </div>

            <div class="card border rounded-3 bg-white p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3">3. Technology Stack</h4>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-2 bg-light h-100">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-window me-1 text-primary"></i> Frontend Architecture</div>
                            <ul class="list-unstyled small text-muted mb-0">
                                <li>• HTML5 &amp; CSS3 semantic structure</li>
                                <li>• Bootstrap 5 grid &amp; responsive components</li>
                                <li>• Vanilla JavaScript for DOM &amp; instant filters</li>
                                <li>• Leaflet.js interactive mapping library</li>
                                <li>• OpenStreetMap open geospatial tiles</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-2 bg-light h-100">
                            <div class="fw-bold text-dark mb-1"><i class="bi bi-server me-1 text-primary"></i> Backend &amp; Database</div>
                            <ul class="list-unstyled small text-muted mb-0">
                                <li>• PHP (Hypertext Preprocessor)</li>
                                <li>• PDO (PHP Data Objects) with prepared statements</li>
                                <li>• MySQL Relational Database</li>
                                <li>• Session-based secure admin authentication</li>
                                <li>• Password hashing via bcrypt</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border rounded-3 bg-white p-4 mb-4">
                <h4 class="fw-bold text-dark mb-3">4. Important Real-World Limitations</h4>
                <p class="text-secondary leading-relaxed mb-3">
                    In order to maintain strict academic integrity and realistic software standards, the following constraints must be noted:
                </p>
                <div class="alert alert-light border mb-0 small text-secondary">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2"><strong>No Live Sensor Integration:</strong> The system does not present live vacant space counts or occupancy status (such as "3 spots left"). No ultrasonic or magnetometer IoT sensors are currently linked to these public lots.</li>
                        <li class="mb-2"><strong>Periodic Verification:</strong> Data points including operating hours, vehicle categories, and fee tariffs are periodically audited by system administrators. Each facility record displays an explicit "Last Verified" date.</li>
                        <li><strong>User Feedback Loop:</strong> A public reporting mechanism is integrated so motorists can flag altered rates, relocated gates, or discontinued parking services.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Academic Information</h5>
                <div class="small">
                    <div class="text-muted mb-1">Project Title</div>
                    <div class="fw-semibold text-dark mb-3">Parking Information and Locator System</div>

                    <div class="text-muted mb-1">Course &amp; Curriculum</div>
                    <div class="fw-semibold text-dark mb-3">Bachelor of Information Management (BIM)</div>

                    <div class="text-muted mb-1">Academic Requirement</div>
                    <div class="fw-semibold text-dark mb-3">6th Semester Summer Project</div>

                    <div class="text-muted mb-1">Study Area</div>
                    <div class="fw-semibold text-dark mb-3">Kathmandu Metropolitan City &amp; Lalitpur</div>

                    <div class="text-muted mb-1">Supervision Context</div>
                    <div class="fw-semibold text-dark">Faculty of Management, Tribhuvan University</div>
                </div>
            </div>

            <div class="card border rounded-3 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3">Need Help or Have Corrections?</h5>
                <p class="text-muted small mb-3">
                    Notice a closed parking lot, changed tariff, or incorrect operating hours? Help keep our database accurate.
                </p>
                <a href="<?php echo $baseUrl; ?>/report.php" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-flag me-1"></i> Submit Information Correction
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
