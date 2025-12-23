<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/BuddyController.php';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($url === '/buddy/matches') {
    $controller = new BuddyController();
    $controller->index();
} 
else if (preg_match('#^/buddy/request/(\d+)$#', $url, $matches)) {
    $controller = new BuddyController();
    $controller->requestMatch($matches[1]);
} 
else {
    http_response_code(404);
    echo "Page not found";
}