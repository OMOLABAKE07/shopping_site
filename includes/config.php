<?php
// Database configuration
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root'); // Change if you have a different username
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');     // Change if you have a password
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'shopping_site');
}

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    // Select the database
    mysqli_select_db($conn, DB_NAME);
} else {
    die("Error creating database: " . mysqli_error($conn));
}

// Site configuration
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Your Shopping Site');
}

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/shopping_site'); // Change this to match your setup
}
?>
