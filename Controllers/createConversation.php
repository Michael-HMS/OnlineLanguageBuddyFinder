<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$other_user_id = $input['other_user_id'] ?? null;

if (!$other_user_id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Other user ID is required"]);
    exit;
}

// 1️⃣ Check if conversation already exists
$stmt = $pdo->prepare("
    SELECT id FROM conversations 
    WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)
    LIMIT 1
");
$stmt->execute([$user_id, $other_user_id, $other_user_id, $user_id]);
$conv = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conv) {
    // Conversation exists
    $conversation_id = $conv['id'];
} else {
    // 2️⃣ Create new conversation
    $stmt2 = $pdo->prepare("
        INSERT INTO conversations (user1_id, user2_id, created_at) 
        VALUES (?, ?, NOW())
    ");
    $stmt2->execute([$user_id, $other_user_id]);
    $conversation_id = $pdo->lastInsertId();
}

// 3️⃣ Return conversation id
echo json_encode([
    "status" => "success",
    "conversation_id" => $conversation_id
]);
