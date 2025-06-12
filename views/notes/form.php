<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= isset($note) ? 'Редагувати' : 'Нова' ?> нотатка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2><?= isset($note) ? '✏️ Редагувати' : '➕ Нова' ?> нотатка</h2>
    <form method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок</label>
            <input type="text" class="form-control" id="title" name="title" required value="<?= $note['title'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Текст нотатки</label>
            <textarea class="form-control" id="content" name="content" rows="6" required><?= $note['content'] ?? '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">💾 Зберегти</button>
        <a href="?controller=note&action=index" class="btn btn-secondary">Назад</a>
    </form>
</div>
</body>
</html>
