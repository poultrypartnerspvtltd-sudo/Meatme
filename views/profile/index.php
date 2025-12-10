<!-- Page Header -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('dashboard')) ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-0">My Profile</h1>
                <p class="text-muted mb-0">Manage your account information</p>
            </div>
        </div>
    </div>
</div>

<!-- Profile Content -->
<div class="container py-5">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h5 class="fw-bold"><?= e(\App\Core\View::escape($user['name'])) ?></h5>
                    <p class="text-muted mb-3"><?= e(\App\Core\View::escape($user['email'])) ?></p>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= e(\App\Core\View::url('profile/password')) ?>" class="btn btn-outline-warning">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </a>
                        <a href="<?= e(\App\Core\View::url('dashboard')) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Form -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Flash Messages -->
                    <?php if (\App\Core\Session::has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?= e(\App\Core\Session::flash('success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (\App\Core\Session::has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?= e(\App\Core\Session::flash('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= e(\App\Core\View::url('profile/update')) ?>">
                        <?= \App\Core\CSRF::field() ?>
                        
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control <?= e(\App\Core\Session::hasError('name') ? 'is-invalid' : '') ?>" 
                                           name="name" 
                                           value="<?= e(\App\Core\View::escape(\App\Core\Session::old('name', $user['name']))) ?>"
                                           required>
                                    <?php if (\App\Core\Session::hasError('name')): ?>
                                        <div class="invalid-feedback">
                                            <?= e(\App\Core\Session::getError('name')[0]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control <?= e(\App\Core\Session::hasError('email') ? 'is-invalid' : '') ?>" 
                                           name="email" 
                                           value="<?= e(\App\Core\View::escape(\App\Core\Session::old('email', $user['email']))) ?>"
                                           required>
                                    <?php if (\App\Core\Session::hasError('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= e(\App\Core\Session::getError('email')[0]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Phone Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" 
                                           class="form-control <?= e(\App\Core\Session::hasError('phone') ? 'is-invalid' : '') ?>" 
                                           name="phone" 
                                           value="<?= e(\App\Core\View::escape(\App\Core\Session::old('phone', $user['phone'] ?? ''))) ?>"
                                           required>
                                    <?php if (\App\Core\Session::hasError('phone')): ?>
                                        <div class="invalid-feedback">
                                            <?= e(\App\Core\Session::getError('phone')[0]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Member Since -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Member Since</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?= e(date('F j, Y', strtotime($user['created_at']))) ?>"
                                           readonly>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Fields marked with * are required
                            </small>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Account ID</label>
                                <div class="fw-bold">#<?= e(str_pad($user['id'], 6, '0', STR_PAD_LEFT)) ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Account Status</label>
                                <div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Last Updated</label>
                                <div class="fw-bold">
                                    <?= e(isset($user['updated_at']) && $user['updated_at'] ? 
                                        date('F j, Y', strtotime($user['updated_at'])) : 
                                        'Never') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Account Type</label>
                                <div class="fw-bold">
                                    <span class="badge bg-primary">Customer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
