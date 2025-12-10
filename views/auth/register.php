<!-- Register Page -->
<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Left Side - Image -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <div class="h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);">
                <div class="text-center text-white p-5">
                    <i class="fas fa-drumstick-bite fa-5x mb-4"></i>
                    <h2 class="fw-bold mb-3">Join MeatMe Family</h2>
                    <p class="lead">Get access to the freshest chicken delivered to your doorstep</p>
                    
                    <!-- Benefits -->
                    <div class="row mt-5">
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-lg me-3"></i>
                                <span>Same-day delivery</span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-lg me-3"></i>
                                <span>Farm-fresh quality</span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-lg me-3"></i>
                                <span>Exclusive offers & discounts</span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-lg me-3"></i>
                                <span>Order tracking & history</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Register Form -->
        <div class="col-lg-6 d-flex align-items-center">
            <div class="w-100 p-5">
                <div class="text-center mb-4">
                    <h1 class="fw-bold text-success">
                        <i class="fas fa-drumstick-bite me-2"></i>MeatMe
                    </h1>
                    <h3 class="mb-3">Create Account</h3>
                    <p class="text-muted">Join thousands of satisfied customers</p>
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
                
                <form method="POST" action="<?= e(\App\Core\View::url('register')) ?>" class="needs-validation" novalidate>
                    <?= \App\Core\CSRF::field() ?>
                    
                    <!-- Full Name -->
                    <div class="form-outline mb-3">
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control <?= e(isset($errors['name']) ? 'is-invalid' : '') ?>"
                               value="<?= e(\App\Core\View::escape($old['name'] ?? '')) ?>"
                               required>
                        <label class="form-label" for="name">Full Name</label>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['name'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-outline mb-3">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control <?= e(isset($errors['email']) ? 'is-invalid' : '') ?>"
                               value="<?= e(\App\Core\View::escape($old['email'] ?? '')) ?>"
                               required>
                        <label class="form-label" for="email">Email Address</label>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['email'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Phone -->
                    <div class="form-outline mb-3">
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-control <?= e(isset($errors['phone']) ? 'is-invalid' : '') ?>"
                               value="<?= e(\App\Core\View::escape($old['phone'] ?? '')) ?>"
                               placeholder="+977-9800000000"
                               required>
                        <label class="form-label" for="phone">Phone Number</label>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['phone'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password -->
                    <div class="form-outline mb-3">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control <?= e(isset($errors['password']) ? 'is-invalid' : '') ?>"
                               required>
                        <label class="form-label" for="password">Password</label>
                        <div class="form-text">
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['password'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="form-outline mb-4">
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control <?= e(isset($errors['password']) ? 'is-invalid' : '') ?>"
                               required>
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= e(implode('<br>', $errors['password'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="<?= e(\App\Core\View::url('terms-of-service')) ?>" target="_blank">Terms of Service</a> 
                            and <a href="<?= e(\App\Core\View::url('privacy-policy')) ?>" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <!-- Newsletter Subscription -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1" checked>
                        <label class="form-check-label" for="newsletter">
                            Subscribe to our newsletter for exclusive offers and updates
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success btn-lg w-100 mb-4">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                    
                    <!-- Divider -->
                    <div class="text-center mb-4">
                        <span class="text-muted">Already have an account?</span>
                    </div>
                    
                    <!-- Login Link -->
                    <a href="<?= e(\App\Core\View::url('login')) ?>" class="btn btn-outline-success btn-lg w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </a>
                </form>
                
                <!-- Security Notice -->
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <small class="text-muted">
                            Your information is secure and will never be shared with third parties.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

.form-check-input:checked {
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

// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.startsWith('977')) {
        value = '+' + value;
    } else if (value.startsWith('9')) {
        value = '+977-' + value;
    }
    
    e.target.value = value;
});
</script>
