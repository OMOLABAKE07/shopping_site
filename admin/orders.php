<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Require admin access
Session::start();

// Check if user is logged in and is an admin
if (!Session::isLoggedIn() || (Session::getCurrentUser()['role'] ?? '') !== 'admin') {
    // Redirect to login page or an access denied page
    redirect('login.php'); // Or a dedicated access denied page
}

$orderModel = new Order();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $orderId = $_POST['order_id'];
        $status = $_POST['status'];
        
        if ($orderModel->updateOrderStatus($orderId, $status)) {
            Session::setFlash('success', 'Order status updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update order status');
        }
    }
}

// Get order details if ID is provided
$orderDetails = null;
if (isset($_GET['id'])) {
    $orderDetails = $orderModel->getOrderDetails($_GET['id']);
}

// Get all orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$orders = $orderModel->getRecentOrders();
$totalOrders = count($orders);
$totalPages = ceil($totalOrders / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?> - Admin Orders</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa fa-shopping-bag"></i> Products</a></li>
                <li class="active"><a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
                <li><a href="categories.php"><i class="fa fa-tags"></i> Categories</a></li>
                <li><a href="users.php"><i class="fa fa-users"></i> Users</a></li>
                <li><a href="reviews.php"><i class="fa fa-star"></i> Reviews</a></li>
                <li><a href="<?php echo BASE_URL; ?>/"><i class="fa fa-home"></i> View Site</a></li>
                <li><a href="<?php echo url('logout.php'); ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Orders</h1>
            </header>

            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <?php if ($orderDetails): ?>
                <!-- Order Details View -->
                <div class="card">
                    <div class="card-header">
                        <h2>Order #<?php echo $orderDetails['id']; ?></h2>
                        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn-secondary">Back to Orders</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Customer Information</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($orderDetails['username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h3>Order Information</h3>
                                <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($orderDetails['created_at'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?php echo $orderDetails['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo Form::generateCSRFToken(); ?>">
                                        <select name="status" class="form-control d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $orderDetails['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $orderDetails['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $orderDetails['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $orderDetails['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $orderDetails['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>Shipping Address</h3>
                                <p><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h3>Billing Address</h3>
                                <p><?php echo nl2br(htmlspecialchars($orderDetails['billing_address'])); ?></p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h3>Order Items</h3>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderDetails['items'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                            <td><strong>$<?php echo number_format($orderDetails['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List View -->
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($orders, $offset, $limit) as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/admin.js"></script>
</body>
</html>
