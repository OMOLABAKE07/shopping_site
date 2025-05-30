<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        self::start();
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => 'Lax'
                ]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Start a new session for flash messages
        session_start();
        session_regenerate_id(true);
    }

    public static function isLoggedIn() {
        return self::get('user_id') !== null;
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        $userModel = new User();
        return $userModel->find(self::get('user_id'));
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::set('redirect_after_login', $_SERVER['REQUEST_URI']);
            header('Location: /login.php');
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        $user = self::getCurrentUser();
        if (!$user || $user['role'] !== 'admin') {
            header('Location: /403.php');
            exit;
        }
    }

    public static function setFlash($type, $message) {
        self::set('flash', [
            'type' => $type,
            'message' => $message
        ]);
    }

    public static function getFlash() {
        $flash = self::get('flash');
        self::remove('flash');
        return $flash;
    }
} 