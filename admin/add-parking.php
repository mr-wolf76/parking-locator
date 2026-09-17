<?php
$adminTitle = 'Add Parking Facility | Parking Locator Admin';
require_once __DIR__ . '/admin_header.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $area = sanitizeInput($_POST['area'] ?? '');
    $latitude = filter_var($_POST['latitude'] ?? '', FILTER_VALIDATE_FLOAT);
    $longitude = filter_var($_POST['longitude'] ?? '', FILTER_VALIDATE_FLOAT);
    $paymentType = sanitizeInput($_POST['payment_type'] ?? 'paid');
    $parkingFee = sanitizeInput($_POST['parking_fee'] ?? '');
    $vehicleType = sanitizeInput($_POST['vehicle_type'] ?? 'both');
    $openingTime = sanitizeInput($_POST['opening_time'] ?? '07:00');
    $closingTime = sanitizeInput($_POST['closing_time'] ?? '21:00');
    $contact = sanitizeInput($_POST['contact'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? 'active');
    $lastVerified = sanitizeInput($_POST['last_verified'] ?? date('Y-m-d'));

    if (empty($name) || empty($address) || empty($area)) {
        $errorMessage = 'Name, address, and area are required fields.';
    } elseif ($latitude === false || $longitude === false) {
        $errorMessage = 'Please provide valid decimal coordinates for latitude and longitude.';
    } elseif ($latitude < 27.0 || $latitude > 28.0 || $longitude < 85.0 || $longitude > 86.0) {
        $errorMessage = 'Coordinates must fall within the Kathmandu Valley geographic boundary.';
    } else {
        $imageName = null;
        if (isset($_FILES['parking_image']) && $_FILES['parking_image']['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $fileTmp = $_FILES['parking_image']['tmp_name'];
            $fileOrigName = $_FILES['parking_image']['name'];
            $ext = strtolower(pathinfo($fileOrigName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExtensions)) {
                $errorMessage = 'Invalid image file type. Allowed formats: JPG, JPEG, PNG, WEBP.';
            } elseif ($_FILES['parking_image']['size'] > 5 * 1024 * 1024) {
                $errorMessage = 'Image file size must be less than 5MB.';
            } else {
                $uploadDir = dirname(__DIR__) . '/uploads/parking/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $imageName = 'parking_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (!move_uploaded_file($fileTmp, $uploadDir . $imageName)) {
                    $errorMessage = 'Failed to save uploaded image.';
                    $imageName = null;
                }
            }
        }

        if (empty($errorMessage)) {
            $created = createParking([
                'name' => $name,
                'address' => $address,
                'area' => $area,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'payment_type' => $paymentType,
                'parking_fee' => $parkingFee,
                'vehicle_type' => $vehicleType,
                'opening_time' => $openingTime . ':00',
                'closing_time' => $closingTime . ':00',
                'contact' => $contact,
                'description' => $description,
                'image' => $imageName,
                'status' => $status,
                'last_verified' => $lastVerified
            ]);

            if ($created) {
                $successMessage = 'Parking facility has been added successfully.';
            } else {
                $errorMessage = 'Database insertion failed. Please check your data and try again.';
            }
        }
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Add Parking Facility</h1>
        <p class="text-muted small mb-0">Register a new parking lot or municipal stand in Kathmandu.</p>
    </div>
    <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Facilities List
    </a>
</div>

<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div><?php echo htmlspecialchars($successMessage); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div><?php echo htmlspecialchars($errorMessage); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border rounded-3 bg-white p-4">
    <form action="<?php echo adminUrl('/admin/add-parking.php'); ?>" method="POST" enctype="multipart/form-data">
        <?php if (!empty(getActiveAdminToken())): ?>
            <input type="hidden" name="admin_token" value="<?php echo htmlspecialchars(getActiveAdminToken()); ?>">
        <?php endif; ?>
        <?php if (session_id()): ?>
            <input type="hidden" name="sid" value="<?php echo htmlspecialchars(session_id()); ?>">
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="fac-name" class="form-label fw-semibold small text-dark">Facility Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="fac-name" class="form-control" placeholder="e.g. Chhaya Center Underground Parking" required>
            </div>

            <div class="col-md-3">
                <label for="fac-area" class="form-label fw-semibold small text-dark">Area / Locality <span class="text-danger">*</span></label>
                <input type="text" name="area" id="fac-area" class="form-control" placeholder="e.g. Thamel, New Road, Baneshwor" required>
            </div>

            <div class="col-md-3">
                <label for="fac-status" class="form-label fw-semibold small text-dark">Status</label>
                <select name="status" id="fac-status" class="form-select">
                    <option value="active" selected>Active (Visible to users)</option>
                    <option value="inactive">Inactive (Hidden)</option>
                </select>
            </div>

            <div class="col-12">
                <label for="fac-address" class="form-label fw-semibold small text-dark">Street Address <span class="text-danger">*</span></label>
                <input type="text" name="address" id="fac-address" class="form-control" placeholder="e.g. Amrit Marg, Thamel, Kathmandu" required>
            </div>

            <div class="col-md-6">
                <label for="fac-lat" class="form-label fw-semibold small text-dark">Latitude (Decimal) <span class="text-danger">*</span></label>
                <input type="number" step="0.00000001" name="latitude" id="fac-lat" class="form-control" placeholder="27.71490000" required>
                <div class="form-text small text-muted">Kathmandu center latitude is approximately 27.7172.</div>
            </div>

            <div class="col-md-6">
                <label for="fac-lng" class="form-label fw-semibold small text-dark">Longitude (Decimal) <span class="text-danger">*</span></label>
                <input type="number" step="0.00000001" name="longitude" id="fac-lng" class="form-control" placeholder="85.31220000" required>
                <div class="form-text small text-muted">Kathmandu center longitude is approximately 85.3240.</div>
            </div>

            <div class="col-md-4">
                <label for="fac-payment" class="form-label fw-semibold small text-dark">Payment Type</label>
                <select name="payment_type" id="fac-payment" class="form-select">
                    <option value="paid" selected>Paid Facility</option>
                    <option value="free">Free Public/Customer Parking</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="fac-vehicle" class="form-label fw-semibold small text-dark">Allowed Vehicle Category</label>
                <select name="vehicle_type" id="fac-vehicle" class="form-select">
                    <option value="both" selected>Both Cars &amp; Motorcycles</option>
                    <option value="car">Cars Only</option>
                    <option value="motorcycle">Motorcycles / Scooters Only</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="fac-fee" class="form-label fw-semibold small text-dark">Parking Fee Specification</label>
                <input type="text" name="parking_fee" id="fac-fee" class="form-control" placeholder="e.g. Rs. 50/hr (Car), Rs. 25/hr (Bike)">
            </div>

            <div class="col-md-3">
                <label for="fac-open" class="form-label fw-semibold small text-dark">Opening Time</label>
                <input type="time" name="opening_time" id="fac-open" class="form-control" value="07:00">
            </div>

            <div class="col-md-3">
                <label for="fac-close" class="form-label fw-semibold small text-dark">Closing Time</label>
                <input type="time" name="closing_time" id="fac-close" class="form-control" value="21:00">
            </div>

            <div class="col-md-3">
                <label for="fac-contact" class="form-label fw-semibold small text-dark">Contact Phone</label>
                <input type="text" name="contact" id="fac-contact" class="form-control" placeholder="e.g. 01-4256789">
            </div>

            <div class="col-md-3">
                <label for="fac-verified" class="form-label fw-semibold small text-dark">Last Verified Date <span class="text-danger">*</span></label>
                <input type="date" name="last_verified" id="fac-verified" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="col-12">
                <label for="fac-desc" class="form-label fw-semibold small text-dark">Description &amp; Operational Notes</label>
                <textarea name="description" id="fac-desc" rows="3" class="form-control" placeholder="Security arrangements, multi-level basement status, entrance guidelines, etc."></textarea>
            </div>

            <div class="col-md-6">
                <label for="parking-image-input" class="form-label fw-semibold small text-dark">Facility Photo (Optional)</label>
                <input type="file" name="parking_image" id="parking-image-input" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div class="form-text small text-muted">Supports JPG, PNG, WEBP up to 5MB.</div>
            </div>

            <div class="col-md-6">
                <div class="image-preview-box">
                    <span id="image-preview-placeholder" class="text-muted small">Photo preview will appear here</span>
                    <img id="image-preview" src="" alt="Preview" class="d-none">
                </div>
            </div>

            <div class="col-12 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Facility
                </button>
                <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="btn btn-light border px-3">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/admin_footer.php';
?>
