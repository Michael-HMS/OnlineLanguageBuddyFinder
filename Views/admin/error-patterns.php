<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$data = $controller->errorPatterns();
$errors = $data['errors'];
?>
<div class="page-header">
    <h1>Error Patterns</h1>
    <p>Monitor conversations with low scores to identify common learning issues</p>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Language</th>
                <th>Message</th>
                <th>Grammar Score</th>
                <th>Vocab Score</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($errors)): ?>
                <tr>
                    <td colspan="7" class="text-center">No error patterns found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($errors as $error): ?>
                    <tr>
                        <td><?php echo $error['id']; ?></td>
                        <td><?php echo htmlspecialchars($error['username'] ?? 'Unknown'); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($error['language']); ?></span></td>
                        <td class="message-cell"><?php echo htmlspecialchars(substr($error['message'], 0, 150)) . (strlen($error['message']) > 150 ? '...' : ''); ?></td>
                        <td>
                            <span class="score score-poor"><?php echo $error['grammar_score']; ?>%</span>
                        </td>
                        <td>
                            <span class="score score-poor"><?php echo $error['vocabulary_score']; ?>%</span>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($error['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

