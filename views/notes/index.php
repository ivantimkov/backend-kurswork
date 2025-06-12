<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Нотатки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h2>🧠 Мої нотатки</h2>
        <a href="?controller=note&action=create" class="btn btn-primary">➕ Створити нотатку</a>
        <a href="?controller=user&action=home" class="btn btn-secondary">🏠 На головну</a>
    </div>

    <?php if (count($notes) > 0): ?>
        <?php foreach ($notes as $note): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <strong><?= htmlspecialchars($note['title']) ?></strong>
                    <div>
                        <a href="?controller=note&action=edit&id=<?= $note['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                        <a href="?controller=note&action=delete&id=<?= $note['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити?')">🗑️</a>
                    </div>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                    <small class="text-muted">Оновлено: <?= $note['updated_at'] ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>У вас поки немає нотаток.</p>
    <?php endif; ?>
</div>
</body>
</html>
