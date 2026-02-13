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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Buddy Finder - Connect, Learn, Practice</title>
    <link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/styles/main.css">
    <?php if ($page === 'chat'): ?>
        <link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/chat/chat.css">
    <?php endif; ?>
</head>
<body>

<nav>
    <a href="index.php" class="logo">🌍 Language Buddy Finder</a>
    <div class="nav-links">
        <?php if ($loggedIn): ?>
            <a href="index.php?page=matches">🔍 Matches</a>
            <a href="index.php?page=profile">👤 Profile</a>
            <a href="index.php?page=logout">🚪 Logout</a>
        <?php else: ?>
            <a href="index.php?page=login">🔑 Login</a>
            <a href="index.php?page=signup">✨ Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<main>
<?php
switch ($page) {
    case 'login':
        if ($loggedIn) header("Location: index.php?page=matches");
        include 'Views/login.php';
        break;

    case 'signup_action':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'Controllers/signup.php';
            exit;
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
        header("Location: index.php");
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
        if ($loggedIn):
            // Logged-in user homepage
            $username = $_SESSION['username'] ?? 'User';
?>
            <div class="welcome-section">
                <h2>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
                <p>Ready to find your language learning partner? Start connecting with native speakers and practice together!</p>
                <div class="quick-actions">
                    <a href="index.php?page=matches" class="btn btn-primary">🔍 Find Matches</a>
                    <a href="index.php?page=profile" class="btn btn-secondary">⚙️ Edit Profile</a>
                </div>
            </div>
            
            <div class="features">
                <h2>How It Works</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h3>Find Your Match</h3>
                        <p>We connect you with native speakers of the language you're learning, who want to learn your native language.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💬</div>
                        <h3>Start Chatting</h3>
                        <p>Begin conversations with your language partners. Practice speaking, ask questions, and learn naturally.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📈</div>
                        <h3>Improve Together</h3>
                        <p>Help each other learn through real conversations. Exchange knowledge and build lasting friendships.</p>
                    </div>
                </div>
            </div>
<?php
        else:
            // Guest homepage
?>
            <div class="hero">
                <h1>🌍 Language Buddy Finder</h1>
                <p>Connect with native speakers around the world. Practice languages through real conversations and make new friends!</p>
                <div class="hero-buttons">
                    <a href="index.php?page=signup" class="btn btn-primary">Get Started Free</a>
                    <a href="index.php?page=login" class="btn btn-secondary">Already a Member? Login</a>
                </div>
            </div>
            
            <div class="features">
                <h2>Why Choose Language Buddy Finder?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🤝</div>
                        <h3>Perfect Language Exchange</h3>
                        <p>Find partners who speak the language you're learning and want to learn your native language. It's a win-win!</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💬</div>
                        <h3>Real Conversations</h3>
                        <p>Practice through authentic chat conversations. Learn how people actually speak, not just from textbooks.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🌐</div>
                        <h3>Global Community</h3>
                        <p>Connect with language learners from all over the world. Expand your cultural horizons while learning.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h3>Instant Matching</h3>
                        <p>Our smart matching system instantly finds compatible language partners based on your learning goals.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3>Safe & Secure</h3>
                        <p>Your privacy is our priority. Chat safely with verified members in a secure environment.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3>Easy to Use</h3>
                        <p>Simple, intuitive interface. Start chatting with your language buddy in minutes, not hours.</p>
                    </div>
                </div>
            </div>
            
            <div class="hero" style="padding: 3rem 2rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Ready to Start Learning?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 2rem;">Join thousands of language learners already improving their skills!</p>
                <a href="index.php?page=signup" class="btn btn-primary" style="font-size: 1.2rem; padding: 1.2rem 3rem;">Create Your Free Account</a>
            </div>
<?php
        endif;
        break;
}
?>
</main>

</body>
</html>
