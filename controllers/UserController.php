<?php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    public function home()
    {
        Auth::check();
        $user = Auth::user();
        $events = Event::getAllByUser($user['id']);
        include __DIR__ . '/../views/user/home.php';
    }

    public static function profile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $user = $_SESSION['user'];
        include __DIR__ . '/../views/user/profile.php';
    }

    public static function updateProfile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'error', 'message' => 'Не авторизовано']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Невірний формат запиту']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $full_name = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');

        if (!$email) {
            echo json_encode(['status' => 'error', 'message' => 'Email обов’язковий']);
            return;
        }

        $result = UserModel::updateUser($userId, $full_name, $email);

        if ($result) {
            $_SESSION['user']['full_name'] = $full_name;
            $_SESSION['user']['email'] = $email;
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Не вдалося оновити профіль']);
        }
    }

    public static function changePasswordForm()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $user = $_SESSION['user'];
        include __DIR__ . '/../views/user/change_password.php';
    }

public static function changePassword()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Користувач не авторизований']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Невірний формат даних']);
        exit;
    }

    $currentPassword = $data['current'] ?? '';
    $newPassword = $data['new'] ?? '';
    $repeatPassword = $data['repeat'] ?? '';

    if ($newPassword !== $repeatPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Нові паролі не збігаються']);
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Невірний поточний пароль']);
        exit;
    }

    if (password_verify($newPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Новий пароль та поточний однакові']);
        exit;
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $success = $updateStmt->execute([$newPasswordHash, $userId]);

    if (!$success) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка оновлення пароля']);
        exit;
    }

    // Логування зміни пароля
    $logStmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
    $logStmt->execute([$userId, "Зміна паролю"]);

    echo json_encode(['status' => 'success']);
    exit;
}



public function calendar()
{
    Auth::check();
    $user = Auth::user();
    $events = Event::getAllByUser($user['id']);

    // Приведення до формату для календаря
    $formattedEvents = array_map(function ($event) {
        return [
            'title' => $event['title'],
            'start' => $event['event_date']
        ];
    }, $events);

    include __DIR__ . '/../views/user/calendar.php';
}

public function getUserEvents()
{
    Auth::check();
    $user = Auth::user();

    $events = Event::getAllByUserCalendar($user['id']);

    $formatted = array_map(function($event) {
        return [
            'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['event_date'],
        'reminder_time' => $event['reminder_time'], // додай це!
        'description' => $event['description'] ?? '',
        'owner_name' => $event['owner_name'] ?? 'ви'
        ];
    }, $events);

    header('Content-Type: application/json');
    echo json_encode($formatted);
}



}
