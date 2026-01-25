<?php

namespace App\Domain;

use PDO;

class PaymentRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create payment record
     */
    public function createPayment($orderId, $method, $amount, $proofUrl = null)
    {
        $validMethods = ['transfer', 'credit_card', 'e-wallet', 'cash', 'cod'];
        if (!in_array($method, $validMethods)) {
            return false;
        }

        $sql = "INSERT INTO payments (order_id, payment_method, amount, payment_proof, payment_status)
                VALUES (:order_id, :payment_method, :amount, :payment_proof, 'pending')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':payment_method' => $method,
            ':amount' => $amount,
            ':payment_proof' => $proofUrl
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Get payment by order
     */
    public function getPaymentByOrder($orderId)
    {
        $sql = "SELECT * FROM payments WHERE order_id = :order_id ORDER BY payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Get payment by ID
     */
    public function getPaymentById($paymentId)
    {
        $sql = "SELECT p.*, o.order_number, o.total_amount as order_total
                FROM payments p
                JOIN orders o ON p.order_id = o.order_id
                WHERE p.payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':payment_id' => $paymentId]);
        return $stmt->fetch();
    }

    /**
     * Verify payment (admin/vendor)
     */
    public function verifyPayment($paymentId, $verifiedBy)
    {
        $sql = "UPDATE payments 
                SET payment_status = 'verified', verified_by = :verified_by, verified_at = NOW()
                WHERE payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':payment_id' => $paymentId, ':verified_by' => $verifiedBy]);
    }

    /**
     * Reject payment
     */
    public function rejectPayment($paymentId, $notes = null)
    {
        $sql = "UPDATE payments SET payment_status = 'failed', notes = :notes WHERE payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':payment_id' => $paymentId, ':notes' => $notes]);
    }

    /**
     * Upload payment proof
     */
    public function uploadProof($paymentId, $proofUrl)
    {
        $sql = "UPDATE payments SET payment_proof = :proof WHERE payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':proof' => $proofUrl, ':payment_id' => $paymentId]);
    }

    /**
     * Get total paid for order
     */
    public function getTotalPaid($orderId)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM payments 
                WHERE order_id = :order_id AND payment_status = 'verified'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Get pending payments (admin)
     */
    public function getPendingPayments($limit = 50)
    {
        $sql = "SELECT p.*, o.order_number, o.total_amount, u.full_name as customer_name
                FROM payments p
                JOIN orders o ON p.order_id = o.order_id
                JOIN users u ON o.user_id = u.user_id
                WHERE p.payment_status = 'pending'
                ORDER BY p.payment_date ASC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
