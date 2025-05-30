<?php
require_once 'Model.php';

class Cart extends Model {
    protected $table = 'cart';

    public function __construct() {
        parent::__construct();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if product already exists in cart
        $existingItem = $this->where('user_id = ? AND product_id = ?', [$userId, $productId]);
        
        if (!empty($existingItem)) {
            // Update quantity if product exists
            $newQuantity = $existingItem[0]['quantity'] + $quantity;
            return $this->update($existingItem[0]['id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item to cart
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateQuantity($cartId, $quantity) {
        if ($quantity <= 0) {
            return $this->delete($cartId);
        }
        return $this->update($cartId, ['quantity' => $quantity]);
    }

    public function removeFromCart($cartId) {
        return $this->delete($cartId);
    }

    public function getCartItems($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.sale_price, p.image_url, p.stock
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND p.status = 'active'
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function getCartTotal($userId) {
        $items = $this->getCartItems($userId);
        $total = 0;
        foreach ($items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }

    public function clearCart($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        return $stmt->execute();
    }

    public function getCartCount($userId) {
        $sql = "SELECT SUM(quantity) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
}
