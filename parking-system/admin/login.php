<?php
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$baseUrl = getBaseUrl();

if (isAdminLoggedIn()) {
    header("Location: " . adminUrl('/admin/index.php'));
    exit;
}

$errorMessage = '';
$loggedOutMessage = '';

if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $loggedOutMessage = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errorMessage = 'Please enter both username and password.';
    } else {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $token = generateAdminToken($admin['id'], $admin['username']);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            setcookie('admin_token', $token, [
                'expires' => time() + (86400 * 7),
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]);

            $targetUrl = "{$baseUrl}/admin/index.php?admin_token=" . urlencode($token) . "&sid=" . urlencode(session_id());
            header("Location: {$targetUrl}");
            exit;
        } else {
            $errorMessage = 'Invalid username or password. Please use admin / admin123.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function() {
            try {
                var currentFetch = window.fetch;
                var proto = Object.getPrototypeOf(window);
                var protoDesc = proto ? Object.getOwnPropertyDescriptor(proto, 'fetch') : null;
                if (protoDesc && !protoDesc.set && protoDesc.configurable) {
                    Object.defineProperty(proto, 'fetch', {
                        get: function() { return currentFetch; },
                        set: function(fn) { currentFetch = fn; },
                        configurable: true,
                        enumerable: true
                    });
                }
            } catch (e) {}

            try {
                var _f = window.fetch ? window.fetch.bind(window) : null;
                Object.defineProperty(window, 'fetch', {
                    get: function() { return _f; },
                    set: function(fn) { _f = fn; },
                    configurable: true,
                    enumerable: true
                });
            } catch (e) {}
        })();
    </script>
    <title>Administrator Login | Parking Locator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <a href="<?php echo $baseUrl; ?>/index.php" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark fw-bold mb-2">
                    <span class="brand-icon-box text-white bg-primary rounded d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-p-square-fill"></i>
                    </span>
                    <span class="fs-5">PARKING LOCATOR</span>
                </a>
                <div class="text-muted small">System Administration &amp; Verification Portal</div>
            </div>

            <?php if (!empty($loggedOutMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-6"></i>
                    <div class="small"><?php echo htmlspecialchars($loggedOutMessage); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                    <div class="small"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border rounded-3 bg-white p-4 shadow-sm mb-3">
                <form action="<?php echo $baseUrl; ?>/admin/login.php" method="POST" id="admin-login-form">
                    <div class="mb-3">
                        <label for="admin-username" class="form-label fw-semibold text-dark small">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="admin-username" class="form-control" required autocomplete="username" value="admin" autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin-password" class="form-label fw-semibold text-dark small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="admin-password" class="form-control" required autocomplete="current-password" value="admin123">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold mb-2 py-2" id="admin-login-submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Admin Panel
                    </button>

                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>/index.php" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i> Return to Public Website
                        </a>
                    </div>
                </form>
            </div>

            <div class="card border border-primary-subtle bg-primary-subtle p-3 rounded-3">
                <div class="d-flex align-items-center gap-2 text-primary fw-semibold small mb-2">
                    <i class="bi bi-shield-check fs-6"></i>
                    <span>System Credentials</span>
                </div>
                <div class="small text-secondary mb-2">
                    <div><strong>Username:</strong> <code class="text-dark">admin</code></div>
                    <div><strong>Password:</strong> <code class="text-dark">admin123</code></div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-medium bg-white" onclick="document.getElementById('admin-username').value='admin'; document.getElementById('admin-password').value='admin123'; document.getElementById('admin-login-form').submit();">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick 1-Click Sign In
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
