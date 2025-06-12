<?php

require_once __DIR__ . '/../config/config.php';

class UserModel
{
    public static function updateUser($id, $full_name, $email)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$full_name, $email, $id]);
    }

    public static function updatePassword($id, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public static function verifyPassword($id, $password)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return password_verify($password, $row['password']);
    }
    public static function getAll() {
    global $pdo;
    return $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
}

public static function deleteById($id) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
public static function getAllUsers()
{
    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT id, username, full_name, email, role FROM users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getStatistics() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) AS total, SUM(role = 'admin') AS admins, SUM(role = 'user') AS users FROM users");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getLoginLogs() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT username, last_login, login_ip FROM users ORDER BY last_login DESC LIMIT 20");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
