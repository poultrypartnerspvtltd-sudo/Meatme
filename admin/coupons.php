<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Coupons Management';

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Coupons Management</h2>
            <p class="text-muted mb-0">Create and manage discount coupons</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-success" data-mdb-toggle="modal" data-mdb-target="#createCouponModal">
                <i class="fas fa-plus me-2"></i>Create Coupon
            </button>
        </div>
    </div>
</div>

<!-- Coupon Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h4 class="fw-bold mb-1">5</h4>
                <p class="text-muted mb-0">Active Coupons</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="fw-bold mb-1">127</h4>
                <p class="text-muted mb-0">Times Used</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-percentage"></i>
                </div>
                <h4 class="fw-bold mb-1">Rs. 15,450</h4>
                <p class="text-muted mb-0">Total Discount</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto mb-2" style="width: 50px; height: 50px;">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="fw-bold mb-1">2</h4>
                <p class="text-muted mb-0">Expiring Soon</p>
            </div>
        </div>
    </div>
</div>

<!-- Coupons List -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">All Coupons</h5>
    </div>
    <div class="card-body">
        <!-- Sample Coupons -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title text-success">WELCOME10</h5>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <p class="card-text">10% off on first order</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Used</small>
                                <div class="fw-bold">45 times</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expires</small>
                                <div class="fw-bold">Dec 31</div>
                            </div>
                        </div>
                        <hr>
                        <div class="btn-group w-100">
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title text-warning">FRESH20</h5>
                            <span class="badge bg-warning">Expiring</span>
                        </div>
                        <p class="card-text">Rs. 200 off on orders above Rs. 1000</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Used</small>
                                <div class="fw-bold">23 times</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expires</small>
                                <div class="fw-bold">Tomorrow</div>
                            </div>
                        </div>
                        <hr>
                        <div class="btn-group w-100">
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title text-info">BULK15</h5>
                            <span class="badge bg-info">Active</span>
                        </div>
                        <p class="card-text">15% off on bulk orders (5kg+)</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Used</small>
                                <div class="fw-bold">12 times</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Expires</small>
                                <div class="fw-bold">Jan 15</div>
                            </div>
                        </div>
                        <hr>
                        <div class="btn-group w-100">
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                            <button class="btn btn-outline-danger btn-sm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Coupon Modal -->
<div class="modal fade" id="createCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Coupon</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" name="coupon_code" placeholder="e.g., SAVE20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" placeholder="Brief description">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Type</label>
                            <select class="form-select">
                                <option>Percentage</option>
                                <option>Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Value</label>
                            <input type="number" class="form-control" placeholder="10">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Order</label>
                            <input type="number" class="form-control" placeholder="500">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" placeholder="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Create Coupon</button>
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
