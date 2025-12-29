<?php
session_start();

$loggedIn = isset($_SESSION['user_id']);
$page = $_GET['page'] ?? 'home';

// simple route guard
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buddy Finder</title>
    <link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/chat/chat.css">

</head>
<body>

<nav>
    <a href="index.php">Home</a>

    <?php if ($loggedIn): ?>
        <a href="index.php?page=matches">Matches</a>
        <a href="index.php?page=profile">Profile</a>
        <a href="index.php?page=logout">Logout</a>


    <?php else: ?>
        <a href="index.php?page=login">Login</a>
        <a href="index.php?page=signup">Signup</a>
    <?php endif; ?>
</nav>

<hr>

<main>
<?php
switch ($page) {
    case 'login':
        if ($loggedIn) header("Location: index.php?page=matches");
        include 'Views/login.php';
        break;

        case 'signup_action':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require 'Controllers/signup.php'; // your existing signup logic
        exit; // stop further rendering of index.php
    } else {
        header("Location: index.php?page=signup");
        exit;
    }
    break;



    case 'signup':
        if ($loggedIn) header("Location: index.php?page=matches");
        include 'Views/signup.php';
        break;

        
case 'logout':
    session_start();
    session_destroy();
    header("Location: index.php"); // redirect to home
    exit;



    case 'matches':
        requireLogin();
        include 'Views/buddy/matches.php';
        break;

    case 'chat':
        requireLogin();
        include 'Views/chat/chat.php';
        break;

    case 'profile':
        requireLogin();
        include 'Views/profile/updateProfile.php';
        break;

    default:
        echo "<h2>Welcome to Buddy Finder</h2>";
        echo $loggedIn
            ? "<p>Select Matches to start chatting.</p>"
            : "<p>Please login or signup.</p>";
}
?>
</main>

</body>
</html>
