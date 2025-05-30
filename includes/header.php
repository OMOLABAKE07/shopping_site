<?php
// Ensure autoloader is loaded
if (!class_exists('Session')) {
    require_once __DIR__ . '/../config/paths.php';
}

Session::start();
$current_user = Session::getCurrentUser();

$datepicker_js = ASSETS_PATH . '/js/vendor/bootstrap-datepicker.min.js';
$datepicker_css = ASSETS_PATH . '/css/bootstrap-datepicker.min.css';

if (!file_exists($datepicker_js)) {
    error_log('Bootstrap datepicker JS file not found: ' . $datepicker_js);
}
if (!file_exists($datepicker_css)) {
    error_log('Bootstrap datepicker CSS file not found: ' . $datepicker_css);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 3 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-datepicker.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
</head>
<body>

<div class="header">
    <div class="container">
        <nav class="navbar navbar-inverse" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="nav-toggle" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo BASE_URL; ?>/" class="navbar-brand">Shopping Site</a>
                </div>
                <div class="collapse navbar-collapse" id="main-nav">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/pages/products.php">Products</a></li>
                        <?php if (Session::isLoggedIn()): ?>
                            <li><a href="<?php echo BASE_URL; ?>/cart.php">Cart</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <?php echo htmlspecialchars($current_user['username'] ?? 'User'); ?> <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo url('profile.php'); ?>">Profile</a></li>
                                    <li><a href="<?php echo url('orders.php'); ?>">Orders</a></li>
                                    <?php if (($current_user['role'] ?? '') === 'admin'): ?>
                                        <li><a href="<?php echo url('admin/'); ?>">Admin</a></li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo url('logout.php'); ?>">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo BASE_URL; ?>/pages/about-us.php">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

<?php if ($flash = Session::getFlash()): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $flash['message']; ?>
        </div>
    </div>
<?php endif; ?>

<!-- jQuery and Bootstrap JS (make sure order is correct) -->
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Bootstrap Datepicker -->
<script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap-datepicker.min.js"></script>

<script>
    (function($) {
        'use strict';
        $(document).ready(function() {
            // Datepicker
            if (typeof $.fn.datepicker !== 'undefined' && $('.datepicker').length > 0) {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: 'bottom auto'
                });
            }

            // Enable dropdown
            $('.dropdown-toggle').dropdown();
        });
    })(jQuery);
</script>

<!-- Optional Custom Scripts -->
<?php if (file_exists(__DIR__ . '/../assets/js/plugins.js')): ?>
    <script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
<?php endif; ?>

<?php if (file_exists(__DIR__ . '/../assets/js/datepicker.js')): ?>
    <script src="<?php echo ASSETS_URL; ?>/js/datepicker.js"></script>
<?php endif; ?>

<?php if (file_exists(__DIR__ . '/../assets/js/main.js')): ?>
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
<?php endif; ?>

</body>
</html>
