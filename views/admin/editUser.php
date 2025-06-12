<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування користувача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">✏️ Редагування користувача</h2>

    <form method="post" action="?controller=admin&action=editUser&id=<?= $user['id'] ?>" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Нікнейм</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Повне ім’я</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Роль</label>
            <select name="role" class="form-select">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Користувач</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Адмін</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">💾 Зберегти</button>
        <a href="?controller=admin&action=users" class="btn btn-secondary ms-2">← Назад</a>
    </form>
</div>

</body>
</html>
