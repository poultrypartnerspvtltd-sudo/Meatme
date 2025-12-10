<?php
// Admin Products Listing
?>
<form id="csrf-token-form" style="display:none;">
    <?= \App\Core\CSRF::field() ?>
</form>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Product Management</h1>
                <p class="text-muted mb-0">Manage store products</p>
            </div>
            <div>
                <a href="<?= e(\App\Core\View::url('admin/products/create')) ?>" class="btn btn-primary">Create Product</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Products</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <p>No products found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= e($product['id']) ?></td>
                                    <td>
                                        <strong><?= e($product['name']) ?></strong>
                                        <div class="small text-muted"><?= e($product['short_description'] ?? '') ?></div>
                                    </td>
                                    <td><?= e($product['sku'] ?? '-') ?></td>
                                    <td><?= e($product['category_name'] ?? '-') ?></td>
                                    <td>Rs. <?= e(number_format($product['price'] ?? 0, 2)) ?></td>
                                    <td><?= e($product['stock_quantity'] ?? 0) ?></td>
                                    <td>
                                        <?php if (!empty($product['is_active'])): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= e(\App\Core\View::url('admin/products/' . $product['id'] . '/edit')) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= e(\App\Core\View::url('admin/products/' . $product['id'])) ?>" style="display:inline-block;" onsubmit="return confirm('Delete this product?');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($paginator)): ?>
                <nav aria-label="Products pagination">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= ($paginator['last_page'] ?? 1); $i++): ?>
                                <li class="page-item <?= ($paginator['current_page'] == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e(\App\Core\View::url('admin/products?page=' . $i)) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>
