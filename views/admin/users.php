<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Користувачі</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>👥 Управління користувачами</h2>
        <a href="?controller=admin&action=dashboard" class="btn btn-outline-secondary">← Назад</a>
    </div>

    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Ім’я</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th class="text-center">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?= $u['role'] === 'admin' ? '<span class="badge bg-primary">Адмін</span>' : '<span class="badge bg-secondary">Користувач</span>' ?>
                            </td>
                            <td class="text-center">
                                <a href="?controller=admin&action=editUser&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning me-1">✏️ Редагувати</a>
                                <a href="?controller=admin&action=deleteUser&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити користувача?')">🗑️ Видалити</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Користувачів не знайдено.</div>
    <?php endif; ?>
</div>

</body>
</html>
