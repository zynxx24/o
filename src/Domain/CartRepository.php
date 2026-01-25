<?php

namespace App\Domain;

use PDO;

class CartRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get or create cart for user and vendor
     */
    public function getOrCreateCart($userId, $vendorId)
    {
        // Check if cart exists
        $sql = "SELECT cart_id FROM cart WHERE user_id = :user_id AND vendor_id = :vendor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':vendor_id' => $vendorId]);
        $cart = $stmt->fetch();

        if ($cart) {
            return $cart['cart_id'];
        }

        // Create new cart
        $sql = "INSERT INTO cart (user_id, vendor_id) VALUES (:user_id, :vendor_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':vendor_id' => $vendorId]);

        return $this->db->lastInsertId();
    }

    /**
     * Get user's cart with items
     */
    public function getCartByUser($userId)
    {
        $sql = "SELECT c.cart_id, c.vendor_id, v.vendor_name, 
                       ci.cart_item_id, ci.quantity, ci.notes,
                       m.item_id, m.item_name, m.price, m.image_url, m.unit, m.min_order,
                       p.package_id, p.package_name, p.price_per_person
                FROM cart c
                JOIN vendors v ON c.vendor_id = v.vendor_id
                LEFT JOIN cart_items ci ON c.cart_id = ci.cart_id
                LEFT JOIN menu_items m ON ci.item_id = m.item_id
                LEFT JOIN packages p ON ci.package_id = p.package_id
                WHERE c.user_id = :user_id
                ORDER BY v.vendor_name, ci.added_at";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Add item to cart
     */
    public function addItem($userId, $vendorId, $itemId, $quantity = 1, $packageId = null, $notes = null)
    {
        $cartId = $this->getOrCreateCart($userId, $vendorId);

        // Check if item already in cart
        $sql = "SELECT cart_item_id, quantity FROM cart_items 
                WHERE cart_id = :cart_id AND (item_id = :item_id OR package_id = :package_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cart_id' => $cartId,
            ':item_id' => $itemId,
            ':package_id' => $packageId
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update quantity
            return $this->updateQuantity($existing['cart_item_id'], $existing['quantity'] + $quantity);
        }

        // Add new item
        $sql = "INSERT INTO cart_items (cart_id, item_id, package_id, quantity, notes) 
                VALUES (:cart_id, :item_id, :package_id, :quantity, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cart_id' => $cartId,
            ':item_id' => $itemId,
            ':package_id' => $packageId,
            ':quantity' => $quantity,
            ':notes' => $notes
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        $sql = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':quantity' => $quantity, ':cart_item_id' => $cartItemId]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartItemId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_item_id = :cart_item_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':cart_item_id' => $cartItemId]);
    }

    /**
     * Clear user's cart
     */
    public function clearCart($userId, $vendorId = null)
    {
        if ($vendorId) {
            $sql = "DELETE c, ci FROM cart c 
                    LEFT JOIN cart_items ci ON c.cart_id = ci.cart_id 
                    WHERE c.user_id = :user_id AND c.vendor_id = :vendor_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId, ':vendor_id' => $vendorId]);
        }

        $sql = "DELETE c, ci FROM cart c 
                LEFT JOIN cart_items ci ON c.cart_id = ci.cart_id 
                WHERE c.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Get cart item count
     */
    public function getCartItemCount($userId)
    {
        $sql = "SELECT COALESCE(SUM(ci.quantity), 0) as total 
                FROM cart c 
                JOIN cart_items ci ON c.cart_id = ci.cart_id 
                WHERE c.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get cart total amount
     */
    public function getCartTotal($userId)
    {
        $sql = "SELECT COALESCE(SUM(
                    CASE 
                        WHEN ci.item_id IS NOT NULL THEN m.price * ci.quantity
                        WHEN ci.package_id IS NOT NULL THEN p.price_per_person * ci.quantity
                        ELSE 0
                    END
                ), 0) as total
                FROM cart c
                JOIN cart_items ci ON c.cart_id = ci.cart_id
                LEFT JOIN menu_items m ON ci.item_id = m.item_id
                LEFT JOIN packages p ON ci.package_id = p.package_id
                WHERE c.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Validate cart item ownership
     */
    public function validateOwnership($userId, $cartItemId)
    {
        $sql = "SELECT ci.cart_item_id 
                FROM cart_items ci 
                JOIN cart c ON ci.cart_id = c.cart_id 
                WHERE ci.cart_item_id = :cart_item_id AND c.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cart_item_id' => $cartItemId, ':user_id' => $userId]);
        return $stmt->fetch() !== false;
    }
}
