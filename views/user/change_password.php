<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Зміна пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Зміна пароля</h2>
    <form id="passwordForm">
        <div class="mb-3">
            <label class="form-label">Поточний пароль</label>
            <input type="password" id="current" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Новий пароль</label>
            <input type="password" id="new" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Повторіть новий пароль</label>
            <input type="password" id="repeat" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Зберегти</button>
    </form>
    <a href="?controller=user&action=profile" class="btn btn-secondary mt-3">Назад</a>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', function(e){
    e.preventDefault();

    const current = document.getElementById('current').value;
    const newPass = document.getElementById('new').value;
    const repeat = document.getElementById('repeat').value;

    fetch('?controller=user&action=changePassword', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({current, new: newPass, repeat}),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        console.log('Server response:', data);
        if (data.status === 'success') {
            alert('Пароль змінено успішно!');
            window.location.href = '?controller=user&action=profile';
        } else {
            alert(data.message || 'Помилка зміни пароля');
        }
    })
    .catch((err) => {
        console.error('Помилка JSON-парсингу або запиту:', err);
        alert('Помилка зв’язку з сервером');
    });
});
</script>


</body>
</html>
