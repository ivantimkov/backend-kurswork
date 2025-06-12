<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Події</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📅 Керування подіями</h2>
        <a href="?controller=admin&action=dashboard" class="btn btn-outline-secondary">← Назад</a>
    </div>

    <?php if (!empty($events)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Опис</th>
                        <th>Дата події</th>
                        <th>Дата нагадування</th>
                        <th>Дата створення події</th>
                        <th>Користувач</th>
                        <th class="text-center">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['id']) ?></td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= htmlspecialchars($event['description']) ?></td>
                            <td><?= htmlspecialchars($event['event_date']) ?></td>
                            <td><?= htmlspecialchars($event['reminder_time']) ?></td>
                            <td><?= htmlspecialchars($event['created_at']) ?></td>
                            <td><?= htmlspecialchars($event['username']) ?></td>
                            <td class="text-center">
                                <a href="?controller=admin&action=editEvent&id=<?= $event['id'] ?>" class="btn btn-sm btn-warning me-1">✏️ Редагувати</a>
                                <a href="?controller=admin&action=deleteEvent&id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити подію?')">🗑️ Видалити</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Подій не знайдено.</div>
    <?php endif; ?>

</div>

</body>
</html>
