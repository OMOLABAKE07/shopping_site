<?php
// Load configuration and autoloader first
require_once __DIR__ . '/config/paths.php';

// Start session if not already started
Session::start();

// Store the current session ID for cookie cleanup
$sessionId = session_id();

// Destroy the session data
Session::destroy();

// Clear the session cookie
if (isset($_COOKIE[session_name()])) {
    // Get cookie parameters
    $params = session_get_cookie_params();
    
    // Delete the cookie
    setcookie(
        session_name(), // Cookie name
        '',            // Empty value
        [
            'expires' => time() - 3600, // Expire in the past
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax' // Modern browsers require this
        ]
    );
}

// Set security headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');

// Set a flash message for the next page
Session::setFlash('success', 'You have been successfully logged out.');

// Redirect to home page with a timestamp to prevent caching
header('Location: ' . BASE_URL . '?logout=' . time());
exit; 