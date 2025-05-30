<?php
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../classes/Auth.php';

// Start session
Session::start();

// Verify CSRF token
if (!Form::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

// Get the action
$action = $_POST['action'] ?? '';

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid action',
    'redirect' => null
];

// Get Auth instance
$auth = Auth::getInstance();

switch ($action) {
    case 'login':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $response['message'] = 'Please fill in all fields';
            break;
        }
        
        if ($auth->login($username, $password)) {
            $response['success'] = true;
            $response['redirect'] = BASE_URL . '/';
        } else {
            $response['message'] = 'Invalid username or password';
        }
        break;
        
    case 'register':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $response['message'] = 'Please fill in all fields';
            break;
        }
        
        if ($password !== $confirmPassword) {
            $response['message'] = 'Passwords do not match';
            break;
        }
        
        if ($auth->register($email, $password, $username)) {
            $response['success'] = true;
            $response['message'] = 'Registration successful! Please login.';
            $response['redirect'] = BASE_URL . '/auth?action=login';
        } else {
            $response['message'] = 'Registration failed. Email may already be in use.';
        }
        break;
        
    default:
        $response['message'] = 'Invalid action';
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
