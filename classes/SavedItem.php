<?php
require_once 'Model.php';

class SavedItem extends Model {
    protected $table = 'saved_items';

    public function __construct() {
        parent::__construct();
    }

    public function saveForLater($userId, $productId, $quantity = 1) {
        // Check if product already exists in saved items
        $existingItem = $this->where('user_id = ? AND product_id = ?', [$userId, $productId]);
        
        if (!empty($existingItem)) {
            // Update quantity if product exists
            $newQuantity = $existingItem[0]['quantity'] + $quantity;
            return $this->update($existingItem[0]['id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item to saved items
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function moveToCart($savedItemId, $userId) {
        // Get saved item details
        $savedItem = $this->find($savedItemId);
        if (!$savedItem || $savedItem['user_id'] !== $userId) {
            return false;
        }

        // Add to cart
        $cartModel = new Cart();
        $success = $cartModel->addToCart($userId, $savedItem['product_id'], $savedItem['quantity']);

        if ($success) {
            // Remove from saved items
            return $this->delete($savedItemId);
        }

        return false;
    }

    public function getSavedItems($userId) {
        $sql = "SELECT s.*, p.name, p.price, p.sale_price, p.image_url, p.stock
                FROM {$this->table} s
                JOIN products p ON s.product_id = p.id
                WHERE s.user_id = ? AND p.status = 'active'
                ORDER BY s.created_at DESC";
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

    public function removeSavedItem($savedItemId, $userId) {
        $savedItem = $this->find($savedItemId);
        if (!$savedItem || $savedItem['user_id'] !== $userId) {
            return false;
        }
        return $this->delete($savedItemId);
    }

    public function getSavedItemCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
} 