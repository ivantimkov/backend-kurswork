<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування події</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">🗓️ Редагування події</h2>

    <form method="post" action="?controller=admin&action=editEvent&id=<?= $event['id'] ?>" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Назва</label>
            <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Опис</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Дата події</label>
            <input type="datetime-local" name="event_date" value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Дата нагадування</label>
            <input type="datetime-local" name="reminder_time" value="<?= date('Y-m-d\TH:i', strtotime($event['reminder_time'])) ?>" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">💾 Зберегти</button>
        <a href="?controller=admin&action=events" class="btn btn-secondary ms-2">← Назад</a>
    </form>
</div>

</body>
</html>
