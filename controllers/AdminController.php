<?php
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Forum.php';



class AdminController
{
    public function dashboard()
    {
        Auth::check(); // перевірка, що користувач увійшов

        $user = $_SESSION['user']; // отримуємо з сесії поточного користувача

        include __DIR__ . '/../views/admin/dashboard.php';
    }

   

public function users()
{
    Auth::check();
    $users = UserModel::getAllUsers(); // метод повинен повертати масив усіх користувачів
    include __DIR__ . '/../views/admin/users.php';
}


    public function events()
    {
        Auth::check('admin');
        $events = Event::getAll();
        include __DIR__ . '/../views/admin/events.php';
    }

    public function deleteUser()
    {
        Auth::check('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            UserModel::deleteById($id);
        }
        header('Location: ?controller=admin&action=users');
    }

    public function deleteEvent()
    {
        Auth::check('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            Event::deleteById($id);
        }
        header('Location: ?controller=admin&action=events');
    }
    public function editUser()
{
    

    $id = $_GET['id'] ?? null;
    if (!$id) {
        header('Location: ?controller=admin&action=users');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'user';

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $full_name, $email, $role, $id]);

        header('Location: ?controller=admin&action=users');
        exit;
    }

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Користувача не знайдено.";
        return;
    }
require_once __DIR__ . '/../views/admin/editUser.php';
    
}
public function editEvent()
{


    $id = $_GET['id'] ?? null;
    if (!$id) {
        header('Location: ?controller=admin&action=events');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $event_date = $_POST['event_date'] ?? '';
        $reminder_time = $_POST['reminder_time'] ?? null;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, reminder_time = ? WHERE id = ?");
        $stmt->execute([$title, $description, $event_date, $reminder_time, $id]);

        header('Location: ?controller=admin&action=events');
        exit;
    }

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "Подію не знайдено.";
        return;
    }

    require_once __DIR__ . '/../views/admin/editEvent.php';
}


    public function statistics()
{
    $db = Database::getConnection();

    // Кількість користувачів
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $usersCount = $stmt->fetchColumn();

    // Кількість подій
    $stmt = $db->query("SELECT COUNT(*) FROM events");
    $eventsCount = $stmt->fetchColumn();

    // Кількість подій з ненадісланими нагадуваннями (notified = 0)
    $stmt = $db->query("SELECT COUNT(*) FROM events WHERE reminder_time IS NOT NULL AND notified = 0");
    $pendingReminders = $stmt->fetchColumn();

    // Події за останні 30 днів (для графіка)
    $startDate = date('Y-m-d', strtotime('-30 days'));
    $stmt = $db->prepare("
        SELECT DATE(event_date) as day, COUNT(*) as count
        FROM events
        WHERE event_date >= :start
        GROUP BY day
        ORDER BY day ASC
    ");
    $stmt->execute([':start' => $startDate]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Підготовка графіка
    $labels = [];
    $countsMap = [];
    for ($i = 0; $i <= 30; $i++) {
        $dateKey = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('d M', strtotime("-$i days"));
        $countsMap[$dateKey] = 0;
    }
    $labels = array_reverse($labels);

    foreach ($rows as $row) {
        $countsMap[$row['day']] = (int)$row['count'];
    }

    $counts = array_values($countsMap);

    // Передача у view
    $stats = [
        'users_count' => $usersCount,
        'events_count' => $eventsCount,
        'pending_reminders' => $pendingReminders,
        'last_month_labels' => $labels,
        'last_month_data' => $counts,
    ];

    require_once __DIR__ . '/../views/admin/statistics.php';
}

public function reminders()
{
    $db = Database::getConnection(); 

    $filter = $_GET['filter'] ?? 'all';

    $query = "SELECT events.*, users.username 
              FROM events 
              JOIN users ON events.user_id = users.id";

    if ($filter === 'upcoming') {
        $query .= " WHERE event_date >= NOW()";
    } elseif ($filter === 'past') {
        $query .= " WHERE event_date < NOW()";
    }

    $query .= " ORDER BY event_date DESC";

    $stmt = $db->query($query);
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/../views/admin/reminders.php';
}




  public function backup()
{
    $host = 'localhost';
    $user = 'root';
    $password = '1234';
    $database = 'reminder_app';
    $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

    // Вказуємо повний шлях до mysqldump
    $mysqldumpPath = '"C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe"'; // заміни на свій шлях, якщо інший

    $command = "$mysqldumpPath --user=$user --password=$password --host=$host $database > $backupFile";

    system($command, $result);

    if ($result === 0 && file_exists($backupFile)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
        header('Content-Length: ' . filesize($backupFile));
        readfile($backupFile);
        unlink($backupFile); // Видалити після завантаження
        exit;
    } else {
        echo "<div class='alert alert-danger m-3'>❌ Помилка створення резервної копії.<br><small>Перевір шлях до mysqldump та права доступу.</small></div>";
    }
}

public function logs()
{
    $pdo = Database::getConnection();

    $filter = $_GET['filter'] ?? 'all';
    $filter = in_array($filter, ['all', 'user', 'action']) ? $filter : 'all';

    $logs = [];

    switch ($filter) {
        case 'user':
            $username = trim($_GET['username'] ?? '');
            if ($username !== '') {
                $stmt = $pdo->prepare(
                    "SELECT l.*, u.username 
                     FROM logs l 
                     LEFT JOIN users u ON l.user_id = u.id 
                     WHERE u.username LIKE :username 
                     ORDER BY l.timestamp DESC"
                );
                $stmt->execute(['username' => '%' . $username . '%']);
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        case 'action':
            $action = trim($_GET['action'] ?? '');
            if ($action !== '') {
                $stmt = $pdo->prepare(
                    "SELECT l.*, u.username 
                     FROM logs l 
                     LEFT JOIN users u ON l.user_id = u.id 
                     WHERE l.action LIKE :action 
                     ORDER BY l.timestamp DESC"
                );
                $stmt->execute(['action' => '%' . $action . '%']);
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        default:
            // Вивести всі логи
            $stmt = $pdo->query(
                "SELECT l.*, u.username 
                 FROM logs l 
                 LEFT JOIN users u ON l.user_id = u.id 
                 ORDER BY l.timestamp DESC"
            );
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }

    include __DIR__ . '/../views/admin/logs.php';
}

public function ajaxLogs()
{
    $pdo = Database::getConnection();

    $filter = $_GET['filter'] ?? 'all';
    $term = trim($_GET['term'] ?? '');

    $logs = [];

    if ($filter === 'user') {
        $stmt = $pdo->prepare(
            "SELECT l.*, u.username 
             FROM logs l 
             LEFT JOIN users u ON l.user_id = u.id 
             WHERE u.username LIKE :term 
             ORDER BY l.timestamp DESC"
        );
        $stmt->execute(['term' => '%' . $term . '%']);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($filter === 'action') {
        $stmt = $pdo->prepare(
            "SELECT l.*, u.username 
             FROM logs l 
             LEFT JOIN users u ON l.user_id = u.id 
             WHERE l.action LIKE :term 
             ORDER BY l.timestamp DESC"
        );
        $stmt->execute(['term' => '%' . $term . '%']);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $stmt = $pdo->query(
            "SELECT l.*, u.username 
             FROM logs l 
             LEFT JOIN users u ON l.user_id = u.id 
             ORDER BY l.timestamp DESC"
        );
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HTML-таблиця
    foreach ($logs as $log) {
        echo "<tr>
            <td>" . htmlspecialchars($log['id']) . "</td>
            <td>" . htmlspecialchars($log['username'] ?? 'Гість') . "</td>
            <td>" . htmlspecialchars($log['action']) . "</td>
            <td>" . htmlspecialchars($log['timestamp']) . "</td>
        </tr>";
    }

    if (empty($logs)) {
        echo '<tr><td colspan="4" class="text-center">Логів не знайдено</td></tr>';
    }

    exit;
}

public function forum()
{
    Auth::check();
    $topics = Forum::getAllTopics();
    require __DIR__ . '/../views/admin/forum.php';
}





}
