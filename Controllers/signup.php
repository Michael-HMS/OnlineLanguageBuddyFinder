<?php
session_start();

// Robust path to the DB config
require __DIR__ . '/../config/database.php';

try {
    // Get POST data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $native_language = trim($_POST['native_language'] ?? '');
    $learning_language = trim($_POST['learning_language'] ?? '');

    // Validate input
    if (!$username || !$email || !$password || !$native_language || !$learning_language) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Email already registered."]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, native_language, learning_language, created_at, status) VALUES (?, ?, ?, ?, ?, NOW(), 'active')");
    $success = $stmt->execute([$username, $email, $hashedPassword, $native_language, $learning_language]);

    if ($success) {
        // Auto-login after signup
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;

        // Redirect to matches or homepage (frontend treats redirect as success)
        header("Location: /index.php?page=matches");
        exit;

    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Failed to register user."]);
        exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error during signup. Please try again later.'
        // For debugging you could temporarily expose $e->getMessage(), but avoid in production.
    ]);
    exit;
}
