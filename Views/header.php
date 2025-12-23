<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Language Buddy Finder</title>
    <link rel="stylesheet" href="../public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Top Header Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <div class="logo">
                    <div class="logo-icon">
                        <span class="logo-letter">A</span>
                        <span class="logo-bubbles">
                            <span class="bubble">A</span>
                            <span class="bubble">文</span>
                        </span>
                    </div>
                    <span class="logo-text">Language Buddy Finder</span>
                </div>
            </div>
            <div class="topbar-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for users..." id="global-search">
                </div>
            </div>
            <div class="topbar-right">
                <div class="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">2</span>
                </div>
                <div class="user-menu">
                    <div class="user-avatar">
                        <?php 
                        $name = $_SESSION['admin_name'] ?? 'Admin';
                        $initials = strtoupper(substr($name, 0, 1));
                        ?>
                        <span><?php echo $initials; ?></span>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($name); ?></span>
                        <span class="user-email"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>@buddyfinder.com</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Sidebar Navigation -->
            <nav class="admin-sidebar">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> User Management</a></li>
                    <li><a href="conversations.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'conversations.php' ? 'active' : ''; ?>"><i class="fas fa-comments"></i> Chat Conversations</a></li>
                    <li><a href="analytics.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Analytics & Reports</a></li>
                    <li><a href="error-patterns.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'error-patterns.php' ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle"></i> Error Patterns</a></li>
                    <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>

            <!-- Content Area -->
            <main class="admin-content">

