<?php
namespace App\Classes;

class Auth {
    private static $instance = null;
    private $db;
    private $session;

    private function __construct() {
        $this->db = Database::getInstance();
        $this->session = new Session();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $this->session->set('user_id', $user['id']);
            $this->session->set('user_role', $user['role']);
            return true;
        }
        return false;
    }

    public function register($email, $password, $name) {
        if ($this->userExists($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password, name, role) VALUES (?, ?, ?, 'user')");
        return $stmt->execute([$email, $hashedPassword, $name]);
    }

    public function logout() {
        $this->session->destroy();
    }

    public function isLoggedIn() {
        return $this->session->has('user_id');
    }

    public function isAdmin() {
        return $this->session->get('user_role') === 'admin';
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT id, email, name, role FROM users WHERE id = ?");
        $stmt->execute([$this->session->get('user_id')]);
        return $stmt->fetch();
    }

    private function userExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
} 