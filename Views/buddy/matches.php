<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggested Buddies</title>
    <style>
        .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; padding: 20px; }
        .card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; text-align: center; background: #f9f9f9; }
        .card img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .btn { background: #4CAF50; color: white; padding: 10px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Suggested Language Buddies</h1>
    <div class="card-grid">
        <?php if (empty($suggestedBuddies)): ?>
            <p>No suggested buddies yet. Update your languages in profile!</p>
        <?php else: ?>
            <?php foreach ($suggestedBuddies as $buddy): ?>
                <div class="card">
                    <img src="default-avatar.jpg" alt="Avatar">
                    <h3><?= htmlspecialchars($buddy['username']) ?></h3>
                    <p><strong>Native:</strong> <?= htmlspecialchars($buddy['native_language'] ?? 'Not set') ?></p>
                    <p><strong>Learning:</strong> <?= htmlspecialchars($buddy['learning_language'] ?? 'Not set') ?></p>
                    <a href="/buddy/request/<?= $buddy['id'] ?>" class="btn">Send Request</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>