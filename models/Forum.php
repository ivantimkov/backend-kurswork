<?php

require_once __DIR__ . '/../config/config.php';



class Forum
{
    public static function getAllTopics()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT ft.*, u.username 
            FROM forum_topics ft
            JOIN users u ON ft.user_id = u.id
            ORDER BY ft.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTopicById($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT ft.*, u.username 
            FROM forum_topics ft
            JOIN users u ON ft.user_id = u.id
            WHERE ft.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRepliesByTopicId($topicId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT fp.*, u.username 
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            WHERE fp.topic_id = ?
            ORDER BY fp.created_at ASC
        ");
        $stmt->execute([$topicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createTopic($userId, $title, $content)
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // Створити тему
            $stmt = $pdo->prepare("INSERT INTO forum_topics (title, user_id) VALUES (?, ?)");
            $stmt->execute([$title, $userId]);
            $topicId = $pdo->lastInsertId();

            // Додати перше повідомлення як опис
            $stmt = $pdo->prepare("INSERT INTO forum_posts (topic_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$topicId, $userId, $content]);

            $pdo->commit();
            return $topicId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function addReply($topicId, $userId, $content)
{
    $pdo = Database::getConnection();

    // Перевірка, чи існує тема
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE id = ?");
    $stmt->execute([$topicId]);

    if ($stmt->fetchColumn() == 0) {
        throw new Exception("❌ Тема з ID $topicId не існує.");
    }

    // Якщо існує — додаємо відповідь
    $stmt = $pdo->prepare("INSERT INTO forum_posts (topic_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$topicId, $userId, $content]);
}
public static function markAsSolved($topicId)
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("UPDATE forum_topics SET is_solved = 1 WHERE id = ?");
    $stmt->execute([$topicId]);
}

// Видалити тему та всі її відповіді
public static function deleteTopic($topicId)
{
    $pdo = Database::getConnection();
    $pdo->beginTransaction();
    try {
        $pdo->prepare("DELETE FROM forum_posts WHERE topic_id = ?")->execute([$topicId]);
        $pdo->prepare("DELETE FROM forum_topics WHERE id = ?")->execute([$topicId]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
  public static function getPostById($postId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = ?");
        $stmt->execute([$postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
// Видалити конкретну відповідь
public static function deleteReply($replyId)
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("DELETE FROM forum_posts WHERE id = ?");
    $stmt->execute([$replyId]);
}
}


