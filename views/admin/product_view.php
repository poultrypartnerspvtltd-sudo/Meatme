<?php
// Admin - View Product (readonly)
?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">View Product</h1>
                <p class="text-muted mb-0">Product details</p>
            </div>
            <div>
                <a href="<?= e(\App\Core\View::url('admin/products')) ?>" class="btn btn-outline-secondary">Back to Products</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <h4><?= e($product['name'] ?? 'Unnamed') ?></h4>
            <p class="text-muted"><?= e($product['short_description'] ?? '') ?></p>
            <dl class="row">
                <dt class="col-sm-3">SKU</dt>
                <dd class="col-sm-9"><?= e($product['sku'] ?? '-') ?></dd>

                <dt class="col-sm-3">Price</dt>
                <dd class="col-sm-9">Rs. <?= e(number_format($product['price'] ?? 0, 2)) ?></dd>

                <dt class="col-sm-3">Stock</dt>
                <dd class="col-sm-9"><?= e($product['stock_quantity'] ?? 0) ?></dd>
            </dl>
        </div>
    </div>
</div>
