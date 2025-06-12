<?php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/UserModel.php';

class NoteController
{
    public function index()
    {
        Auth::check();
        $userId = $_SESSION['user']['id'];
        $notes = Note::getAllByUser($userId);
        require __DIR__ . '/../views/notes/index.php';
    }

    public function create()
    {
        Auth::check();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Note::create($_SESSION['user']['id'], $_POST['title'], $_POST['content']);
            header('Location: ?controller=note&action=index');
            exit;
        }
        require __DIR__ . '/../views/notes/form.php';
    }

    public function edit()
    {
        Auth::check();
        $userId = $_SESSION['user']['id'];
        $id = $_GET['id'] ?? null;
        $note = Note::getById($id, $userId);
        if (!$note) {
            header('Location: ?controller=note&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Note::update($id, $userId, $_POST['title'], $_POST['content']);
            header('Location: ?controller=note&action=index');
            exit;
        }

        require __DIR__ . '/../views/notes/form.php';
    }

    public function delete()
    {
        Auth::check();
        $userId = $_SESSION['user']['id'];
        $id = $_GET['id'] ?? null;
        Note::delete($id, $userId);
        header('Location: ?controller=note&action=index');
        exit;
    }
}

