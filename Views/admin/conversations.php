<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$data = $controller->conversations();
$conversations = $data['conversations'];
$current_page = $data['current_page'];
$total_pages = $data['total_pages'];
?>
<div class="page-header">
    <h1>Chat Conversations</h1>
    <p>Monitor chatbot conversations and user interactions</p>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Language</th>
                <th>Topic</th>
                <th>Message</th>
                <th>Bot Response</th>
                <th>Grammar Score</th>
                <th>Vocab Score</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($conversations)): ?>
                <tr>
                    <td colspan="9" class="text-center">No conversations found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <tr>
                        <td><?php echo $conv['id']; ?></td>
                        <td><?php echo htmlspecialchars($conv['username'] ?? 'Unknown'); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($conv['language']); ?></span></td>
                        <td><?php echo htmlspecialchars($conv['topic'] ?? '-'); ?></td>
                        <td class="message-cell"><?php echo htmlspecialchars(substr($conv['message'], 0, 100)) . (strlen($conv['message']) > 100 ? '...' : ''); ?></td>
                        <td class="message-cell"><?php echo htmlspecialchars(substr($conv['bot_response'] ?? '', 0, 100)) . (strlen($conv['bot_response'] ?? '') > 100 ? '...' : ''); ?></td>
                        <td>
                            <?php if ($conv['grammar_score'] > 0): ?>
                                <span class="score score-<?php echo $conv['grammar_score'] >= 70 ? 'good' : ($conv['grammar_score'] >= 50 ? 'medium' : 'poor'); ?>">
                                    <?php echo $conv['grammar_score']; ?>%
                                </span>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($conv['vocabulary_score'] > 0): ?>
                                <span class="score score-<?php echo $conv['vocabulary_score'] >= 70 ? 'good' : ($conv['vocabulary_score'] >= 50 ? 'medium' : 'poor'); ?>">
                                    <?php echo $conv['vocabulary_score']; ?>%
                                </span>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($conv['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>" class="btn">Previous</a>
        <?php endif; ?>
        
        <span>Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>" class="btn">Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

