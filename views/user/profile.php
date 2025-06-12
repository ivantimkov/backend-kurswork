<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Профіль користувача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
        }

        .profile-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        h2 {
            font-weight: 600;
        }

        .form-control:disabled {
            background-color: #e9ecef;
        }

        .btn + .btn {
            margin-left: 10px;
        }

        #calendar {
            display: none; /* приховано, якщо не використовується */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="profile-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Профіль</h2>
            <a href="?controller=user&action=home" class="btn btn-outline-secondary btn-sm">← На головну</a>
        </div>

        <form id="profileForm" class="mb-4">
            <div class="mb-3">
                <label for="username" class="form-label">Ім'я користувача (нікнейм)</label>
                <input type="text" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Повне ім'я</label>
                <input type="text" id="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Введіть ваше ім'я">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Введіть вашу пошту" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Зберегти зміни</button>
        </form>

        <div class="d-flex justify-content-between">
            <a href="?controller=user&action=changePasswordForm" class="btn btn-warning">Змінити пароль</a>
            <a href="?controller=auth&action=logout" class="btn btn-secondary">Вийти</a>
        </div>
    </div>
</div>

<script>
// Обробка форми зміни профілю через AJAX
document.getElementById('profileForm').addEventListener('submit', function(e){
    e.preventDefault();

    const full_name = document.getElementById('full_name').value.trim();
    const email = document.getElementById('email').value.trim();

    if(!email){
        alert('Email не може бути пустим');
        return;
    }

    fetch('?controller=user&action=updateProfile', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({full_name, email})
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            alert('Профіль успішно оновлено!');
        } else {
            alert(data.message || 'Помилка оновлення профілю.');
        }
    })
    .catch(() => alert('Помилка зв’язку з сервером'));
});
</script>
</body>
</html>
