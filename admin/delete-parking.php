<?php
$adminTitle = 'Confirm Delete Facility | Parking Locator Admin';
require_once __DIR__ . '/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$facility = getParkingById($id);

if (!$facility) {
    echo '<div class="alert alert-danger">Parking facility record not found.</div>';
    require_once __DIR__ . '/admin_footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = (int)($_POST['confirm_id'] ?? 0);
    if ($confirmId === $id) {
        deleteParking($confirmId);
        header("Location: " . adminUrl('/admin/parking.php?deleted=1'));
        exit;
    }
}
?>

<div class="row justify-content-center py-4">
    <div class="col-md-8 col-lg-6">
        <div class="card border-danger border rounded-3 bg-white p-4">
            <div class="text-center mb-3">
                <div class="stat-icon-wrapper bg-danger-subtle text-danger mx-auto mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Delete Parking Facility</h4>
                <p class="text-muted small mb-0">This action cannot be undone. Please confirm removal.</p>
            </div>

            <div class="p-3 bg-light rounded-2 border mb-4">
                <div class="fw-bold text-dark fs-6 mb-1"><?php echo htmlspecialchars($facility['name']); ?></div>
                <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($facility['address']); ?>, <?php echo htmlspecialchars($facility['area']); ?></div>
                <div class="small">
                    <span class="text-muted">Type:</span> <span class="fw-medium text-capitalize"><?php echo htmlspecialchars($facility['payment_type']); ?></span> |
                    <span class="text-muted">Vehicles:</span> <span class="fw-medium text-capitalize"><?php echo htmlspecialchars($facility['vehicle_type']); ?></span>
                </div>
            </div>

            <p class="text-danger small mb-4 text-center">
                Are you sure you want to permanently delete this parking facility from the system?
            </p>

            <form action="<?php echo adminUrl('/admin/delete-parking.php?id=' . $facility['id']); ?>" method="POST">
                <?php if (!empty(getActiveAdminToken())): ?>
                    <input type="hidden" name="admin_token" value="<?php echo htmlspecialchars(getActiveAdminToken()); ?>">
                <?php endif; ?>
                <?php if (session_id()): ?>
                    <input type="hidden" name="sid" value="<?php echo htmlspecialchars(session_id()); ?>">
                <?php endif; ?>
                <input type="hidden" name="confirm_id" value="<?php echo $facility['id']; ?>">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger flex-grow-1" id="confirm-delete-btn">
                        <i class="bi bi-trash me-1"></i> Yes, Delete Facility
                    </button>
                    <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="btn btn-light border px-4">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/admin_footer.php';
?>
