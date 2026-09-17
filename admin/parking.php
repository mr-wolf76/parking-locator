<?php
$adminTitle = 'Manage Parking Facilities | Parking Locator Admin';
require_once __DIR__ . '/admin_header.php';

$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$areaFilter = isset($_GET['area']) ? sanitizeInput($_GET['area']) : '';
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

$facilities = getAllParkingAdmin($search, $areaFilter, $statusFilter);
$allAreas = getParkingAreas();
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Parking Facilities</h1>
        <p class="text-muted small mb-0">Complete inventory of monitored parking locations across Kathmandu.</p>
    </div>
    <div>
        <a href="<?php echo adminUrl('/admin/add-parking.php'); ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Add Facility
        </a>
    </div>
</div>

<div class="card border rounded-3 bg-white mb-4 p-3">
    <form action="<?php echo $baseUrl; ?>/admin/parking.php" method="GET" class="row g-2 align-items-center">
        <?php if (!empty(getActiveAdminToken())): ?>
            <input type="hidden" name="admin_token" value="<?php echo htmlspecialchars(getActiveAdminToken()); ?>">
        <?php endif; ?>
        <?php if (session_id()): ?>
            <input type="hidden" name="sid" value="<?php echo htmlspecialchars(session_id()); ?>">
        <?php endif; ?>
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name or address..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="area" class="form-select form-select-sm">
                <option value="">All Areas</option>
                <?php foreach ($allAreas as $a): ?>
                    <option value="<?php echo htmlspecialchars($a['area']); ?>" <?php echo ($areaFilter === $a['area']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($a['area']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" <?php echo ($statusFilter === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($statusFilter === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-secondary btn-sm flex-grow-1">Filter</button>
            <a href="<?php echo adminUrl('/admin/parking.php'); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="card border rounded-3 bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="fw-semibold text-dark">
            Total Records: <span class="badge bg-light text-dark border"><?php echo count($facilities); ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Name &amp; Location</th>
                    <th>Area</th>
                    <th>Payment</th>
                    <th>Vehicle</th>
                    <th>Fee Structure</th>
                    <th>Operating Hours</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($facilities) === 0): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            No facilities match the specified criteria.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($facilities as $item): ?>
                    <tr>
                        <td class="ps-3 text-muted small font-monospace">#<?php echo $item['id']; ?></td>
                        <td>
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
                            <?php echo !empty($item['parking_fee']) ? htmlspecialchars($item['parking_fee']) : 'Free'; ?>
                        </td>
                        <td class="small text-muted">
                            <?php echo formatTime($item['opening_time']); ?> – <?php echo formatTime($item['closing_time']); ?>
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
                                <a href="<?php echo $baseUrl; ?>/parking-details.php?id=<?php echo $item['id']; ?>" target="_blank" class="btn btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo adminUrl('/admin/edit-parking.php?id=' . $item['id']); ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo adminUrl('/admin/delete-parking.php?id=' . $item['id']); ?>" class="btn btn-outline-danger" title="Delete">
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
