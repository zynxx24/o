<?php

namespace App\Domain;

use PDO;
use App\Middleware\Security;

class UserRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create new user
     */
    public function createUser($data)
    {
        $sql = "INSERT INTO users (username, email, password_hash, full_name, phone, address, role, status) 
                VALUES (:username, :email, :password_hash, :full_name, :phone, :address, :role, 'active')";

        $stmt = $this->db->prepare($sql);

        $passwordHash = Security::hashPassword($data['password']);

        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password_hash' => $passwordHash,
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':role' => $data['role'] ?? 'customer'
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find user by ID
     */
    public function findById($id)
    {
        $sql = "SELECT user_id, username, email, full_name, phone, address, role, status, created_at 
                FROM users WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        $fields = [];
        $params = [':user_id' => $userId];

        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params[':full_name'] = $data['full_name'];
        }
        if (isset($data['phone'])) {
            $fields[] = 'phone = :phone';
            $params[':phone'] = $data['phone'];
        }
        if (isset($data['address'])) {
            $fields[] = 'address = :address';
            $params[':address'] = $data['address'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update password
     */
    public function updatePassword($userId, $newPassword)
    {
        $sql = "UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':password_hash' => Security::hashPassword($newPassword)
        ]);
    }

    /**
     * Verify user password
     */
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        return Security::verifyPassword($password, $user['password_hash']);
    }

    /**
     * Get all users (admin)
     */
    public function getAllUsers($limit = 50, $offset = 0)
    {
        $sql = "SELECT user_id, username, email, full_name, phone, role, status, created_at 
                FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
