<?php
session_start();
require __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current user data
$stmt = $pdo->prepare("SELECT username, email, native_language, learning_language FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

// Incoming values (may be partial)
$input = [
    'username' => trim($_POST['username'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'native_language' => trim($_POST['native_language'] ?? ''),
    'learning_language' => trim($_POST['learning_language'] ?? '')
];

$fields = [];
$values = [];

// Compare & build dynamic query
foreach ($input as $key => $value) {
    if ($value !== '' && $value !== $current[$key]) {
        $fields[] = "$key = ?";
        $values[] = $value;
    }
}

// Nothing changed
if (empty($fields)) {
    echo json_encode([
        "status" => "noop",
        "message" => "No changes detected"
    ]);
    exit;
}

// Execute update
$values[] = $user_id;
$sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($values);

echo json_encode([
    "status" => "success",
    "updated_fields" => array_keys(array_filter($input, fn($v, $k) => $v !== '' && $v !== $current[$k], ARRAY_FILTER_USE_BOTH))
]);
