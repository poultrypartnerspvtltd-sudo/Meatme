<!-- Category Products Page -->
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url()) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= e(\App\Core\View::url('products')) ?>">Products</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($category['name']) ?></li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="text-muted mb-0"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
            <p class="text-muted mt-2">
                Showing <?= e(count($products)) ?> of <?= e($totalProducts) ?> products
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-outline-primary">
                <i class="fas fa-th-large me-2"></i>View All Products
            </a>
        </div>
    </div>

    <!-- Subcategories (if any) -->
    <?php if (!empty($subcategories)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3">Subcategories</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($subcategories as $subcategory): ?>
                    <a href="<?= e(\App\Core\View::url('category/' . $subcategory['slug'])) ?>"
                       class="btn btn-outline-secondary btn-sm">
                        <?= htmlspecialchars($subcategory['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sort Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label class="form-label fw-bold me-2 mb-0">Sort by:</label>
                    <select class="form-select d-inline-block w-auto" onchange="updateSort(this.value)">
                        <option value="name" <?= e(($sortBy ?? '') === 'name' ? 'selected' : '') ?>>Name (A-Z)</option>
                        <option value="price_low" <?= e(($sortBy ?? '') === 'price_low' ? 'selected' : '') ?>>Price (Low to High)</option>
                        <option value="price_high" <?= e(($sortBy ?? '') === 'price_high' ? 'selected' : '') ?>>Price (High to Low)</option>
                        <option value="newest" <?= e(($sortBy ?? '') === 'newest' ? 'selected' : '') ?>>Newest First</option>
                        <option value="popular" <?= e(($sortBy ?? '') === 'popular' ? 'selected' : '') ?>>Most Popular</option>
                    </select>
                </div>
                <div>
                    <span class="text-muted">
                        <?= e($totalProducts) ?> product<?= e($totalProducts !== 1 ? 's' : '') ?> in this category
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <!-- No Products Found -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>No products found</h4>
            <p class="text-muted">This category doesn't have any products yet.</p>
            <div class="mt-4">
                <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success me-2">
                    <i class="fas fa-th-large me-2"></i>Browse All Products
                </a>
                <a href="<?= e(\App\Core\View::url()) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
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
                        ?>

                        <div class="position-relative">
                            <img src="<?= e($image ? \App\Core\View::asset($image['image_path']) : \App\Core\View::asset('images/Alivechicks.png')) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="height: 250px; object-fit: cover;">

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
                                <?= htmlspecialchars($product['freshness_indicator'] ?? 'Fresh') ?>
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
                            <!-- Product Name -->
                            <h5 class="card-title mb-2">
                                <a href="<?= e(\App\Core\View::url('products/' . $product['slug'])) ?>"
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h5>

                            <!-- Description -->
                            <p class="card-text text-muted flex-grow-1 small">
                                <?= e(\App\Core\View::truncate($product['short_description'] ?? $product['description'], 80)) ?>
                            </p>

                            <!-- Stock Status -->
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <small class="text-success mb-2">
                                    <i class="fas fa-check-circle me-1"></i>In Stock (<?= e($product['stock_quantity']) ?> <?= htmlspecialchars($product['unit']) ?>)
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
                                <small class="text-muted">per <?= htmlspecialchars($product['unit']) ?></small>
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
            <nav aria-label="Category products pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous -->
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= e($currentPage - 1) ?><?= e($sortBy && $sortBy !== 'name' ? '&sort=' . $sortBy : '') ?>">
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
                            <a class="page-link" href="?page=<?= e($i) ?><?= e($sortBy && $sortBy !== 'name' ? '&sort=' . $sortBy : '') ?>">
                                <?= e($i) ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= e($currentPage + 1) ?><?= e($sortBy && $sortBy !== 'name' ? '&sort=' . $sortBy : '') ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.page-link {
    color: var(--bs-success);
}

.page-item.active .page-link {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}
</style>

<script>
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    // Reset to page 1 when sorting changes
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
// Add to Cart functionality removed on category page
</script>
