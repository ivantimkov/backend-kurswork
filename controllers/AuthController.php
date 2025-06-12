<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserModel.php';
class AuthController
{
   public function login()
{
    $pdo = Database::getConnection();

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::authenticate($username, $password);

        if ($user) {
            $_SESSION['user'] = $user;

            // Лог успішного входу
            $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
            $stmt->execute([$user['id'], "Успішний вхід"]);

            // Перенаправлення
            if ($user['role'] === 'admin') {
                header("Location: ?controller=admin&action=dashboard");
            } else {
                header("Location: ?controller=user&action=home");
            }
            exit;
        } else {
            // Лог невдалого входу (user_id NULL)
            $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (NULL, ?)");
            $stmt->execute(["Невдалий вхід користувача '{$username}'"]);

            $error = "Невірний логін або пароль";
        }
    }

    include __DIR__ . '/../views/auth/login.php';
}
   public function register()
{
    $pdo = Database::getConnection();

    $error = null;
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $full_name = '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($username) || empty($password) || empty($confirm)) {
            $error = "Усі поля обов'язкові";
        } elseif ($password !== $confirm) {
            $error = "Паролі не співпадають";
        } else {
            // Перевірка унікальності
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Користувач з таким ім'ям вже існує";
            } else {
                // Реєстрація
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $full_name, $hashed, $email]);

                // Отримуємо id нового користувача
                $newUserId = $pdo->lastInsertId();

                // Лог реєстрації
                $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
                $stmt->execute([$newUserId, "Реєстрація нового користувача"]);

                $success = "Реєстрація успішна. Тепер увійдіть.";
                header("Location: ?controller=auth&action=login&registered=1");
                exit;
            }
        }
    }

    include __DIR__ . '/../views/auth/register.php';
}


    public function logout()
{
    $pdo = Database::getConnection();

    // Якщо користувач авторизований — логування виходу
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['id'] ?? null;

        if ($userId) {
            $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
            $stmt->execute([$userId, "Вихід користувача"]);
        }
    }

    session_destroy();
    $_SESSION = [];
    header("Location: ?controller=auth&action=login");
    exit;
}



}
