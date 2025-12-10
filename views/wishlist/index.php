<!-- Page Header -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
                        <li class="breadcrumb-item active">Wishlist</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold mb-0">My Wishlist</h1>
                <p class="text-muted mb-0">Your favorite products saved for later</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex align-items-center justify-content-md-end">
                    <i class="fas fa-heart text-danger fa-2x me-3"></i>
                    <div>
                        <div class="fw-bold"><?= e(count($wishlistItems)) ?> Items</div>
                        <small class="text-muted">In your wishlist</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Wishlist Content -->
<div class="container py-5">
    <?php if (empty($wishlistItems)): ?>
        <!-- Empty Wishlist -->
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="py-5">
                    <i class="far fa-heart fa-5x text-muted mb-4"></i>
                    <h3 class="fw-bold mb-3">Your wishlist is empty</h3>
                    <p class="text-muted mb-4">
                        Start adding products you love to your wishlist. 
                        You can add items by clicking the heart icon on any product.
                    </p>
                    <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Wishlist Items -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Wishlist Items (<?= e(count($wishlistItems)) ?>)</h5>
                            <button class="btn btn-outline-danger btn-sm" onclick="clearWishlist()">
                                <i class="fas fa-trash me-1"></i>Clear All
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($wishlistItems as $item): ?>
                            <div class="wishlist-item border-bottom p-4" data-product-id="<?= e($item['product_id']) ?>">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <div class="position-relative">
                                            <?php if ($item['image']): ?>
                                                <img src="<?= e(\App\Core\View::asset($item['image'])) ?>" 
                                                     alt="<?= e(\App\Core\View::escape($item['name'])) ?>"
                                                     class="img-fluid rounded">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="height: 120px;">
                                                    <i class="fas fa-drumstick-bite fa-2x text-success"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Stock Status -->
                                            <?php if ($item['stock_quantity'] <= 0): ?>
                                                <span class="position-absolute top-0 start-0 badge bg-danger m-2">
                                                    Out of Stock
                                                </span>
                                            <?php elseif ($item['stock_quantity'] <= 5): ?>
                                                <span class="position-absolute top-0 start-0 badge bg-warning m-2">
                                                    Low Stock
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h5 class="fw-bold mb-2">
                                            <a href="<?= e(\App\Core\View::url('products/' . $item['slug'])) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= e(\App\Core\View::escape($item['name'])) ?>
                                            </a>
                                        </h5>
                                        
                                        <?php if ($item['short_description']): ?>
                                            <p class="text-muted mb-2"><?= e(\App\Core\View::escape($item['short_description'])) ?></p>
                                        <?php endif; ?>
                                        
                                        <!-- Price -->
                                        <div class="mb-2">
                                            <span class="h5 fw-bold text-success">
                                                Rs. <?= e(number_format($item['price'], 2)) ?>
                                            </span>
                                            <?php if ($item['compare_price'] && $item['compare_price'] > $item['price']): ?>
                                                <span class="text-muted text-decoration-line-through ms-2">
                                                    Rs. <?= e(number_format($item['compare_price'], 2)) ?>
                                                </span>
                                                <span class="badge bg-danger ms-2">
                                                    <?= e(round((($item['compare_price'] - $item['price']) / $item['compare_price']) * 100)) ?>% OFF
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Stock Info -->
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>
                                            <?= e($item['stock_quantity']) ?> <?= e(\App\Core\View::escape($item['unit'])) ?> available
                                        </small>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="col-md-3 text-md-end">
                                        <div class="d-grid gap-2">
                                            <!-- Add to Cart removed intentionally -->
                                            <button class="btn btn-outline-danger" 
                                                    data-action="remove-from-wishlist" 
                                                    data-product-id="<?= e($item['product_id']) ?>">
                                                <i class="fas fa-heart me-2"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Wishlist Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-heart me-2"></i>Wishlist Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Items:</span>
                            <strong><?= e(count($wishlistItems)) ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Available Items:</span>
                            <strong class="text-success">
                                <?= e(count(array_filter($wishlistItems, function($item) { return $item['stock_quantity'] > 0; }))) ?>
                            </strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span>Out of Stock:</span>
                            <strong class="text-danger">
                                <?= e(count(array_filter($wishlistItems, function($item) { return $item['stock_quantity'] <= 0; }))) ?>
                            </strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <!-- Add All to Cart removed intentionally -->
                            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-outline-success">
                                <i class="fas fa-plus me-2"></i>Continue Shopping
                            </a>
                        </div>
                        
                        <hr>
                        
                        <!-- Share Wishlist -->
                        <h6 class="fw-bold mb-3">Share Your Wishlist</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="shareWishlist('facebook')">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill" onclick="shareWishlist('twitter')">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill" onclick="shareWishlist('whatsapp')">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm flex-fill" onclick="copyWishlistLink()">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Recommendations -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>You Might Also Like
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Based on your wishlist items</p>
                        
                        <!-- Sample recommendations -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-drumstick-bite text-success d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Fresh Chicken Wings</h6>
                                <small class="text-success fw-bold">Rs. 380/kg</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-drumstick-bite text-success d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Chicken Thighs</h6>
                                <small class="text-success fw-bold">Rs. 420/kg</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Wishlist specific functions (Add to Cart removed)

function clearWishlist() {
    if (confirm('Are you sure you want to remove all items from your wishlist?')) {
        const removeButtons = document.querySelectorAll('[data-action="remove-from-wishlist"]');
        removeButtons.forEach(button => button.click());
    }
}

function shareWishlist(platform) {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out my wishlist at MeatMe - Fresh Chicken!');
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${text}%20${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyWishlistLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        App.showToast('success', 'Wishlist link copied to clipboard!');
    }).catch(() => {
        App.showToast('error', 'Failed to copy link');
    });
}

// Remove wishlist item animation
document.addEventListener('click', function(e) {
    if (e.target.closest('[data-action="remove-from-wishlist"]')) {
        const button = e.target.closest('[data-action="remove-from-wishlist"]');
        const wishlistItem = button.closest('.wishlist-item');
        
        // Add fade out animation
        wishlistItem.style.transition = 'opacity 0.3s ease';
        wishlistItem.style.opacity = '0.5';
        
        // Remove item after successful API call
        setTimeout(() => {
            if (wishlistItem.parentNode) {
                wishlistItem.remove();
                
                // Check if wishlist is now empty
                const remainingItems = document.querySelectorAll('.wishlist-item');
                if (remainingItems.length === 0) {
                    location.reload(); // Reload to show empty state
                }
            }
        }, 500);
    }
});
</script>
