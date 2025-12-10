<?php
/**
 * Web Routes for MeatMe Application
 */

use App\Core\Router;

// Public Routes
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// Product Routes
$router->get('/products', 'ProductController@index');
$router->get('/products/{slug}', 'ProductController@show');
$router->get('/category/{slug}', 'ProductController@category');
$router->get('/search', 'ProductController@search');

// Authentication Routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login', ['CSRF']);
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register', ['CSRF']);
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword', ['CSRF']);
$router->get('/reset-password/{token}', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword', ['CSRF']);

// User Dashboard Routes (Protected)
$router->get('/dashboard', 'DashboardController@index', ['Auth']);

// Profile Routes (Protected)
$router->get('/profile', 'ProfileController@index', ['Auth']);
$router->post('/profile/update', 'ProfileController@update', ['Auth', 'CSRF']);
$router->get('/profile/password', 'ProfileController@password', ['Auth']);
$router->post('/profile/password', 'ProfileController@updatePassword', ['Auth', 'CSRF']);

// Order Routes (Protected)
$router->get('/orders', 'OrderController@index', ['Auth']);
$router->get('/orders/{id}', 'OrderController@show', ['Auth']);

// Address Management
$router->get('/addresses', 'AddressController@index', ['Auth']);
$router->post('/addresses', 'AddressController@store', ['Auth', 'CSRF']);
$router->put('/addresses/{id}', 'AddressController@update', ['Auth', 'CSRF']);
$router->delete('/addresses/{id}', 'AddressController@delete', ['Auth', 'CSRF']);

// Cart Routes
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add', ['CSRF']);
$router->post('/cart/update', 'CartController@update', ['CSRF']);
$router->post('/cart/remove', 'CartController@remove', ['CSRF']);
$router->post('/cart/clear', 'CartController@clear', ['CSRF']);

// Checkout Routes
$router->get('/checkout', 'OrderController@checkout', ['Auth']);
$router->post('/orders/process', 'OrderController@process', ['Auth', 'CSRF']);
$router->get('/order-success', 'OrderController@success');

// Payment Routes
$router->post('/payment/stripe', 'PaymentController@stripe', ['Auth', 'CSRF']);
$router->post('/payment/razorpay', 'PaymentController@razorpay', ['Auth', 'CSRF']);
$router->post('/payment/paypal', 'PaymentController@paypal', ['Auth', 'CSRF']);
$router->post('/payment/esewa', 'PaymentController@esewa', ['Auth', 'CSRF']);
$router->get('/payment/callback/{gateway}', 'PaymentController@callback');

// Wishlist Routes
$router->get('/wishlist', 'WishlistController@index', ['Auth']);
$router->post('/wishlist/add', 'WishlistController@add', ['Auth', 'CSRF']);
$router->post('/wishlist/remove', 'WishlistController@remove', ['Auth', 'CSRF']);

// Review Routes
$router->post('/reviews', 'ReviewController@store', ['Auth', 'CSRF']);
$router->get('/reviews/{product}', 'ReviewController@index');

// Coupon Routes removed: coupon feature disabled

// Contact & Support
$router->get('/contact', 'ContactController@index');
$router->post('/contact', 'ContactController@submit', ['CSRF']);
$router->get('/faq', 'ContactController@faq');

// Static Pages
$router->get('/about', 'PageController@about');
$router->get('/privacy-policy', 'PageController@privacy');
$router->get('/terms-of-service', 'PageController@terms');
$router->get('/delivery-policy', 'PageController@delivery');
$router->get('/refund-policy', 'PageController@refund');

// API Routes
$router->get('/api/products/search', 'Api\ProductController@search');
$router->get('/api/products/suggestions', 'Api\ProductController@suggestions');
$router->get('/api/cart/count', 'Api\CartController@count');
$router->get('/api/notifications', 'Api\NotificationController@index', ['Auth']);
$router->get('/api/orders', 'Api\OrderController@index', ['Auth']);
$router->get('/api/orders/{id}', 'Api\OrderController@show', ['Auth']);

// Admin Routes
$router->get('/admin', 'Admin\DashboardController@index', ['AdminAuth']);
$router->get('/admin/login', 'Admin\AuthController@showLogin');
$router->post('/admin/login', 'Admin\AuthController@login', ['CSRF']);
$router->get('/admin/logout', 'Admin\AuthController@logout');

// Admin Dashboard
$router->get('/admin/dashboard', 'Admin\DashboardController@index', ['AdminAuth']);
$router->get('/admin/analytics', 'Admin\DashboardController@analytics', ['AdminAuth']);

// Admin Updates Routes

// Admin Product Management
$router->get('/admin/products', 'Admin\ProductController@index', ['AdminAuth']);
$router->get('/admin/products/create', 'Admin\ProductController@create', ['AdminAuth']);
$router->post('/admin/products', 'Admin\ProductController@store', ['AdminAuth', 'CSRF']);
$router->get('/admin/products/{id}', 'Admin\ProductController@show', ['AdminAuth']);
$router->get('/admin/products/{id}/edit', 'Admin\ProductController@edit', ['AdminAuth']);
$router->put('/admin/products/{id}', 'Admin\ProductController@update', ['AdminAuth', 'CSRF']);
$router->delete('/admin/products/{id}', 'Admin\ProductController@delete', ['AdminAuth', 'CSRF']);

// Admin Category Management
$router->get('/admin/categories', 'Admin\CategoryController@index', ['AdminAuth']);
$router->post('/admin/categories', 'Admin\CategoryController@store', ['AdminAuth', 'CSRF']);
$router->put('/admin/categories/{id}', 'Admin\CategoryController@update', ['AdminAuth', 'CSRF']);
$router->delete('/admin/categories/{id}', 'Admin\CategoryController@delete', ['AdminAuth', 'CSRF']);

// Admin Order Management
$router->get('/admin/orders', 'Admin\OrderController@index', ['AdminAuth']);
$router->get('/admin/orders/{id}', 'Admin\OrderController@show', ['AdminAuth']);
$router->put('/admin/orders/{id}/status', 'Admin\OrderController@updateStatus', ['AdminAuth', 'CSRF']);
$router->post('/admin/orders/{id}/refund', 'Admin\OrderController@refund', ['AdminAuth', 'CSRF']);

// Admin User Management
$router->get('/admin/users', 'Admin\UserController@index', ['AdminAuth']);
$router->get('/admin/users/{id}', 'Admin\UserController@show', ['AdminAuth']);
$router->put('/admin/users/{id}/status', 'Admin\UserController@updateStatus', ['AdminAuth', 'CSRF']);

// Admin Coupon Management removed

// Admin Review Management
$router->get('/admin/reviews', 'Admin\ReviewController@index', ['AdminAuth']);
$router->put('/admin/reviews/{id}/approve', 'Admin\ReviewController@approve', ['AdminAuth', 'CSRF']);
$router->delete('/admin/reviews/{id}', 'Admin\ReviewController@delete', ['AdminAuth', 'CSRF']);

// Admin Contact Management
$router->get('/admin/contacts', 'Admin\ContactController@index', ['AdminAuth']);
$router->get('/admin/contacts/{id}', 'Admin\ContactController@show', ['AdminAuth']);
$router->put('/admin/contacts/{id}/status', 'Admin\ContactController@updateStatus', ['AdminAuth', 'CSRF']);

// Admin FAQ Management
$router->get('/admin/faqs', 'Admin\FaqController@index', ['AdminAuth']);
$router->post('/admin/faqs', 'Admin\FaqController@store', ['AdminAuth', 'CSRF']);
$router->put('/admin/faqs/{id}', 'Admin\FaqController@update', ['AdminAuth', 'CSRF']);
$router->delete('/admin/faqs/{id}', 'Admin\FaqController@delete', ['AdminAuth', 'CSRF']);

// Admin Settings
$router->get('/admin/settings', 'Admin\SettingController@index', ['AdminAuth']);
$router->post('/admin/settings', 'Admin\SettingController@update', ['AdminAuth', 'CSRF']);

// Admin Reports
$router->get('/admin/reports/sales', 'Admin\ReportController@sales', ['AdminAuth']);
$router->get('/admin/reports/products', 'Admin\ReportController@products', ['AdminAuth']);
$router->get('/admin/reports/customers', 'Admin\ReportController@customers', ['AdminAuth']);
$router->get('/admin/reports/export/{type}', 'Admin\ReportController@export', ['AdminAuth']);

// File Upload Routes
$router->post('/upload/image', 'UploadController@image', ['Auth', 'CSRF']);
$router->post('/admin/upload/image', 'UploadController@adminImage', ['AdminAuth', 'CSRF']);
?>
