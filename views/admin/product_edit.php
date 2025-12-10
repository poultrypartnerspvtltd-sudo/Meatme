<?php
// Admin - Edit Product (basic form)
?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Edit Product</h1>
                <p class="text-muted mb-0">Modify product details</p>
            </div>
            <div>
                <a href="<?= e(\App\Core\View::url('admin/products')) ?>" class="btn btn-outline-secondary">Back to Products</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="<?= e(\App\Core\View::url('admin/products/' . ($product['id'] ?? ''))) ?>">
                <input type="hidden" name="_method" value="PUT">
                <?= \App\Core\CSRF::field() ?>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= e($product['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= e($product['price'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="<?= e($product['stock_quantity'] ?? 0) ?>" min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Short Description</label>
                    <input type="text" name="short_description" class="form-control" value="<?= e($product['short_description'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- Select Category --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= \App\Core\View::escape($cat['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="<?= e($product['sku'] ?? '') ?>">
                </div>

                <button class="btn btn-primary" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>
