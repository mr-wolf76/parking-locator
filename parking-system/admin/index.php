<?php
$adminTitle = 'Admin Dashboard | Parking Locator';
require_once __DIR__ . '/admin_header.php';

$stats = getAdminStatistics();
$recentParking = getAllParkingAdmin();
$displayParking = array_slice($recentParking, 0, 8);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Dashboard</h1>
        <p class="text-muted small mb-0">System metrics and registered parking facilities overview.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo adminUrl('/admin/add-parking.php'); ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Add Facility
        </a>
    </div>
</div>

<?php if ($stats['pending_reports'] > 0): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4 py-2 px-3 border-warning" role="alert">
        <div class="d-flex align-items-center gap-2 small">
            <i class="bi bi-bell-fill text-warning fs-6"></i>
            <span>You have <strong><?php echo $stats['pending_reports']; ?></strong> pending user issue report(s) requiring verification.</span>
        </div>
        <a href="<?php echo adminUrl('/admin/reports.php'); ?>" class="btn btn-warning btn-sm text-dark py-0 px-2 fw-semibold">
            Review Reports
        </a>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-medium mb-1">Total Facilities</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo $stats['total']; ?></div>
                </div>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="bi bi-p-square"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-medium mb-1">Free Parking</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo $stats['free']; ?></div>
                </div>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-medium mb-1">Paid Parking</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo $stats['paid']; ?></div>
                </div>
                <div class="stat-icon-wrapper bg-info-subtle text-info">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-medium mb-1">Active Facilities</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo $stats['active']; ?></div>
                </div>
                <div class="stat-icon-wrapper bg-secondary-subtle text-secondary">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border rounded-3 bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Registered Facilities</h5>
        <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="btn btn-outline-secondary btn-sm">
            View All (<?php echo count($recentParking); ?>)
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">Name</th>
                    <th>Area</th>
                    <th>Payment</th>
                    <th>Vehicle</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Last Verified</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($displayParking as $item): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($item['address']); ?></div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($item['area']); ?></span>
                        </td>
                        <td>
                            <?php if ($item['payment_type'] === 'free'): ?>
                                <span class="badge badge-payment-free">Free</span>
                            <?php else: ?>
                                <span class="badge badge-payment-paid">Paid</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-capitalize small"><?php echo htmlspecialchars($item['vehicle_type']); ?></span>
                        </td>
                        <td class="small text-muted">
                            <?php echo !empty($item['parking_fee']) ? htmlspecialchars($item['parking_fee']) : ($item['payment_type'] === 'free' ? 'Free' : 'N/A'); ?>
                        </td>
                        <td>
                            <?php if ($item['status'] === 'active'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary text-white">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?php echo formatDate($item['last_verified']); ?>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $baseUrl; ?>/parking-details.php?id=<?php echo $item['id']; ?>" target="_blank" class="btn btn-outline-secondary" title="View Public Page">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo adminUrl('/admin/edit-parking.php?id=' . $item['id']); ?>" class="btn btn-outline-primary" title="Edit Facility">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo adminUrl('/admin/delete-parking.php?id=' . $item['id']); ?>" class="btn btn-outline-danger" title="Delete Facility">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/admin_footer.php';
?>
