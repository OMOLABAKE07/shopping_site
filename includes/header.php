<?php
// Ensure autoloader is loaded
if (!class_exists('Session')) {
    require_once __DIR__ . '/../config/paths.php';
}

Session::start();
$current_user = Session::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?></title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
</head>
<body>
    <div class="header">
        <div class="container">
            <nav class="navbar navbar-inverse" role="navigation">
                <div class="navbar-header">
                    <button type="button" id="nav-toggle" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo BASE_URL; ?>/" class="navbar-brand scroll-top">Shopping Site</a>
                </div>
                <div id="main-nav" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav pull-right">
                        <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php">Products</a></li>
                         <?php if (Session::isLoggedIn()): ?>
                            <li><a href="<?php echo BASE_URL; ?>/cart.php">Cart</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <?php echo htmlspecialchars(Session::getCurrentUser()['username'] ?? ''); ?> <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo BASE_URL; ?>/profile.php">Profile</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>/orders.php">Orders</a></li>
                                     <?php if (Session::isLoggedIn() && Session::getCurrentUser()['role'] === 'admin'): ?>
                                        <li><a href="<?php echo BASE_URL; ?>/admin/">Admin</a></li>
                                     <?php endif; ?>
                                    <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo BASE_URL; ?>/about-us.php">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact Us</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <?php if ($flash = Session::getFlash()): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- jQuery (CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    
    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>window.bootstrap || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"><\/script>')</script>

    <!-- Custom JS - Only load if files exist -->
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