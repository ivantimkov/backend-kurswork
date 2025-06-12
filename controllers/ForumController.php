<?php

require_once __DIR__ . '/../models/Forum.php';
require_once __DIR__ . '/../helpers/Auth.php';

class ForumController
{
    // Показ списку всіх тем
    public function index()
    {
        Auth::check();
        $topics = Forum::getAllTopics();
        require __DIR__ . '/../views/forum/index.php';
    }

    // Перегляд конкретної теми і її відповідей
    public function topic()
{
    Auth::check();

    if (empty($_GET['id'])) {
        header('Location: ?controller=forum&action=index');
        exit;
    }

    $topic_id = (int) $_GET['id'];
    $topic = Forum::getTopicById($topic_id);
    $posts = Forum::getRepliesByTopicId($topic_id);

    if (!$topic) {
        header('Location: ?controller=forum&action=index');
        exit;
    }

    // ⬇️ Забираємо перше повідомлення як опис теми
    $initialPost = array_shift($posts);

    // ⬇️ Передаємо на сторінку topic.php
    require __DIR__ . '/../views/forum/topic.php';
}


    // Створення нової теми (форма)
    public function create()
{
    Auth::check();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $userId = $_SESSION['user_id'];

        if ($title && $content) {
            $topicId = Forum::createTopic($title, $content, $userId);
            header("Location: ?controller=forum&action=topic&id=$topicId");
            exit;
        }
    }

    require __DIR__ . '/../views/forum/create.php';
}


    // Обробка створення нової теми
    public function store()
    {
        Auth::check();
        $user = Auth::user();

        if (!empty($_POST['title']) && !empty($_POST['content'])) {
            Forum::createTopic($user['id'], $_POST['title'], $_POST['content']);
        }

        header('Location: ?controller=forum&action=index');
        exit;
    }

    // Обробка додавання відповіді
    public function reply()
{
    Auth::check();


    $userId = $_SESSION['user']['id'];
    $topicId = $_POST['topic_id'] ?? null;
    $content = trim($_POST['content'] ?? '');

    if (!$topicId || empty($content)) {
        echo "❌ Дані не передані.";
        return;
    }

    try {
        Forum::addReply($topicId, $userId, $content);
        header("Location: ?controller=forum&action=topic&id=$topicId");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
// Позначити тему вирішеною
public function solve()
{
    Auth::check();
    if (!empty($_GET['id'])) {
        Forum::markAsSolved((int)$_GET['id']);
    }
    header('Location: ?controller=forum&action=index');
    exit;
}

// Видалити тему
public function deleteTopic()
{
    Auth::check();
    if (!empty($_GET['id'])) {
        Forum::deleteTopic((int)$_GET['id']);
    }
    header('Location: ?controller=forum&action=index');
    exit;
}

// Видалити відповідь
public function deleteReply()
{
    Auth::check();

    if (!empty($_POST['post_id']) && !empty($_POST['topic_id'])) {
        $replyId = (int)$_POST['post_id'];
        $topicId = (int)$_POST['topic_id'];

        // Додаткова перевірка: чи поточний користувач є автором або адміном
        $user = $_SESSION['user'];
        $isAdmin = $user['role'] === 'admin';

        // Отримати всі відповіді, щоб перевірити власника
        $posts = Forum::getRepliesByTopicId($topicId);
        $targetPost = null;
        foreach ($posts as $post) {
            if ($post['id'] == $replyId) {
                $targetPost = $post;
                break;
            }
        }

        if ($targetPost && ($targetPost['user_id'] == $user['id'] || $isAdmin)) {
            Forum::deleteReply($replyId);
        } else {
            echo "❌ У вас немає прав на видалення цього повідомлення.";
            exit;
        }

        header("Location: ?controller=forum&action=topic&id=$topicId");
        exit;
    }

    echo "❌ Дані для видалення не передані.";
}


}
