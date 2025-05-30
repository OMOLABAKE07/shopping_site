<?php
// Define root directory
define('ROOT_DIR', dirname(__DIR__));

// Define paths to important directories
define('CLASSES_PATH', ROOT_DIR . '/classes');
define('INCLUDES_PATH', ROOT_DIR . '/includes');
define('PAGES_PATH', ROOT_DIR . '/pages');
define('ASSETS_PATH', ROOT_DIR . '/assets');
define('CONFIG_PATH', ROOT_DIR . '/config');

// Define URL paths
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Get the root path by removing 'pages' from the script name if it exists
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$script_name = str_replace('/pages', '', $script_name);
$script_name = $script_name === '/' ? '' : $script_name;

// Build base URL without trailing slash
$base_url = rtrim($protocol . $host . $script_name, '/');

// Define site name
define('SITE_NAME', 'Shopping Site');

// Add the paths to PHP's include path
set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_DIR);

// Autoload classes
spl_autoload_register(function ($class) {
    $file = CLASSES_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Ensure Model class is loaded (it's a dependency)
if (!class_exists('Model')) {
    require_once CLASSES_PATH . '/Model.php';
}

// Helper function to generate URLs
function url($path = '') {
    // If the path starts with a '/', treat it as relative to the site root
    if (strpos($path, '/') === 0) {
        return SITE_URL . $path;
    } else {
        // Otherwise, treat it as relative to the current script's directory base URL
        $path = ltrim($path, '/');
        // Re-calculate base_url based on current script for relative paths if needed, 
        // but for simplicity and consistency with previous fixes, let's assume SITE_URL 
        // is the base we want unless the path is root-relative.
        // However, the core issue is BASE_URL is affected by the current script dir.
        // Let's redefine BASE_URL to always be the root for URL generation.
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $root_base_url = rtrim($protocol . $host, '/');

        return $root_base_url . ($path ? '/' . $path : '');
    }
}

// Helper function to generate asset URLs
function asset_url($path = '') {
    $path = ltrim($path, '/');
    // Use the root-based BASE_URL for assets
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $root_base_url = rtrim($protocol . $host, '/');
    return $root_base_url . '/assets' . ($path ? '/' . $path : '');
}

// Helper function to redirect
function redirect($path = '') {
    header('Location: ' . url($path));
    exit;
}

// Helper function to check if current URL matches
function is_current_url($path) {
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    // Also trim leading slash from path for consistent comparison
    $check_path = trim($path, '/');
    return $current_path === $check_path;
}

// Redefine BASE_URL and SITE_URL to always be the root
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$root_base_url = rtrim($protocol . $host, '/');
define('BASE_URL', $root_base_url);
define('SITE_URL', $root_base_url);
define('ASSETS_URL', $root_base_url . '/assets');

// ... existing code ... 