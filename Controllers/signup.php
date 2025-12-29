<?php

require 'config/database.php';



// Get POST data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$native_language = trim($_POST['native_language'] ?? '');
$learning_language = trim($_POST['learning_language'] ?? '');

// Validate input
if (!$username || !$email || !$password || !$native_language || !$learning_language) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409); // Conflict
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
    session_start();
    $user_id = $pdo->lastInsertId();
    $_SESSION['user_id'] = $user_id;

    // Redirect to matches or homepage
   header("Location: /OnlineLanguageBuddyFinder/index.php?page=matches");
exit;

} else {
    session_start();
    $_SESSION['signup_error'] = "Failed to register user.";
    header("Location: ../index.php?page=signup");
    exit;
}
