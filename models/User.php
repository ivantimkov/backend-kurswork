<?php

require_once __DIR__ . '/../config/config.php';

class User
{
    public static function authenticate($username, $password)
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}

