<?php
/**
 * Admin - Categories
 */
?>
<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Categories</h1>
        <a href="<?= \App\Core\View::url('/admin/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-danger"><?= e(\App\Core\Session::flash('error')) ?></div>
    <?php endif; ?>
    <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success"><?= e(\App\Core\Session::flash('success')) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Add Category</h5>
                    <form id="create-category" method="POST" action="<?= \App\Core\View::url('/admin/categories') ?>">
                        <?= \App\Core\CSRF::field() ?>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if (empty($categories)): ?>
                <p>No categories found.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                            <tr id="cat-<?= $c['id'] ?>">
                                <td><?= $c['id'] ?></td>
                                <td><?= \App\Core\View::escape($c['name']) ?></td>
                                <td><?= \App\Core\View::escape($c['slug'] ?? '-') ?></td>
                                <td><?= isset($c['product_count']) ? (int)$c['product_count'] : '-' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?= $c['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                                <li class="page-item <?= $p == $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= \App\Core\View::url('/admin/categories?page=' . $p) ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= \App\Core\CSRF::meta() ?>

<script>
function getCsrf() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function deleteCategory(id) {
    if (!confirm('Delete category #' + id + '? This cannot be undone.')) return;

    const token = getCsrf();
    const body = '_method=DELETE&csrf_token=' + encodeURIComponent(token);

    fetch('<?= \App\Core\View::url('/admin/categories/') ?>' + id, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body
    }).then(r => r.json()).then(data => {
        if (data.success) {
            const row = document.getElementById('cat-' + id);
            if (row) row.remove();
            alert('Category deleted');
        } else {
            alert(data.message || 'Failed to delete');
        }
    }).catch(err => {
        console.error(err);
        alert('Request failed');
    });
}
</script>
