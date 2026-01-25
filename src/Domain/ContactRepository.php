<?php

namespace App\Domain;

use PDO;

class ContactRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new contact message
     */
    public function createMessage(string $name, string $email, string $subject, string $message): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contact_messages (name, email, subject, message, is_read, created_at)
            VALUES (:name, :email, :subject, :message, FALSE, NOW())
        ");

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all messages with optional limit
     */
    public function getAllMessages(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM contact_messages 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent unread messages
     */
    public function getRecentMessages(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM contact_messages 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get count of unread messages
     */
    public function getUnreadCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $messageId): bool
    {
        $stmt = $this->db->prepare("UPDATE contact_messages SET is_read = TRUE WHERE message_id = :id");
        return $stmt->execute([':id' => $messageId]);
    }

    /**
     * Mark all messages as read
     */
    public function markAllAsRead(): bool
    {
        return $this->db->exec("UPDATE contact_messages SET is_read = TRUE WHERE is_read = FALSE") !== false;
    }

    /**
     * Get message by ID
     */
    public function getById(int $messageId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM contact_messages WHERE message_id = :id");
        $stmt->execute([':id' => $messageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Delete message
     */
    public function delete(int $messageId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE message_id = :id");
        return $stmt->execute([':id' => $messageId]);
    }

    /**
     * Get subject label in Indonesian
     */
    public static function getSubjectLabel(string $subject): string
    {
        $labels = [
            'general' => 'Pertanyaan Umum',
            'order' => 'Masalah Pesanan',
            'partnership' => 'Kerjasama Bisnis',
            'vendor' => 'Daftar Vendor',
            'feedback' => 'Saran & Masukan',
            'complaint' => 'Komplain'
        ];
        return $labels[$subject] ?? ucfirst($subject);
    }
}
