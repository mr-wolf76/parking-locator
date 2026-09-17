<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Report Incorrect Parking Information | Parking Locator';

$selectedParkingId = isset($_GET['parking_id']) ? (int)$_GET['parking_id'] : 0;
$facilities = getActiveParking();
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parkingId = isset($_POST['parking_id']) ? (int)$_POST['parking_id'] : 0;
    $issueType = isset($_POST['issue_type']) ? sanitizeInput($_POST['issue_type']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    $contact = isset($_POST['reporter_contact']) ? sanitizeInput($_POST['reporter_contact']) : '';

    if ($parkingId <= 0) {
        $errorMessage = 'Please select a valid parking facility.';
    } elseif (empty($issueType)) {
        $errorMessage = 'Please choose an issue category.';
    } elseif (empty($message) || strlen($message) < 5) {
        $errorMessage = 'Please provide details about the incorrect information (minimum 5 characters).';
    } else {
        $created = createReport($parkingId, $issueType, $message, $contact);
        if ($created) {
            $successMessage = 'Thank you. Your report has been submitted for administrative review and database verification.';
            $selectedParkingId = 0;
        } else {
            $errorMessage = 'An error occurred while saving your report. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="h3 fw-bold text-dark mb-1">Report Incorrect Information</h1>
        <p class="text-muted mb-0 small">
            Help maintain reliable parking data across Kathmandu Valley by reporting inaccuracies.
        </p>
    </div>
</div>

<div class="container py-4 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-8">
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

            <div class="card border rounded-3 bg-white p-4 p-md-5">
                <form action="<?php echo $baseUrl; ?>/report.php" method="POST" id="report-issue-form">
                    <div class="mb-3">
                        <label for="report-parking-id" class="form-label fw-semibold text-dark">
                            Parking Facility <span class="text-danger">*</span>
                        </label>
                        <select name="parking_id" id="report-parking-id" class="form-select" required>
                            <option value="">-- Choose Parking Facility --</option>
                            <?php foreach ($facilities as $fac): ?>
                                <option value="<?php echo $fac['id']; ?>" <?php echo ($selectedParkingId === (int)$fac['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fac['name']); ?> (<?php echo htmlspecialchars($fac['area']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="report-issue-type" class="form-label fw-semibold text-dark">
                            Issue Category <span class="text-danger">*</span>
                        </label>
                        <select name="issue_type" id="report-issue-type" class="form-select" required>
                            <option value="">-- Select nature of problem --</option>
                            <option value="Incorrect location">Incorrect map location / coordinates</option>
                            <option value="Incorrect fee">Incorrect or changed parking fee</option>
                            <option value="Incorrect opening hours">Incorrect operating hours / gate timing</option>
                            <option value="Parking no longer available">Facility permanently closed or demolished</option>
                            <option value="Other">Other discrepancy</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="report-message" class="form-label fw-semibold text-dark">
                            Details of Discrepancy <span class="text-danger">*</span>
                        </label>
                        <textarea name="message" id="report-message" rows="4" class="form-control" placeholder="Describe the discrepancy clearly (e.g. Current bike rate is Rs 30, gate closes at 9 PM on Saturdays, etc.)" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="report-contact" class="form-label fw-semibold text-dark">
                            Your Contact (Optional)
                        </label>
                        <input type="text" name="reporter_contact" id="report-contact" class="form-control" placeholder="Email or phone number (in case clarification is needed)">
                        <div class="form-text small text-muted">We only use this if our verification team needs to confirm details.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4" id="submit-report-btn">
                            <i class="bi bi-send me-1"></i> Submit Report
                        </button>
                        <a href="<?php echo $baseUrl; ?>/find-parking.php" class="btn btn-light border px-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
