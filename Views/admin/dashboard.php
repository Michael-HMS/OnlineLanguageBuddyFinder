<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$data = $controller->dashboard();
$stats = $data['stats'];
$recent_logs = $data['recent_logs'];
?>
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Overview of platform statistics and recent activity</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['total_students']); ?></h3>
            <p>Total Students</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['total_tutors']); ?></h3>
            <p>Total Tutors</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['total_active_accounts']); ?></h3>
            <p>Active Accounts</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['total_conversations']); ?></h3>
            <p>Total Conversations</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🔥</div>
        <div class="stat-content">
            <h3><?php echo number_format($stats['active_users']); ?></h3>
            <p>Active Users (30 days)</p>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <h2>Recent Activity Logs</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No recent activity</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($log['action']); ?></span></td>
                            <td><?php echo htmlspecialchars($log['description'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

