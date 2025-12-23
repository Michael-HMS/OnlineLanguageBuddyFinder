<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$data = $controller->users();
$users = $data['users'];
$current_page = $data['current_page'];
$total_pages = $data['total_pages'];
$role_filter = $data['role_filter'];
?>
<div class="page-header">
    <h1>User Management</h1>
    <p>Manage users, roles, and account status</p>
</div>

<div class="filter-section">
    <form method="GET" class="filter-form">
        <select name="role" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Students</option>
            <option value="tutor" <?php echo $role_filter === 'tutor' ? 'selected' : ''; ?>>Tutors</option>
            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
        </select>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="9" class="text-center">No users found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></td>
                        <td>
                            <select class="role-select" data-user-id="<?php echo $user['id']; ?>" onchange="updateRole(<?php echo $user['id']; ?>, this.value)">
                                <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                <option value="tutor" <?php echo $user['role'] === 'tutor' ? 'selected' : ''; ?>>Tutor</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-toggle" onclick="toggleStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 0 : 1; ?>)">
                                <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?>" class="btn">Previous</a>
        <?php endif; ?>
        
        <span>Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?>" class="btn">Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

