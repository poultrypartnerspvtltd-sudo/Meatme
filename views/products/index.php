<!-- Products Listing Page -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2"><?= e(\App\Core\View::escape($title)) ?></h1>
            <p class="text-muted mb-0">
                <?php if ($selectedCategory): ?>
                    Showing products in "<?= e(\App\Core\View::escape($selectedCategory['name'])) ?>" category
                <?php else: ?>
                    Discover our fresh, farm-to-table chicken products
                <?php endif; ?>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="d-flex flex-column align-items-end gap-2">
                <p class="text-muted mb-0">
                    Showing <?= e(count($products)) ?> of <?= e($totalProducts) ?> products
                </p>
                <a href="<?= e(\App\Core\View::url('orders')) ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-box me-1"></i>View My Orders
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Search Products</label>
                        <form method="GET" action="<?= e(\App\Core\View::url('products')) ?>">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search..." 
                                       value="<?= e(\App\Core\View::escape($search ?? '')) ?>">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Categories</label>
                        <div class="list-group list-group-flush">
                            <a href="<?= e(\App\Core\View::url('products')) ?>" 
                               class="list-group-item list-group-item-action border-0 px-0 <?= e(!$selectedCategory ? 'active' : '') ?>">
                                All Categories
                                <span class="badge bg-light text-dark float-end"><?= e($totalProducts) ?></span>
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="<?= e(\App\Core\View::url('products?category=' . $category['id'])) ?>" 
                                   class="list-group-item list-group-item-action border-0 px-0 <?= e($selectedCategory && $selectedCategory['id'] == $category['id'] ? 'active' : '') ?>">
                                    <?= e(\App\Core\View::escape($category['name'])) ?>
                                    <span class="badge bg-light text-dark float-end"><?= e($category['product_count'] ?? 0) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Sort By</label>
                        <select class="form-select" onchange="updateSort(this.value)">
                            <option value="name" <?= e(($sortBy ?? '') === 'name' ? 'selected' : '') ?>>Name (A-Z)</option>
                            <option value="price_low" <?= e(($sortBy ?? '') === 'price_low' ? 'selected' : '') ?>>Price (Low to High)</option>
                            <option value="price_high" <?= e(($sortBy ?? '') === 'price_high' ? 'selected' : '') ?>>Price (High to Low)</option>
                            <option value="newest" <?= e(($sortBy ?? '') === 'newest' ? 'selected' : '') ?>>Newest First</option>
                            <option value="popular" <?= e(($sortBy ?? '') === 'popular' ? 'selected' : '') ?>>Most Popular</option>
                        </select>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-redo me-1"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Featured Categories -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-star me-2"></i>Popular Categories</h6>
                </div>
                <div class="card-body p-2">
                    <?php 
                    $popularCategories = array_slice($categories, 0, 4);
                    foreach ($popularCategories as $category): 
                    ?>
                        <a href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>" 
                           class="d-block p-2 text-decoration-none text-dark rounded mb-1 hover-bg-light">
                            <small class="fw-bold"><?= e(\App\Core\View::escape($category['name'])) ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <!-- No Products Found -->
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse all categories.</p>
                    <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success">
                        <i class="fas fa-th-large me-2"></i>View All Products
                    </a>
                </div>
            <?php else: ?>
                <!-- Products Grid -->
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <?php
                                $productModel = new \App\Models\Product();
                                $productModel->id = $product['id'];
                                $image = $productModel->primaryImage();

                                // Determine image URL: prefer DB image, else try to find a matching file in assets/images by product name,
                                // fallback to a generic placeholder.
                                $imageUrl = '';
                                if (!empty($image) && !empty($image['image_path'])) {
                                    $imageUrl = \App\Core\View::asset($image['image_path']);
                                } else {
                                    // Server-side search in assets/images for a filename that matches product name
                                    $imagesDir = __DIR__ . '/../../assets/images/';
                                    $found = null;
                                    $productKey = preg_replace('/[^a-z0-9]/', '', strtolower($product['name']));
                                    $extensions = ['png','jpg','jpeg','webp'];
                                    if (is_dir($imagesDir)) {
                                        $files = scandir($imagesDir);
                                        foreach ($files as $f) {
                                            if (in_array($f, ['.','..'])) continue;
                                            $nameNormalized = strtolower(preg_replace('/[^a-z0-9]/', '', $f));
                                            foreach ($extensions as $ext) {
                                                if (str_ends_with(strtolower($f), '.' . $ext) && strpos($nameNormalized, $productKey) !== false) {
                                                    $found = $f;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }

                                    if ($found) {
                                        $imageUrl = \App\Core\View::asset('images/' . $found);
                                    } else {
                                        // generic fallback
                                        $imageUrl = \App\Core\View::asset('images/Alivechickens.png');
                                    }
                                }
                                ?>

                                <div class="position-relative">
                                    <img src="<?= e($imageUrl) ?>" class="card-img-top" alt="<?= e(\App\Core\View::escape($product['name'])) ?>" style="height: 250px; object-fit: cover;">
                                    
                                    <!-- Badges -->
                                    <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                            <?= e(round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100)) ?>% OFF
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-star"></i> Featured
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="badge bg-success position-absolute bottom-0 start-0 m-2">
                                        <?= e(\App\Core\View::escape($product['freshness_indicator'] ?? 'Fresh')) ?>
                                    </span>
                                    
                                    <!-- Quick Actions -->
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <button class="btn btn-light btn-sm rounded-circle me-1" 
                                                data-action="add-to-wishlist" 
                                                data-product-id="<?= e($product['id']) ?>"
                                                title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Category -->
                                    <small class="text-muted mb-1"><?= e(\App\Core\View::escape($product['category_name'] ?? '')) ?></small>
                                    
                                    <!-- Product Name -->
                                    <h5 class="card-title mb-2">
                                        <a href="<?= e(\App\Core\View::url('products/' . $product['slug'])) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= e(\App\Core\View::escape($product['name'])) ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Description -->
                                    <p class="card-text text-muted flex-grow-1 small">
                                        <?= e(\App\Core\View::truncate($product['short_description'] ?? $product['description'], 80)) ?>
                                    </p>
                                    
                                    <!-- Stock Status -->
                                    <?php if ($product['stock_quantity'] > 0): ?>
                                        <small class="text-success mb-2">
                                            <i class="fas fa-check-circle me-1"></i>In Stock (<?= e($product['stock_quantity']) ?> <?= e($product['unit']) ?>)
                                        </small>
                                    <?php else: ?>
                                        <small class="text-danger mb-2">
                                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                                        </small>
                                    <?php endif; ?>
                                    
                                    <!-- Price -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="h5 text-success mb-0"><?= e(\App\Core\View::formatPrice($product['price'])) ?></span>
                                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                                <small class="text-muted text-decoration-line-through ms-2">
                                                    <?= e(\App\Core\View::formatPrice($product['compare_price'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">per <?= e(\App\Core\View::escape($product['unit'])) ?></small>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <a href="<?= e(\App\Core\View::url('products/' . $product['slug'])) ?>" 
                                           class="btn btn-outline-success flex-grow-1">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        <!-- Add to Cart removed intentionally -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Products pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= e($currentPage - 1) ?><?= e($search ? '&search=' . urlencode($search) : '') ?><?= e($selectedCategory ? '&category=' . $selectedCategory['id'] : '') ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php 
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): 
                            ?>
                                <li class="page-item <?= e($i == $currentPage ? 'active' : '') ?>">
                                    <a class="page-link" href="?page=<?= e($i) ?><?= e($search ? '&search=' . urlencode($search) : '') ?><?= e($selectedCategory ? '&category=' . $selectedCategory['id'] : '') ?>">
                                        <?= e($i) ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= e($currentPage + 1) ?><?= e($search ? '&search=' . urlencode($search) : '') ?><?= e($selectedCategory ? '&category=' . $selectedCategory['id'] : '') ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

.list-group-item.active {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}

.page-link {
    color: var(--bs-success);
}

.page-item.active .page-link {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}

@media (max-width: 991px) {
    .col-lg-3 {
        order: 2;
    }
    
    .col-lg-9 {
        order: 1;
    }
}
</style>

<script>
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}
// Add to Cart functionality removed on listing page
</script>
