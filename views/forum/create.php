<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Нова тема</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>📝 Створити нову тему</h2>
    <form method="post" action="?controller=forum&action=store">
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок теми</label>
            <input type="text" name="title" id="title" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Повідомлення</label>
            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Опублікувати</button>
        <a href="?controller=forum&action=index" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
</body>
</html>
