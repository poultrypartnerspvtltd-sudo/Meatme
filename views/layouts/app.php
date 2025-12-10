<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(isset($title) ? $title . ' - ' : '') ?>MeatMe - Fresh Chicken</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Fresh, hygienically processed chicken meat directly from farms to your table. Same-day delivery in Kathmandu Valley.">
    <meta name="keywords" content="fresh chicken, organic chicken, farm chicken, chicken delivery, meat delivery, kathmandu">
    <meta name="author" content="MeatMe">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= e(isset($title) ? $title . ' - ' : '') ?>MeatMe - Fresh Chicken">
    <meta property="og:description" content="Fresh, hygienically processed chicken meat directly from farms to your table.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(\App\Core\View::url()) ?>">
    <meta property="og:image" content="<?= e(\App\Core\View::asset('images/og-image.jpg')) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= e(\App\Core\View::asset('images/favicon.ico')) ?>">
    <link rel="apple-touch-icon" href="<?= e(\App\Core\View::asset('images/apple-touch-icon.png')) ?>">
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= e(\App\Core\View::asset('css/style.css')) ?>" rel="stylesheet">
    
    <!-- Theme Colors -->
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #ff6f00;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196f3;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        [data-theme="dark"] {
            --primary-color: #4caf50;
            --secondary-color: #ffab40;
            --light-color: #212529;
            --dark-color: #f8f9fa;
        }
    </style>
</head>
<body style="padding-top: 76px !important;">
    <form id="csrf-token-form" style="display:none;" aria-hidden="true">
        <?= \App\Core\CSRF::field() ?>
    </form>
    <script>
        window.csrfToken = document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value || '';
    </script>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm" style="position: fixed !important; top: 0 !important; width: 100% !important; z-index: 1030 !important;">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?= e(\App\Core\View::url()) ?>">
                <img src="<?= e(\App\Core\View::asset('images/logo-meatme.jpeg')) ?>" alt="MeatMe" style="height:40px; width:auto;" class="me-2">
                <span class="fw-bold text-success">MeatMe</span>
            </a>
            
            <!-- Phone Number -->
            <div class="ms-auto d-none d-md-flex align-items-center me-3">
                <a href="tel:9811075627" class="text-decoration-none text-dark">
                    <i class="fas fa-phone-alt me-2 text-success"></i>
                    <span class="d-none d-lg-inline">Call for Order: </span>9811075627
                </a>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler d-lg-none" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url()) ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('products')) ?>">Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-mdb-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $categories = \App\Models\Category::active();
                            foreach ($categories as $category): ?>
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>"><?= e($category['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('about')) ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(\App\Core\View::url('contact')) ?>">Contact</a>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Search -->
                    <li class="nav-item me-2">
                        <form class="d-flex" action="<?= e(\App\Core\View::url('search')) ?>" method="GET">
                            <div class="input-group">
                                <input type="search" class="form-control form-control-sm" name="q" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>">
                                <button class="btn btn-outline-success btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                    
                    <!-- Theme Toggle -->
                    <li class="nav-item me-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleTheme()">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="<?= e(\App\Core\View::url('cart')) ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                <?= e(\App\Core\Session::getCartCount()) ?>
                            </span>
                        </a>
                    </li>
                    
                    <!-- User Menu -->
                    <?php if (\App\Core\Auth::check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-mdb-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?= e(\App\Core\Auth::user()['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('dashboard')) ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('profile')) ?>"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('orders')) ?>"><i class="fas fa-box me-2"></i>Orders</a></li>
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('wishlist')) ?>"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (\App\Core\Auth::isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('admin')) ?>"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= e(\App\Core\View::url('logout')) ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= e(\App\Core\View::url('login')) ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm" href="<?= e(\App\Core\View::url('register')) ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= e(\App\Core\Session::flash('success')) ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= e(\App\Core\Session::flash('error')) ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (\App\Core\Session::hasFlash('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= e(\App\Core\Session::flash('warning')) ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>
        <?= $content ?? '' /* verified safe: rendered view HTML */ ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-success mb-3">
                        <i class="fas fa-drumstick-bite me-2"></i>MeatMe
                    </h5>
                    <p class="mb-3">Fresh, hygienically processed chicken meat directly from farms to your table. We ensure quality, freshness, and same-day delivery.</p>
                    <div class="d-flex">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= e(\App\Core\View::url()) ?>" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="<?= e(\App\Core\View::url('products')) ?>" class="text-light text-decoration-none">Products</a></li>
                        <li><a href="<?= e(\App\Core\View::url('about')) ?>" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="<?= e(\App\Core\View::url('contact')) ?>" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= e(\App\Core\View::url('category/whole-chicken')) ?>" class="text-light text-decoration-none">Whole Chicken</a></li>
                        <li><a href="<?= e(\App\Core\View::url('category/chicken-breast')) ?>" class="text-light text-decoration-none">Chicken Breast</a></li>
                        <li><a href="<?= e(\App\Core\View::url('category/chicken-legs')) ?>" class="text-light text-decoration-none">Chicken Legs</a></li>
                        <li><a href="<?= e(\App\Core\View::url('category/boneless-cuts')) ?>" class="text-light text-decoration-none">Boneless Cuts</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= e(\App\Core\View::url('faq')) ?>" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="<?= e(\App\Core\View::url('delivery-policy')) ?>" class="text-light text-decoration-none">Delivery Policy</a></li>
                        <li><a href="<?= e(\App\Core\View::url('refund-policy')) ?>" class="text-light text-decoration-none">Refund Policy</a></li>
                        <li><a href="<?= e(\App\Core\View::url('privacy-policy')) ?>" class="text-light text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Contact Info</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-phone me-2"></i><a href="tel:+9779811075627" class="text-decoration-none text-light">+977-9811075627</a></li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i><a href="mailto:meatme9898@gmail.com" class="text-decoration-none text-light">meatme9898@gmail.com</a></li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Butwal, Nepal</li>
                        <li><i class="fas fa-clock me-2"></i>24/7 Service</li>
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter Signup -->
            <div class="row mt-4">
                <div class="col-lg-6 mx-auto">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3"><i class="fas fa-envelope me-2"></i>Subscribe to Our Newsletter</h5>
                            <p class="card-text mb-3">Get exclusive offers, updates, and fresh chicken tips delivered to your inbox!</p>
                            <form id="newsletter-form" class="d-flex gap-2" onsubmit="handleNewsletterSubmit(event)">
                                <input type="email" class="form-control" id="newsletter-email" name="email" placeholder="Enter your email" required aria-label="Newsletter email">
                                <button type="submit" class="btn btn-light" id="newsletter-submit">
                                    <i class="fas fa-paper-plane me-1"></i>Subscribe
                                </button>
                            </form>
                            <div id="newsletter-message" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= e(date('Y')) ?> MeatMe. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> for fresh food lovers</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Bottom Navigation -->
    <div class="d-md-none fixed-bottom bg-white border-top">
        <div class="row g-0">
            <div class="col text-center py-2">
                <a href="<?= e(\App\Core\View::url()) ?>" class="text-decoration-none text-dark">
                    <i class="fas fa-home d-block"></i>
                    <small>Home</small>
                </a>
            </div>
            <div class="col text-center py-2">
                <a href="<?= e(\App\Core\View::url('products')) ?>" class="text-decoration-none text-dark">
                    <i class="fas fa-th-large d-block"></i>
                    <small>Products</small>
                </a>
            </div>
            <div class="col text-center py-2">
                <a href="<?= e(\App\Core\View::url('cart')) ?>" class="text-decoration-none text-dark position-relative">
                    <i class="fas fa-shopping-cart d-block"></i>
                    <small>Cart</small>
                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                        <?= e(\App\Core\Session::getCartCount()) ?>
                    </span>
                </a>
            </div>
            <div class="col text-center py-2">
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="<?= e(\App\Core\View::url('dashboard')) ?>" class="text-decoration-none text-dark">
                        <i class="fas fa-user d-block"></i>
                        <small>Profile</small>
                    </a>
                <?php else: ?>
                    <a href="<?= e(\App\Core\View::url('login')) ?>" class="text-decoration-none text-dark">
                        <i class="fas fa-sign-in-alt d-block"></i>
                        <small>Login</small>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/9779811075627?text=Hi%20MeatMe!%20I%20would%20like%20to%20inquire%20about%20your%20fresh%20chicken%20products." target="_blank" class="whatsapp-float" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <!-- Scripts -->
    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= e(\App\Core\View::asset('js/app.js')) ?>"></script>
    
    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        
        // Newsletter Form Handler
        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const emailInput = document.getElementById('newsletter-email');
            const submitBtn = document.getElementById('newsletter-submit');
            const messageDiv = document.getElementById('newsletter-message');
            const email = emailInput.value.trim();
            
            // Basic email validation
            if (!email || !email.includes('@')) {
                messageDiv.innerHTML = '<div class="alert alert-danger mb-0">Please enter a valid email address.</div>';
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Subscribing...';
            
            // Simulate API call (replace with actual endpoint)
            setTimeout(() => {
                messageDiv.innerHTML = '<div class="alert alert-success mb-0">Thank you for subscribing! Check your email for confirmation.</div>';
                emailInput.value = '';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Subscribe';
                
                // Track analytics event
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_signup', {
                        'event_category': 'engagement',
                        'event_label': 'footer_newsletter'
                    });
                }
                
                // Clear message after 5 seconds
                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 5000);
            }, 1000);
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Update icon
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        });
        
        // CSRF Token for AJAX requests
        const globalCsrfInput = document.querySelector('#csrf-token-form input[name="csrf_token"]');
        window.csrfToken = globalCsrfInput ? globalCsrfInput.value : '';

        // MeatMe JavaScript Object for cart operations
        window.MeatMe = {
            updateCart: function(productId, input) {
                const quantity = parseFloat(input.value);
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                return this.makeRequest('POST', '<?= e(\App\Core\View::url("cart/update")) ?>', formData)
                    .then(response => {
                        if (response.success) {
                            this.updateCartTotals(response);
                            this.showToast('success', response.message);
                            if (input) {
                                input.dataset.lastValue = quantity;
                            }
                        } else {
                            this.showToast('error', response.message || 'Unable to update cart.');
                            if (input && input.dataset.previousValue) {
                                input.value = input.dataset.previousValue;
                            }
                        }
                        return response;
                    })
                    .catch(error => {
                        console.error('Cart update error:', error);
                        this.showToast('error', 'Failed to update cart. Please try again.');
                        if (input && input.dataset.previousValue) {
                            input.value = input.dataset.previousValue;
                        }
                        throw error;
                    });
            },

            removeFromCart: function(productId) {
                const formData = new FormData();
                formData.append('product_id', productId);

                return this.makeRequest('POST', '<?= e(\App\Core\View::url("cart/remove")) ?>', formData)
                    .then(response => {
                        if (response.success) {
                            this.updateCartTotals(response);
                            this.showToast('success', response.message);
                        } else {
                            this.showToast('error', response.message || 'Failed to remove item.');
                        }
                        return response;
                    })
                    .catch(error => {
                        console.error('Cart remove error:', error);
                        this.showToast('error', 'Failed to remove item. Please try again.');
                        throw error;
                    });
            },

            clearCart: function() {
                return this.makeRequest('POST', '<?= e(\App\Core\View::url("cart/clear")) ?>', {})
                    .then(response => {
                        if (response.success) {
                            this.updateCartTotals(response);
                            this.showToast('success', response.message);
                        } else {
                            this.showToast('error', response.message || 'Failed to clear cart.');
                        }
                        return response;
                    })
                    .catch(error => {
                        console.error('Cart clear error:', error);
                        this.showToast('error', 'Failed to clear cart. Please try again.');
                        throw error;
                    });
            },

            makeRequest: function(method, url, data) {
                let requestData = data;

                // Ensure we always send FormData with CSRF token
                if (data instanceof FormData) {
                    requestData = data;
                } else {
                    requestData = new FormData();
                    if (data && typeof data === 'object') {
                        Object.keys(data).forEach(key => {
                            requestData.append(key, data[key]);
                        });
                    }
                }

                if (!requestData.has('csrf_token')) {
                    requestData.append('csrf_token', window.csrfToken);
                }

                const headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (window.csrfToken) {
                    headers['X-CSRF-TOKEN'] = window.csrfToken;
                }

                return fetch(url, {
                    method: method,
                    headers: headers,
                    body: requestData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                });
            },

            showToast: function(type, message) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                `;

                document.body.appendChild(toast);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 3000);
            },

            updateHeaderCartCount: function(count) {
                const navCartBadge = document.querySelector('#cart-count');
                if (navCartBadge) {
                    navCartBadge.textContent = count;
                }
            },

            updateCartTotals: function(data) {
                const toNumber = (value) => {
                    const number = Number(value ?? 0);
                    return Number.isNaN(number) ? 0 : number;
                };

                const formatCurrency = (value) => 'Rs. ' + toNumber(value).toFixed(2);
                const cartCount = toNumber(data.cartCount ?? data.count ?? 0);

                // Update header counts
                this.updateHeaderCartCount(cartCount);

                const headerCount = document.querySelector('[data-cart-item-count]');
                if (headerCount) {
                    headerCount.textContent = cartCount;
                }

                const headerText = document.querySelector('[data-cart-item-text]');
                if (headerText) {
                    headerText.textContent = cartCount === 1 ? 'item' : 'items';
                }

                // Update totals on cart page when present
                const subtotalEl = document.querySelector('[data-cart-subtotal]');
                const deliveryEl = document.querySelector('[data-cart-delivery]');
                const totalEl = document.querySelector('[data-cart-total]');

                if (subtotalEl) {
                    subtotalEl.textContent = formatCurrency(data.subtotal);
                }

                if (deliveryEl) {
                    const deliveryFee = toNumber(data.deliveryFee);
                    if (deliveryFee === 0) {
                        deliveryEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>FREE';
                        deliveryEl.classList.add('text-success');
                    } else {
                        deliveryEl.textContent = formatCurrency(deliveryFee);
                        deliveryEl.classList.remove('text-success');
                    }
                }

                if (totalEl) {
                    totalEl.textContent = formatCurrency(data.total);
                }
            }
        };
    </script>
</body>
</html>
