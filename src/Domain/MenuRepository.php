<?php

namespace App\Domain;

use PDO;

class MenuRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getItemsByVendorId($vendorId)
    {
        // Join with categories to group them easily if needed
        $sql = "SELECT m.*, c.category_name 
                FROM menu_items m 
                LEFT JOIN categories c ON m.category_id = c.category_id 
                WHERE m.vendor_id = :vendor_id AND m.is_available = TRUE 
                ORDER BY m.category_id ASC, m.item_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':vendor_id', $vendorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllVendorItems($vendorId)
    {
        $sql = "SELECT m.*, c.category_name 
                FROM menu_items m 
                LEFT JOIN categories c ON m.category_id = c.category_id 
                WHERE m.vendor_id = :vendor_id 
                ORDER BY m.category_id ASC, m.item_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':vendor_id', $vendorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
