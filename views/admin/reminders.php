<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>📬 Повідомлення / Нагадування — Адмін панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">📬 Повідомлення / Нагадування</h2>
                <a href="?controller=admin&action=dashboard" class="btn btn-outline-secondary">← Назад</a>
            </div>

            <form method="get" class="mb-3">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="reminders">
                <div class="btn-group" role="group">
                    <button type="submit" name="filter" value="all" class="btn btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">Усі</button>
                    <button type="submit" name="filter" value="upcoming" class="btn btn-outline-success <?= $filter === 'upcoming' ? 'active' : '' ?>">Майбутні</button>
                    <button type="submit" name="filter" value="past" class="btn btn-outline-secondary <?= $filter === 'past' ? 'active' : '' ?>">Минулі</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>👤 Користувач</th>
                            <th>📌 Назва</th>
                            <th>📅 Дата події</th>
                            <th>⏰ Нагадування</th>
                            <th>🕓 Створено</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reminders as $reminder): ?>
                            <tr>
                                <td><?= htmlspecialchars($reminder['username']) ?></td>
                                <td><?= htmlspecialchars($reminder['title']) ?></td>
                                <td><?= htmlspecialchars($reminder['event_date']) ?></td>
                                <td><?= $reminder['reminder_time'] ?? '—' ?></td>
                                <td><?= $reminder['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
</body>
</html>
