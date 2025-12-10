<!-- Shopping Cart Page -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active">Shopping Cart</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="fw-bold mb-2 text-success">
                        <i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart
                    </h1>
                    <p class="text-muted mb-0">Review your items and proceed to checkout</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-success fs-6 px-3 py-2 d-flex align-items-center gap-1" data-cart-count-badge>
                        <i class="fas fa-box me-1"></i>
                        <span data-cart-item-count><?= e($cartCount) ?></span>
                        <span data-cart-item-text><?= e($cartCount !== 1 ? 'items' : 'item') ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    

    <div data-cart-empty-state class="text-center py-5 my-5 <?= e(empty($cartItems) ? '' : 'd-none') ?>">
        <div class="empty-cart-icon mb-4">
            <i class="fas fa-shopping-cart fa-5x text-muted opacity-50"></i>
        </div>
        <h2 class="fw-bold mb-3">🛒 Your cart is empty</h2>
        <p class="text-muted mb-4 fs-5">Looks like you haven't added any farm-fresh chicken products yet.</p>
        <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success btn-lg px-5 py-3">
            <i class="fas fa-shopping-bag me-2"></i>Start Shopping Now
        </a>
    </div>

    <div data-cart-items-wrapper class="<?= e(empty($cartItems) ? 'd-none' : '') ?>">
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-shopping-bag me-2 text-success"></i>Your Items
                                </h5>
                                <small class="text-muted">Review and update quantities as needed</small>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-danger btn-sm" data-cart-clear onclick="handleClearCart(this)" title="Remove all items from cart">
                                    <i class="fas fa-trash-alt me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" data-cart-items-container>
                        <?php foreach ($cartItems as $index => $item): ?>
                            <div class="cart-item p-4 <?= e($index < count($cartItems) - 1 ? 'border-bottom' : '') ?>"
                                 data-product-id="<?= e($item['product']['id']) ?>">
                                <div class="row align-items-center g-3">
                                    <!-- Product Image -->
                                    <div class="col-4 col-md-2">
                                        <?php 
                                        $productModel = new \App\Models\Product();
                                        $productModel->id = $item['product']['id'];
                                        $image = $productModel->primaryImage();
                                        ?>
                                        <a href="<?= e(\App\Core\View::url('products/' . $item['product']['slug'])) ?>" class="text-decoration-none">
                                            <img src="<?= e($image ? \App\Core\View::asset($image['image_path']) : \App\Core\View::asset('images/placeholder-product.jpg')) ?>" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 alt="<?= e(\App\Core\View::escape($item['product']['name'])) ?>"
                                                 style="height: 100px; width: 100px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="col-8 col-md-4">
                                        <h6 class="mb-2 fw-bold">
                                            <a href="<?= e(\App\Core\View::url('products/' . $item['product']['slug'])) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= e(\App\Core\View::escape($item['product']['name'])) ?>
                                            </a>
                                        </h6>
                                        <div class="mb-2">
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="fas fa-leaf me-1"></i><?= e(\App\Core\View::escape($item['product']['freshness_indicator'] ?? 'Fresh')) ?>
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-success fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i>In Stock
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-tag me-1"></i>
                                                <?= e(\App\Core\View::formatPrice($item['price'])) ?> / <?= e($item['product']['unit']) ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small fw-semibold text-muted mb-2">
                                            <i class="fas fa-sort-numeric-up me-1"></i>Quantity
                                        </label>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    data-quantity-action="decrease"
                                                    title="Decrease quantity">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control text-center fw-bold" 
                                                   value="<?= e($item['quantity']) ?>"
                                                   min="<?= e($item['product']['min_quantity']) ?>"
                                                   max="<?= e($item['product']['max_quantity']) ?>"
                                                   step="<?= e($item['product']['unit'] === 'kg' ? '0.25' : '1') ?>"
                                                   data-product-id="<?= e($item['product']['id']) ?>"
                                                   onchange="updateCartItem(this)"
                                                   aria-label="Quantity">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    data-quantity-action="increase"
                                                    title="Increase quantity">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Min: <?= e($item['product']['min_quantity']) ?> <?= e($item['product']['unit']) ?>
                                        </small>
                                    </div>
                                    
                                    <!-- Price & Actions -->
                                    <div class="col-12 col-md-3 text-md-end">
                                        <div class="mb-3">
                                            <div class="h5 fw-bold text-success mb-1">
                                                <?= e(\App\Core\View::formatPrice($item['total'])) ?>
                                            </div>
                                            <small class="text-muted">
                                                Total for <?= e($item['quantity']) ?> <?= e($item['product']['unit']) ?>
                                            </small>
                                        </div>
                                        <button class="btn btn-outline-danger btn-sm w-100 w-md-auto" 
                                                onclick="handleRemoveFromCart(<?= e($item['product']['id']) ?>, this)"
                                                title="Remove this item">
                                            <i class="fas fa-trash-alt me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-4">
                    <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-outline-success btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" data-cart-summary>
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Price Breakdown -->
                        <div class="price-breakdown mb-3">
                            <!-- Subtotal -->
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <span class="text-muted">
                                    <i class="fas fa-shopping-bag me-1"></i>Subtotal
                                </span>
                                <span class="fw-semibold" data-cart-subtotal><?= e(\App\Core\View::formatPrice($subtotal)) ?></span>
                            </div>
                            
                            <!-- Delivery Fee -->
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <span class="text-muted">
                                    <i class="fas fa-truck me-1"></i>Delivery Fee
                                </span>
                                <span class="fw-semibold <?= e($deliveryFee == 0 ? 'text-success' : '') ?>" data-cart-delivery>
                                    <?php if ($deliveryFee == 0): ?>
                                        <i class="fas fa-check-circle me-1"></i>FREE
                                    <?php else: ?>
                                        <?= e(\App\Core\View::formatPrice($deliveryFee)) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <?php if ($deliveryFee > 0): ?>
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <small class="mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Free delivery</strong> within 5 km
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success py-2 px-3 mb-3">
                                    <small class="mb-0">
                                        <i class="fas fa-gift me-1"></i>
                                        <strong>Congratulations!</strong> You've qualified for free delivery!
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <hr class="my-3">
                        
                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold h5 mb-0">Total Amount:</span>
                            <span class="fw-bold h4 text-success" data-cart-total><?= e(\App\Core\View::formatPrice($total)) ?></span>
                        </div>
                        
                        <!-- Coupon feature removed -->
                        
                        <!-- Checkout Button -->
                        <?php if (\App\Core\Auth::check()): ?>
                            <a href="<?= e(\App\Core\View::url('checkout')) ?>" class="btn btn-success w-100 btn-lg mb-3 py-3" data-cart-checkout>
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <div class="mb-3">
                                <a href="<?= e(\App\Core\View::url('login')) ?>" class="btn btn-success w-100 btn-lg py-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                </a>
                                <p class="text-center text-muted small mt-2 mb-0">
                                    New customer? <a href="<?= e(\App\Core\View::url('register')) ?>" class="text-success fw-semibold">Create an account</a>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Security Notice -->
                        <div class="text-center pt-3 border-top">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-shield-alt text-success me-1"></i>
                                <strong>Secure Checkout</strong>
                            </small>
                            <small class="text-muted">
                                Your payment information is encrypted and secure
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Information -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-truck me-2 text-success"></i>Delivery Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="delivery-info-list">
                            <div class="delivery-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="delivery-icon me-3">
                                        <i class="fas fa-clock fa-lg text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1">Same-Day Delivery</strong>
                                        <small class="text-muted">Order before 2 PM for same-day delivery</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="delivery-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="delivery-icon me-3">
                                        <i class="fas fa-map-marker-alt fa-lg text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1">Delivery Area</strong>
                                        <small class="text-muted">Delivery within Butwal city</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="delivery-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="delivery-icon me-3">
                                        <i class="fas fa-thermometer-half fa-lg text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1">Cold Chain Maintained</strong>
                                        <small class="text-muted">Products kept fresh with temperature-controlled delivery</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="delivery-item">
                                <div class="d-flex align-items-start">
                                    <div class="delivery-icon me-3">
                                        <i class="fas fa-phone fa-lg text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block mb-1">Need Help?</strong>
                                        <small class="text-muted">
                                            Call us at 
                                            <a href="tel:9811075627" class="text-success fw-semibold text-decoration-none">
                                                <i class="fas fa-phone-alt me-1"></i>9811075627
                                            </a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Cart Item Styles */
.cart-item {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.cart-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.cart-item img {
    transition: transform 0.3s ease;
}

.cart-item:hover img {
    transform: scale(1.05);
}

/* Sticky Summary */
.sticky-top {
    position: sticky;
    top: 100px;
    z-index: 10;
}

/* Quantity Input */
.input-group .form-control {
    font-weight: 600;
    border-left: none;
    border-right: none;
}

.input-group .btn {
    border-color: #dee2e6;
    transition: all 0.2s ease;
}

.input-group .btn:hover {
    background-color: #2e7d32;
    color: white;
    border-color: #2e7d32;
}

/* Price Breakdown */
.price-breakdown .border-bottom {
    border-color: #e9ecef !important;
}

/* Delivery Info */
.delivery-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f7f0;
    border-radius: 50%;
}

.delivery-item {
    transition: all 0.2s ease;
}

.delivery-item:hover {
    padding-left: 5px;
}

/* Empty Cart */
.empty-cart-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .sticky-top {
        position: relative;
        top: auto;
    }
    
    .cart-item {
        padding: 1.5rem !important;
    }
    
    .cart-item img {
        height: 80px !important;
        width: 80px !important;
    }
}

/* Button Improvements */
.btn-success {
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
    transition: all 0.3s ease;
}

.btn-success:hover {
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
    transform: translateY(-2px);
}

.btn-outline-danger {
    transition: all 0.2s ease;
}

.btn-outline-danger:hover {
    transform: scale(1.05);
}

/* Card Improvements */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

/* Badge Improvements */
.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}
</style>

<script>
const cartItemsWrapper = document.querySelector('[data-cart-items-wrapper]');
const cartEmptyState = document.querySelector('[data-cart-empty-state]');
const cartItemsContainer = document.querySelector('[data-cart-items-container]');
const cartHeaderCount = document.querySelector('[data-cart-item-count]');
const cartHeaderText = document.querySelector('[data-cart-item-text]');
const checkoutButton = document.querySelector('[data-cart-checkout]');

document.querySelectorAll('input[data-product-id]').forEach(input => {
    input.dataset.lastValue = input.value;
});

function getCartCount(value) {
    const count = Number(value ?? 0);
    return Number.isNaN(count) ? 0 : count;
}

function updateCartHeader(count) {
    const safeCount = getCartCount(count);
    if (cartHeaderCount) {
        cartHeaderCount.textContent = safeCount;
    }
    if (cartHeaderText) {
        cartHeaderText.textContent = safeCount === 1 ? 'item' : 'items';
    }
}

function toggleCartEmptyState(count) {
    const safeCount = getCartCount(count);
    const hasItemsInDOM = cartItemsContainer && cartItemsContainer.querySelectorAll('[data-product-id]').length > 0;
    const isEmpty = safeCount === 0 || !hasItemsInDOM;

    if (cartItemsWrapper) {
        cartItemsWrapper.classList.toggle('d-none', isEmpty);
    }
    if (cartEmptyState) {
        cartEmptyState.classList.toggle('d-none', !isEmpty);
    }
    if (checkoutButton) {
        checkoutButton.toggleAttribute('disabled', isEmpty);
        checkoutButton.classList.toggle('disabled', isEmpty);
    }
}

function setLoadingState(button, isLoading, label = 'Please wait...') {
    if (!button) return;

    if (isLoading) {
        button.dataset.originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${label}`;
    } else {
        button.disabled = false;
        if (button.dataset.originalContent) {
            button.innerHTML = button.dataset.originalContent;
            delete button.dataset.originalContent;
        }
    }
}

function updateCartItem(input) {
    if (!input || !window.MeatMe) return;

    const productId = input.getAttribute('data-product-id');
    const quantity = parseFloat(input.value);

    if (!productId || Number.isNaN(quantity)) {
        return;
    }

    const previousValue = input.dataset.lastValue ?? input.value;
    input.dataset.previousValue = previousValue;
    input.dataset.lastValue = quantity;

    window.MeatMe.updateCart(productId, input);
}

function handleRemoveFromCart(productId, trigger) {
    if (!window.MeatMe) {
        alert('Cart functionality is currently unavailable. Please refresh the page and try again.');
        return;
    }

    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    const button = trigger || null;
    const itemRow = button ? button.closest('[data-product-id]') : cartItemsContainer?.querySelector(`[data-product-id="${productId}"]`);

    setLoadingState(button, true, 'Removing...');

    window.MeatMe.removeFromCart(productId)
        .then(response => {
            if (response && response.success) {
                if (itemRow) {
                    itemRow.remove();
                }
                const remainingCount = getCartCount(response.cartCount);
                updateCartHeader(remainingCount);
                toggleCartEmptyState(remainingCount);
            }
        })
        .catch(error => {
            console.error('Remove cart item error:', error);
        })
        .finally(() => {
            setLoadingState(button, false);
        });
}

function handleClearCart(trigger) {
    if (!window.MeatMe) {
        alert('Cart functionality is currently unavailable. Please refresh the page and try again.');
        return;
    }

    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }

    const button = trigger || null;
    setLoadingState(button, true, 'Clearing...');

    window.MeatMe.clearCart()
        .then(response => {
            if (response && response.success) {
                if (cartItemsContainer) {
                    cartItemsContainer.innerHTML = '';
                }
                const remainingCount = getCartCount(response.cartCount);
                updateCartHeader(remainingCount);
                toggleCartEmptyState(remainingCount);
            }
        })
        .catch(error => {
            console.error('Clear cart error:', error);
        })
        .finally(() => {
            setLoadingState(button, false);
        });
}

// Coupon support removed: applyCoupon() disabled

document.addEventListener('click', function(e) {
    const target = e.target.closest('[data-quantity-action]');
    if (!target) return;
    
    const action = target.getAttribute('data-quantity-action');
    const input = target.parentNode?.querySelector('input[type="number"]');
    
    if (!input) return;
    
    const step = parseFloat(input.step) || 1;
    const min = parseFloat(input.min) || 0;
    const max = parseFloat(input.max) || Infinity;
    let currentValue = parseFloat(input.value) || 0;
    
    input.dataset.previousValue = currentValue;
    
    if (action === 'increase' && currentValue < max) {
        currentValue += step;
    } else if (action === 'decrease' && currentValue > min) {
        currentValue -= step;
    }
    
    currentValue = Math.min(Math.max(currentValue, min), max);
    input.value = step < 1 ? currentValue.toFixed(2) : currentValue;
    updateCartItem(input);
});

document.addEventListener('change', function(e) {
    if (e.target.matches('input[data-product-id]')) {
        const input = e.target;
        input.dataset.previousValue = input.dataset.lastValue ?? input.value;
        updateCartItem(input);
    }
});

updateCartHeader(<?= e((int) $cartCount) ?>);
toggleCartEmptyState(<?= e((int) $cartCount) ?>);
</script>