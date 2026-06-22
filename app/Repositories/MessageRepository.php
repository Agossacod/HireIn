<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class MessageRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function create(int $senderId, int $recipientId, string $subject, string $body): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (sender_user_id, recipient_user_id, subject, body, created_at)
             VALUES (:sender_user_id, :recipient_user_id, :subject, :body, NOW())'
        );

        return $stmt->execute([
            ':sender_user_id' => $senderId,
            ':recipient_user_id' => $recipientId,
            ':subject' => $subject,
            ':body' => $body,
        ]);
    }

    public function getMessagesForUser(int $userId, int $limit = 40): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.id, m.subject, m.body, m.created_at,
                    m.sender_user_id, sender.fullname AS sender_name, sender.role AS sender_role,
                    m.recipient_user_id, recipient.fullname AS recipient_name, recipient.role AS recipient_role
             FROM messages m
             JOIN users sender ON sender.id = m.sender_user_id
             JOIN users recipient ON recipient.id = m.recipient_user_id
             WHERE m.sender_user_id = :user_id OR m.recipient_user_id = :user_id
             ORDER BY m.created_at DESC
             LIMIT :limit'
        );

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
