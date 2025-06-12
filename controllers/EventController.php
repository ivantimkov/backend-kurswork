<?php

require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../helpers/Auth.php';

class EventController
{
    // Повертає всі події користувача у форматі JSON (для AJAX)
    public function listJson()
{
    Auth::check();
    $user = Auth::user();
    $userId = $user['id'];

    $events = Event::getAllByUser($userId);

    foreach ($events as &$event) {
        // Додаємо is_owner
        $event['is_owner'] = ($event['user_id'] == $userId);

        // Якщо не власник, додаємо ім’я друга, який створив подію
        if (!$event['is_owner']) {
            $friendName = Event::getUserNameById($event['user_id']);
            $event['added_by_friend_name'] = $friendName ?: 'Невідомо';
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'events' => $events]);
    exit;
}


    // Створення або оновлення події (отримує JSON)
    public function save()
{
    Auth::check();
    $user = Auth::user();
    $input = json_decode(file_get_contents('php://input'), true);

    // Валідація обов'язкових полів
    if (empty($input['title']) || empty($input['event_date'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Назва та дата події обов’язкові']);
        exit;
    }

    $id = !empty($input['id']) ? (int)$input['id'] : null;
    $title = trim($input['title']);
    $description = trim($input['description'] ?? '');
    $event_date = $input['event_date'];
    $reminder_time = $input['reminder_time'] ?? null;
    $friend_ids = isset($input['friend_ids']) && is_array($input['friend_ids']) ? $input['friend_ids'] : [];

    if ($id) {
        // Отримуємо подію, щоб перевірити доступ
        $event = Event::getById($id, $user['id']);
        if (!$event) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Подію не знайдено або немає доступу']);
            exit;
        }

        // Оновлюємо подію
        $result = Event::update($id, $user['id'], $title, $description, $event_date, $reminder_time);
        if ($result) {
            // Оновлюємо друзів, прив’язаних до події
            $friendsUpdated = Event::updateFriends($id, $friend_ids);
            if ($friendsUpdated) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Помилка оновлення друзів']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Помилка оновлення події']);
        }
    } else {
        // Створюємо нову подію
        $event_id = Event::create($user['id'], $title, $description, $event_date, $reminder_time);
        if ($event_id) {
            // Додаємо друзів до нової події
            $friendsUpdated = Event::updateFriends($event_id, $friend_ids);
            if ($friendsUpdated) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Помилка додавання друзів']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Помилка збереження в базі']);
        }
    }
    exit;
}



    // Видалення події (POST id)
    public function delete()
    {
        Auth::check();
        $user = Auth::user();

        $input = json_decode(file_get_contents('php://input'), true);
        $id = !empty($input['id']) ? (int)$input['id'] : null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Невказано ID події']);
            exit;
        }

        $event = Event::getById($id, $user['id']);
        if (!$event) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Подію не знайдено або немає доступу']);
            exit;
        }

        $result = Event::delete($id, $user['id']);
        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Помилка видалення з бази']);
        }
        exit;
    }
}
