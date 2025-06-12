<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="text-center mb-4">Реєстрація</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Логін</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email (необов’язково)</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Повторіть пароль</label>
                    <input type="password" name="confirm" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Зареєструватися</button>
            </form>
            <div class="text-center mt-3">
                <a href="?controller=auth&action=login">Вже є акаунт? Увійти</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
