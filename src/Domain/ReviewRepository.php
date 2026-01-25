<?php

namespace App\Domain;

use PDO;

class ReviewRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create review
     */
    public function createReview($data)
    {
        $sql = "INSERT INTO reviews (order_id, user_id, vendor_id, rating, food_rating, service_rating, delivery_rating, review_text, images)
                VALUES (:order_id, :user_id, :vendor_id, :rating, :food_rating, :service_rating, :delivery_rating, :review_text, :images)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':order_id' => $data['order_id'],
            ':user_id' => $data['user_id'],
            ':vendor_id' => $data['vendor_id'],
            ':rating' => $data['rating'],
            ':food_rating' => $data['food_rating'] ?? null,
            ':service_rating' => $data['service_rating'] ?? null,
            ':delivery_rating' => $data['delivery_rating'] ?? null,
            ':review_text' => $data['review_text'] ?? null,
            ':images' => $data['images'] ?? null
        ]);

        // Update vendor rating
        $this->updateVendorRating($data['vendor_id']);

        return $this->db->lastInsertId();
    }

    /**
     * Get reviews by vendor
     */
    public function getReviewsByVendor($vendorId, $limit = 20, $offset = 0)
    {
        $sql = "SELECT r.*, u.full_name as reviewer_name
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.vendor_id = :vendor_id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':vendor_id', $vendorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get review by order
     */
    public function getReviewByOrder($orderId)
    {
        $sql = "SELECT * FROM reviews WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetch();
    }

    /**
     * Check if user can review order
     */
    public function canReview($userId, $orderId)
    {
        // Check if order belongs to user and is completed
        $sql = "SELECT o.order_id 
                FROM orders o 
                LEFT JOIN reviews r ON o.order_id = r.order_id
                WHERE o.order_id = :order_id 
                AND o.user_id = :user_id 
                AND o.status = 'completed'
                AND r.review_id IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Update vendor rating (called after new review)
     */
    private function updateVendorRating($vendorId)
    {
        $sql = "UPDATE vendors SET 
                rating = (SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE vendor_id = :vendor_id1),
                total_reviews = (SELECT COUNT(*) FROM reviews WHERE vendor_id = :vendor_id2)
                WHERE vendor_id = :vendor_id3";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':vendor_id1' => $vendorId,
            ':vendor_id2' => $vendorId,
            ':vendor_id3' => $vendorId
        ]);
    }

    /**
     * Add vendor response to review
     */
    public function addVendorResponse($reviewId, $response)
    {
        $sql = "UPDATE reviews SET vendor_response = :response, response_date = NOW() WHERE review_id = :review_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':response' => $response, ':review_id' => $reviewId]);
    }

    /**
     * Get vendor rating summary
     */
    public function getVendorRatingSummary($vendorId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    COALESCE(AVG(rating), 0) as avg_rating,
                    COALESCE(AVG(food_rating), 0) as avg_food_rating,
                    COALESCE(AVG(service_rating), 0) as avg_service_rating,
                    COALESCE(AVG(delivery_rating), 0) as avg_delivery_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM reviews WHERE vendor_id = :vendor_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':vendor_id' => $vendorId]);
        return $stmt->fetch();
    }
}
