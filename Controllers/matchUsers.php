<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// 1️⃣ Get current user's languages
$stmt = $pdo->prepare("SELECT native_language, learning_language FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

// 2️⃣ Find matches
$sql = "
SELECT id, username, native_language, learning_language
FROM users
WHERE native_language = ? AND learning_language = ? AND id != ?
";
$stmt2 = $pdo->prepare($sql);
$stmt2->execute([$user['learning_language'], $user['native_language'], $user_id]);
$matches = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// 3️⃣ Return matches
echo json_encode([
    "status" => "success",
    "matches" => $matches
]);
