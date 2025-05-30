<?php
require_once __DIR__ . '/../config/paths.php';

// Start session
Session::start();

// Clear all session data
Session::destroy();

// Redirect to home page
header('Location: ' . BASE_URL . '/');
exit; 