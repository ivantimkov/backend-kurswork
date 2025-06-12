<?php
// Сторінка пошуку користувачів для додавання у друзі
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Пошук друзів</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
<div class="container" style="max-width: 800px;">
    <h2>🔍 Пошук користувачів</h2>

    <form method="get" action="" class="mb-4">
        <input type="hidden" name="controller" value="friend" />
        <input type="hidden" name="action" value="search" />
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Ім'я або email" value="<?= htmlspecialchars($query ?? '') ?>" />
            <button class="btn btn-primary" type="submit">Пошук</button>
        </div>
    </form>

    <?php if (isset($results)): ?>
        <?php if (count($results) === 0): ?>
            <p>Користувачів не знайдено.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($results as $user): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($user['full_name']) ?></strong><br />
                            <small><?= htmlspecialchars($user['email']) ?></small>
                        </div>
                        <form method="post" action="?controller=friend&action=send" style="margin:0;">
                            <input type="hidden" name="friend_id" value="<?= $user['id'] ?>" />
                            <button class="btn btn-sm btn-outline-primary">Додати у друзі</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <a href="?controller=friend&action=friend" class="btn btn-outline-secondary mt-3">Назад до друзів</a>
</div>
</body>
</html>
