<?php

require_once __DIR__ . '/../config/config.php';


class Note
{
    public static function getAllByUser($userId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY updated_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id, $userId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($userId, $title, $content)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $content]);
    }

    public static function update($id, $userId, $title, $content)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $id, $userId]);
    }

    public static function delete($id, $userId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }
}


