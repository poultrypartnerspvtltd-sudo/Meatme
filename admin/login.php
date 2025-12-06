<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success = 'You have been logged out successfully.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $admin_username = trim($_POST['username'] ?? '');
    $admin_password = $_POST['password'] ?? '';
    
    if (empty($admin_username) || empty($admin_password)) {
        $error = 'Please enter both username and password.';
    } else {
        global $mysqli;
        // Check credentials using prepared statement
        $stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM admins WHERE username = ? AND role IN ('admin', 'super_admin')");
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if ($admin && password_verify($admin_password, $admin['password_hash'])) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MeatMe</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #ff6f00;
            --success-color: #4caf50;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        [data-theme="dark"] {
            --primary-color: #4caf50;
            --secondary-color: #ffab40;
            --light-color: #212529;
            --dark-color: #f8f9fa;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--success-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .admin-logo {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--success-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--success-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card admin-login-card">
                    <div class="card-body p-5">
                        <!-- Logo and Title -->
                        <div class="text-center mb-4">
                            <h1 class="admin-logo fw-bold mb-2">
                                <i class="fas fa-drumstick-bite me-2"></i>MeatMe
                            </h1>
                            <h4 class="text-muted mb-2">Admin Panel</h4>
                            <p class="text-muted small">Secure administrative access</p>
                        </div>
                        
                        <!-- Display Messages -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?>
                                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?= e($success) ?>
                                <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Login Form -->
                        <form method="POST" action="">
                            <?= csrf_field() ?>
                            <!-- Username -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" 
                                       name="username" 
                                       class="form-control form-control-lg"
                                       placeholder="Enter admin username"
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                       required>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control form-control-lg"
                                       placeholder="Enter password"
                                       required>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-admin text-white w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to Admin Panel
                            </button>
                        </form>
                        
                        <!-- Demo Credentials -->
                        <div class="alert alert-info">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Demo Credentials:</h6>
                            <small>
                                <strong>Username:</strong> admin<br>
                                <strong>Password:</strong> admin123
                            </small>
                        </div>
                        
                        <!-- Back to Website -->
                        <div class="text-center mt-4">
                            <a href="/Meatme/" class="text-decoration-none">
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
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin-theme', newTheme);
            
            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        
        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('admin-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const icon = document.getElementById('theme-icon');
            icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
</body>
</html>
