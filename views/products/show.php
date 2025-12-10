<!-- Product Detail Page -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('products')) ?>">Products</a></li>
            <?php if ($category): ?>
                <li class="breadcrumb-item">
                    <a href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>">
                        <?= e(\App\Core\View::escape($category['name'])) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= e(\App\Core\View::escape($product['name'])) ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <?php 
                    $mainImage = !empty($images) ? $images[0] : null;
                    $mainImageSrc = $mainImage ? \App\Core\View::asset($mainImage['image_path']) : \App\Core\View::asset('images/placeholder-product.jpg');
                    ?>
                    <img id="mainProductImage" 
                         src="<?= e($mainImageSrc) ?>" 
                         class="img-fluid rounded shadow-sm w-100" 
                         alt="<?= e(\App\Core\View::escape($product['name'])) ?>"
                         style="height: 400px; object-fit: cover;">
                    
                    <!-- Badges -->
                    <div class="position-absolute top-0 start-0 m-3">
                        <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                            <span class="badge bg-danger fs-6 me-2">
                                <?= e(round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100)) ?>% OFF
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($product['is_featured']): ?>
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="position-absolute bottom-0 start-0 m-3">
                        <span class="badge bg-success fs-6">
                            <?= e(\App\Core\View::escape($product['freshness_indicator'])) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Thumbnail Images -->
                <?php if (count($images) > 1): ?>
                    <div class="thumbnail-images">
                        <div class="row g-2">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="col-3">
                                    <img src="<?= e(\App\Core\View::asset($image['Alivechicks.png'])) ?>" 
                                         class="img-fluid rounded cursor-pointer thumbnail-img <?= e($index === 0 ? 'active' : '') ?>" 
                                         alt="<?= e(\App\Core\View::escape($image['alt_text'] ?? $product['name'])) ?>"
                                         style="height: 80px; object-fit: cover;"
                                         onclick="changeMainImage('<?= e(\App\Core\View::asset($image['image_path'])) ?>', this)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Information -->
        <div class="col-lg-6">
            <div class="product-info">
                <!-- Category -->
                <?php if ($category): ?>
                    <div class="mb-2">
                        <a href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>" 
                           class="badge bg-light text-dark text-decoration-none">
                            <?= e(\App\Core\View::escape($category['name'])) ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Product Name -->
                <h1 class="fw-bold mb-3"><?= e(\App\Core\View::escape($product['name'])) ?></h1>
                
                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rating-stars me-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= e($i <= $rating['average'] ? 'text-warning' : 'text-muted') ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted">
                        <?= e($rating['average']) ?> (<?= e($rating['count']) ?> review<?= e($rating['count'] != 1 ? 's' : '') ?>)
                    </span>
                </div>
                
                <!-- Price -->
                <div class="price-section mb-4">
                    <div class="d-flex align-items-baseline">
                        <span class="h2 text-success fw-bold me-3">
                            <?= e(\App\Core\View::formatPrice($product['price'])) ?>
                        </span>
                        <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                            <span class="h5 text-muted text-decoration-line-through me-2">
                                <?= e(\App\Core\View::formatPrice($product['compare_price'])) ?>
                            </span>
                            <span class="badge bg-danger">
                                Save <?= e(\App\Core\View::formatPrice($product['compare_price'] - $product['price'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">per <?= e(\App\Core\View::escape($product['unit'])) ?></small>
                </div>
                
                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <strong>In Stock</strong> - <?= e($product['stock_quantity']) ?> <?= e($product['unit']) ?> available
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-times-circle me-2"></i>
                            <div>
                                <strong>Out of Stock</strong> - Currently unavailable
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <?php if ($product['short_description']): ?>
                    <div class="mb-4">
                        <p class="lead"><?= e(\App\Core\View::escape($product['short_description'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Add to Cart Form -->
                <?php if ($product['stock_quantity'] > 0): ?>
                    <div class="add-to-cart-section mb-4">
                        <form id="addToCartForm" method="POST" action="<?= e(\App\Core\View::url('cart/add')) ?>">
                            <?= \App\Core\CSRF::field() ?>
                            <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                            <input type="hidden" name="price" value="<?= e($product['price']) ?>">
                            
                        <div class="row align-items-end">
                                <!-- Quantity Selector -->
                            <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Quantity (<?= e(\App\Core\View::escape($product['unit'])) ?>)</label>
                                    <div class="input-group input-group-lg">
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                                id="decreaseQty"
                                            data-quantity-action="decrease">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number"
                                           class="form-control text-center"
                                           id="quantity"
                                           name="quantity"
                                           value="1"
                                               min="1"
                                           max="<?= e(min($product['max_quantity'], $product['stock_quantity'])) ?>"
                                               step="1"
                                               data-product-id="<?= e($product['id']) ?>"
                                               data-price="<?= e($product['price']) ?>">
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                                id="increaseQty"
                                            data-quantity-action="increase">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                        Min: 1 <?= e($product['unit']) ?>,
                                        Max: <?= e(min($product['max_quantity'], $product['stock_quantity'])) ?> <?= e($product['unit']) ?>
                                </small>
                            </div>

                                <!-- Price Display & Add Button -->
                            <div class="col-md-8 mb-3">
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Price:</span>
                                            <span class="h4 fw-bold text-success mb-0" id="totalPrice">
                                                <?= e(\App\Core\View::formatPrice($product['price'])) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?= e(\App\Core\View::formatPrice($product['price'])) ?> per <?= e($product['unit']) ?>
                                        </small>
                                    </div>
                                    <button type="submit" 
                                            class="btn btn-success btn-lg w-100"
                                            id="addToCartBtn"
                                            style="background-color: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Out of Stock</strong> - This product is currently unavailable.
                        </div>
                <?php endif; ?>
                
                <!-- Product Features -->
                <div class="product-features mb-4">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-success"></i>
                                <?= e(\App\Core\View::escape($product['processing_time'])) ?>
                            </small>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-weight me-2 text-success"></i>
                                Weight: <?= e($product['weight']) ?> kg (approx)
                            </small>
                        </div>
                        <?php if ($product['farm_source']): ?>
                            <div class="col-12 mb-2">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt me-2 text-success"></i>
                                    Source: <?= e(\App\Core\View::escape($product['farm_source'])) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h6 class="mb-2">Share this product:</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm" onclick="shareProduct('facebook')">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm" onclick="shareProduct('twitter')">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm" onclick="shareProduct('whatsapp')">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyProductLink()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-mdb-toggle="tab" data-mdb-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <?php if ($product['nutritional_info']): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nutrition-tab" data-mdb-toggle="tab" data-mdb-target="#nutrition" type="button" role="tab">
                            Nutrition Facts
                        </button>
                    </li>
                <?php endif; ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-mdb-toggle="tab" data-mdb-target="#reviews" type="button" role="tab">
                        Reviews (<?= e($rating['count']) ?>)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        <?php if ($product['description']): ?>
                            <p><?= e(nl2br(\App\Core\View::escape($product['description']))) ?></p>
                        <?php else: ?>
                            <p class="text-muted">No detailed description available.</p>
                        <?php endif; ?>
                        
                        <!-- Product Specifications -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6>Product Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>SKU:</td>
                                        <td><?= e(\App\Core\View::escape($product['sku'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Unit:</td>
                                        <td><?= e(\App\Core\View::escape($product['unit'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Weight:</td>
                                        <td><?= e($product['weight']) ?> kg (approx)</td>
                                    </tr>
                                    <?php if ($product['dimensions']): ?>
                                        <tr>
                                            <td>Dimensions:</td>
                                            <td><?= e(\App\Core\View::escape($product['dimensions'])) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Quality Assurance</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Farm Fresh Quality</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Hygienically Processed</li>
                                    <li><i class="fas fa-check text-success me-2"></i>No Antibiotics</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Cold Chain Maintained</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Same Day Delivery</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nutrition Tab -->
                <?php if ($product['nutritional_info']): ?>
                    <div class="tab-pane fade" id="nutrition" role="tabpanel">
                        <div class="p-4">
                            <h6>Nutritional Information (per 100g)</h6>
                            <?php 
                            $nutrition = json_decode($product['nutritional_info'], true);
                            if ($nutrition):
                            ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <?php foreach ($nutrition as $key => $value): ?>
                                                <tr>
                                                    <td><?= e(ucfirst(str_replace('_', ' ', $key))) ?>:</td>
                                                    <td><strong><?= e(\App\Core\View::escape($value)) ?></strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <!-- Review Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="display-4 fw-bold text-success"><?= e($rating['average']) ?></div>
                                <div class="rating-stars mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= e($i <= $rating['average'] ? 'text-warning' : 'text-muted') ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-muted"><?= e($rating['count']) ?> review<?= e($rating['count'] != 1 ? 's' : '') ?></p>
                            </div>
                        </div>
                        
                        <!-- Reviews List -->
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= e(\App\Core\View::escape($review['user_name'])) ?></h6>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= e($i <= $review['rating'] ? 'text-warning' : 'text-muted') ?> small"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted"><?= e(\App\Core\View::timeAgo($review['created_at'])) ?></small>
                                    </div>
                                    
                                    <?php if ($review['title']): ?>
                                        <h6><?= e(\App\Core\View::escape($review['title'])) ?></h6>
                                    <?php endif; ?>
                                    
                                    <?php if ($review['comment']): ?>
                                        <p class="mb-0"><?= e(nl2br(\App\Core\View::escape($review['comment']))) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No reviews yet. Be the first to review this product!</p>
                        <?php endif; ?>
                        
                        <!-- Add Review Form (for logged in users) -->
                        <?php if (\App\Core\Auth::check()): ?>
                            <div class="mt-4">
                                <h6>Write a Review</h6>
                                <form id="reviewForm" class="mt-3">
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating-input">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" name="rating" value="<?= e($i) ?>" id="star<?= e($i) ?>">
                                                <label for="star<?= e($i) ?>"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Title (Optional)</label>
                                        <input type="text" class="form-control" name="title" placeholder="Review title">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea class="form-control" name="comment" rows="3" placeholder="Share your experience with this product"></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-star me-2"></i>Submit Review
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Related Products</h3>
                <div class="row">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <?php 
                                $relatedProductModel = new \App\Models\Product();
                                $relatedProductModel->id = $relatedProduct['id'];
                                $relatedImage = $relatedProductModel->primaryImage();
                                ?>
                                
                                <div class="position-relative">
                                    <img src="<?= e($relatedImage ? \App\Core\View::asset($relatedImage['image_path']) : \App\Core\View::asset('images/placeholder-product.jpg')) ?>" 
                                         class="card-img-top" 
                                         alt="<?= e(\App\Core\View::escape($relatedProduct['name'])) ?>" 
                                         style="height: 200px; object-fit: cover;">
                                    
                                    <?php if ($relatedProduct['compare_price'] && $relatedProduct['compare_price'] > $relatedProduct['price']): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                            <?= e(round((($relatedProduct['compare_price'] - $relatedProduct['price']) / $relatedProduct['compare_price']) * 100)) ?>% OFF
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="<?= e(\App\Core\View::url('products/' . $relatedProduct['slug'])) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= e(\App\Core\View::escape($relatedProduct['name'])) ?>
                                        </a>
                                    </h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-bold"><?= e(\App\Core\View::formatPrice($relatedProduct['price'])) ?></span>
                                        <!-- Add to Cart quick action removed intentionally -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.product-gallery .main-image {
    position: relative;
}

.thumbnail-img {
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.thumbnail-img.active,
.thumbnail-img:hover {
    border-color: var(--bs-success);
}

.cursor-pointer {
    cursor: pointer;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    color: #ddd;
    font-size: 1.5rem;
    margin-right: 0.25rem;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}

.product-card {
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .main-image img {
        height: 300px !important;
    }
}
</style>

<script>
function changeMainImage(src, thumbnail) {
    document.getElementById('mainProductImage').src = src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-img').forEach(img => img.classList.remove('active'));
    thumbnail.classList.add('active');
}

function shareProduct(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${title} ${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        if (window.MeatMe) {
            window.MeatMe.showToast('success', 'Product link copied to clipboard!');
        }
    });
}

// Quantity controls and live price calculation for product detail page
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const totalPriceElement = document.getElementById('totalPrice');
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    
    if (!quantityInput || !totalPriceElement) return;
    
    const basePrice = parseFloat(quantityInput.getAttribute('data-price')) || 0;
    const min = parseFloat(quantityInput.getAttribute('min')) || 1;
    const max = parseFloat(quantityInput.getAttribute('max')) || Infinity;
    
    // Function to update total price
    function updateTotalPrice() {
        const quantity = parseFloat(quantityInput.value) || 1;
        const total = basePrice * quantity;
        
        // Format price (assuming formatPrice is available via View helper)
        totalPriceElement.textContent = 'Rs. ' + total.toFixed(2);
        
        // Update hidden input for form submission
        const hiddenQty = document.querySelector('input[name="quantity"]');
        if (hiddenQty) {
            hiddenQty.value = quantity;
        }
    }
    
    // Decrease button
    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentValue = parseFloat(quantityInput.value) || 1;
            if (currentValue > min) {
                quantityInput.value = currentValue - 1;
                updateTotalPrice();
            }
        });
    }
    
    // Increase button
    if (increaseBtn) {
        increaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentValue = parseFloat(quantityInput.value) || 1;
            if (currentValue < max) {
                quantityInput.value = currentValue + 1;
                updateTotalPrice();
            }
        });
    }
    
    // Quantity input change
    quantityInput.addEventListener('change', function() {
        let value = parseFloat(this.value) || 1;
        if (value < min) value = min;
        if (value > max) value = max;
        this.value = value;
        updateTotalPrice();
    });
    
    // Quantity input keyup for real-time updates
    quantityInput.addEventListener('input', function() {
        updateTotalPrice();
    });
    
    // Initial price calculation
    updateTotalPrice();
});

// Add to Cart form submission
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.getElementById('addToCartForm');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (addToCartForm && addToCartBtn) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const originalText = addToCartBtn.innerHTML;
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            const formData = new FormData(this);
            
            const csrfToken = document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value 
                || formData.get('csrf_token') 
                || window.MeatMe?.config?.csrfToken 
                || '';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                const refreshedToken = response.headers.get('X-CSRF-TOKEN');
                if (refreshedToken) {
                    const globalInput = document.querySelector('#csrf-token-form input[name="csrf_token"]');
                    if (globalInput) {
                        globalInput.value = refreshedToken;
                    }
                    if (window.MeatMe) {
                        window.MeatMe.config.csrfToken = refreshedToken;
                    }
                    window.csrfToken = refreshedToken;
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update cart count in navbar
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cartCount || 0;
                        cartCountElement.style.display = (data.cartCount > 0) ? 'inline' : 'none';
                    }
                    
                    // Show success message
                    if (window.MeatMe) {
                        window.MeatMe.showToast('success', data.message || 'Product added to cart!');
                    } else {
                        alert(data.message || 'Product added to cart!');
                    }
                    
                    // Reset button
                    addToCartBtn.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                    setTimeout(() => {
                        addToCartBtn.innerHTML = originalText;
                        addToCartBtn.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.MeatMe) {
                    window.MeatMe.showToast('error', error.message || 'Failed to add to cart');
                } else {
                    alert(error.message || 'Failed to add to cart');
                }
                addToCartBtn.innerHTML = originalText;
                addToCartBtn.disabled = false;
            });
        });
    }
});

// Review form submission
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('product_id', <?= e($product['id']) ?>);

    if (window.MeatMe) {
        window.MeatMe.makeRequest('POST', '/reviews', Object.fromEntries(formData))
        .then(response => {
            if (response.success) {
                window.MeatMe.showToast('success', response.message);
                this.reset();
                // Reload reviews section
                setTimeout(() => location.reload(), 1000);
            } else {
                window.MeatMe.showToast('error', response.message);
            }
        });
});

// Add to Cart functionality removed from product page

