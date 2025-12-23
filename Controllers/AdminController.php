<?php
/**
 * Admin Controller - Request Handler
 * Processes admin actions and returns data for views
 */

require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    private $admin;

    public function __construct($skipSession = false) {
        $this->admin = new Admin();
        if (!$skipSession) $this->checkSession();
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $page = basename($_SERVER['PHP_SELF']);
        if ($page === 'login.php' || $page === 'index.php') return;
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: login.php');
            exit();
        }
    }

    // Authentication
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return null;
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Please fill in all fields'];
        }

        $user = $this->admin->authenticate($username, $password);
        if ($user) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $this->admin->logActivity($user['id'], 'admin_login', 'Admin logged in');
            header('Location: dashboard.php');
            exit();
        }
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['admin_id'])) {
            $this->admin->logActivity($_SESSION['admin_id'], 'admin_logout', 'Admin logged out');
        }
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Dashboard
    public function dashboard() {
        return [
            'stats' => $this->admin->getDashboardStats(),
            'recent_logs' => $this->admin->getRecentActivityLogs(10)
        ];
    }

    public function getRecentUsers($limit = 4) {
        return $this->admin->getAllUsers(1, $limit);
    }

    public function getUserLanguages($id) {
        return $this->admin->getUserLanguages($id);
    }

    public function timeAgo($datetime) {
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' minute' . (floor($diff / 60) == 1 ? '' : 's') . ' ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hour' . (floor($diff / 3600) == 1 ? '' : 's') . ' ago';
        if ($diff < 2592000) return floor($diff / 86400) . ' day' . (floor($diff / 86400) == 1 ? '' : 's') . ' ago';
        return date('M d, Y', strtotime($datetime));
    }

    // Users Management
    public function users() {
        $page = (int)($_GET['page'] ?? 1);
        $role = $_GET['role'] ?? null;
        $status_filter = $_GET['status'] ?? null;
        $limit = 20;

        return [
            'users' => $this->admin->getAllUsers($page, $limit, $role, $status_filter),
            'current_page' => $page,
            'total_pages' => ceil($this->admin->getTotalUsersCount($role) / $limit),
            'role_filter' => $role,
            'status_filter' => $status_filter
        ];
    }

    // AJAX Handlers
    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $result = $this->admin->updateUserRole($_POST['user_id'] ?? 0, $_POST['new_role'] ?? '');
        echo json_encode(['success' => $result, 'message' => $result ? 'Role updated' : 'Update failed']);
        exit();
    }

    // Update user status (active/suspended)
    public function updateUserStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $user_id = (int)($_POST['user_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        
        // Validate status
        if (!in_array($status, ['active', 'suspended'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            exit();
        }
        
        $result = $this->admin->updateUserStatus($user_id, $status);
        
        if ($result) {
            $this->admin->logActivity(
                $_SESSION['admin_id'] ?? null,
                'user_status_updated',
                "User ID $user_id status changed to $status"
            );
        }
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? "User status updated to $status" : 'Update failed'
        ]);
        exit();
    }

    // Legacy method for backward compatibility
    public function toggleUserStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $status = (int)($_POST['status'] ?? 0);
        $new_status = $status ? 'active' : 'suspended';
        $_POST['status'] = $new_status;
        $this->updateUserStatus();
    }

    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $result = $this->admin->deleteUser($_POST['user_id'] ?? 0);
        echo json_encode(['success' => $result, 'message' => $result ? 'User deleted' : 'Cannot delete admin users']);
        exit();
    }

    // Conversations
    public function conversations() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 50;
        $total = $this->admin->getTotalConversationsCount();
        
        return [
            'conversations' => $this->admin->getChatConversations($page, $limit),
            'current_page' => $page,
            'total_pages' => ceil($total / $limit)
        ];
    }

    // Analytics
    public function analytics() {
        return ['analytics' => $this->admin->getAnalytics()];
    }

    // Error Patterns
    public function errorPatterns() {
        return ['errors' => $this->admin->getErrorPatterns(50)];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $controller = new AdminController(true);
    switch ($_POST['action']) {
        case 'update_role': $controller->updateUserRole(); break;
        case 'update_status': $controller->updateUserStatus(); break;
        case 'toggle_status': $controller->toggleUserStatus(); break;
        case 'delete_user': $controller->deleteUser(); break;
    }
}
?>
