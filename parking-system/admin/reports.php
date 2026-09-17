<?php
$adminTitle = 'User Reports & Data Verification | Parking Locator Admin';
require_once __DIR__ . '/admin_header.php';

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['status'])) {
    $reportId = (int)$_POST['report_id'];
    $newStatus = sanitizeInput($_POST['status']);
    if (in_array($newStatus, ['pending', 'reviewed', 'resolved'])) {
        updateReportStatus($reportId, $newStatus);
        $successMessage = 'Report status updated successfully.';
    }
}

$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$reports = getAllReports($statusFilter);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">User Issue Reports</h1>
        <p class="text-muted small mb-0">Review crowdsourced data discrepancies reported by motorists in Kathmandu.</p>
    </div>
    <div class="btn-group btn-group-sm">
        <a href="<?php echo adminUrl('/admin/reports.php'); ?>" class="btn <?php echo empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary'; ?>">All</a>
        <a href="<?php echo adminUrl('/admin/reports.php?status=pending'); ?>" class="btn <?php echo ($statusFilter === 'pending') ? 'btn-warning text-dark' : 'btn-outline-secondary'; ?>">Pending</a>
        <a href="<?php echo adminUrl('/admin/reports.php?status=reviewed'); ?>" class="btn <?php echo ($statusFilter === 'reviewed') ? 'btn-info text-white' : 'btn-outline-secondary'; ?>">Reviewed</a>
        <a href="<?php echo adminUrl('/admin/reports.php?status=resolved'); ?>" class="btn <?php echo ($statusFilter === 'resolved') ? 'btn-success' : 'btn-outline-secondary'; ?>">Resolved</a>
    </div>
</div>

<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div><?php echo htmlspecialchars($successMessage); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border rounded-3 bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="fw-semibold text-dark">
            Report Records: <span class="badge bg-light text-dark border"><?php echo count($reports); ?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Reported Facility</th>
                    <th>Issue Type</th>
                    <th style="min-width: 260px;">Message</th>
                    <th>Reporter</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reports) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No issue reports registered.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($reports as $r): ?>
                    <tr>
                        <td class="ps-3 text-muted small font-monospace">#<?php echo $r['id']; ?></td>
                        <td>
                            <?php if (!empty($r['parking_name'])): ?>
                                <a href="<?php echo $baseUrl; ?>/parking-details.php?id=<?php echo $r['parking_id']; ?>" target="_blank" class="fw-semibold text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($r['parking_name']); ?>
                                </a>
                                <div class="text-muted small"><?php echo htmlspecialchars($r['parking_area'] ?? ''); ?></div>
                            <?php else: ?>
                                <span class="text-muted">[Deleted Facility]</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['issue_type']); ?></span>
                        </td>
                        <td class="small text-secondary">
                            <?php echo nl2br(htmlspecialchars($r['message'])); ?>
                        </td>
                        <td class="small text-muted">
                            <?php echo !empty($r['reporter_contact']) ? htmlspecialchars($r['reporter_contact']) : '<span class="text-muted fst-italic">Anonymous</span>'; ?>
                        </td>
                        <td class="small text-muted">
                            <?php echo formatDate($r['created_at']); ?>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">Pending</span>
                            <?php elseif ($r['status'] === 'reviewed'): ?>
                                <span class="badge bg-info-subtle text-info border border-info">Reviewed</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success">Resolved</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <form action="<?php echo adminUrl('/admin/reports.php'); ?>" method="POST" class="d-inline-flex gap-1">
                                <?php if (!empty(getActiveAdminToken())): ?>
                                    <input type="hidden" name="admin_token" value="<?php echo htmlspecialchars(getActiveAdminToken()); ?>">
                                <?php endif; ?>
                                <?php if (session_id()): ?>
                                    <input type="hidden" name="sid" value="<?php echo htmlspecialchars(session_id()); ?>">
                                <?php endif; ?>
                                <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                                <select name="status" class="form-select form-select-sm" style="width: 110px;" onchange="this.form.submit()">
                                    <option value="pending" <?php echo ($r['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewed" <?php echo ($r['status'] === 'reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                                    <option value="resolved" <?php echo ($r['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </form>
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
