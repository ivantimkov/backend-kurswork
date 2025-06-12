<?php
$userId = $_SESSION['user']['id'];
$isAdmin = $_SESSION['user']['role'] === 'admin'; // Перевірка ролі
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Форум</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Форум</h2>
        <div>
            <a href="?controller=forum&action=create" class="btn btn-primary">➕ Нова тема</a>
            <?php if ($isAdmin): ?>
                <a href="?controller=admin&action=forum" class="btn btn-warning">🛠️ Керування форумом</a>
            <?php endif; ?>
            <a href="?controller=user&action=home" class="btn btn-secondary">🏠 На головну</a>
        </div>
    </div>

    <?php if (count($topics) > 0): ?>
        <ul class="list-group">
            <?php foreach ($topics as $topic): ?>
                <?php
                // Перевірка: чи користувач залишив повідомлення в темі
                $pdo = Database::getConnection();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_posts WHERE topic_id = ? AND user_id = ?");
                $stmt->execute([$topic['id'], $userId]);
                $hasUserPosted = $stmt->fetchColumn() > 0;

                $isSolved = $topic['is_solved']; // передається разом з темами
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="?controller=forum&action=topic&id=<?= $topic['id'] ?>">
                            <strong><?= htmlspecialchars($topic['title']) ?></strong>
                        </a>
                        <div class="text-muted small">
                            Автор: <?= htmlspecialchars($topic['username']) ?> | <?= $topic['created_at'] ?>
                        </div>
                    </div>
                    <?php if ($isSolved): ?>
                        <span class="badge bg-success">✅</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>На форумі ще немає тем.</p>
    <?php endif; ?>
</div>
</body>
</html>
