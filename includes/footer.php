<?php
$baseUrl = getBaseUrl();
?>
<footer class="bg-white border-top mt-auto py-4" id="main-footer">
    <div class="container">
        <div class="row align-items-center gy-3">
            <div class="col-md-6 text-center text-md-start">
                <div class="fw-semibold text-dark mb-1">Parking Information and Locator System</div>
                <div class="text-muted small">Academic Summer Project — Bachelor of Information Management (BIM)</div>
                <div class="text-muted small">Kathmandu, Nepal</div>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-3 small">
                    <a href="<?php echo $baseUrl; ?>/index.php" class="text-decoration-none text-secondary" id="footer-link-home">Home</a>
                    <a href="<?php echo $baseUrl; ?>/find-parking.php" class="text-decoration-none text-secondary" id="footer-link-find">Find Parking</a>
                    <a href="<?php echo $baseUrl; ?>/areas.php" class="text-decoration-none text-secondary" id="footer-link-areas">Parking Areas</a>
                    <a href="<?php echo $baseUrl; ?>/about.php" class="text-decoration-none text-secondary" id="footer-link-about">About</a>
                    <a href="<?php echo $baseUrl; ?>/admin/login.php" class="text-decoration-none text-secondary" id="footer-link-admin">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?php echo $baseUrl; ?>/assets/js/main.js"></script>
</body>
</html>
