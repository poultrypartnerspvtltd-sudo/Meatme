<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Get product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$productId = intval($_GET['id']);

// Fetch product details
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $_SESSION['error'] = 'Product not found';
        header('Location: products.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error fetching product: ' . $e->getMessage();
    header('Location: products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $compare_price = !empty($_POST['compare_price']) ? floatval($_POST['compare_price']) : null;
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name)) {
            throw new Exception('Product name is required');
        }
        
        if ($price <= 0) {
            throw new Exception('Price must be greater than 0');
        }
        
        // Handle image upload
        $image_path = null;
        $updateImage = false;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/products/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid image format. Only JPG, PNG, and WebP are allowed.');
            }
            
            // Generate unique filename
            $fileName = uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                // Store relative path without 'assets/' prefix for asset helper
                $image_path = 'uploads/products/' . $fileName;
                $updateImage = true;
                
                // Delete old image if it exists
                $oldImagePath = '../' . $product['image_path'];
                if ($product['image_path'] && file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            } else {
                throw new Exception('Failed to upload image');
            }
        }
        
        // Generate slug from name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Update product in database
        if ($updateImage) {
            $stmt = $pdo->prepare("
                UPDATE products SET 
                    name = ?, slug = ?, description = ?, price = ?, compare_price = ?, 
                    category_id = ?, stock_quantity = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $name, $slug, $description, $price, $compare_price,
                $category_id, $stock_quantity, $is_active, $productId
            ]);
            
            // Update product image if new image was uploaded
            try {
                // Delete existing product images
                $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);
                
                // Insert new image
                $stmt = $pdo->prepare("
                    INSERT INTO product_images (product_id, image_path, is_primary, created_at) 
                    VALUES (?, ?, 1, NOW())
                ");
                $stmt->execute([$productId, $image_path]);
            } catch (PDOException $e) {
                // If product_images table doesn't exist, just continue
                error_log("Product images table error: " . $e->getMessage());
            }
        } else {
            $stmt = $pdo->prepare("
                UPDATE products SET 
                    name = ?, slug = ?, description = ?, price = ?, compare_price = ?, 
                    category_id = ?, stock_quantity = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $name, $slug, $description, $price, $compare_price,
                $category_id, $stock_quantity, $is_active, $productId
            ]);
        }
        
        $success = 'Product updated successfully!';
        
        // Refresh product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Redirect to products page after 2 seconds
        header("refresh:2;url=products.php");
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch categories for dropdown
$categories = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Categories table might not exist, continue without categories
    error_log("Categories fetch error: " . $e->getMessage());
}

// Get current product image
$currentImage = null;
try {
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_primary = 1");
    $stmt->execute([$productId]);
    $imageResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentImage = $imageResult ? $imageResult['image_path'] : null;
} catch (PDOException $e) {
    // If product_images table doesn't exist, just continue
    error_log("Product images fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - MeatMe Admin</title>
    
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.phtml'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Product: <?= htmlspecialchars($product['name']) ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
                        </a>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Edit Product Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>Edit Product Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="edit_product.php?id=<?= e($productId) ?>" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Product Name *</label>
                                            <input type="text" class="form-control" name="name" 
                                                   value="<?= htmlspecialchars($product['name']) ?>" 
                                                   placeholder="Enter product name" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category_id">
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= e($category['id']) ?>" 
                                                            <?= e(($product['category_id'] == $category['id']) ? 'selected' : '') ?>>
                                                        <?= htmlspecialchars($category['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control product-description-editor" name="description" rows="6" 
                                                  placeholder="Enter detailed product description with formatting"><?= htmlspecialchars($product['description']) ?></textarea>
                                        <small class="text-muted">Use the rich text editor above for formatting, links, and images</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Price (Rs.) *</label>
                                            <input type="number" class="form-control" name="price" 
                                                   value="<?= e($product['price']) ?>" 
                                                   step="0.01" min="0" placeholder="0.00" required>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Compare Price (Rs.)</label>
                                            <input type="number" class="form-control" name="compare_price" 
                                                   value="<?= e($product['compare_price']) ?>" 
                                                   step="0.01" min="0" placeholder="0.00">
                                            <small class="text-muted">Original price for discount display</small>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stock Quantity</label>
                                            <input type="number" class="form-control" name="stock_quantity" 
                                                   value="<?= e($product['stock_quantity']) ?>" 
                                                   min="0" placeholder="0">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <?php if ($currentImage): ?>
                                            <div class="mb-2">
                                                <img src="../<?= htmlspecialchars($currentImage) ?>" 
                                                     alt="Current product image" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 150px; max-height: 150px;">
                                                <small class="d-block text-muted">Current image</small>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current image. Supported formats: JPG, PNG, WebP. Max size: 5MB</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" 
                                                   id="is_active" <?= e($product['is_active'] ? 'checked' : '') ?>>
                                            <label class="form-check-label" for="is_active">
                                                Active (visible to customers)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="products.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Product
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Product Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <strong>Product ID:</strong> #<?= e($product['id']) ?>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Created:</strong> <?= e(date('M j, Y', strtotime($product['created_at']))) ?>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Last Updated:</strong> 
                                        <?= e($product['updated_at'] ? date('M j, Y', strtotime($product['updated_at'])) : 'Never') ?>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Status:</strong> 
                                        <span class="badge <?= e($product['is_active'] ? 'bg-success' : 'bg-secondary') ?>">
                                            <?= e($product['is_active'] ? 'Active' : 'Inactive') ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="delete_product.php?id=<?= e($productId) ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                        <i class="fas fa-trash me-2"></i>Delete Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- MDBootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

    <!-- TinyMCE WYSIWYG Editor -->
    <script src="https://cdn.tiny.cloud/1/xa7j2idk76099ylk6p43yo2ds5ssbzneba9liesymd94ygcq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
    // Initialize TinyMCE for product descriptions
    tinymce.init({
        selector: '.product-description-editor',
        height: 400,
        skin: 'oxide', // Modern theme
        content_css: 'default', // Compatible with light/dark themes
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
        ],
        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | ' +
                 'alignleft aligncenter alignright alignjustify | ' +
                 'bullist numlist outdent indent | removeformat | ' +
                 'forecolor backcolor | link image media | emoticons charmap | ' +
                 'fullscreen preview code | help',
        toolbar_mode: 'sliding',
        menubar: 'edit view insert format tools table help',
        statusbar: true,
        branding: false,
        promotion: false,
        image_advtab: true,
        image_title: true,
        automatic_uploads: false,
        file_picker_types: 'image',
        paste_data_images: true,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; line-height: 1.6; }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
    </script>
</body>
</html>
