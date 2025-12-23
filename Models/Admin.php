<?php
/**
 * Admin Model - Database Operations
 * Handles all admin-related database queries
 */

require_once __DIR__ . '/../config/database.php';

class Admin {
    private $conn;
    private $tables = [
        'users' => 'users',
        'chats' => 'chat_conversations',
        'logs' => 'activity_logs'
    ];

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Authentication (check both is_active and status)
    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare(
            "SELECT id, username, email, password, role, first_name, last_name, status
             FROM {$this->tables['users']} 
             WHERE username = :username AND role = 'admin' AND is_active = 1 AND status = 'active' LIMIT 1"
        );
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $this->conn->prepare("UPDATE {$this->tables['users']} SET last_login = NOW() WHERE id = :id")
                ->execute([':id' => $user['id']]);
            return $user;
        }
        return false;
    }

    // Dashboard Statistics (optimized single query)
    public function getDashboardStats() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM {$this->tables['users']} WHERE role = 'student') as total_students,
                    (SELECT COUNT(*) FROM {$this->tables['users']} WHERE role = 'tutor') as total_tutors,
                    (SELECT COUNT(*) FROM {$this->tables['users']} WHERE is_active = 1) as total_active_accounts,
                    (SELECT COUNT(*) FROM {$this->tables['chats']}) as total_conversations,
                    (SELECT COUNT(DISTINCT user_id) FROM {$this->tables['chats']} 
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as active_users";
        return $this->conn->query($query)->fetch();
    }

    // Users Management (include status field)
    public function getAllUsers($page = 1, $limit = 20, $role = null, $status_filter = null) {
        $offset = ($page - 1) * $limit;
        $conditions = [];
        
        if ($role && in_array($role, ['student', 'tutor', 'admin'])) {
            $conditions[] = "role = :role";
        }
        if ($status_filter && in_array($status_filter, ['active', 'suspended'])) {
            $conditions[] = "status = :status";
        }
        
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $query = "SELECT id, username, email, role, first_name, last_name, is_active, status, created_at, last_login 
                  FROM {$this->tables['users']} $where 
                  ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        if ($role) $stmt->bindParam(':role', $role);
        if ($status_filter) $stmt->bindParam(':status', $status_filter);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalUsersCount($role = null) {
        $where = $role ? "WHERE role = :role" : "";
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->tables['users']} $where");
        if ($role) $stmt->execute([':role' => $role]);
        else $stmt->execute();
        return $stmt->fetch()['count'];
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->tables['users']} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateUserRole($id, $role) {
        if (!in_array($role, ['student', 'tutor', 'admin'])) return false;
        return $this->conn->prepare("UPDATE {$this->tables['users']} SET role = :role WHERE id = :id")
            ->execute([':role' => $role, ':id' => $id]);
    }

    // Update user status (new status field: active/suspended)
    public function updateUserStatus($id, $status) {
        if (!in_array($status, ['active', 'suspended'])) return false;
        
        // Update both status and is_active for backward compatibility
        $is_active = ($status === 'active') ? 1 : 0;
        return $this->conn->prepare(
            "UPDATE {$this->tables['users']} SET status = :status, is_active = :is_active WHERE id = :id"
        )->execute([
            ':status' => $status,
            ':is_active' => $is_active,
            ':id' => $id
        ]);
    }

    // Legacy method for backward compatibility
    public function toggleUserStatus($id, $status) {
        $new_status = $status ? 'active' : 'suspended';
        return $this->updateUserStatus($id, $new_status);
    }

    // Get users by status
    public function getUsersByStatus($status) {
        if (!in_array($status, ['active', 'suspended'])) return [];
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->tables['users']} WHERE status = :status ORDER BY created_at DESC"
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function deleteUser($id) {
        $user = $this->getUserById($id);
        if ($user && $user['role'] === 'admin') return false;
        return $this->conn->prepare("DELETE FROM {$this->tables['users']} WHERE id = :id")
            ->execute([':id' => $id]);
    }

    // Conversations
    public function getChatConversations($page = 1, $limit = 50) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT c.*, u.username, u.email 
                  FROM {$this->tables['chats']} c
                  LEFT JOIN {$this->tables['users']} u ON c.user_id = u.id
                  ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalConversationsCount() {
        return $this->conn->query("SELECT COUNT(*) as count FROM {$this->tables['chats']}")->fetch()['count'];
    }

    // Activity Logs
    public function getRecentActivityLogs($limit = 20) {
        $stmt = $this->conn->prepare(
            "SELECT al.*, u.username 
             FROM {$this->tables['logs']} al
             LEFT JOIN {$this->tables['users']} u ON al.user_id = u.id
             ORDER BY al.created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function logActivity($user_id, $action, $description = null) {
        return $this->conn->prepare(
            "INSERT INTO {$this->tables['logs']} (user_id, action, description, ip_address) 
             VALUES (:user_id, :action, :description, :ip)"
        )->execute([
            ':user_id' => $user_id,
            ':action' => $action,
            ':description' => $description,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }

    // Analytics
    public function getAnalytics() {
        return [
            'top_languages' => $this->conn->query(
                "SELECT language, COUNT(*) as count FROM {$this->tables['chats']} 
                 GROUP BY language ORDER BY count DESC LIMIT 10"
            )->fetchAll(),
            'top_users' => $this->conn->query(
                "SELECT u.id, u.username, u.email, COUNT(c.id) as conversation_count 
                 FROM {$this->tables['users']} u
                 LEFT JOIN {$this->tables['chats']} c ON u.id = c.user_id
                 WHERE u.role = 'student' GROUP BY u.id ORDER BY conversation_count DESC LIMIT 10"
            )->fetchAll(),
            'daily_activity' => $this->conn->query(
                "SELECT DATE(created_at) as date, COUNT(*) as count FROM {$this->tables['chats']} 
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY DATE(created_at) ORDER BY date DESC"
            )->fetchAll(),
            'average_scores' => $this->conn->query(
                "SELECT AVG(grammar_score) as avg_grammar, AVG(vocabulary_score) as avg_vocabulary 
                 FROM {$this->tables['chats']} WHERE grammar_score > 0 OR vocabulary_score > 0"
            )->fetch()
        ];
    }

    public function getErrorPatterns($limit = 20) {
        $stmt = $this->conn->prepare(
            "SELECT c.*, u.username FROM {$this->tables['chats']} c
             LEFT JOIN {$this->tables['users']} u ON c.user_id = u.id
             WHERE c.grammar_score < 50 OR c.vocabulary_score < 50
             ORDER BY c.created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // User Languages (from chat conversations)
    public function getUserLanguages($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT language FROM {$this->tables['chats']} 
             WHERE user_id = :id ORDER BY created_at DESC LIMIT 5"
        );
        $stmt->execute([':id' => $user_id]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'learns' => array_slice($languages, 0, 2),
            'fluent' => array_slice($languages, 2, 2)
        ];
    }
}
?>
