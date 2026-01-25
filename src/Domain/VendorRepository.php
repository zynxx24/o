<?php

namespace App\Domain;

use PDO;

class VendorRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllVendors()
    {
        $sql = "SELECT * FROM vendors WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getVendorById($id)
    {
        $sql = "SELECT * FROM vendors WHERE vendor_id = :id AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function searchVendors($query = '', $city = '', $sort = 'rating')
    {
        $sql = "SELECT * FROM vendors WHERE status = 'active'";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (vendor_name LIKE :query OR description LIKE :query2)";
            $params[':query'] = '%' . $query . '%';
            $params[':query2'] = '%' . $query . '%';
        }

        if (!empty($city)) {
            $sql .= " AND city = :city";
            $params[':city'] = $city;
        }

        switch ($sort) {
            case 'price_low':
                $sql .= " ORDER BY rating ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY rating DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY created_at DESC";
                break;
            case 'rating':
            default:
                $sql .= " ORDER BY rating DESC, total_reviews DESC";
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
