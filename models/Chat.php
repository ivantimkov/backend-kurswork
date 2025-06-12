<?php

require_once __DIR__ . '/../config/config.php';

class Chat
{
    public static function getMessages($userId, $friendId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = :userId AND receiver_id = :friendId)
               OR (sender_id = :friendId AND receiver_id = :userId)
            ORDER BY created_at ASC
        ");
        $stmt->execute(['userId' => $userId, 'friendId' => $friendId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function sendMessage($senderId, $receiverId, $message)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        return $stmt->execute([$senderId, $receiverId, $message]);
    }
}
