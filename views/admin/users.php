<?php
/**
 * Admin - Users
 */
?>
<div class="container p-4">
    <h1>Users</h1>

    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr id="user-row-<?= $u['id'] ?>">
                        <td><?= $u['id'] ?></td>
                        <td><?= \App\Core\View::escape($u['name'] ?? '') ?></td>
                        <td><?= \App\Core\View::escape($u['email'] ?? '') ?></td>
                        <td><?= \App\Core\View::escape($u['role'] ?? 'customer') ?></td>
                        <td class="user-status"><?= \App\Core\View::escape($u['status'] ?? 'active') ?></td>
                        <td><?= isset($u['created_at']) ? \App\Core\View::formatDate($u['created_at']) : '-' ?></td>
                        <td>
                            <a href="<?= \App\Core\View::url('/admin/users/' . $u['id']) ?>" class="btn btn-sm btn-info">View</a>
                            <?php if (($u['status'] ?? 'active') === 'blocked'): ?>
                                <button class="btn btn-sm btn-success" onclick="setStatus(<?= $u['id'] ?>, 'active')">Unblock</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-warning" onclick="setStatus(<?= $u['id'] ?>, 'blocked')">Block</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <li class="page-item <?= $p == $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= \App\Core\View::url('/admin/users?page=' . $p) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <a href="<?= \App\Core\View::url('/admin/dashboard') ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<?= \App\Core\CSRF::meta() ?>

<script>
function getCsrf() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function setStatus(id, status) {
    const token = getCsrf();
    const body = 'status=' + encodeURIComponent(status) + '&csrf_token=' + encodeURIComponent(token);
    fetch('<?= \App\Core\View::url('/admin/users/') ?>' + id + '/status', {
        method: 'PUT',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': token
        },
        body: body
    }).then(res => res.json()).then(data => {
        if (data.success) {
            const row = document.getElementById('user-row-' + id);
            if (row) {
                const statusCell = row.querySelector('.user-status');
                if (statusCell) statusCell.textContent = data.status;
            }
            alert(data.message);
        } else {
            alert(data.message || 'Error');
        }
    }).catch(err => {
        console.error(err);
        alert('Request failed');
    });
}
</script>
