<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

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

// Include header and footer
include INCLUDES_PATH . '/header.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update cart item quantity via AJAX
    function updateCartItem(cartId, quantity) {
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
                // Update the UI
                updateCartUI(data);
                // Show success message
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
                // Remove the item row from the table
                const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
                if (row) {
                    row.remove();
                }
                // Update the UI
                updateCartUI(data);
                // Show success message
                showMessage('success', data.message);
                // If cart is empty, reload the page to show empty cart message
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
                // Reload the page to show empty cart
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
        // Update cart count in header
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = data.cart_count;
        }

        // Update cart total
        const cartTotal = document.querySelector('.cart-total');
        if (cartTotal) {
            cartTotal.textContent = '$' + data.cart_total.toFixed(2);
        }

        // Update cart dropdown items
        const cartDropdown = document.querySelector('.cart-dropdown-menu .cart-items');
        if (cartDropdown && data.items) {
            if (data.items.length === 0) {
                cartDropdown.innerHTML = '<div class="cart-empty"><p>Your cart is empty</p></div>';
            } else {
                cartDropdown.innerHTML = data.items.map(item => {
                    const displayPrice = item.sale_price || item.price;
                    return `
                        <div class="cart-item" data-cart-id="${item.id}">
                            <div class="cart-item-image">
                                <img src="${ASSETS_URL}/img/${item.image_url}" alt="${item.name}">
                            </div>
                            <div class="cart-item-details">
                                <h6>${item.name}</h6>
                                <p>$${displayPrice.toFixed(2)} x ${item.quantity}</p>
                            </div>
                            <button type="button" class="remove-item" onclick="removeCartItem(${item.id})">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    `;
                }).join('');
            }
        }

        // Update cart table if it exists
        const cartTable = document.querySelector('#cart-table tbody');
        if (cartTable && data.items) {
            if (data.items.length === 0) {
                cartTable.innerHTML = '<tr><td colspan="5" class="text-center">Your cart is empty</td></tr>';
                // Hide cart actions if cart is empty
                const cartActions = document.querySelector('.cart-actions');
                if (cartActions) cartActions.style.display = 'none';
            } else {
                cartTable.innerHTML = data.items.map(item => {
                    const displayPrice = item.sale_price || item.price;
                    return `
                        <tr data-cart-id="${item.id}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${ASSETS_URL}/img/${item.image_url}" 
                                         alt="${item.name}"
                                         class="img-thumbnail mr-3"
                                         style="width: 80px;">
                                    <div>
                                        <h5 class="mb-0">${item.name}</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                ${item.sale_price ? `
                                    <span class="text-muted text-decoration-line-through">$${item.price.toFixed(2)}</span><br>
                                    <span class="text-danger">$${item.sale_price.toFixed(2)}</span>
                                ` : `
                                    $${item.price.toFixed(2)}
                                `}
                            </td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm"
                                            onclick="updateCartItem(${item.id}, ${item.quantity - 1})">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           class="form-control form-control-sm text-center" 
                                           value="${item.quantity}"
                                           min="1"
                                           max="${item.stock}"
                                           onchange="updateCartItem(${item.id}, this.value)">
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm"
                                            onclick="updateCartItem(${item.id}, ${item.quantity + 1})">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td>$${(displayPrice * item.quantity).toFixed(2)}</td>
                            <td>
                                <button type="button" 
                                        class="btn btn-primary btn-sm"
                                        onclick="saveForLater(${item.id})">
                                    <i class="fa fa-bookmark"></i> Save
                                </button>
                                <button type="button" 
                                        class="btn btn-danger btn-sm"
                                        onclick="removeCartItem(${item.id})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
                // Show cart actions if cart has items
                const cartActions = document.querySelector('.cart-actions');
                if (cartActions) cartActions.style.display = 'block';
            }
        }
    }

    // Function to show messages
    function showMessage(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto dismiss after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Add event listeners to quantity inputs
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const quantity = parseInt(this.value);
            const maxStock = parseInt(this.dataset.maxStock);
            
            if (quantity > maxStock) {
                this.value = maxStock;
                showMessage('warning', `Only ${maxStock} items available in stock.`);
                quantity = maxStock;
            }
            
            updateCartItem(cartId, quantity);
        });
    });

    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-item-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cartId = this.dataset.cartId;
            removeCartItem(cartId);
        });
    });

    // Add event listener to clear cart button
    const clearCartBtn = document.querySelector('.clear-cart-btn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            clearCart();
        });
    }

    // Add to your existing JavaScript
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
                
                // Update saved items section if it exists
                if (data.saved_items) {
                    updateSavedItems(data.saved_items);
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
                
                // Update saved items section if it exists
                if (data.saved_items) {
                    updateSavedItems(data.saved_items);
                }
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', 'An error occurred while moving the item to cart.');
        });
    }

    function removeSavedItem(savedItemId) {
        if (!confirm('Are you sure you want to remove this item from saved items?')) {
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
                // Remove the item from saved items table
                const row = document.querySelector(`tr[data-saved-item-id="${savedItemId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update saved items section
                updateSavedItems(data.saved_items);
                
                // Show success message
                showMessage('success', data.message);
                
                // If no saved items left, reload the page
                if (data.saved_items.length === 0) {
                    window.location.reload();
                }
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', 'An error occurred while removing the saved item.');
        });
    }

    function updateSavedItems(items) {
        const savedItemsTable = document.querySelector('#saved-items-table tbody');
        if (!savedItemsTable) return;

        if (items.length === 0) {
            savedItemsTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No saved items</td>
                </tr>
            `;
            return;
        }

        savedItemsTable.innerHTML = items.map(item => {
            const displayPrice = item.sale_price || item.price;
            return `
                <tr data-saved-item-id="${item.id}">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${ASSETS_URL}/img/${item.image_url}" 
                                 alt="${item.name}"
                                 class="img-thumbnail"
                                 style="width: 80px; margin-right: 15px;">
                            <div>
                                <h5 class="mb-0">
                                    <a href="${BASE_URL}/product-details.php?id=${item.product_id}">
                                        ${item.name}
                                    </a>
                                </h5>
                                ${item.stock < 5 ? `
                                    <span class="text-danger">
                                        Only ${item.stock} left in stock!
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                    <td>
                        ${item.sale_price ? `
                            <span class="text-muted text-decoration-line-through">$${item.price.toFixed(2)}</span><br>
                            <span class="text-danger">$${item.sale_price.toFixed(2)}</span>
                        ` : `
                            $${item.price.toFixed(2)}
                        `}
                    </td>
                    <td>${item.quantity}</td>
                    <td>
                        $${(displayPrice * item.quantity).toFixed(2)}
                    </td>
                    <td>
                        <button type="button" 
                                class="btn btn-primary btn-sm move-to-cart-btn"
                                onclick="moveToCart(${item.id})">
                            <i class="fa fa-shopping-cart"></i> Move to Cart
                        </button>
                        <button type="button" 
                                class="btn btn-danger btn-sm remove-saved-item-btn"
                                onclick="removeSavedItem(${item.id})">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }
});
</script>

<main>
    <section class="featured-places">
        <div class="container">
            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <h2>Shopping Cart</h2>

            <?php if (empty($cartItems) && empty($savedItems)): ?>
                <div class="text-center">
                    <p>Your cart is empty.</p>
                    <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php if (!empty($cartItems)): ?>
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
                                            <tr data-cart-id="<?php echo $item['id']; ?>">
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
                                                        <div class="input-group" style="width: 120px;">
                                                            <input type="number" 
                                                                   name="quantity" 
                                                                   class="form-control quantity-input" 
                                                                   data-cart-id="<?php echo $item['id']; ?>"
                                                                   data-max-stock="<?php echo $item['stock']; ?>"
                                                                   value="<?php echo $item['quantity']; ?>"
                                                                   min="1"
                                                                   max="<?php echo $item['stock']; ?>">
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="item-total" data-cart-id="<?php echo $item['id']; ?>">
                                                    $<?php echo number_format($displayPrice * $item['quantity'], 2); ?>
                                                </td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-primary btn-sm save-for-later-btn"
                                                            onclick="saveForLater(<?php echo $item['id']; ?>)">
                                                        <i class="fa fa-bookmark"></i> Save for Later
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm remove-item-btn"
                                                            data-cart-id="<?php echo $item['id']; ?>">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="clear_cart">
                                    <button type="button" class="btn btn-danger clear-cart-btn">
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
                                        <span class="cart-total">$<?php echo number_format($cartTotal, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Shipping:</span>
                                        <span>Calculated at checkout</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total:</strong>
                                        <strong class="cart-total">$<?php echo number_format($cartTotal, 2); ?></strong>
                                    </div>
                                    <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary w-100">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($savedItems)): ?>
                    <div class="mt-5">
                        <h3>Saved for Later</h3>
                        <div class="table-responsive">
                            <table id="saved-items-table" class="table table-hover">
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
                                        <?php $displayPrice = $item['sale_price'] ?? $item['price']; ?>
                                        <tr data-saved-item-id="<?php echo $item['id']; ?>">
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
                                                <?php if ($item['sale_price']): ?>
                                                    <span class="text-muted text-decoration-line-through">$<?php echo number_format($item['price'], 2); ?></span><br>
                                                    <span class="text-danger">$<?php echo number_format($item['sale_price'], 2); ?></span>
                                                <?php else: ?>
                                                    $<?php echo number_format($item['price'], 2); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($displayPrice * $item['quantity'], 2); ?></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm move-to-cart-btn"
                                                        onclick="moveToCart(<?php echo $item['id']; ?>)">
                                                    <i class="fa fa-shopping-cart"></i> Move to Cart
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm remove-saved-item-btn"
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
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?> 