<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json'); // Ensure output is JSON

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$conversation_id = $_GET['conversation_id'] ?? null;

if (!$conversation_id) {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

// Fetch messages with sender username
$stmt = $pdo->prepare("
    SELECT m.id, m.sender_id, m.message, m.sent_at, u.username
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.sent_at ASC
");
$stmt->execute([$conversation_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch conversation participants
$stmt2 = $pdo->prepare("SELECT user1_id, user2_id FROM conversations WHERE id = ?");
$stmt2->execute([$conversation_id]);
$conv = $stmt2->fetch(PDO::FETCH_ASSOC);

if (!$conv) {
    echo json_encode([
        'messages' => $messages,
        'otherUser' => null
    ]);
    exit;
}

// Determine the other user
$otherUserId = ($conv['user1_id'] == $user_id) ? $conv['user2_id'] : $conv['user1_id'];
$stmt3 = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt3->execute([$otherUserId]);
$otherUser = $stmt3->fetch(PDO::FETCH_ASSOC);

// Return clean JSON
// Return clean JSON including loggedInUserId
echo json_encode([
    'messages' => $messages,
    'otherUser' => $otherUser ?? null,
    'loggedInUserId' => $user_id // from session
]);

