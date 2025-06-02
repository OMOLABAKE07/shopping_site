<?php
require_once 'Model.php';

class Product extends Model {
    protected $table = 'products';
    protected $fillable = [
        'name', 'description', 'price', 'sale_price', 'category_id',
        'stock', 'sku', 'image_url', 'status', 'featured'
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all products with their category information
     */
    public function getAllWithCategory() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.name";
        $result = $this->db->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    /**
     * Get a product by ID with its category information
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get products by category ID
     */
    public function getByCategory($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = ? AND status = 'active' ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    /**
     * Get featured products
     */
    public function getFeatured() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.featured = 1 AND p.status = 'active' 
                ORDER BY p.name";
        $result = $this->db->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    /**
     * Search products by name or description
     */
    public function search($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.name LIKE ? OR p.description LIKE ?) 
                AND p.status = 'active' 
                ORDER BY p.name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    /**
     * Update product stock
     */
    public function updateStock($id, $quantity) {
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $quantity, $id, $quantity);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock($id, $quantity = 1) {
        $sql = "SELECT stock FROM products WHERE id = ? AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row && $row['stock'] >= $quantity;
    }

    /**
     * Get products with low stock (less than 10 items)
     */
    public function getLowStock() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.stock < 10 AND p.status = 'active' 
                ORDER BY p.stock ASC";
        $result = $this->db->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    /**
     * Get products on sale
     */
    public function getOnSale() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.sale_price IS NOT NULL 
                AND p.sale_price < p.price 
                AND p.status = 'active' 
                ORDER BY (p.price - p.sale_price) / p.price DESC";
        $result = $this->db->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
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
        // Default parameters with validation
        $category_id = filter_var($params['category_id'] ?? null, FILTER_VALIDATE_INT);
        $search = trim($params['search'] ?? '');
        $sort = $this->validateSort($params['sort'] ?? 'name_asc');
        $page = max(1, filter_var($params['page'] ?? 1, FILTER_VALIDATE_INT));
        $per_page = max(1, min(50, filter_var($params['per_page'] ?? 12, FILTER_VALIDATE_INT)));
        $min_price = filter_var($params['min_price'] ?? null, FILTER_VALIDATE_FLOAT);
        $max_price = filter_var($params['max_price'] ?? null, FILTER_VALIDATE_FLOAT);
        $in_stock = filter_var($params['in_stock'] ?? null, FILTER_VALIDATE_BOOLEAN);
        $featured = filter_var($params['featured'] ?? null, FILTER_VALIDATE_BOOLEAN);
        
        $offset = ($page - 1) * $per_page;

        // Base query with joins for related data
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') as review_count,
                (SELECT AVG(rating) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') as average_rating
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

        // Add search filter with improved relevance
        if ($search) {
            $sql .= " AND (
                p.name LIKE ? 
                OR p.description LIKE ? 
                OR p.sku LIKE ?
                OR c.name LIKE ?
            )";
            $types .= 'ssss';
            $searchTerm = "%{$search}%";
            $values[] = $searchTerm;
            $values[] = $searchTerm;
            $values[] = $searchTerm;
            $values[] = $searchTerm;
        }

        // Add price range filter
        if ($min_price !== null) {
            $sql .= " AND p.price >= ?";
            $types .= 'd';
            $values[] = $min_price;
        }
        if ($max_price !== null) {
            $sql .= " AND p.price <= ?";
            $types .= 'd';
            $values[] = $max_price;
        }

        // Add stock filter
        if ($in_stock !== null) {
            $sql .= $in_stock ? " AND p.stock > 0" : " AND p.stock = 0";
        }

        // Add featured filter
        if ($featured !== null) {
            $sql .= " AND p.featured = ?";
            $types .= 'i';
            $values[] = $featured ? 1 : 0;
        }

        // Add sorting with improved options
        $sql .= $this->getSortClause($sort);

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
            // Format numeric values
            $row['average_rating'] = round($row['average_rating'], 1);
            $row['price'] = (float)$row['price'];
            $row['sale_price'] = $row['sale_price'] ? (float)$row['sale_price'] : null;
            $products[] = $row;
        }
        return $products;
    }

    public function getCount($params = []) {
        // Reuse the same parameter processing as getAll
        $category_id = filter_var($params['category_id'] ?? null, FILTER_VALIDATE_INT);
        $search = trim($params['search'] ?? '');
        $min_price = filter_var($params['min_price'] ?? null, FILTER_VALIDATE_FLOAT);
        $max_price = filter_var($params['max_price'] ?? null, FILTER_VALIDATE_FLOAT);
        $in_stock = filter_var($params['in_stock'] ?? null, FILTER_VALIDATE_BOOLEAN);
        $featured = filter_var($params['featured'] ?? null, FILTER_VALIDATE_BOOLEAN);

        // Base query
        $sql = "SELECT COUNT(DISTINCT p.id) as total 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        
        $types = '';
        $values = [];

        // Add the same filters as getAll
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $types .= 'i';
            $values[] = $category_id;
        }

        if ($search) {
            $sql .= " AND (
                p.name LIKE ? 
                OR p.description LIKE ? 
                OR p.sku LIKE ?
                OR c.name LIKE ?
            )";
            $types .= 'ssss';
            $searchTerm = "%{$search}%";
            $values[] = $searchTerm;
            $values[] = $searchTerm;
            $values[] = $searchTerm;
            $values[] = $searchTerm;
        }

        if ($min_price !== null) {
            $sql .= " AND p.price >= ?";
            $types .= 'd';
            $values[] = $min_price;
        }
        if ($max_price !== null) {
            $sql .= " AND p.price <= ?";
            $types .= 'd';
            $values[] = $max_price;
        }

        if ($in_stock !== null) {
            $sql .= $in_stock ? " AND p.stock > 0" : " AND p.stock = 0";
        }

        if ($featured !== null) {
            $sql .= " AND p.featured = ?";
            $types .= 'i';
            $values[] = $featured ? 1 : 0;
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Get pagination metadata
     * @param array $params Query parameters
     * @return array Pagination metadata
     */
    public function getPaginationMeta($params = []) {
        $total = $this->getCount($params);
        $page = max(1, filter_var($params['page'] ?? 1, FILTER_VALIDATE_INT));
        $per_page = max(1, min(50, filter_var($params['per_page'] ?? 12, FILTER_VALIDATE_INT)));
        
        $total_pages = ceil($total / $per_page);
        $page = min($page, max(1, $total_pages)); // Ensure page is within valid range
        
        return [
            'current_page' => $page,
            'per_page' => $per_page,
            'total_items' => $total,
            'total_pages' => $total_pages,
            'has_next_page' => $page < $total_pages,
            'has_prev_page' => $page > 1,
            'next_page' => $page < $total_pages ? $page + 1 : null,
            'prev_page' => $page > 1 ? $page - 1 : null,
            'offset' => ($page - 1) * $per_page
        ];
    }

    /**
     * Validate and return sort parameter
     * @param string $sort Sort parameter
     * @return string Validated sort parameter
     */
    private function validateSort($sort) {
        $validSorts = [
            'name_asc', 'name_desc',
            'price_asc', 'price_desc',
            'created_desc', 'created_asc',
            'rating_desc', 'rating_asc',
            'popularity_desc'
        ];
        return in_array($sort, $validSorts) ? $sort : 'name_asc';
    }

    /**
     * Get SQL ORDER BY clause based on sort parameter
     * @param string $sort Sort parameter
     * @return string SQL ORDER BY clause
     */
    private function getSortClause($sort) {
        switch ($sort) {
            case 'name_desc':
                return " ORDER BY p.name DESC";
            case 'price_asc':
                return " ORDER BY COALESCE(p.sale_price, p.price) ASC";
            case 'price_desc':
                return " ORDER BY COALESCE(p.sale_price, p.price) DESC";
            case 'created_desc':
                return " ORDER BY p.created_at DESC";
            case 'created_asc':
                return " ORDER BY p.created_at ASC";
            case 'rating_desc':
                return " ORDER BY (SELECT AVG(rating) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') DESC";
            case 'rating_asc':
                return " ORDER BY (SELECT AVG(rating) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') ASC";
            case 'popularity_desc':
                return " ORDER BY (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) DESC";
            default: // name_asc
                return " ORDER BY p.name ASC";
        }
    }
}
