<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Друзі і запити в друзі</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .top-buttons {
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="p-4 bg-light">

<div class="container" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center top-buttons">
        <h2>Друзі та запити</h2>
        <div>
            <a href="?controller=friend&action=search" class="btn btn-primary me-2">🔍 Пошук друзів</a>
            <a href="?controller=user&action=home" class="btn btn-outline-secondary">← Назад</a>
        </div>
    </div>

    <h4 class="section-title">📬 Запити в друзі</h4>

    <?php if (!empty($requests)): ?>
        <?php foreach ($requests as $request): ?>
            <div class="card mb-3 p-3">
                <strong>Ім'я:</strong> <?= htmlspecialchars($request['full_name'] ?? 'Невідомо') ?><br />
                <strong>Email:</strong> <?= htmlspecialchars($request['email'] ?? 'Невідомо') ?><br />

                <?php if (isset($request['id'])): ?>
                    <div class="mt-2">
                        <a href="?controller=friend&action=accept&id=<?= (int)$request['id'] ?>" class="btn btn-sm btn-success me-2">Прийняти</a>
                        <a href="?controller=friend&action=reject&id=<?= (int)$request['id'] ?>" class="btn btn-sm btn-danger">Відхилити</a>
                    </div>
                <?php else: ?>
                    <span class="text-danger">Немає ідентифікатора запиту</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Немає нових запитів в друзі.</div>
    <?php endif; ?>

    <hr>

    <h4 class="section-title">👥 Ваші друзі</h4>

   <?php if (!empty($friends)): ?>
    <ul class="list-group">
        <?php foreach ($friends as $friend): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($friend['full_name'] ?? 'Невідомо') ?></strong><br>
                    <small><?= htmlspecialchars($friend['email'] ?? '') ?></small>
                </div>
                <div class="d-flex align-items-center">
                    <?php if (isset($friend['id'])): ?>
                        <a href="?controller=chat&action=index&friend_id=<?= (int)$friend['id'] ?>" 
                           class="btn btn-sm btn-outline-primary me-2">Чат</a>
                        <a href="?controller=friend&action=remove&id=<?= (int)$friend['id'] ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Ви впевнені, що хочете видалити цього друга?')">Видалити</a>
                    <?php else: ?>
                        <span class="text-muted">ID друга не визначено</span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="alert alert-warning">У вас поки що немає друзів.</div>
<?php endif; ?>
</div>

</body>
</html>
