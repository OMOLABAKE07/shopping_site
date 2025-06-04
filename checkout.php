<?php
// Load configuration and autoloader first
require_once __DIR__ . '/config/paths.php';

// Start session and require login
Session::start();
Session::requireLogin();

// Get current user
$currentUser = Session::getCurrentUser();
if (!$currentUser) {
    Session::destroy();
    Session::setFlash('error', 'Your session has expired. Please login again.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Get cart items and total
$cartModel = new Cart();
$cartItems = $cartModel->getCartItems($currentUser['id']);
$cartTotal = $cartModel->getCartTotal($currentUser['id']);

// If cart is empty, redirect to cart page
if (empty($cartItems)) {
    Session::setFlash('error', 'Your cart is empty. Please add items before checkout.');
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address1 = $_POST['address1'] ?? '';
    $address2 = $_POST['address2'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $country = $_POST['country'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    // Validate required fields
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($phone)) $errors[] = 'Phone is required';
    if (empty($address1)) $errors[] = 'Address is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($state)) $errors[] = 'State is required';
    if (empty($zip)) $errors[] = 'Zip code is required';
    if (empty($country)) $errors[] = 'Country is required';
    if (empty($payment_method)) $errors[] = 'Payment method is required';
    if (!$terms) $errors[] = 'You must agree to the terms and conditions';

    if (empty($errors)) {
        try {
            // Create shipping address string
            $shippingAddress = implode(', ', array_filter([
                $address1,
                $address2,
                $city,
                $state,
                $zip,
                $country
            ]));

            // Create order
            $orderModel = new Order();
            $orderId = $orderModel->createOrder(
                $currentUser['id'],
                $cartItems,
                $shippingAddress,
                $shippingAddress, // Using same address for billing
                $payment_method
            );

            if ($orderId) {
                Session::setFlash('success', 'Your order has been placed successfully! Order ID: ' . $orderId);
                header('Location: ' . BASE_URL . '/order-confirmation.php?id=' . $orderId);
                exit;
            } else {
                $errors[] = 'Failed to create order. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo SITE_NAME; ?> - Checkout</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS Files -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/fontAwesome.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
        <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">

        <!-- Modernizr -->
        <script src="<?php echo ASSETS_URL; ?>/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

<body>
    <section class="banner banner-secondary" id="top" style="background-image: url('<?php echo ASSETS_URL; ?>/img/banner-image-1-1920x300.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>Checkout</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-4 col-md-5 pull-right">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Order Summary</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <em>Subtotal</em>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <em>Shipping</em>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <strong>Free</strong>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <em>Tax (8%)</em>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <strong>$<?php echo number_format($cartTotal * 0.08, 2); ?></strong>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <strong>Total</strong>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <strong>$<?php echo number_format($cartTotal * 1.08, 2); ?></strong>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-7">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Title:</label>
                                        <select name="title" class="form-control" required>
                                            <option value="">-- Choose --</option>
                                            <option value="dr">Dr.</option>
                                            <option value="miss">Miss</option>
                                            <option value="mr">Mr.</option>
                                            <option value="mrs">Mrs.</option>
                                            <option value="ms">Ms.</option>
                                            <option value="other">Other</option>
                                            <option value="prof">Prof.</option>
                                            <option value="rev">Rev.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Name:</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Email:</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Phone:</label>
                                        <input type="tel" name="phone" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Address 1:</label>
                                        <input type="text" name="address1" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Address 2:</label>
                                        <input type="text" name="address2" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">City:</label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">State:</label>
                                        <input type="text" name="state" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Zip:</label>
                                        <input type="text" name="zip" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Country:</label>
                                        <select name="country" class="form-control" required>
                                            <option value="">-- Choose --</option>
                                            <option value="us">United States</option>
                                            <option value="uk">United Kingdom</option>
                                            <option value="ca">Canada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label">Payment Method:</label>
                                        <select name="payment_method" class="form-control" required>
                                            <option value="">-- Choose --</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">
                                    <input type="checkbox" name="terms" value="1" required>
                                    I agree with the <a href="<?php echo BASE_URL; ?>/terms.php" target="_blank">Terms & Conditions</a>
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Place Order</button>
                                <a href="<?php echo BASE_URL; ?>/cart.php" class="btn btn-default">Back to Cart</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    </script>

<?php include __DIR__ . '/includes/footer.php'; ?> 