<!-- Login Page -->
<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Left Side - Image -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <div class="h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);">
                <div class="text-center text-white p-5">
                    <i class="fas fa-drumstick-bite fa-5x mb-4"></i>
                    <h2 class="fw-bold mb-3">Welcome Back to MeatMe</h2>
                    <p class="lead">Fresh chicken, straight from our farm to your table</p>
                    <div class="row mt-5">
                        <div class="col-4 text-center">
                            <i class="fas fa-leaf fa-2x mb-2"></i>
                            <p class="small">Farm Fresh</p>
                        </div>
                        <div class="col-4 text-center">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <p class="small">Safe & Clean</p>
                        </div>
                        <div class="col-4 text-center">
                            <i class="fas fa-truck fa-2x mb-2"></i>
                            <p class="small">Same Day Delivery</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center">
            <div class="w-100 p-5">
                <div class="text-center mb-5">
                    <h1 class="fw-bold text-success">
                        <i class="fas fa-drumstick-bite me-2"></i>MeatMe
                    </h1>
                    <h3 class="mb-3">Sign In</h3>
                    <p class="text-muted">Enter your credentials to access your account</p>
                </div>
                
                <!-- Display Errors -->
                <?php if (\App\Core\Session::hasFlash('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= e(\App\Core\Session::flash('error')) ?>
                    </div>
                <?php endif; ?>
                
                <?php 
                $errors = \App\Core\Session::flash('errors') ?? [];
                $old = \App\Core\Session::flash('old') ?? [];
                ?>
                
                <form method="POST" action="<?= e(\App\Core\View::url('login')) ?>" class="needs-validation" novalidate>
                    <?= \App\Core\CSRF::field() ?>
                    
                    <!-- Email -->
                    <div class="form-outline mb-4">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control form-control-lg <?= e(isset($errors['email']) ? 'is-invalid' : '') ?>"
                               value="<?= e(\App\Core\View::escape($old['email'] ?? '')) ?>"
                               required>
                        <label class="form-label" for="email">Email Address</label>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['email'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password -->
                    <div class="form-outline mb-4">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control form-control-lg <?= e(isset($errors['password']) ? 'is-invalid' : '') ?>"
                               required>
                        <label class="form-label" for="password">Password</label>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['password'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="<?= e(\App\Core\View::url('forgot-password')) ?>" class="text-decoration-none">
                            Forgot password?
                        </a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success btn-lg w-100 mb-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                    
                    <!-- Divider -->
                    <div class="text-center mb-4">
                        <span class="text-muted">Don't have an account?</span>
                    </div>
                    
                    <!-- Register Link -->
                    <a href="<?= e(\App\Core\View::url('register')) ?>" class="btn btn-outline-success btn-lg w-100">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </a>
                </form>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.form-outline {
    position: relative;
}

.form-control:focus {
    border-color: #4caf50;
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #1b5e20 0%, #388e3c 100%);
    transform: translateY(-1px);
}

.btn-outline-success {
    border: 2px solid #4caf50;
    color: #4caf50;
}

.btn-outline-success:hover {
    background-color: #4caf50;
    border-color: #4caf50;
}

@media (max-width: 991px) {
    .container-fluid .row {
        min-height: auto;
    }
    
    .col-lg-6 {
        padding: 2rem 1rem;
    }
}
</style>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Auto-fill demo credentials
function fillDemo(type) {
    if (type === 'admin') {
        document.getElementById('email').value = 'admin@meatme.com';
        document.getElementById('password').value = 'admin123';
    } else {
        document.getElementById('email').value = 'john@example.com';
        document.getElementById('password').value = 'password123';
    }
}
</script>
