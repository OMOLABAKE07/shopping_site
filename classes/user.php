<?php
if (!class_exists('Model')) {
    require_once __DIR__ . '/Model.php';
}

class User extends Model {
    protected $table = 'users';

    public function __construct() {
        parent::__construct();
    }

    public function register($data) {
        // Hash the password before storing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    public function login($email, $password) {
        $user = $this->where('email = ?', [$email]);
        
        // Debug logging
        error_log("Login attempt for email: " . $email);
        error_log("User found: " . (!empty($user) ? 'yes' : 'no'));
        
        if (!empty($user) && password_verify($password, $user[0]['password'])) {
            // Don't return the password in the user data
            unset($user[0]['password']);
            error_log("Login successful for user ID: " . $user[0]['id']);
            return $user[0];
        }
        
        error_log("Login failed for email: " . $email);
        return false;
    }

    public function updateProfile($id, $data) {
        // Don't allow password updates through this method
        if (isset($data['password'])) {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }

    public function changePassword($id, $currentPassword, $newPassword) {
        $user = $this->find($id);
        if ($user && password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->update($id, ['password' => $hashedPassword]);
        }
        return false;
    }

    public function isAdmin($id) {
        $user = $this->find($id);
        return $user && $user['role'] === 'admin';
    }

    public function getOrders($userId) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }
}
