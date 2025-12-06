<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = 'Products Management';

// Fetch products with images
$products = [];
$query = "
    SELECT p.*, c.name as category_name,
           pi.image_path as primary_image
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    ORDER BY p.created_at DESC
";
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    error_log("Products fetch error: " . $mysqli->error);
}

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Products Management</h2>
            <p class="text-muted mb-0">Manage your product catalog</p>
        </div>
        <div class="col-auto">
            <a href="add_product.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Products (<?= e(count($products)) ?>)</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search products...">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p class="text-muted">Start by adding your first product to the catalog.</p>
                <a href="#" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['primary_image']): ?>
                                        <img src="../<?= htmlspecialchars($product['primary_image']) ?>" 
                                             alt="Product image" 
                                             class="img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($product['name']) ?></h6>
                                        <small class="text-muted">ID: #<?= e($product['id']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>Rs. <?= e(number_format($product['price'], 2)) ?></strong>
                                    <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                        <br><small class="text-muted text-decoration-line-through">
                                            Rs. <?= e(number_format($product['compare_price'], 2)) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= e($product['stock_quantity'] > 10 ? 'bg-success' : ($product['stock_quantity'] > 0 ? 'bg-warning' : 'bg-danger')) ?>">
                                        <?= e($product['stock_quantity']) ?> <?= htmlspecialchars($product['unit']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= e($product['is_active'] ? 'bg-success' : 'bg-secondary') ?>">
                                        <?= e($product['is_active'] ? 'Active' : 'Inactive') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_product.php?id=<?= e($product['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?= e($product['id']) ?>" 
                                           class="btn btn-outline-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
