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

    public function getAll($params = []) {
        $category_id = $params['category_id'] ?? null;
        $search = $params['search'] ?? '';
        $sort = $params['sort'] ?? 'name_asc';
        $page = $params['page'] ?? 1;
        $per_page = $params['per_page'] ?? 12;
        $offset = ($page - 1) * $per_page;

        // Base query
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        $types = '';
        $values = [];

        // Add category filter
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $types .= 'i';
            $values[] = $category_id;
        }

        // Add search filter
        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $types .= 'ss';
            $searchTerm = "%{$search}%";
            $values[] = $searchTerm;
            $values[] = $searchTerm;
        }

        // Add sorting
        switch ($sort) {
            case 'name_desc':
                $sql .= " ORDER BY p.name DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            default: // name_asc
                $sql .= " ORDER BY p.name ASC";
        }

        // Add pagination
        $sql .= " LIMIT ? OFFSET ?";
        $types .= 'ii';
        $values[] = $per_page;
        $values[] = $offset;

        $stmt = $this->db->prepare($sql);
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getCount($params = []) {
        $category_id = $params['category_id'] ?? null;
        $search = $params['search'] ?? '';

        // Base query
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} p 
                WHERE p.status = 'active'";
        $types = '';
        $values = [];

        // Add category filter
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $types .= 'i';
            $values[] = $category_id;
        }

        // Add search filter
        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $types .= 'ss';
            $searchTerm = "%{$search}%";
            $values[] = $searchTerm;
            $values[] = $searchTerm;
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
