<?php
/**
 * Admin - Settings
 */
?>
<div class="container p-4">
    <h1>Settings</h1>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger"><?= e(\App\Core\Session::flash('error')) ?></div>
    <?php endif; ?>
    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success"><?= e(\App\Core\Session::flash('success')) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= \App\Core\View::url('/admin/settings') ?>">
        <?= \App\Core\CSRF::field() ?>

        <div class="mb-3">
            <label class="form-label">Site Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($config['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Site URL</label>
            <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($config['url'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Timezone</label>
            <input type="text" name="timezone" class="form-control" value="<?= htmlspecialchars($config['timezone'] ?? '') ?>">
        </div>

        <button class="btn btn-primary">Save Settings</button>
        <a href="<?= \App\Core\View::url('/admin/dashboard') ?>" class="btn btn-secondary ms-2">Back to Dashboard</a>
    </form>
</div>
