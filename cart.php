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

// Get cart items and total for display
$cartItems = $cartModel->getCartItems($currentUser['id']);
$cartTotal = $cartModel->getCartTotal($currentUser['id']);

// Get saved items
$savedItemModel = new SavedItem();
$savedItems = $savedItemModel->getSavedItems($currentUser['id']);

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $response = ['success' => false, 'message' => 'Invalid request.'];
        
        switch ($_POST['action']) {
            case 'update_quantity':
                if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
                    $cartId = (int)$_POST['cart_id'];
                    $quantity = (int)$_POST['quantity'];
                    
                    // Get current cart item to check stock
                    $cartItem = $cartModel->find($cartId);
                    if ($cartItem && $cartItem['user_id'] === $currentUser['id']) {
                        $productModel = new Product();
                        $product = $productModel->find($cartItem['product_id']);
                        
                        if ($product && $quantity <= $product['stock']) {
                            if ($cartModel->updateQuantity($cartId, $quantity)) {
                                $response = [
                                    'success' => true,
                                    'message' => 'Cart updated successfully.',
                                    'cart_total' => $cartModel->getCartTotal($currentUser['id']),
                                    'cart_count' => $cartModel->getCartCount($currentUser['id']),
                                    'items' => $cartModel->getCartItems($currentUser['id'])
                                ];
                            } else {
                                $response['message'] = 'Failed to update cart.';
                            }
                        } else {
                            $response['message'] = 'Requested quantity exceeds available stock.';
                        }
                    } else {
                        $response['message'] = 'Invalid cart item.';
                    }
                }
                break;

            case 'remove_item':
                if (isset($_POST['cart_id'])) {
                    $cartId = (int)$_POST['cart_id'];
                    $cartItem = $cartModel->find($cartId);
                    
                    if ($cartItem && $cartItem['user_id'] === $currentUser['id']) {
                        if ($cartModel->removeFromCart($cartId)) {
                            $response = [
                                'success' => true,
                                'message' => 'Item removed from cart.',
                                'cart_total' => $cartModel->getCartTotal($currentUser['id']),
                                'cart_count' => $cartModel->getCartCount($currentUser['id'])
                            ];
                        } else {
                            $response['message'] = 'Failed to remove item.';
                        }
                    } else {
                        $response['message'] = 'Invalid cart item.';
                    }
                }
                break;

            case 'clear_cart':
                if ($cartModel->clearCart($currentUser['id'])) {
                    $response = [
                        'success' => true,
                        'message' => 'Cart cleared successfully.',
                        'cart_total' => 0,
                        'cart_count' => 0
                    ];
                } else {
                    $response['message'] = 'Failed to clear cart.';
                }
                break;

            case 'save_for_later':
                if (isset($_POST['cart_id'])) {
                    $cartId = (int)$_POST['cart_id'];
                    $cartItem = $cartModel->find($cartId);
                    
                    if ($cartItem && $cartItem['user_id'] === $currentUser['id']) {
                        // Save to saved items
                        if ($savedItemModel->saveForLater($currentUser['id'], $cartItem['product_id'], $cartItem['quantity'])) {
                            // Remove from cart
                            if ($cartModel->removeFromCart($cartId)) {
                                $response = [
                                    'success' => true,
                                    'message' => 'Item saved for later.',
                                    'cart_total' => $cartModel->getCartTotal($currentUser['id']),
                                    'cart_count' => $cartModel->getCartCount($currentUser['id']),
                                    'saved_items' => $savedItemModel->getSavedItems($currentUser['id'])
                                ];
                            } else {
                                $response['message'] = 'Failed to remove item from cart.';
                            }
                        } else {
                            $response['message'] = 'Failed to save item for later.';
                        }
                    } else {
                        $response['message'] = 'Invalid cart item.';
                    }
                }
                break;

            case 'move_to_cart':
                if (isset($_POST['saved_item_id'])) {
                    $savedItemId = (int)$_POST['saved_item_id'];
                    if ($savedItemModel->moveToCart($savedItemId, $currentUser['id'])) {
                        $response = [
                            'success' => true,
                            'message' => 'Item moved to cart.',
                            'cart_total' => $cartModel->getCartTotal($currentUser['id']),
                            'cart_count' => $cartModel->getCartCount($currentUser['id']),
                            'saved_items' => $savedItemModel->getSavedItems($currentUser['id'])
                        ];
                    } else {
                        $response['message'] = 'Failed to move item to cart.';
                    }
                }
                break;

            case 'remove_saved_item':
                if (isset($_POST['saved_item_id'])) {
                    $savedItemId = (int)$_POST['saved_item_id'];
                    if ($savedItemModel->removeSavedItem($savedItemId, $currentUser['id'])) {
                        $response = [
                            'success' => true,
                            'message' => 'Item removed from saved items.',
                            'saved_items' => $savedItemModel->getSavedItems($currentUser['id'])
                        ];
                    } else {
                        $response['message'] = 'Failed to remove saved item.';
                    }
                }
                break;

            case 'add_to_cart':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $productId = (int)$_POST['product_id'];
                    $quantity = (int)$_POST['quantity'];
                    
                    // Validate product exists and is in stock
                    $productModel = new Product();
                    $product = $productModel->find($productId);
                    
                    if ($product && $product['status'] === 'active') {
                        if ($quantity <= $product['stock']) {
                            if ($cartModel->addToCart($currentUser['id'], $productId, $quantity)) {
                                $response = [
                                    'success' => true,
                                    'message' => 'Product added to cart successfully.',
                                    'cart_count' => $cartModel->getCartCount($currentUser['id'])
                                ];
                            } else {
                                $response['message'] = 'Failed to add product to cart.';
                            }
                        } else {
                            $response['message'] = 'Requested quantity exceeds available stock.';
                        }
                    } else {
                        $response['message'] = 'Product not found or no longer available.';
                    }
                }
                break;
        }

        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // For non-AJAX requests, set flash message and redirect
        Session::setFlash($response['success'] ? 'success' : 'error', $response['message']);
        header('Location: ' . BASE_URL . '/cart.php');
        exit;
    }
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section class="featured-places cart-section">
        <div class="container">
            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <h2 class="cart-title">Shopping Cart</h2>

            <?php if (empty($cartItems) && empty($savedItems)): ?>
                <div class="text-center empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php if (!empty($cartItems)): ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-hover cart-table">
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
                                            <tr data-cart-id="<?php echo $item['id']; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php 
                                                            $imagePath = $item['image'] ?? 'default-product.jpg';
                                                            if (strpos($imagePath, '/assets/img/') === 0) {
                                                                echo ASSETS_URL . substr($imagePath, 11); // Remove /assets/img/ prefix
                                                            } else {
                                                                echo ASSETS_URL . '/img/' . $imagePath;
                                                            }
                                                        ?>" 
                                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                             class="img-thumbnail mr-3 cart-item-image"
                                                             style="width: 80px;">
                                                        <div>
                                                            <h5 class="mb-0 cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                            <?php if (isset($item['options']) && $item['options']): ?>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($item['options']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cart-item-price">
                                                    <?php if ($item['sale_price']): ?>
                                                        <span class="text-muted text-decoration-line-through">
                                                            $<?php echo number_format($item['price'], 2); ?>
                                                        </span><br>
                                                        <span class="text-danger">
                                                            $<?php echo number_format($item['sale_price'], 2); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        $<?php echo number_format($item['price'], 2); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="input-group quantity-group" style="width: 120px;">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary btn-sm quantity-btn"
                                                                onclick="updateCartItem(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-center quantity-input" 
                                                               value="<?php echo $item['quantity']; ?>"
                                                               min="1"
                                                               max="<?php echo $item['stock']; ?>"
                                                               onchange="updateCartItem(<?php echo $item['id']; ?>, this.value)">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary btn-sm quantity-btn"
                                                                onclick="updateCartItem(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="item-total" data-cart-id="<?php echo $item['id']; ?>">
                                                    $<?php 
                                                        $price = $item['sale_price'] ?? $item['price'];
                                                        echo number_format($price * $item['quantity'], 2); 
                                                    ?>
                                                </td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm action-btn save-btn"
                                                            onclick="saveForLater(<?php echo $item['id']; ?>)">
                                                        <i class="fa fa-bookmark"></i> Save
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm action-btn remove-btn"
                                                            onclick="removeCartItem(<?php echo $item['id']; ?>)">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="button" 
                                        class="btn btn-danger clear-cart-btn"
                                        onclick="clearCart()">
                                    <i class="fa fa-trash"></i> Clear Cart
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card order-summary-card">
                                <div class="card-body">
                                    <h5 class="card-title">Order Summary</h5>
                                    <div class="summary-item d-flex justify-content-between mb-3">
                                        <span>Subtotal:</span>
                                        <span class="cart-total">$<?php echo number_format($cartTotal, 2); ?></span>
                                    </div>
                                    <div class="summary-item d-flex justify-content-between mb-3">
                                        <span>Shipping:</span>
                                        <span>Free</span>
                                    </div>
                                    <div class="summary-item d-flex justify-content-between mb-3">
                                        <span>Tax (8%):</span>
                                        <span>$<?php echo number_format($cartTotal * 0.08, 2); ?></span>
                                    </div>
                                    <hr>
                                    <div class="summary-item d-flex justify-content-between mb-3">
                                        <strong>Total:</strong>
                                        <strong>$<?php echo number_format($cartTotal * 1.08, 2); ?></strong>
                                    </div>
                                    <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary w-100 checkout-btn">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($savedItems)): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <h3 class="saved-title">Saved for Later</h3>
                            <div class="table-responsive">
                                <table class="table table-hover cart-table">
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
                                        <?php foreach ($savedItems as $item): ?>
                                            <tr data-saved-item-id="<?php echo $item['id']; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php 
                                                            $imagePath = $item['image'] ?? 'default-product.jpg';
                                                            if (strpos($imagePath, '/assets/img/') === 0) {
                                                                echo ASSETS_URL . substr($imagePath, 11); // Remove /assets/img/ prefix
                                                            } else {
                                                                echo ASSETS_URL . '/img/' . $imagePath;
                                                            }
                                                        ?>" 
                                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                             class="img-thumbnail mr-3 cart-item-image"
                                                             style="width: 80px;">
                                                        <div>
                                                            <h5 class="mb-0 cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                            <?php if (isset($item['options']) && $item['options']): ?>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($item['options']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cart-item-price">
                                                    <?php if ($item['sale_price']): ?>
                                                        <span class="text-muted text-decoration-line-through">
                                                            $<?php echo number_format($item['price'], 2); ?>
                                                        </span><br>
                                                        <span class="text-danger">
                                                            $<?php echo number_format($item['sale_price'], 2); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        $<?php echo number_format($item['price'], 2); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>
                                                    $<?php 
                                                        $price = $item['sale_price'] ?? $item['price'];
                                                        echo number_format($price * $item['quantity'], 2); 
                                                    ?>
                                                </td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-primary btn-sm action-btn move-to-cart-btn"
                                                            onclick="moveToCart(<?php echo $item['id']; ?>)">
                                                        <i class="fa fa-shopping-cart"></i> Move to Cart
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm action-btn remove-btn"
                                                            onclick="removeSavedItem(<?php echo $item['id']; ?>)">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>



<script>
// Function to update cart item quantity via AJAX
function updateCartItem(cartId, quantity) {
    if (quantity < 1) return;
    
    const formData = new FormData();
    formData.append('action', 'update_quantity');
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI(data);
            showMessage('success', data.message);
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while updating the cart.');
    });
}

// Function to remove cart item via AJAX
function removeCartItem(cartId) {
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'remove_item');
    formData.append('cart_id', cartId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
            if (row) {
                row.remove();
            }
            updateCartUI(data);
            showMessage('success', data.message);
            if (data.cart_count === 0) {
                window.location.reload();
            }
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while removing the item.');
    });
}

// Function to save item for later via AJAX
function saveForLater(cartId) {
    const formData = new FormData();
    formData.append('action', 'save_for_later');
    formData.append('cart_id', cartId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
            if (row) {
                row.remove();
            }
            updateCartUI(data);
            showMessage('success', data.message);
            if (data.cart_count === 0) {
                window.location.reload();
            }
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while saving the item.');
    });
}

// Function to move saved item to cart via AJAX
function moveToCart(savedItemId) {
    const formData = new FormData();
    formData.append('action', 'move_to_cart');
    formData.append('saved_item_id', savedItemId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-saved-item-id="${savedItemId}"]`);
            if (row) {
                row.remove();
            }
            updateCartUI(data);
            showMessage('success', data.message);
            window.location.reload();
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while moving the item to cart.');
    });
}

// Function to remove saved item via AJAX
function removeSavedItem(savedItemId) {
    if (!confirm('Are you sure you want to remove this saved item?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'remove_saved_item');
    formData.append('saved_item_id', savedItemId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-saved-item-id="${savedItemId}"]`);
            if (row) {
                row.remove();
            }
            showMessage('success', data.message);
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while removing the saved item.');
    });
}

// Function to clear cart via AJAX
function clearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'clear_cart');

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'An error occurred while clearing the cart.');
    });
}

// Function to update cart UI elements
function updateCartUI(data) {
    // Update cart total
    const totalElement = document.querySelector('.cart-total');
    if (totalElement) {
        totalElement.textContent = `$${data.cart_total.toFixed(2)}`;
    }

    // Update cart count in header if it exists
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = data.cart_count;
    }

    // Update individual item totals
    if (data.items) {
        data.items.forEach(item => {
            const itemTotalElement = document.querySelector(`.item-total[data-cart-id="${item.id}"]`);
            if (itemTotalElement) {
                const price = item.sale_price || item.price;
                itemTotalElement.textContent = `$${(price * item.quantity).toFixed(2)}`;
            }
        });
    }
}

// Function to show messages
function showMessage(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>