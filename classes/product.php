<?php
require_once 'Model.php';

class Product extends Model {
    protected $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function getFeaturedProducts($limit = 4) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.featured = 1 AND p.status = 'active' 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getProductsByCategory($categoryId, $limit = 12, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.status = 'active' 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $categoryId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function searchProducts($query, $limit = 12, $offset = 0) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.name LIKE ? OR p.description LIKE ?) 
                AND p.status = 'active' 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssii', $searchTerm, $searchTerm, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getProductWithImages($productId) {
        $sql = "SELECT p.*, c.name as category_name,
                GROUP_CONCAT(pi.image_url) as images
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id
                WHERE p.id = ?
                GROUP BY p.id";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product && $product['images']) {
            $product['images'] = explode(',', $product['images']);
        } else {
            $product['images'] = [];
        }
        
        return $product;
    }

    public function updateStock($productId, $quantity) {
        $sql = "UPDATE {$this->table} 
                SET stock = stock - ? 
                WHERE id = ? AND stock >= ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $quantity, $productId, $quantity);
        return $stmt->execute();
    }

    public function getProductReviews($productId) {
        $sql = "SELECT r.*, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }
}
