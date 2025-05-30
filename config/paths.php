<?php
// Define the root directory of the application
define('ROOT_PATH', dirname(__DIR__));

// Define paths to important directories
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Define URL paths
define('BASE_URL', 'http://localhost:8052');
define('ASSETS_URL', BASE_URL . '/assets');
define('SITE_URL', BASE_URL);

// Define site name
define('SITE_NAME', 'Shopping Site');

// Add the paths to PHP's include path
set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH);

// Autoload classes
spl_autoload_register(function ($class) {
    // Convert class name to filename
    $filename = str_replace('\\', '/', $class) . '.php';
    
    // Try direct path first
    $file = CLASSES_PATH . '/' . $filename;
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    // Try lowercase version
    $file = CLASSES_PATH . '/' . strtolower($filename);
    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    // Try in subdirectories
    $subdirectories = ['Model', 'User', 'Session', 'Form', 'Cart', 'Category', 'Order', 'Product', 'Review'];
    foreach ($subdirectories as $subdir) {
        $file = CLASSES_PATH . '/' . $subdir . '/' . $filename;
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        
        // Try lowercase version in subdirectories
        $file = CLASSES_PATH . '/' . strtolower($subdir) . '/' . strtolower($filename);
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

    return false;
}); 

// Ensure Model class is loaded as it's a dependency
if (!class_exists('Model')) {
    require_once CLASSES_PATH . '/Model.php';
} 