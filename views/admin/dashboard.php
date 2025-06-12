<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Адмін-панель</a>
        <div class="d-flex">
            <a href="?controller=user&action=home" class="btn btn-outline-light btn-sm me-2">На головну</a>
            <a href="?controller=auth&action=logout" class="btn btn-outline-light btn-sm">Вийти</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">👑 Вітаю, Адміністраторе <?= htmlspecialchars($user['username']) ?>!</h2>
    <p class="mb-4">Оберіть розділ для керування:</p>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="?controller=admin&action=users" class="btn btn-primary w-100 p-3">
                👥 Керування користувачами
            </a>
        </div>
        <div class="col-md-4">
            <a href="?controller=admin&action=events" class="btn btn-success w-100 p-3">
                📅 Керування подіями
            </a>
        </div>
        <div class="col-md-4">
    <a href="?controller=admin&action=forum" class="btn btn-secondary w-100 p-3">
        💬 Керування форумом
    </a>
</div>
        <div class="col-md-3">
        <a href="?controller=admin&action=statistics" class="btn btn-info w-100 p-3">
            📊 Статистика
        </a>
    </div>
    <div class="col-md-4">
    <a href="?controller=admin&action=reminders" class="btn btn-info w-100 p-3">
        🔔 Повідомлення / Нагадування
    </a>
</div>
<div class="col-md-4">
    <a href="?controller=admin&action=logs" class="btn btn-info w-100 p-3">
        📋 Логи дій
    </a>
</div>

       <div class="col-md-4">
    <a href="?controller=admin&action=backup" class="btn btn-warning w-100 p-3">
        💾 Резервне копіювання бази
    </a>
        </div>

        <div class="col-md-4">
            <a href="?controller=auth&action=logout" class="btn btn-danger w-100 p-3">
                🚪 Вийти
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
