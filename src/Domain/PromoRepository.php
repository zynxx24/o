<?php

namespace App\Domain;

use PDO;

class PromoRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Validate promo code
     */
    public function validatePromo($code, $orderAmount = 0, $vendorId = null)
    {
        $sql = "SELECT * FROM promos 
                WHERE promo_code = :code 
                AND is_active = TRUE 
                AND valid_from <= CURDATE() 
                AND valid_until >= CURDATE()
                AND (usage_limit IS NULL OR used_count < usage_limit)
                AND (vendor_id IS NULL OR vendor_id = :vendor_id)
                AND min_order <= :order_amount";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':code' => strtoupper($code),
            ':vendor_id' => $vendorId,
            ':order_amount' => $orderAmount
        ]);

        return $stmt->fetch();
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($promo, $orderAmount)
    {
        if (!$promo) {
            return 0;
        }

        $discount = 0;
        if ($promo['discount_type'] === 'percentage') {
            $discount = ($orderAmount * $promo['discount_value']) / 100;
        } else {
            $discount = $promo['discount_value'];
        }

        // Apply max discount cap
        if ($promo['max_discount'] && $discount > $promo['max_discount']) {
            $discount = $promo['max_discount'];
        }

        return $discount;
    }

    /**
     * Apply promo to order
     */
    public function applyPromo($promoId, $userId, $orderId, $discountAmount)
    {
        $sql = "INSERT INTO promo_usage (promo_id, user_id, order_id, discount_amount)
                VALUES (:promo_id, :user_id, :order_id, :discount_amount)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':promo_id' => $promoId,
            ':user_id' => $userId,
            ':order_id' => $orderId,
            ':discount_amount' => $discountAmount
        ]);

        // Increment used_count (trigger should handle this, but adding backup)
        if ($result) {
            $sql = "UPDATE promos SET used_count = used_count + 1 WHERE promo_id = :promo_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':promo_id' => $promoId]);
        }

        return $result;
    }

    /**
     * Check if user already used promo
     */
    public function hasUserUsedPromo($promoId, $userId)
    {
        $sql = "SELECT COUNT(*) FROM promo_usage WHERE promo_id = :promo_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':promo_id' => $promoId, ':user_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get active promos
     */
    public function getActivePromos($vendorId = null)
    {
        $sql = "SELECT * FROM promos 
                WHERE is_active = TRUE 
                AND valid_from <= CURDATE() 
                AND valid_until >= CURDATE()
                AND (usage_limit IS NULL OR used_count < usage_limit)";

        $params = [];
        if ($vendorId) {
            $sql .= " AND (vendor_id IS NULL OR vendor_id = :vendor_id)";
            $params[':vendor_id'] = $vendorId;
        }

        $sql .= " ORDER BY valid_until ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get promo by code
     */
    public function getPromoByCode($code)
    {
        $sql = "SELECT * FROM promos WHERE promo_code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => strtoupper($code)]);
        return $stmt->fetch();
    }
}
