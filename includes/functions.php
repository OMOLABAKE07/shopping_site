// includes/functions.php
<?php
// Sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Get current page name
function get_current_page() {
    return basename($_SERVER['PHP_SELF']);
}
?>