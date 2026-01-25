<?php

namespace App\Domain;

use PDO;

class OrderRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Generate unique order number
     */
    public function generateOrderNumber()
    {
        $year = date('Y');
        $sql = "SELECT COUNT(*) + 1 as next_num FROM orders WHERE YEAR(created_at) = :year";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year]);
        $result = $stmt->fetch();
        $number = str_pad($result['next_num'], 6, '0', STR_PAD_LEFT);
        return "ORD-{$year}-{$number}";
    }

    /**
     * Create new order
     */
    public function createOrder($data)
    {
        $orderNumber = $this->generateOrderNumber();

        $sql = "INSERT INTO orders (
                    user_id, vendor_id, order_number, order_type, event_type,
                    event_date, event_time, delivery_address, delivery_city,
                    num_people, subtotal, tax, delivery_fee, discount, total_amount,
                    status, payment_status, special_request
                ) VALUES (
                    :user_id, :vendor_id, :order_number, :order_type, :event_type,
                    :event_date, :event_time, :delivery_address, :delivery_city,
                    :num_people, :subtotal, :tax, :delivery_fee, :discount, :total_amount,
                    'pending', 'unpaid', :special_request
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':vendor_id' => $data['vendor_id'],
            ':order_number' => $orderNumber,
            ':order_type' => $data['order_type'] ?? 'custom',
            ':event_type' => $data['event_type'] ?? null,
            ':event_date' => $data['event_date'],
            ':event_time' => $data['event_time'],
            ':delivery_address' => $data['delivery_address'],
            ':delivery_city' => $data['delivery_city'] ?? null,
            ':num_people' => $data['num_people'],
            ':subtotal' => $data['subtotal'],
            ':tax' => $data['tax'] ?? 0,
            ':delivery_fee' => $data['delivery_fee'] ?? 0,
            ':discount' => $data['discount'] ?? 0,
            ':total_amount' => $data['total_amount'],
            ':special_request' => $data['special_request'] ?? null
        ]);

        return [
            'order_id' => $this->db->lastInsertId(),
            'order_number' => $orderNumber
        ];
    }

    /**
     * Add order items
     */
    public function addOrderItem($orderId, $item)
    {
        $sql = "INSERT INTO order_items (order_id, item_id, package_id, item_name, quantity, unit_price, subtotal, notes)
                VALUES (:order_id, :item_id, :package_id, :item_name, :quantity, :unit_price, :subtotal, :notes)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':order_id' => $orderId,
            ':item_id' => $item['item_id'] ?? null,
            ':package_id' => $item['package_id'] ?? null,
            ':item_name' => $item['item_name'],
            ':quantity' => $item['quantity'],
            ':unit_price' => $item['unit_price'],
            ':subtotal' => $item['quantity'] * $item['unit_price'],
            ':notes' => $item['notes'] ?? null
        ]);
    }

    /**
     * Get orders by user
     */
    public function getOrdersByUser($userId, $limit = 20, $offset = 0)
    {
        $sql = "SELECT o.*, v.vendor_name 
                FROM orders o
                JOIN vendors v ON o.vendor_id = v.vendor_id
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order by ID with details
     */
    public function getOrderById($orderId)
    {
        $sql = "SELECT o.*, v.vendor_name, v.phone as vendor_phone, v.email as vendor_email,
                       u.full_name as customer_name, u.phone as customer_phone, u.email as customer_email
                FROM orders o
                JOIN vendors v ON o.vendor_id = v.vendor_id
                JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetch();
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, m.image_url 
                FROM order_items oi
                LEFT JOIN menu_items m ON oi.item_id = m.item_id
                WHERE oi.order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status)
    {
        $validStatuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':order_id' => $orderId]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $reason = null)
    {
        $sql = "UPDATE orders SET status = 'cancelled', cancellation_reason = :reason WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':reason' => $reason, ':order_id' => $orderId]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderId, $status)
    {
        $validStatuses = ['unpaid', 'partial', 'paid', 'refunded'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE orders SET payment_status = :status WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':order_id' => $orderId]);
    }

    /**
     * Validate order ownership
     */
    public function validateOwnership($userId, $orderId)
    {
        $sql = "SELECT order_id FROM orders WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Get order count by user
     */
    public function getOrderCountByUser($userId)
    {
        $sql = "SELECT COUNT(*) FROM orders WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get orders by vendor (for vendor dashboard)
     */
    public function getOrdersByVendor($vendorId, $status = null, $limit = 20)
    {
        $sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                WHERE o.vendor_id = :vendor_id";

        $params = [':vendor_id' => $vendorId];

        if ($status) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY o.event_date ASC, o.event_time ASC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
