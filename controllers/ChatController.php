<?php

require_once __DIR__ . '/../models/Chat.php';
require_once __DIR__ . '/../models/Friend.php';
require_once __DIR__ . '/../helpers/Auth.php';

class ChatController
{
    public function index()
    {
        Auth::check();
        $user = Auth::user();

        $friendId = $_GET['friend_id'] ?? null;
        if (!$friendId) {
            die("Не вказано друга для чату.");
        }

        if (!Friend::areFriends($user['id'], $friendId)) {
            die("Цей користувач не є вашим другом.");
        }

        $messages = Chat::getMessages($user['id'], $friendId);
        include __DIR__ . '/../views/chat/index.php';
    }

    public function send()
    {
        Auth::check();
        $user = Auth::user();

        $friendId = $_POST['friend_id'] ?? null;
        $message = trim($_POST['message'] ?? '');

        if ($friendId && $message !== '') {
            Chat::sendMessage($user['id'], $friendId, $message);
        }

        exit;
    }
    public function getMessages()
{
    Auth::check();
    $user = Auth::user();
    $friendId = $_GET['friend_id'] ?? 0;

    if (!Friend::areFriends($user['id'], $friendId)) {
        http_response_code(403);
        echo json_encode(['error' => 'Not friends']);
        exit;
    }

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT m.*, u.full_name FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE 
            (sender_id = :user_id AND receiver_id = :friend_id)
            OR 
            (sender_id = :friend_id AND receiver_id = :user_id)
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([
        ':user_id' => $user['id'],
        ':friend_id' => $friendId,
    ]);

    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
}
