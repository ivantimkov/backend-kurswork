<?php

require_once __DIR__ . '/../config/config.php';

class EventFriend
{
    public static function setFriendsForEvent($eventId, $friendIds)
    {
        $db = Database::getInstance()->getConnection();

        // Очистити попередніх друзів
        $stmt = $db->prepare("DELETE FROM event_friends WHERE event_id = ?");
        $stmt->execute([$eventId]);

        // Додати нових друзів
        if (!empty($friendIds)) {
            $stmt = $db->prepare("INSERT INTO event_friends (event_id, friend_id) VALUES (?, ?)");
            foreach ($friendIds as $friendId) {
                $stmt->execute([$eventId, $friendId]);
            }
        }
    }

    public static function getFriendsForEvent($eventId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT users.id, users.name FROM event_friends JOIN users ON users.id = event_friends.friend_id WHERE event_id = ?");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteByEvent($eventId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM event_friends WHERE event_id = ?");
        $stmt->execute([$eventId]);
    }
}
