<?php

require_once __DIR__ . '/../models/Friend.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/Auth.php';

class FriendController
{
    public function friend()
    {
        Auth::check();
        $user = Auth::user();
        $friends = Friend::getFriends($user['id']);
        $requests = Friend::getFriendRequests($user['id']);
        include __DIR__ . '/../views/friend/friend.php';
    }

    public function search()
    {
        Auth::check();
        $user = Auth::user();
        $query = $_GET['q'] ?? '';
        $results = [];

        if (!empty($query)) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT id, full_name, email FROM users WHERE (full_name LIKE ? OR email LIKE ?) AND id != ?");
            $stmt->execute(["%$query%", "%$query%", $user['id']]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        include __DIR__ . '/../views/friend/search.php';
    }

    public function send()
    {
        Auth::check();
        $user = Auth::user();

        if (isset($_POST['friend_id'])) {
            $friendId = (int)$_POST['friend_id'];
            if ($friendId !== $user['id'] && !Friend::exists($user['id'], $friendId)) {
                Friend::sendRequest($user['id'], $friendId);
            }
        }

        header('Location: ?controller=friend&action=friend');
        exit;
    }

    public function accept()
    {
        Auth::check();
        if (isset($_GET['id'])) {
            Friend::acceptRequest((int)$_GET['id']);
        }
        header('Location: ?controller=friend&action=friend');
        exit;
    }

    public function reject()
    {
        Auth::check();
        if (isset($_GET['id'])) {
            Friend::rejectRequest((int)$_GET['id']);
        }
        header('Location: ?controller=friend&action=friend');
        exit;
    }

    public function remove()
    {
        Auth::check();
        $user = Auth::user();
        if (isset($_GET['id'])) {
            Friend::removeFriend($user['id'], (int)$_GET['id']);
        }
        header('Location: ?controller=friend&action=friend');
        exit;
    }
 public function listJson() {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Не авторизовано']);
        return;
    }

    $userId = $_SESSION['user']['id'];
    $friends = Friend::getFriends($userId); // Використовуємо статичний метод
    echo json_encode(['status' => 'success', 'friends' => $friends]);
}



}
