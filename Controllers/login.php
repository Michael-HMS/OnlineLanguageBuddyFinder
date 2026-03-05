<?php
session_start();
header('Content-Type: application/json');

try {
    // Robust path to the DB config
    require __DIR__ . '/../config/database.php';

    // Get POST data
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate input
    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit;
    }

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        exit;
    }

    // Verify password (expects password_hash stored in DB)
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        exit;
    }

    // Login successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    echo json_encode([
        "status" => "success",
        "message" => "Logged in successfully",
        "user" => [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email']
        ]
    ]);
} catch (Throwable $e) {
    // Ensure the frontend always gets valid JSON even on unexpected errors
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error during login. Please try again later.'
        // For debugging you could temporarily expose $e->getMessage(), but avoid in production.
    ]);
}
