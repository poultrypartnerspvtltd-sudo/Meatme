<!-- Page Header -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('dashboard')) ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-0">Change Password</h1>
                <p class="text-muted mb-0">Update your account password for security</p>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Content -->
<div class="container py-5">
    <div class="row">
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
                        <a href="<?= e(\App\Core\View::url('profile')) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
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

                    <form method="POST" action="<?= e(\App\Core\View::url('profile/password')) ?>">
                        <?= \App\Core\CSRF::field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Password *</label>
                            <input type="password" name="current_password" class="form-control <?= e(\App\Core\Session::hasError('current_password') ? 'is-invalid' : '') ?>" required>
                            <?php if (\App\Core\Session::hasError('current_password')): ?>
                                <div class="invalid-feedback"><?= e(\App\Core\Session::getError('current_password')[0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password *</label>
                            <input type="password" name="password" class="form-control <?= e(\App\Core\Session::hasError('password') ? 'is-invalid' : '') ?>" required>
                            <?php if (\App\Core\Session::hasError('password')): ?>
                                <div class="invalid-feedback"><?= e(\App\Core\Session::getError('password')[0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm New Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Fields marked with * are required</small>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
