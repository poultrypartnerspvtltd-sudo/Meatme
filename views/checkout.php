<!-- Checkout Page -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('cart')) ?>">Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2">Checkout</h1>
            <p class="text-muted mb-0">Complete your order by providing delivery details and payment information.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <p class="text-muted mb-0">
                <?= e($cartCount) ?> item<?= e($cartCount != 1 ? 's' : '') ?> in cart
            </p>
        </div>
    </div>

    <form id="checkoutForm" method="POST" action="<?= e(\App\Core\View::url('orders/process')) ?>">
        <?= \App\Core\CSRF::field() ?>
        
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8 mb-4">
                <!-- Shipping Method Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">
                            <i class="fas fa-truck me-2 text-success"></i>Shipping Method
                        </h4>
                    </div>
                <div class="card-body p-4">
                            <div class="row">
                            <!-- Self Pickup -->
                                <div class="col-md-6 mb-3">
                                <div class="shipping-option card h-100 border-2" data-shipping="self_pickup">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3">
                                            <i class="fas fa-store fa-3x text-success"></i>
                                        </div>
                                        <h5 class="card-title">Self Pickup</h5>
                                        <p class="card-text text-muted mb-3">Pick up your order from our store</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="shipping_type"
                                                   id="self_pickup" value="self_pickup" required>
                                            <label class="form-check-label fw-bold" for="self_pickup">
                                                Self Pickup (Free)
                                            </label>
                                        </div>
                                        <small class="text-success d-block mt-2">
                                            <i class="fas fa-check-circle"></i> No shipping charge
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Home Delivery -->
                                <div class="col-md-6 mb-3">
                                <div class="shipping-option card h-100 border-2" data-shipping="home_delivery">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3">
                                            <i class="fas fa-truck fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title">Home Delivery</h5>
                                        <p class="card-text text-muted mb-3">We'll deliver to your doorstep</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="shipping_type"
                                                   id="home_delivery" value="home_delivery" required>
                                            <label class="form-check-label fw-bold" for="home_delivery">
                                                Home Delivery (Rs. 10)
                                            </label>
                                        </div>
                                        <small class="text-primary d-block mt-2">
                                            <i class="fas fa-info-circle"></i> Rs. 10 delivery charge
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                                </div>
                            </div>

                <!-- Billing Details Form (Shown when shipping method selected) -->
                <div class="card border-0 shadow-sm" id="billingForm" style="display: none;">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">
                            <i class="fas fa-user me-2 text-success"></i>Billing Details
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Email -->
                            <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                       value="<?= e(\App\Core\Auth::user()['email'] ?? '') ?>" required>
                            </div>

                        <!-- Name Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label fw-bold">First Name *</label>
                                <input type="text" class="form-control form-control-lg" id="first_name" name="first_name"
                                       value="<?= e(\App\Core\Auth::user()['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label fw-bold">Last Name *</label>
                                <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" required>
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="mb-3">
                            <label for="country" class="form-label fw-bold">Country *</label>
                            <select class="form-select form-select-lg" id="country" name="country" required>
                                <option value="Nepal" selected>Nepal</option>
                                <option value="India">India</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Address Fields (Only shown for Home Delivery) -->
                        <div id="addressFields">
                            <!-- Address Line 1 -->
                            <div class="mb-3">
                                <label for="address_line_1" class="form-label">Address Line 1 (Optional)</label>
                                <input type="text" class="form-control form-control-lg" id="address_line_1" name="address_line_1"
                                       placeholder="Street address">
                            </div>

                            <!-- Apartment / Suite -->
                            <div class="mb-3">
                                <label for="apartment" class="form-label">Apartment / Suite (Optional)</label>
                                <input type="text" class="form-control form-control-lg" id="apartment" name="apartment"
                                       placeholder="Apartment, suite, unit, etc.">
                        </div>

                            <!-- City, State, Postal Code -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City (Optional)</label>
                                    <input type="text" class="form-control form-control-lg" id="city" name="city"
                                           placeholder="City">
                                            </div>
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State / Zone (Optional)</label>
                                    <input type="text" class="form-control form-control-lg" id="state" name="state"
                                           placeholder="State / Zone">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code (Optional)</label>
                                    <input type="text" class="form-control form-control-lg" id="postal_code" name="postal_code"
                                           placeholder="Postal Code">
                                </div>
                            </div>
                            <div class="alert alert-info mb-3">
                                <small><i class="fas fa-info-circle me-1"></i> Address fields are optional. If not provided, we'll contact you to confirm the delivery address.</small>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">Phone Number *</label>
                            <input type="tel" class="form-control form-control-lg" id="phone" name="phone"
                                   value="<?= e(\App\Core\Auth::user()['phone'] ?? '') ?>" required>
                                        </div>

                        <!-- Note to Order -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Add Note to Order (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Any special instructions for your order..."></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card me-2 text-success"></i>Payment Method
                            </h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="payment_cod" value="COD" checked required>
                                <label class="form-check-label fw-bold" for="payment_cod">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Pay when you receive your order
                            </small>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="<?= e(\App\Core\View::url('terms-of-service')) ?>" target="_blank">Terms & Conditions</a>
                                    and <a href="<?= e(\App\Core\View::url('privacy-policy')) ?>" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <a href="<?= e(\App\Core\View::url('cart')) ?>" class="btn btn-outline-secondary w-100 btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success w-100 btn-lg" id="placeOrderBtn">
                                    <i class="fas fa-check me-2"></i>Place Order
                                </button>
                            </div>
                        </div>
                </div>
            </div>
        </div>

            <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;" data-cart-totals>
                <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Your Order</h5>
                </div>
                <div class="card-body">
                    <!-- Order Items -->
                    <div class="mb-3">
                        <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <div class="flex-grow-1">
                                        <span class="fw-bold"><?= e(\App\Core\View::escape($item['product']['name'])) ?></span>
                                        <br>
                                        <small class="text-muted">
                                            <?= e($item['quantity']) ?> <?= e($item['product']['unit']) ?> × 
                                            <?= e(\App\Core\View::formatPrice($item['price'])) ?>
                                        </small>
                                    </div>
                                    <span class="fw-bold text-success"><?= e(\App\Core\View::formatPrice($item['total'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Subtotal -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                            <span><?= e(\App\Core\View::formatPrice($subtotal)) ?></span>
                    </div>

                        <!-- Shipping (Updated dynamically) -->
                        <div class="d-flex justify-content-between mb-2" id="shippingRow">
                            <span>Shipping:</span>
                            <span id="shippingAmount" class="text-success">Free</span>
                    </div>

                    <hr>

                    <!-- Total -->
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold h5">Total:</span>
                            <span class="fw-bold h5 text-success" id="grandTotal">
                                <?= e(\App\Core\View::formatPrice($subtotal)) ?>
                            </span>
                    </div>

                    <!-- Delivery Info -->
                    <div class="alert alert-light border">
                        <h6 class="mb-3 fw-bold"><i class="fas fa-truck text-success me-2"></i>Delivery Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-clock text-success me-2 mt-1"></i>
                                    <div>
                                        <strong class="d-block">Same-day delivery available</strong>
                                        <small class="text-muted">Order before 2 PM for same-day delivery</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt text-success me-2 mt-1"></i>
                                    <div>
                                        <strong class="d-block">Delivery within Butwal city</strong>
                                        <small class="text-muted">We deliver fresh chicken to your doorstep</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-thermometer-half text-success me-2 mt-1"></i>
                                    <div>
                                        <strong class="d-block">Cold chain maintained</strong>
                                        <small class="text-muted">Products kept fresh with temperature-controlled delivery</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 pt-2 border-top">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-phone text-success me-2 mt-1"></i>
                                    <div>
                                        <strong class="d-block">Need Help?</strong>
                                        <small class="text-muted">
                                            Call us at 
                                            <a href="tel:9811075627" class="text-success fw-semibold text-decoration-none">
                                                9811075627
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
    </form>
</div>

<style>
.shipping-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border-color: #dee2e6 !important;
}

.shipping-option:hover {
    border-color: #2e7d32 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.1);
}

.shipping-option.selected {
    border-color: #2e7d32 !important;
    background-color: #f8fff9;
}

.form-check-input:checked {
    background-color: #2e7d32;
    border-color: #2e7d32;
}

.sticky-top {
    position: sticky;
    top: 100px;
    z-index: 10;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative;
        top: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingOptions = document.querySelectorAll('.shipping-option');
    const billingForm = document.getElementById('billingForm');
    const addressFields = document.getElementById('addressFields');
    const shippingRow = document.getElementById('shippingRow');
    const shippingAmount = document.getElementById('shippingAmount');
    const grandTotal = document.getElementById('grandTotal');
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderBtn = document.getElementById('placeOrderBtn');

    const subtotal = <?= e($subtotal) ?>;
    const shippingCharge = 10;

    // Shipping method selection
    shippingOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            shippingOptions.forEach(opt => opt.classList.remove('selected'));

            // Add selected class to clicked option
            this.classList.add('selected');

            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            // Show billing form
            billingForm.style.display = 'block';
            
            // Handle shipping type
            const shippingType = this.dataset.shipping;

            if (shippingType === 'self_pickup') {
                // Hide address fields for self pickup
                addressFields.style.display = 'none';
                // Make address fields not required
                addressFields.querySelectorAll('input[required], select[required]').forEach(field => {
                    field.removeAttribute('required');
                });
                
                // Update shipping cost
                shippingAmount.textContent = 'Free';
                shippingAmount.classList.remove('text-primary');
                shippingAmount.classList.add('text-success');
                grandTotal.textContent = 'Rs. ' + subtotal.toFixed(2);
            } else if (shippingType === 'home_delivery') {
                // Show address fields for home delivery
                addressFields.style.display = 'block';
                // Make address fields optional (not required)
                addressFields.querySelectorAll('input[required], select[required]').forEach(field => {
                    field.removeAttribute('required');
                });
                
                // Update shipping cost
                shippingAmount.textContent = 'Rs. ' + shippingCharge.toFixed(2);
                shippingAmount.classList.remove('text-success');
                shippingAmount.classList.add('text-primary');
                const total = subtotal + shippingCharge;
                grandTotal.textContent = 'Rs. ' + total.toFixed(2);
            }

            // Trigger change event
            radio.dispatchEvent(new Event('change'));
        });
    });

    // Form validation and submission
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate shipping method
        const shippingMethod = document.querySelector('input[name="shipping_type"]:checked');
        if (!shippingMethod) {
            alert('Please select a shipping method.');
            return;
        }

        // Validate required fields
        const requiredFields = ['email', 'first_name', 'last_name', 'country', 'phone'];
        let isValid = true;

        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                if (element) {
                element.classList.add('is-invalid');
                }
                isValid = false;
            } else {
                if (element) {
                element.classList.remove('is-invalid');
            }
            }
        });
        
        // Address fields are optional for home delivery - no validation needed
        // If not provided, default values will be used on the backend

        if (!isValid) {
            alert('Please fill in all required fields.');
            return;
        }

        // Show loading state
        placeOrderBtn.disabled = true;
        placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        // Build address string
        let address = '';
        if (shippingMethod.value === 'home_delivery') {
            const addressLine1 = document.getElementById('address_line_1').value || '';
            const apartment = document.getElementById('apartment').value || '';
            const city = document.getElementById('city').value || '';
            const state = document.getElementById('state').value || '';
            const postalCode = document.getElementById('postal_code').value || '';
            
            // Build address if fields are provided, otherwise use empty string (backend will use defaults)
            if (addressLine1 || city || state || postalCode) {
                address = addressLine1;
                if (apartment) {
                    address += ', ' + apartment;
                }
                if (city) {
                    address += (address ? ', ' : '') + city;
                }
                if (state) {
                    address += (address ? ', ' : '') + state;
                }
                if (postalCode) {
                    address += (address ? ' ' : '') + postalCode;
                }
            }
        } else {
            address = 'Self Pickup - Store Location';
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('first_name', document.getElementById('first_name').value);
        formData.append('last_name', document.getElementById('last_name').value);
        formData.append('name', document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value);
        formData.append('country', document.getElementById('country').value);
        formData.append('address', address);
        // Add individual address fields (optional for home delivery)
        formData.append('address_line_1', document.getElementById('address_line_1').value || '');
        formData.append('apartment', document.getElementById('apartment').value || '');
        formData.append('city', document.getElementById('city').value || '');
        formData.append('state', document.getElementById('state').value || '');
        formData.append('postal_code', document.getElementById('postal_code').value || '');
        formData.append('phone', document.getElementById('phone').value);
        formData.append('notes', document.getElementById('notes').value || '');
        formData.append('payment_method', document.querySelector('input[name="payment_method"]:checked').value);
        formData.append('shipping_type', shippingMethod.value);

        // Submit form
        fetch(checkoutForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to success page
                window.location.href = '<?= e(\App\Core\View::url('order-success')) ?>?order_id=' + data.order_id + '&order_number=' + encodeURIComponent(data.order_number);
            } else {
                alert(data.message || 'Failed to process order. Please try again.');
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = '<i class="fas fa-check me-2"></i>Place Order';
            }
        })
        .catch(error => {
            console.error('Checkout error:', error);
            alert('An error occurred. Please try again.');
            placeOrderBtn.disabled = false;
            placeOrderBtn.innerHTML = '<i class="fas fa-check me-2"></i>Place Order';
        });
    });
});
</script>
