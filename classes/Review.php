<?php
require_once 'Model.php';

class Review extends Model {
    protected $table = 'reviews';

    public function __construct() {
        parent::__construct();
    }

    public function addReview($userId, $productId, $rating, $comment) {
        // Check if user has already reviewed this product
        $existingReview = $this->where('user_id = ? AND product_id = ?', [$userId, $productId]);
        if (!empty($existingReview)) {
            return false; // User has already reviewed this product
        }

        // Check if user has purchased the product
        $sql = "SELECT COUNT(*) as purchased 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $userId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['purchased'] == 0) {
            return false; // User hasn't purchased the product
        }

        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending' // Reviews need approval
        ];

        return $this->create($data);
    }

    public function getProductReviews($productId, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.username 
                FROM {$this->table} r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $productId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }

    public function getProductRating($productId) {
        $sql = "SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                FROM {$this->table} 
                WHERE product_id = ? AND status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateReviewStatus($reviewId, $status) {
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        return $this->update($reviewId, ['status' => $status]);
    }

    public function getUserReviews($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, p.name as product_name 
                FROM {$this->table} r 
                JOIN products p ON r.product_id = p.id 
                WHERE r.user_id = ? 
                ORDER BY r.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }

    public function getPendingReviews($limit = 10, $offset = 0) {
        return $this->where('status = ? LIMIT ? OFFSET ?', ['pending', $limit, $offset]);
    }
} 