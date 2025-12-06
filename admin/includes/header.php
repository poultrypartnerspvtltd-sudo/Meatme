<?php
// Session should already be started by the calling page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(isset($page_title) ? $page_title . ' - ' : '') ?>MeatMe Admin</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #ff6f00;
            --success-color: #4caf50;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 280px;
        }
        
        [data-theme="dark"] {
            --primary-color: #4caf50;
            --secondary-color: #ffab40;
            --light-color: #212529;
            --dark-color: #f8f9fa;
            background-color: #121212;
            color: #ffffff;
        }
        
        [data-theme="dark"] body {
            background-color: #121212;
            color: #ffffff;
        }
        
        [data-theme="dark"] .card {
            background-color: #1e1e1e;
            border-color: #333;
            color: #ffffff;
        }
        
        [data-theme="dark"] .navbar {
            background-color: #1e1e1e !important;
            border-bottom: 1px solid #333;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            transition: all 0.3s ease;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--success-color) 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e0e0e0;
            padding: 1rem 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            border-radius: 0;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            border-left-color: white;
            transform: translateX(5px);
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-profile {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--success-color) 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        [data-theme="dark"] .page-header {
            background: #1e1e1e;
            color: white;
        }
        
        [data-theme="dark"] .stat-card {
            background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
            color: white;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .navbar {
                padding: 1rem;
            }
        }
        
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .mobile-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <form id="csrf-token-form" style="display:none;">
        <?= csrf_field() ?>
    </form>
    <script>
        window.csrfToken = document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value || '';
    </script>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>
