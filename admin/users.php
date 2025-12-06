<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Users Management';

// Fetch users
global $mysqli;
$users = [];
$result = $mysqli->query("SELECT * FROM users ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    error_log("Users fetch error: " . $mysqli->error);
}

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Users Management</h2>
            <p class="text-muted mb-0">Manage customer accounts and permissions</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export Users
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e(count($users)) ?></h4>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-check"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e(count(array_filter($users, function($u) { return ($u['role'] ?? 'user') === 'user'; }))) ?></h4>
                <p class="text-muted mb-0">Customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e(count(array_filter($users, function($u) { return ($u['role'] ?? 'user') === 'admin'; }))) ?></h4>
                <p class="text-muted mb-0">Admins</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h4 class="fw-bold mb-1">
                    <?= e(count(array_filter($users, function($u) { return date('Y-m-d', strtotime($u['created_at'])) === date('Y-m-d'); }))) ?>
                </h4>
                <p class="text-muted mb-0">New Today</p>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Users (<?= e(count($users)) ?>)</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search users...">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>No users found</h5>
                <p class="text-muted">Users will appear here once they register on your website.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($user['name']) ?></h6>
                                            <small class="text-muted">ID: <?= e($user['id']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span><?= htmlspecialchars($user['email']) ?></span>
                                    <?php if (isset($user['email_verified_at']) && $user['email_verified_at']): ?>
                                        <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= e(($user['role'] ?? 'user') === 'admin' ? 'bg-danger' : 'bg-primary') ?>">
                                        <?= e(ucfirst($user['role'] ?? 'user')) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= e(date('M j, Y', strtotime($user['created_at']))) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
