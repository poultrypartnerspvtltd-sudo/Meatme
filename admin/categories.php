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

$page_title = 'Categories Management';

// Function to generate unique slug
function generateSlug($name, $mysqli, $excludeId = null) {
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $slug = $baseSlug;
    $counter = 1;

    do {
        $query = "SELECT COUNT(*) as count FROM categories WHERE slug = ?";
        if ($excludeId !== null) {
            $query .= " AND id != ?";
        }
        
        $stmt = $mysqli->prepare($query);
        if ($excludeId !== null) {
            $stmt->bind_param("si", $slug, $excludeId);
        } else {
            $stmt->bind_param("s", $slug);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $exists = $row['count'] ?? 0;

        if ($exists > 0) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    } while ($exists > 0);

    return $slug;
}

// Handle form submissions
$error = '';
$success = '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $name = trim($_POST['category_name']);
    $description = trim($_POST['category_description'] ?? '');

    if (!empty($name)) {
        global $mysqli;
        // Check if category name already exists
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Category name already exists.";
        } else {
            $slug = generateSlug($name, $mysqli);
            $stmt = $mysqli->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $slug, $description);
            if ($stmt->execute()) {
                $success = "Category added successfully!";
                // Redirect to prevent form resubmission
                header("Location: categories.php?success=1");
                exit;
            } else {
                $error = "Database error: " . $mysqli->error;
            }
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Handle Edit Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category_name'])) {
    $id = (int)$_POST['edit_id'];
    $name = trim($_POST['edit_category_name']);
    $description = trim($_POST['edit_category_description'] ?? '');

    if (!empty($name)) {
        global $mysqli;
        // Check if category name already exists (excluding current)
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Category name already exists.";
        } else {
            $slug = generateSlug($name, $mysqli, $id);
            $stmt = $mysqli->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $slug, $description, $id);
            if ($stmt->execute()) {
                $success = "Category updated successfully!";
                header("Location: categories.php?updated=1");
                exit;
            } else {
                $error = "Database error: " . $mysqli->error;
            }
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        global $mysqli;
        // Check if category has products
        $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $productCount = $row['count'] ?? 0;

        if ($productCount > 0) {
            $error = "Cannot delete category. It has {$productCount} product(s) assigned.";
        } else {
            $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: categories.php?deleted=1");
                exit;
            } else {
                $error = "Database error: " . $mysqli->error;
            }
        }
    }
}

// Handle URL parameters for success messages
if (isset($_GET['success'])) {
    $success = "Category added successfully!";
} elseif (isset($_GET['updated'])) {
    $success = "Category updated successfully!";
} elseif (isset($_GET['deleted'])) {
    $success = "Category deleted successfully!";
}

// Get category to edit (if edit parameter is set)
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $editCategory = $result->fetch_assoc();
    }
}

// Get all categories
global $mysqli;
$result = $mysqli->query("SELECT * FROM categories ORDER BY id DESC");
$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get product counts for each category
$productCounts = [];
$result = $mysqli->query("SELECT category_id, COUNT(*) as count FROM products WHERE category_id IS NOT NULL GROUP BY category_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productCounts[$row['category_id']] = $row['count'];
    }
}

error_log('[Admin] Categories page loaded: ' . count($categories) . ' categories');

include 'includes/header.php';
include 'includes/sidebar.phtml';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-2">Categories Management</h2>
            <p class="text-muted mb-0">Create and manage product categories</p>
        </div>
        <div class="col-auto">
            <a href="?action=add" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add Category
            </a>
        </div>
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

<?php if (isset($_GET['action']) && $_GET['action'] === 'add' || $editCategory): ?>
<!-- Add/Edit Category Form -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus me-2"></i>
            <?= e($editCategory ? 'Edit Category: ' . ($editCategory['name'] ?? '') : 'Add New Category') ?>
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="categories.php">
            <?= csrf_field() ?>
            <?php if ($editCategory): ?>
                <input type="hidden" name="edit_id" value="<?= e($editCategory['id']) ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           id="categoryName"
                           name="<?= e($editCategory ? 'edit_category_name' : 'category_name') ?>"
                           value="<?= e($editCategory ? ($editCategory['name'] ?? '') : '') ?>"
                           placeholder="Enter category name"
                           required>
                    <div class="form-text">Enter a unique name for the category</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="categoryDescription" class="form-label">Description</label>
                    <textarea class="form-control"
                              id="categoryDescription"
                              name="<?= e($editCategory ? 'edit_category_description' : 'category_description') ?>"
                              rows="3"
                              placeholder="Enter category description"><?= e($editCategory ? ($editCategory['description'] ?? '') : '') ?></textarea>
                    <div class="form-text">Optional description for the category</div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="categories.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Categories
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>
                    <?= e($editCategory ? 'Update Category' : 'Add Category') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= e(count($categories)) ?></h4>
                        <p class="mb-0">Total Categories</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php
                        $totalProducts = array_sum($productCounts);
                        ?>
                        <h4 class="mb-0"><?= e($totalProducts) ?></h4>
                        <p class="mb-0">Products Categorized</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php
                        $uncategorizedCount = 0;
                        $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE category_id IS NULL");
                        $uncategorizedCount = $stmt->fetchColumn();
                        ?>
                        <h4 class="mb-0"><?= e($uncategorizedCount) ?></h4>
                        <p class="mb-0">Uncategorized</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php
                        $recentCategories = 0;
                        $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                        $recentCategories = $stmt->fetchColumn();
                        ?>
                        <h4 class="mb-0"><?= e($recentCategories) ?></h4>
                        <p class="mb-0">Added This Week</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>All Categories
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h4>No Categories Yet</h4>
                <p class="text-muted">Create your first product category to get started.</p>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Category
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?= e($category['id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($category['name']) ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($category['description'])): ?>
                                        <span title="<?= htmlspecialchars($category['description']) ?>">
                                            <?= strlen($category['description']) > 50 ? htmlspecialchars(substr($category['description'], 0, 50)) . '...' : htmlspecialchars($category['description']) ?>
                                        </span>
                                    <?php else: ?>
                                        <em class="text-muted">No description</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= e($productCounts[$category['id']] ?? 0) ?> products
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= e(date('M j, Y', strtotime($category['created_at']))) ?><br>
                                        <?= e(date('g:i A', strtotime($category['created_at']))) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?edit=<?= e($category['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <a href="?delete=<?= e($category['id']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash me-1"></i>Delete
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

<?php endif; ?>

        </div> <!-- End container-fluid -->
    </div> <!-- End main-content -->

<!-- MDBootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

</body>
</html>
