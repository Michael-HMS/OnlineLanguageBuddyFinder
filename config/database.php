// mysql://root:EWJYJxQkVxeVnUCOcYhpgKepKqnKcQGa@shinkansen.proxy.rlwy.net:16451/railway
<?php
$host = shinkansen.proxy.rlwy.net;
$db   = railway;
$user = root;
$pass = EWJYJxQkVxeVnUCOcYhpgKepKqnKcQGa; 
$port = 16451;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Example SELECT
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([1]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}