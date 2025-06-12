<?php

require_once __DIR__ . '/../config/config.php';

class Friend {
    public static function sendRequest($userId, $friendId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $friendId]);
    }

    public static function exists($userId, $friendId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        $stmt->execute([$userId, $friendId, $friendId, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getFriendRequests($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT f.id, u.full_name, u.email FROM friends f JOIN users u ON u.id = f.user_id WHERE f.friend_id = ? AND f.status = 'pending'");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function acceptRequest($requestId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE friends SET status = 'accepted' WHERE id = ?");
        return $stmt->execute([$requestId]);
    }

    public static function rejectRequest($requestId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE friends SET status = 'rejected' WHERE id = ?");
        return $stmt->execute([$requestId]);
    }

    public static function getFriends($userId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, u.email
            FROM users u
            JOIN friends f ON (
                (f.user_id = ? AND f.friend_id = u.id) OR 
                (f.friend_id = ? AND f.user_id = u.id)
            )
            WHERE f.status = 'accepted'
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function removeFriend($userId, $friendId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        return $stmt->execute([$userId, $friendId, $friendId, $userId]);
    }
public static function areFriends($userId1, $userId2)
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM friends
        WHERE 
            (
                (user_id = :user1 AND friend_id = :user2) OR 
                (user_id = :user2 AND friend_id = :user1)
            )
            AND status = 'accepted'
    ");
    $stmt->execute([
        ':user1' => $userId1,
        ':user2' => $userId2
    ]);
    return $stmt->fetchColumn() > 0;
}
 public function getFriendsForUser($userId) {
     $pdo = Database::getConnection();
        $stmt = $this->$pdo->prepare("
            SELECT u.id, u.username 
            FROM users u 
            JOIN friends f 
              ON (f.user_id = ? AND f.friend_id = u.id AND f.status = 'accepted')
              OR (f.friend_id = ? AND f.user_id = u.id AND f.status = 'accepted')
        ");
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $friends = [];
        while ($row = $result->fetch_assoc()) {
            $friends[] = $row;
        }

        return $friends;
    }

}
