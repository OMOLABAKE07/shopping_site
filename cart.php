<?php
// Load configuration and autoloader first
require_once __DIR__ . '/config/paths.php';

// Start session and require login BEFORE accessing cart
Session::start();
Session::requireLogin(); // This will redirect to login if not logged in

// Get current user and verify we have a valid user
$currentUser = Session::getCurrentUser();
if (!$currentUser) {
    // If somehow we don't have a valid user after requireLogin(),
    // destroy the session and redirect to login
    Session::destroy();
    Session::setFlash('error', 'Your session has expired. Please login again.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Now we can safely use the user ID
$cartModel = new Cart();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Basic CSRF check (you should implement a more robust one)
        // if (!isset($_POST['csrf_token']) || !Form::verifyCSRFToken($_POST['csrf_token'])) {
        //     Session::setFlash('error', 'Invalid request.');
        //     header('Location: ' . BASE_URL . '/cart.php');
        //     exit;
        // }

        switch ($_POST['action']) {
            case 'update_quantity':
                if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
                    $cartId = (int)$_POST['cart_id'];
                    $quantity = (int)$_POST['quantity'];
                    if ($cartModel->updateQuantity($cartId, $quantity)) {
                        Session::setFlash('success', 'Cart updated successfully.');
                    } else {
                         Session::setFlash('error', 'Failed to update cart.');
                    }
                }
                break;

            case 'remove_item':
                 if (isset($_POST['cart_id'])) {
                    $cartId = (int)$_POST['cart_id'];
                    if ($cartModel->removeFromCart($cartId)){
                         Session::setFlash('success', 'Item removed from cart.');
                    } else {
                         Session::setFlash('error', 'Failed to remove item.');
                    }
                }
                break;

            case 'clear_cart':
                if ($cartModel->clearCart($currentUser['id'])){
                    Session::setFlash('success', 'Cart cleared successfully.');
                } else {
                    Session::setFlash('error', 'Failed to clear cart.');
                }
                break;
        }

        // Redirect to prevent form resubmission
        header('Location: ' . BASE_URL . '/cart.php');
        exit;
    }
}

// Get cart items and total for display
$cartItems = $cartModel->getCartItems($currentUser['id']);
$cartTotal = $cartModel->getCartTotal($currentUser['id']);

// Include header and footer
include INCLUDES_PATH . '/header.php';
?>

<main>
    <section class="featured-places">
        <div class="container">
            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <h2>Shopping Cart</h2>

            <?php if (empty($cartItems)): ?>
                <div class="text-center">
                    <p>Your cart is empty.</p>
                    <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo ASSETS_URL; ?>/img/<?php echo $item['image_url']; ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="img-thumbnail"
                                                         style="width: 80px; margin-right: 15px;">
                                                    <div>
                                                        <h5 class="mb-0">
                                                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $item['product_id']; ?>">
                                                                <?php echo htmlspecialchars($item['name']); ?>
                                                            </a>
                                                        </h5>
                                                        <?php if ($item['stock'] < 5): ?>
                                                            <span class="text-danger">
                                                                Only <?php echo $item['stock']; ?> left in stock!
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php $displayPrice = $item['sale_price'] ?? $item['price']; ?>
                                                <?php if ($item['sale_price']): ?>
                                                    <span class="text-muted text-decoration-line-through">$<?php echo number_format($item['price'], 2); ?></span><br>
                                                    <span class="text-danger">$<?php echo number_format($item['sale_price'], 2); ?></span>
                                                <?php else: ?>
                                                    $<?php echo number_format($item['price'], 2); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form action="" method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="update_quantity">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                    <!-- Add CSRF token here if using it -->
                                                    <div class="input-group" style="width: 120px;">
                                                        <input type="number" 
                                                               name="quantity" 
                                                               class="form-control" 
                                                               value="<?php echo $item['quantity']; ?>"
                                                               min="1"
                                                               max="<?php echo $item['stock']; ?>"
                                                               onchange="this.form.submit()">
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                $<?php echo number_format($displayPrice * $item['quantity'], 2); ?>
                                            </td>
                                            <td>
                                                <form action="" method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                     <!-- Add CSRF token here if using it -->
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                             <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="clear_cart">
                                 <!-- Add CSRF token here if using it -->
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear your cart?')">
                                    Clear Cart
                                </button>
                            </form>
                            <a href="<?php echo BASE_URL; ?>/" class="btn btn-secondary">Continue Shopping</a>
                        </div>
                    </div>

                    <div class="col-md-4">
                         <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Subtotal:</span>
                                    <span>$<?php echo number_format($cartTotal, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Shipping:</span>
                                    <span>Calculated at checkout</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary w-100">
                                    Proceed to Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?> 