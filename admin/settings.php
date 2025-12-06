<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Site Settings';

$settingsDir = __DIR__ . '/../storage';
$settingsFile = $settingsDir . '/admin_settings.json';

$defaultSettings = [
    'site_name' => 'MeatMe',
    'support_email' => 'meatme9898@gmail.com',
    'support_phone' => '+977-9811075627',
    'contact_address' => 'Butwal, Nepal',
    'maintenance_mode' => false,
    'homepage_announcement' => ''
];

$settings = $defaultSettings;

if (file_exists($settingsFile)) {
    $decoded = json_decode(file_get_contents($settingsFile), true);
    if (is_array($decoded)) {
        $settings = array_merge($settings, $decoded);
    }
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \App\Core\CSRF::validate();

    $updatedSettings = [
        'site_name' => trim($_POST['site_name'] ?? '') ?: $defaultSettings['site_name'],
        'support_email' => trim($_POST['support_email'] ?? '') ?: $defaultSettings['support_email'],
        'support_phone' => trim($_POST['support_phone'] ?? '') ?: $defaultSettings['support_phone'],
        'contact_address' => trim($_POST['contact_address'] ?? '') ?: $defaultSettings['contact_address'],
        'maintenance_mode' => isset($_POST['maintenance_mode']),
        'homepage_announcement' => trim($_POST['homepage_announcement'] ?? '')
    ];

    if (!is_dir($settingsDir)) {
        if (!mkdir($settingsDir, 0755, true) && !is_dir($settingsDir)) {
            $errorMessage = 'Unable to create settings directory.';
        }
    }

    if (!$errorMessage) {
        $json = json_encode($updatedSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($settingsFile, $json) !== false) {
            $settings = $updatedSettings;
            $successMessage = 'Settings have been updated successfully.';
            error_log('[Admin] Settings updated by user ID ' . ($_SESSION['admin_id'] ?? 'unknown'));
        } else {
            $errorMessage = 'Failed to save settings file.';
        }
    }
}

error_log('[Admin] Settings page loaded by user ID ' . ($_SESSION['admin_id'] ?? 'unknown'));

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Site Settings</h2>
            <p class="text-muted mb-0">Manage global preferences and contact information for your store.</p>
        </div>
        <div class="col-auto">
            <a href="/Meatme/" class="btn btn-outline-success" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>View Storefront
            </a>
        </div>
    </div>
</div>

<?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= e($successMessage) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= e($errorMessage) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-success"></i>General Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-12">
                        <label class="form-label fw-bold">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name']) ?>" required>
                        <small class="text-muted">Displayed in the storefront header and emails.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="<?= e($settings['support_email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Support Phone</label>
                        <input type="text" name="support_phone" class="form-control" value="<?= e($settings['support_phone']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Contact Address</label>
                        <textarea name="contact_address" class="form-control" rows="2" required><?= e($settings['contact_address']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Homepage Announcement</label>
                        <textarea name="homepage_announcement" class="form-control" rows="3" placeholder="Optional banner message shown on the storefront."><?= e($settings['homepage_announcement']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="maintenance_mode">Maintenance Mode</label>
                        </div>
                        <small class="text-muted">When enabled, non-admin visitors will see a maintenance notice.</small>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2 text-primary"></i>Quick Checklist</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Site Name</span>
                        <span class="badge bg-success"><?= e($settings['site_name']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Support Email</span>
                        <span class="badge bg-info"><?= e($settings['support_email']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Support Phone</span>
                        <span class="badge bg-info"><?= e($settings['support_phone']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Maintenance Mode</span>
                        <?php if ($settings['maintenance_mode']): ?>
                            <span class="badge bg-warning text-dark">Enabled</span>
                        <?php else: ?>
                            <span class="badge bg-success">Disabled</span>
                        <?php endif; ?>
                    </li>
                </ul>

                <hr class="my-4">

                <h6 class="fw-bold mb-2">System Notes</h6>
                <ul class="list-unstyled small text-muted mb-0">
                    <li><i class="fas fa-shield-alt me-2 text-success"></i>Settings are stored in <code>storage/admin_settings.json</code>.</li>
                    <li><i class="fas fa-history me-2 text-success"></i>Recent changes are logged to <code>php_error.log</code>.</li>
                    <li><i class="fas fa-tools me-2 text-success"></i>Extend this page to sync with .env or external services.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>


