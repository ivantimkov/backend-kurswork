<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Керування форумом</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">💬 Керування форумом</h2>
    <a href="?controller=forum&action=create" class="btn btn-success mb-3">➕ Створити нову тему</a>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Заголовок теми</th>
                <th>Автор</th>
                <th>Дата створення</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($topics as $topic): ?>
            <tr>
                <td><?= htmlspecialchars($topic['id']) ?></td>
                <td><?= htmlspecialchars($topic['title']) ?></td>
                <td><?= htmlspecialchars($topic['username']) ?></td>
                <td><?= htmlspecialchars($topic['created_at']) ?></td>
                <td>
                    <?php if ($topic['is_solved']): ?>
                        <span class="badge bg-success">Вирішено</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Не вирішено</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?controller=forum&action=topic&id=<?= $topic['id'] ?>" class="btn btn-primary btn-sm">Переглянути</a>
                    
                    <!-- Якщо тема ще не вирішена — дати можливість вирішити -->
                    <?php if (!$topic['is_solved']): ?>
                        <a href="?controller=forum&action=solve&id=<?= $topic['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Позначити тему як вирішену?')">✅ Вирішено</a>
                    <?php endif; ?>

                    <a href="?controller=forum&action=deleteTopic&id=<?= $topic['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Ви дійсно хочете видалити тему?')">🗑️ Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
