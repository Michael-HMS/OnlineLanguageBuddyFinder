<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$data = $controller->analytics();
$analytics = $data['analytics'];
?>
<div class="page-header">
    <h1>Analytics & Reports</h1>
    <p>Platform statistics and learning insights</p>
</div>

<div class="analytics-section">
    <h2>Most Practiced Languages</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Language</th>
                    <th>Conversations</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_lang_convs = array_sum(array_column($analytics['top_languages'], 'count'));
                foreach ($analytics['top_languages'] as $lang): 
                    $percentage = $total_lang_convs > 0 ? ($lang['count'] / $total_lang_convs * 100) : 0;
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($lang['language']); ?></strong></td>
                        <td><?php echo number_format($lang['count']); ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                <span><?php echo number_format($percentage, 1); ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="analytics-section">
    <h2>Top Active Users</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Conversations</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($analytics['top_users'])): ?>
                    <tr>
                        <td colspan="3" class="text-center">No user data available</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($analytics['top_users'] as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><strong><?php echo number_format($user['conversation_count']); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="analytics-section">
    <h2>Average Scores</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <h3><?php echo number_format($analytics['average_scores']['avg_grammar'] ?? 0, 1); ?>%</h3>
                <p>Average Grammar Score</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <h3><?php echo number_format($analytics['average_scores']['avg_vocabulary'] ?? 0, 1); ?>%</h3>
                <p>Average Vocabulary Score</p>
            </div>
        </div>
    </div>
</div>

<div class="analytics-section">
    <h2>Daily Activity (Last 30 Days)</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Conversations</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($analytics['daily_activity'])): ?>
                    <tr>
                        <td colspan="2" class="text-center">No activity data available</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($analytics['daily_activity'] as $day): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($day['date'])); ?></td>
                            <td><?php echo number_format($day['count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

