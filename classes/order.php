<?php
require_once 'Model.php';

class Order extends Model {
    protected $table = 'orders';

    public function __construct() {
        parent::__construct();
    }

    public function createOrder($userId, $cartItems, $shippingAddress, $billingAddress, $paymentMethod) {
        $this->db->beginTransaction();
        
        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $totalAmount += $price * $item['quantity'];
            }
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'payment_method' => $paymentMethod,
                'status' => 'pending'
            ];
            
            $orderId = $this->create($orderData);
            
            if (!$orderId) {
                throw new Exception("Failed to create order");
            }
            
            // Create order items
            $productModel = new Product();
            foreach ($cartItems as $item) {
                // Check stock
                if (!$productModel->updateStock($item['product_id'], $item['quantity'])) {
                    throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                }
                
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['sale_price'] ?? $item['price']
                ];
                
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('iiid', 
                    $orderItemData['order_id'],
                    $orderItemData['product_id'],
                    $orderItemData['quantity'],
                    $orderItemData['price']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create order item");
                }
            }
            
            // Clear cart
            $cartModel = new Cart();
            $cartModel->clearCart($userId);
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getOrderDetails($orderId) {
        $sql = "SELECT o.*, u.username, u.email,
                GROUP_CONCAT(
                    CONCAT(oi.product_id, ':', oi.quantity, ':', oi.price)
                    SEPARATOR '|'
                ) as items
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.id = ?
                GROUP BY o.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if ($order && $order['items']) {
            $items = [];
            $itemStrings = explode('|', $order['items']);
            foreach ($itemStrings as $itemString) {
                list($productId, $quantity, $price) = explode(':', $itemString);
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
            $order['items'] = $items;
        } else {
            $order['items'] = [];
        }
        
        return $order;
    }

    public function updateOrderStatus($orderId, $status) {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        return $this->update($orderId, ['status' => $status]);
    }

    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT o.*, 
                COUNT(oi.id) as total_items,
                SUM(oi.quantity) as total_quantity
                FROM {$this->table} o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getRecentOrders($limit = 10) {
        $sql = "SELECT o.*, u.username, u.email,
                COUNT(oi.id) as total_items
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }
}
