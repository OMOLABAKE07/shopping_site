<?php
// Load configuration and autoloader
require_once __DIR__ . '/config/paths.php';

// Start session and check if user is logged in
Session::start();
if (!Session::isLoggedIn()) {
    redirect('login.php'); // Redirect to login if not logged in
}

// Get current user data
$current_user = Session::getCurrentUser();

// Get user's orders
$orderModel = new Order();
$userOrders = $orderModel->getUserOrders($current_user['id']);

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?> - My Orders</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
</head>
<body>

<div class="wrap">
    <?php require_once __DIR__ . '/includes/header.php'; ?>

    <section class="banner banner-secondary" id="top" style="background-image: url('<?php echo ASSETS_URL; ?>/img/banner-image-1-1920x300.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>My Orders</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2>My Order History</h2>
                        <?php if (!empty($userOrders)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userOrders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                                            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                            <td>$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                                            <td><a href="<?php echo url('order-details.php?id=' . $order['id']); ?>" class="btn btn-primary btn-sm">View Details</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>You have not placed any orders yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</div>

<!-- JavaScript Files -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

</body>
</html> 