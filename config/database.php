<?php
// Read only the DB_* variables you defined (both locally via .env and on Railway)
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$port = getenv('DB_PORT');

// As a safety net, if any of these are missing, you can optionally fall back for local dev:
if (!$host) $host = 'localhost';
if (!$db)   $db   = 'language_buddy_finder';
if (!$user) $user = 'root';
if (!$port) $port = '3306';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Always return a clean JSON error so fetch() can handle it
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database connection failed.',
        // TEMP: include driver error to debug Railway connection issues.
        'details' => $e->getMessage(),
        'config'  => [
            'host' => $host,
            'db'   => $db,
            'port' => $port,
        ],
    ]);
    exit;
}