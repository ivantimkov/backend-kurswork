<?php
require_once __DIR__ . '/../config/config.php';

class Event
{
    protected static $pdo;


    protected static function getDb()
    {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdo;
    }

    public static function getAllByUser($userId)
    {
        $db = self::getDb();
        $stmt1 = $db->prepare("SELECT e.*, u.username AS owner_name
        FROM events e
        JOIN users u ON e.user_id = u.id
        WHERE e.user_id = ?");
    $stmt1->execute([$userId]);
    $ownEvents = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Події, куди його додали як друга
    $stmt2 = $db->prepare("SELECT e.*, u.username AS owner_name
        FROM event_friends ef
        JOIN events e ON ef.event_id = e.id
        JOIN users u ON e.user_id = u.id
        WHERE ef.user_id = ?");
    $stmt2->execute([$userId]);
    $sharedEvents = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Позначимо чужі події
    foreach ($sharedEvents as &$event) {
        $event['shared'] = true;
    }

    return array_merge($ownEvents, $sharedEvents);
    }
public static function getAllByUserCalendar($userId)
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT e.id, e.title, e.event_date, e.reminder_time, e.description, u.username AS owner_name
        FROM events e
        JOIN users u ON e.user_id = u.id
        WHERE e.user_id = ?

        UNION

        SELECT e.id, e.title, e.event_date, e.reminder_time, e.description, u.username AS owner_name
        FROM event_friends ef
        JOIN events e ON ef.event_id = e.id
        JOIN users u ON e.user_id = u.id
        WHERE ef.user_id = ?
        ORDER BY event_date ASC
    ");

    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





    public static function getById($id, $userId)
    {
        $pdo = self::getDb();
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public static function create($userId, $title, $description, $event_date, $reminder_time)
{
    $pdo = self::getDb();
    $stmt = $pdo->prepare("INSERT INTO events (user_id, title, description, event_date, reminder_time) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$userId, $title, $description, $event_date, $reminder_time]);

    if ($success) {
        return $pdo->lastInsertId(); //  Повертаємо ID нової події
    }

    return false;
}



    public static function update($id, $userId, $title, $description, $event_date, $reminder_time)
    {
        $pdo = self::getDb();
        $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, reminder_time = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$title, $description, $event_date, $reminder_time, $id, $userId]);
    }

    public static function delete($id, $userId)
    {
        $pdo = self::getDb();
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
    public static function getAll() {
    $pdo = Database::getConnection();
    return $pdo->query("SELECT events.*, users.username FROM events JOIN users ON events.user_id = users.id")->fetchAll(PDO::FETCH_ASSOC);
}
public static function getAllEventsWithUsernames()
{
    $db = Database::getConnection();
    $sql = "
        SELECT events.*, users.username 
        FROM events
        JOIN users ON events.user_id = users.id
        ORDER BY events.event_date ASC
    ";
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getStatistics() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) AS total, SUM(event_date >= CURDATE()) AS upcoming FROM events");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getUpcomingEvents() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT e.title, e.event_date, u.username FROM events e JOIN users u ON e.user_id = u.id WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public static function updateFriends(int $event_id, array $friend_ids): bool
{
    $db = Database::getConnection();

    try {
        // Видаляємо старі зв’язки
        $stmtDelete = $db->prepare("DELETE FROM event_friends WHERE event_id = ?");
        $stmtDelete->execute([$event_id]);

        if (count($friend_ids) === 0) {
            return true; // якщо друзів немає — просто очищаємо і виходимо
        }

        $stmtInsert = $db->prepare("INSERT INTO event_friends (event_id, user_id) VALUES (?, ?)");

        error_log('friend_ids: ' . print_r($friend_ids, true));

        foreach ($friend_ids as $friend_id) {
            $stmtInsert->execute([$event_id, $friend_id]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Помилка при вставці друга в подію: " . $e->getMessage());
return false;

    }
}

public static function getUserNameById($id)
{
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['full_name'] : null;
}



}
