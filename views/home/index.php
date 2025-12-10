<!-- Hero Section (if exists) -->
<?php if (!empty($homepageContent['hero'])): ?>
<section class="hero-section bg-success text-white py-5 <?= htmlspecialchars($homepageContent['hero']['css_classes'] ?? '') ?>" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <img src="/assets/images/logo-meatme.jpeg" 
     alt="Meat Me Logo" 
     class="img-fluid mb-4" 
     style="max-width:200px; display:block; margin:0 auto;">

                <h1 class="display-4 fw-bold mb-4"><?= htmlspecialchars($homepageContent['hero']['title'] ?? 'Fresh Chicken, Straight from Our Farm') ?></h1>
                <div class="lead mb-4"><?= e($homepageContent['hero']['content'] ?? 'Experience the finest quality chicken meat, hygienically processed and delivered fresh to your doorstep. Farm-to-table freshness guaranteed.') ?></div>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!empty($homepageContent['hero']['button_text'])): ?>
                        <a href="<?= e(\App\Core\View::url($homepageContent['hero']['button_link'] ?? 'products')) ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i><?= htmlspecialchars($homepageContent['hero']['button_text']) ?>
                        </a>
                    <?php else: ?>
                        <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Explore Fresh Cuts
                        </a>
                    <?php endif; ?>
                    <a href="<?= e(\App\Core\View::url('about')) ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Learn More
                    </a>
                </div>

                <!-- Key Features -->
                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-leaf fa-2x me-3 text-warning"></i>
                            <div>
                                <h6 class="mb-1">Farm Fresh</h6>
                                <small>Direct from farm</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt fa-2x me-3 text-warning"></i>
                            <div>
                                <h6 class="mb-1">Hygienically Processed</h6>
                                <small>Safe & clean</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-truck fa-2x me-3 text-warning"></i>
                            <div>
                                <h6 class="mb-1">Same-Day Delivery</h6>
                                <small>Fresh to your door</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <?php if (!empty($homepageContent['hero']['image'])): ?>
                    <img src="<?= e(\App\Core\View::asset('uploads/content/' . $homepageContent['hero']['image'])) ?>" alt="<?= htmlspecialchars($homepageContent['hero']['title'] ?? 'Fresh Chicken') ?>" class="img-fluid rounded shadow-lg" style="max-height: 500px; object-fit: cover;">
                <?php else: ?>
                    <img src="<?= e(\App\Core\View::asset('images/hero-chicken.jpg')) ?>" alt="Fresh Chicken" class="img-fluid rounded shadow-lg" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Dynamic Content Sections -->
<?php foreach ($homepageContent as $sectionName => $section): ?>
    <?php if ($sectionName !== 'hero' && $section['is_active']): ?>
        <section class="py-5 <?= e($sectionName === 'testimonials' || $sectionName === 'categories' ? 'bg-light' : '') ?> <?= htmlspecialchars($section['css_classes'] ?? '') ?>" id="section-<?= htmlspecialchars($sectionName) ?>">

            <div class="container">
                <?php if ($sectionName === 'featured_products'): ?>
                    <!-- Featured Products Section -->
                    <div class="text-center mb-5">
                        <h2 class="fw-bold"><?= htmlspecialchars($section['title']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($section['content']) ?></p>
                    </div>

                    <div class="row">
                        <!-- Main Content - Products -->
                        <div class="col-lg-9">
                            <div class="row">
                        <?php foreach ($featuredProducts as $product): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 product-card">
                                    <?php
                                    $productModel = new \App\Models\Product();
                                    $productModel->id = $product['id'];
                                    $image = $productModel->primaryImage();
                                    ?>

                                    <div class="position-relative">
                                        <img src="<?= e($image ? \App\Core\View::asset($image['image_path']) : \App\Core\View::asset('assets/images/placeholder-product.jpg')) ?>"
                                             class="card-img-top" alt="<?= e(\App\Core\View::escape($product['name'])) ?>" style="height: 250px; object-fit: cover;">

                                        <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                                <?= e(round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100)) ?>% OFF
                                            </span>
                                        <?php endif; ?>

                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                            <?= e(\App\Core\View::escape($product['freshness_indicator'])) ?>
                                        </span>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= e(\App\Core\View::escape($product['name'])) ?></h5>
                                        <p class="card-text text-muted flex-grow-1"><?= e(\App\Core\View::truncate($product['short_description'], 80)) ?></p>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="h5 text-success mb-0"><?= e(\App\Core\View::formatPrice($product['price'])) ?></span>
                                                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                                    <small class="text-muted text-decoration-line-through ms-2"><?= e(\App\Core\View::formatPrice($product['compare_price'])) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">per <?= e(\App\Core\View::escape($product['unit'])) ?></small>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="<?= e(\App\Core\View::url('products/' . $product['slug'])) ?>" class="btn btn-outline-success flex-grow-1">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <!-- Add to Cart removed intentionally -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success btn-lg">
                                    <i class="fas fa-th-large me-2"></i>View All Products
                                </a>
                            </div>
                        </div>
                    </div>

                <?php elseif ($sectionName === 'categories'): ?>
                    <!-- Categories Section -->
                    <div class="text-center mb-5">
                        <h2 class="fw-bold"><?= htmlspecialchars($section['title']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($section['content']) ?></p>
                    </div>

                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                            <div class="col-lg-2 col-md-4 col-6 mb-4">
                                <a href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>" class="text-decoration-none">
                                    <div class="card text-center border-0 shadow-sm h-100 category-card">
                                        <div class="card-body p-3">
                                            <div class="mb-3">
                                                <?php
                                                $icons = [
                                                    'whole-chicken' => 'fas fa-drumstick-bite',
                                                    'chicken-breast' => 'fas fa-heart',
                                                    'chicken-legs' => 'fas fa-bone',
                                                    'chicken-wings' => 'fas fa-feather-alt',
                                                    'boneless-cuts' => 'fas fa-cut',
                                                    'marinated-chicken' => 'fas fa-pepper-hot'
                                                ];
                                                $icon = $icons[$category['slug']] ?? 'fas fa-drumstick-bite';
                                                ?>
                                                <i class="<?= e($icon) ?> fa-3x text-success"></i>
                                            </div>
                                            <h6 class="card-title mb-2"><?= e(\App\Core\View::escape($category['name'])) ?></h6>
                                            <small class="text-muted"><?= e($category['product_count']) ?> products</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($sectionName === 'testimonials'): ?>
                    <!-- Testimonials Section -->
                    <div class="text-center mb-5">
                        <h2 class="fw-bold"><?= htmlspecialchars($section['title']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($section['content']) ?></p>
                    </div>

                    <div class="row">
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= e($i <= $testimonial['rating'] ? 'text-warning' : 'text-muted') ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="card-text">"<?= e(\App\Core\View::escape($testimonial['comment'])) ?>"</p>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= e(\App\Core\View::escape($testimonial['name'])) ?></h6>
                                                <small class="text-muted"><?= e(\App\Core\View::escape($testimonial['location'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($sectionName === 'cta'): ?>
                    <!-- Call-to-Action Section -->
                    <div class="bg-success text-white">
                        <div class="container text-center py-5">
                            <h2 class="fw-bold mb-3"><?= htmlspecialchars($section['title']) ?></h2>
                            <div class="lead mb-4"><?= e($section['content']) ?></div>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <?php if (!empty($section['button_text'])): ?>
                                    <a href="<?= e(\App\Core\View::url($section['button_link'] ?? 'products')) ?>" class="btn btn-light btn-lg">
                                        <i class="fas fa-shopping-bag me-2"></i><?= htmlspecialchars($section['button_text']) ?>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= e(\App\Core\View::url('contact')) ?>" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-phone me-2"></i>Contact Us
                                </a>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Generic Content Section -->
                    <div class="container">
                        <div class="row align-items-center">
                            <?php if (!empty($section['image'])): ?>
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <img src="<?= e(\App\Core\View::asset('uploads/content/' . $section['image'])) ?>"
                                         alt="<?= htmlspecialchars($section['title']) ?>"
                                         class="img-fluid rounded shadow-lg"
                                         style="max-height: 400px; object-fit: cover;">
                                </div>
                                <div class="col-lg-6">
                                    <h2 class="fw-bold mb-4"><?= htmlspecialchars($section['title']) ?></h2>
                                    <div class="mb-4"><?= e($section['content']) ?></div>
                                    <?php if (!empty($section['button_text'])): ?>
                                        <a href="<?= e(\App\Core\View::url($section['button_link'] ?? '#')) ?>" class="btn btn-success btn-lg">
                                            <i class="fas fa-arrow-right me-2"></i><?= htmlspecialchars($section['button_text']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="col-12 text-center">
                                    <h2 class="fw-bold mb-4"><?= htmlspecialchars($section['title']) ?></h2>
                                    <div class="mb-4 lead"><?= e($section['content']) ?></div>
                                    <?php if (!empty($section['button_text'])): ?>
                                        <a href="<?= e(\App\Core\View::url($section['button_link'] ?? '#')) ?>" class="btn btn-success btn-lg">
                                            <i class="fas fa-arrow-right me-2"></i><?= htmlspecialchars($section['button_text']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Static Sections (fallback if not in CMS) -->
<?php if (empty($homepageContent['featured_products'])): ?>
<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Our most popular and freshest chicken cuts</p>
        </div>

        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        <?php
                        $productModel = new \App\Models\Product();
                        $productModel->id = $product['id'];
                        $image = $productModel->primaryImage();
                        ?>

                        <div class="position-relative">
                            <img src="<?= e($image ? \App\Core\View::asset($image['image_path']) : \App\Core\View::asset('assets/images/placeholder-product.jpg')) ?>"
                                 class="card-img-top" alt="<?= e(\App\Core\View::escape($product['name'])) ?>" style="height: 250px; object-fit: cover;">

                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                    <?= e(round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100)) ?>% OFF
                                </span>
                            <?php endif; ?>

                            <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                <?= e(\App\Core\View::escape($product['freshness_indicator'])) ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= e(\App\Core\View::escape($product['name'])) ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?= e(\App\Core\View::truncate($product['short_description'], 80)) ?></p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="h5 text-success mb-0"><?= e(\App\Core\View::formatPrice($product['price'])) ?></span>
                                    <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                        <small class="text-muted text-decoration-line-through ms-2"><?= e(\App\Core\View::formatPrice($product['compare_price'])) ?></small>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">per <?= e(\App\Core\View::escape($product['unit'])) ?></small>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= e(\App\Core\View::url('products/' . $product['slug'])) ?>" class="btn btn-outline-success flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <!-- Add to Cart removed intentionally -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-success btn-lg">
                <i class="fas fa-th-large me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (empty($homepageContent['categories'])): ?>
<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Shop by Category</h2>
            <p class="text-muted">Choose from our wide range of fresh chicken cuts</p>
        </div>

        <div class="row">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?= e(\App\Core\View::url('category/' . $category['slug'])) ?>" class="text-decoration-none">
                        <div class="card text-center border-0 shadow-sm h-100 category-card">
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <?php
                                    $icons = [
                                        'whole-chicken' => 'fas fa-drumstick-bite',
                                        'chicken-breast' => 'fas fa-heart',
                                        'chicken-legs' => 'fas fa-bone',
                                        'chicken-wings' => 'fas fa-feather-alt',
                                        'boneless-cuts' => 'fas fa-cut',
                                        'marinated-chicken' => 'fas fa-pepper-hot'
                                    ];
                                    $icon = $icons[$category['slug']] ?? 'fas fa-drumstick-bite';
                                    ?>
                                    <i class="<?= e($icon) ?> fa-3x text-success"></i>
                                </div>
                                <h6 class="card-title mb-2"><?= e(\App\Core\View::escape($category['name'])) ?></h6>
                                <small class="text-muted"><?= e($category['product_count']) ?> products</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (empty($homepageContent['about'])): ?>
<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose MeatMe?</h2>
            <p class="text-muted">We're committed to delivering the freshest, highest quality chicken</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-leaf fa-2x text-white"></i>
                    </div>
                    <h5>Farm Fresh</h5>
                    <p class="text-muted">Direct from our partner farms, ensuring maximum freshness and quality in every cut.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x text-white"></i>
                    </div>
                    <h5>Hygienically Processed</h5>
                    <p class="text-muted">State-of-the-art processing facilities with strict hygiene and safety standards.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-route fa-2x text-white"></i>
                    </div>
                    <h5>Full Traceability</h5>
                    <p class="text-muted">Know exactly where your chicken comes from with our complete farm-to-table tracking.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck fa-2x text-white"></i>
                    </div>
                    <h5>Same-Day Delivery</h5>
                    <p class="text-muted">Order before 2 PM and get fresh chicken delivered to your doorstep the same day.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (empty($homepageContent['testimonials'])): ?>
<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Customers Say</h2>
            <p class="text-muted">Real reviews from satisfied customers</p>
        </div>

        <div class="row">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= e($i <= $testimonial['rating'] ? 'text-warning' : 'text-muted') ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="card-text">"<?= e(\App\Core\View::escape($testimonial['comment'])) ?>"</p>
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= e(\App\Core\View::escape($testimonial['name'])) ?></h6>
                                    <small class="text-muted"><?= e(\App\Core\View::escape($testimonial['location'])) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (empty($homepageContent['cta'])): ?>
<!-- CTA Section -->
<section class="py-5 bg-success text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Experience Farm-Fresh Chicken?</h2>
        <p class="lead mb-4">Join thousands of satisfied customers who trust MeatMe for their daily chicken needs</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-light btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
            <a href="<?= e(\App\Core\View::url('contact')) ?>" class="btn btn-outline-light btn-lg">
                <i class="fas fa-phone me-2"></i>Contact Us
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.hero-section {
    min-height: 70vh;
}

.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}

.whatsapp-float {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 100px;
    right: 20px;
    background-color: #25d366;
    color: white;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.whatsapp-float:hover {
    background-color: #128c7e;
    color: white;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .whatsapp-float {
        bottom: 80px;
        right: 15px;
        width: 50px;
        height: 50px;
        font-size: 24px;
    }
}
</style>

<script>
function showToast(type, message) {
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
}
</script>
