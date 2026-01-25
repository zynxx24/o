<?php

namespace App\Domain;

use PDO;

class CategoryRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all categories
     */
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE category_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get categories with item count
     */
    public function getCategoriesWithCount()
    {
        $sql = "SELECT c.*, COUNT(m.item_id) as item_count 
                FROM categories c 
                LEFT JOIN menu_items m ON c.category_id = m.category_id AND m.is_available = TRUE
                GROUP BY c.category_id 
                ORDER BY c.category_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
