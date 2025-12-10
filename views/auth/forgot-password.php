<?php $this->extend('layouts/app'); ?>

<?php $this->section('content'); ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-success mb-3"></i>
                        <h2 class="fw-bold">Forgot Password?</h2>
                        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= e($_SESSION['error']; unset($_SESSION['error'])) ?>
                            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= e($_SESSION['success']; unset($_SESSION['success'])) ?>
                            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= e(url('forgot-password')) ?>">
                        <?= csrf_field() ?>
                        
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control form-control-lg" required>
                            <label class="form-label" for="email">Email Address</label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg btn-block mb-4">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>

                        <div class="text-center">
                            <p class="mb-0">
                                Remember your password? 
                                <a href="<?= e(url('login')) ?>" class="text-success fw-bold">Login here</a>
                            </p>
                            <p class="mt-2">
                                Don't have an account? 
                                <a href="<?= e(url('register')) ?>" class="text-success fw-bold">Register</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
