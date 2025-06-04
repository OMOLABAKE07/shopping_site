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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>

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
    <link rel="icon" type="image/x-icon" href="<?php echo ASSETS_URL; ?>/img/favicon.ico">
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
                            <?php
                            $cartModel = new Cart();
                            $cartCount = $cartModel->getCartCount($current_user['id']);
                            $cartItems = $cartModel->getCartItems($current_user['id']);
                            $cartTotal = $cartModel->getCartTotal($current_user['id']);
                            ?>
                            <li class="dropdown cart-dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="cart-count"><?php echo $cartCount; ?></span>
                                </a>
                                <div class="dropdown-menu cart-dropdown-menu">
                                    <?php if (empty($cartItems)): ?>
                                        <div class="cart-empty">
                                            <p>Your cart is empty</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="cart-items">
                                            <?php foreach ($cartItems as $item): ?>
                                                <?php $displayPrice = $item['sale_price'] ?? $item['price']; ?>
                                                <div class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                                                    <div class="cart-item-image">
                                                        <img src="<?php echo ASSETS_URL; ?>/img/<?php echo $item['image_url']; ?>" 
                                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                    </div>
                                                    <div class="cart-item-details">
                                                        <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                                        <p class="cart-item-price">
                                                            <?php if ($item['sale_price']): ?>
                                                                <span class="text-muted text-decoration-line-through">$<?php echo number_format($item['price'], 2); ?></span>
                                                                <span class="text-danger">$<?php echo number_format($item['sale_price'], 2); ?></span>
                                                            <?php else: ?>
                                                                $<?php echo number_format($item['price'], 2); ?>
                                                            <?php endif; ?>
                                                            x <?php echo $item['quantity']; ?>
                                                        </p>
                                                    </div>
                                                    <button type="button" class="btn btn-link btn-sm remove-item-btn" data-cart-id="<?php echo $item['id']; ?>">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="cart-footer">
                                            <div class="cart-total">
                                                <strong>Total:</strong>
                                                <span>$<?php echo number_format($cartTotal, 2); ?></span>
                                            </div>
                                            <div class="cart-buttons">
                                                <a href="<?php echo BASE_URL; ?>/cart.php" class="btn btn-default btn-sm">View Cart</a>
                                                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary btn-sm">Checkout</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
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

<style>
/* Mini Cart Styles */
.cart-dropdown {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    min-width: 18px;
    text-align: center;
}

.cart-dropdown-menu {
    width: 320px;
    padding: 15px;
    right: 0;
    left: auto;
}

.cart-empty {
    text-align: center;
    padding: 20px;
    color: #666;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    flex: 1;
}

.cart-item-details h6 {
    margin: 0 0 5px;
    font-size: 14px;
    line-height: 1.2;
}

.cart-item-price {
    margin: 0;
    font-size: 12px;
}

.cart-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.cart-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.cart-buttons {
    display: flex;
    gap: 10px;
}

.cart-buttons .btn {
    flex: 1;
}

.remove-item-btn {
    color: #999;
    padding: 0;
    margin-left: 10px;
}

.remove-item-btn:hover {
    color: #e74c3c;
}

/* Ensure dropdown stays open on hover */
.cart-dropdown:hover .dropdown-menu {
    display: block;
}
</style>

<script>
// Add this to your existing jQuery ready function
$(document).ready(function() {
    // Handle remove item in mini-cart
    $('.cart-dropdown-menu .remove-item-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const cartId = $(this).data('cart-id');
        const cartItem = $(this).closest('.cart-item');
        
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: {
                action: 'remove_item',
                cart_id: cartId
            },
            success: function(response) {
                if (response.success) {
                    // Remove the item from mini-cart
                    cartItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update cart count
                        $('.cart-count').text(response.cart_count);
                        
                        // Update cart total
                        $('.cart-total span').text('$' + response.cart_total.toFixed(2));
                        
                        // If cart is empty, show empty message
                        if (response.cart_count === 0) {
                            $('.cart-dropdown-menu').html('<div class="cart-empty"><p>Your cart is empty</p></div>');
                        }
                    });
                    
                    // Show success message
                    showMessage('success', response.message);
                } else {
                    showMessage('error', response.message);
                }
            },
            error: function() {
                showMessage('error', 'An error occurred while removing the item.');
            }
        });
    });
    
    // Function to show messages (reuse from cart.php)
    function showMessage(type, message) {
        const alertDiv = $('<div>')
            .addClass(`alert alert-${type} alert-dismissible fade show`)
            .html(`
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            `);
        
        $('.container').prepend(alertDiv);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.alert('close');
        }, 5000);
    }
});
</script>

</body>
</html>
