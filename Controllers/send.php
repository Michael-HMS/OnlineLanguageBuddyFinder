<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$conversation_id = $_POST['conversation_id'] ?? null;
$message = trim($_POST['message'] ?? '');
$sender_id = $_SESSION['user_id'];

if (!$conversation_id || $message === '') {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

// Insert message
$stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
$stmt->execute([$conversation_id, $sender_id, $message]);

echo json_encode(["status" => "success"]);
