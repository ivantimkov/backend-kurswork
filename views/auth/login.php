<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center mb-4">Вхід до системи</h3>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Логін</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Увійти</button>
                    <div class="text-center mt-3">
    <a href="?controller=auth&action=register">Ще не маєш акаунта? Зареєструватися</a>
</div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>
