<?php
$userId = $_SESSION['user']['id'];
$isAdmin = $_SESSION['user']['role'] === 'admin';
?>

<?php if (!isset($topic['id'])) {
    echo "❌ Помилка: тема не знайдена.";
    exit;
} ?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($topic['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2><?= htmlspecialchars($topic['title']) ?></h2>
    <p><strong>Автор:</strong> <?= htmlspecialchars($topic['username']) ?> | <?= $topic['created_at'] ?></p>

    <hr>
    <div>
        <h5>Опис теми:</h5>
        <p><?= nl2br(htmlspecialchars($initialPost['content'] ?? '')) ?></p>
    </div>

    <hr>
    <h5>Відповіді:</h5>

    <?php if (empty($posts)): ?>
        <p>Немає відповідей</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="border rounded p-2 mb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1"><strong><?= htmlspecialchars($post['username']) ?></strong> — <?= $post['created_at'] ?></p>
                        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    </div>
                    <?php if ($post['user_id'] == $userId || $isAdmin): ?>
                        <form method="post" action="?controller=forum&action=deleteReply" onsubmit="return confirm('Ви дійсно хочете видалити це повідомлення?');">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <input type="hidden" name="topic_id" value="<?= $topic['id'] ?>">
                            <button class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="post" action="?controller=forum&action=reply">
        <input type="hidden" name="topic_id" value="<?= $topic['id'] ?>" />
        <div class="mb-3">
            <label for="reply" class="form-label">Напишіть відповідь:</label>
            <textarea name="content" id="reply" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Надіслати</button>
        <a href="?controller=forum&action=index" class="btn btn-secondary">Назад</a>
    </form>
</div>
</body>
</html>
