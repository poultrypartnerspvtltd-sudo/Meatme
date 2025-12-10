<?php
/**
 * Admin - User Detail
 */
?>
<div class="container p-4">
    <h1>User Detail</h1>

    <dl class="row">
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9"><?= $detail['id'] ?? '-' ?></dd>

        <dt class="col-sm-3">Name</dt>
        <dd class="col-sm-9"><?= \App\Core\View::escape($detail['name'] ?? '-') ?></dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9"><?= \App\Core\View::escape($detail['email'] ?? '-') ?></dd>

        <dt class="col-sm-3">Role</dt>
        <dd class="col-sm-9"><?= \App\Core\View::escape($detail['role'] ?? '-') ?></dd>

        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9"><?= \App\Core\View::escape($detail['status'] ?? '-') ?></dd>

        <dt class="col-sm-3">Joined</dt>
        <dd class="col-sm-9"><?= isset($detail['created_at']) ? \App\Core\View::formatDate($detail['created_at']) : '-' ?></dd>
    </dl>

    <a href="<?= \App\Core\View::url('/admin/users') ?>" class="btn btn-secondary">Back to Users</a>
</div>
