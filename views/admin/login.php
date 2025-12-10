<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(isset($title) ? $title . ' - ' : '') ?>MeatMe Admin</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .admin-login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .admin-logo {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card admin-login-card border-0">
                    <div class="card-body p-5">
                        <!-- Logo and Title -->
                        <div class="text-center mb-4">
                            <h1 class="admin-logo fw-bold mb-2">
                                <i class="fas fa-drumstick-bite me-2"></i>MeatMe
                            </h1>
                            <h4 class="text-muted mb-0">Admin Panel</h4>
                            <p class="text-muted small">Secure administrative access</p>
                        </div>
                        
                        <!-- Display Flash Messages -->
                        <?php if (\App\Core\Session::hasFlash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= e(\App\Core\Session::flash('error')) ?>
                                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (\App\Core\Session::hasFlash('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?= e(\App\Core\Session::flash('success')) ?>
                                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Login Form -->
                        <form method="POST" action="<?= e(\App\Core\View::url('admin/login')) ?>">
                            <?= \App\Core\CSRF::field() ?>
                            
                            <!-- Email -->
                            <div class="form-outline mb-4">
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control form-control-lg"
                                       required>
                                <label class="form-label" for="email">
                                    <i class="fas fa-envelope me-2"></i>Admin Email
                                </label>
                            </div>
                            
                            <!-- Password -->
                            <div class="form-outline mb-4">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control form-control-lg"
                                       required>
                                <label class="form-label" for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                            </div>
                            
                            <!-- Remember Me -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to Admin Panel
                            </button>
                        </form>
                        
                        <!-- Demo Credentials -->
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Demo Credentials:</h6>
                            <small>
                                <strong>Email:</strong> admin@meatme.com<br>
                                <strong>Password:</strong> admin123
                            </small>
                        </div>
                        
                        <!-- Back to Website -->
                        <div class="text-center mt-4">
                            <a href="<?= e(\App\Core\View::url()) ?>" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Back to Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    
    <script>
        // Auto-fill demo credentials
        document.addEventListener('DOMContentLoaded', function() {
            // You can uncomment these lines for easier testing
            // document.getElementById('email').value = 'admin@meatme.com';
            // document.getElementById('password').value = 'admin123';
        });
    </script>
</body>
</html>
