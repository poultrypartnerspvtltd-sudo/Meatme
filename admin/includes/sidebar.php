<?php require_once __DIR__ . '/../../app/helpers.php'; ?>
<!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand text-center">
            <h3 class="text-white fw-bold mb-0">
                <i class="fas fa-drumstick-bite me-2"></i>MeatMe
            </h3>
            <small class="text-white-50">Admin Panel</small>
        </div>
        
        <!-- Navigation -->
        <ul class="nav flex-column sidebar-nav flex-grow-1">
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '') ?>" href="index.php">
                    <i class="fas fa-tachometer-alt me-3"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '') ?>" href="products.php">
                    <i class="fas fa-box me-3"></i>Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '') ?>" href="categories.php">
                    <i class="fas fa-tags me-3"></i>Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '') ?>" href="orders.php">
                    <i class="fas fa-shopping-cart me-3"></i>Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '') ?>" href="users.php">
                    <i class="fas fa-users me-3"></i>Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'coupons.php' ? 'active' : '') ?>" href="coupons.php">
                    <i class="fas fa-ticket-alt me-3"></i>Coupons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : '') ?>" href="reports.php">
                    <i class="fas fa-chart-bar me-3"></i>Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(basename($_SERVER['PHP_SELF']) === 'content_editor.php' ? 'active' : '') ?>" href="content_editor.php">
                    <i class="fas fa-edit me-3"></i>Website Content
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= e(strpos($_SERVER['REQUEST_URI'], '/admin/updates') !== false ? 'active' : '') ?>" href="/Meatme/admin/updates">
                    <i class="fas fa-bullhorn me-3"></i>Latest Updates
                </a>
            </li>
            <li class="nav-item mt-3">
                <hr class="text-white-50">
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Meatme/" target="_blank">
                    <i class="fas fa-external-link-alt me-3"></i>View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt me-3"></i>Logout
                </a>
            </li>
        </ul>
        
        <!-- Admin Profile -->
        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="d-flex align-items-center text-white mb-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($_SESSION['admin_username']) ?></div>
                        <small class="text-white-50"><?= e(ucfirst($_SESSION['admin_role'])) ?></small>
                    </div>
                </div>
                
                <!-- Theme Toggle -->
                <button class="btn btn-outline-light btn-sm w-100 mb-2" onclick="toggleTheme()">
                    <i class="fas fa-moon me-2" id="theme-icon"></i>
                    <span id="theme-text">Dark Mode</span>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container-fluid">
                <!-- Mobile Menu Toggle -->
                <button class="btn btn-outline-success d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Page Title -->
                <div class="navbar-brand mb-0 h1">
                    <?= isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard' ?>
                </div>
                
                <!-- Right Side Items -->
                <div class="d-flex align-items-center">
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary position-relative" data-mdb-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart me-2"></i>New order received</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>New user registered</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-box me-2"></i>Low stock alert</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                        </ul>
                    </div>
                    
                    <!-- Admin Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" data-mdb-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($_SESSION['admin_username']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="container-fluid p-4">
            
            <script>
                // Sidebar Toggle
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('mainContent');
                    const overlay = document.getElementById('mobileOverlay');
                    
                    if (window.innerWidth <= 768) {
                        // Mobile behavior
                        sidebar.classList.toggle('show');
                        overlay.classList.toggle('show');
                    } else {
                        // Desktop behavior
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                    }
                }
                
                // Theme Toggle
                function toggleTheme() {
                    const html = document.documentElement;
                    const currentTheme = html.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    html.setAttribute('data-theme', newTheme);
                    localStorage.setItem('admin-theme', newTheme);
                    
                    updateThemeUI(newTheme);
                }
                
                function updateThemeUI(theme) {
                    const icons = document.querySelectorAll('#theme-icon');
                    const texts = document.querySelectorAll('#theme-text');
                    
                    icons.forEach(icon => {
                        icon.className = theme === 'dark' ? 'fas fa-sun me-2' : 'fas fa-moon me-2';
                    });
                    
                    texts.forEach(text => {
                        text.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
                    });
                }
                
                // Load saved theme
                document.addEventListener('DOMContentLoaded', function() {
                    const savedTheme = localStorage.getItem('admin-theme') || 'light';
                    document.documentElement.setAttribute('data-theme', savedTheme);
                    updateThemeUI(savedTheme);
                    
                    // Close mobile sidebar when clicking outside
                    document.addEventListener('click', function(e) {
                        const sidebar = document.getElementById('sidebar');
                        const toggleBtn = document.querySelector('[onclick="toggleSidebar()"]');
                        const overlay = document.getElementById('mobileOverlay');
                        
                        if (window.innerWidth <= 768 && 
                            !sidebar.contains(e.target) && 
                            !toggleBtn.contains(e.target) &&
                            sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                            overlay.classList.remove('show');
                        }
                    });
                });
            </script>
