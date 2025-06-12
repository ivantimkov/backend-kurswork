<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>📜 Логи — Адмін панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">

<div class="container" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📜 Логи дій користувачів</h2>
        <a href="?controller=admin&action=dashboard" class="btn btn-outline-secondary">← Назад</a>
    </div>

    <form class="row g-3 mb-4 align-items-end" onsubmit="return false;">
        <div class="col-md-3">
            <label for="filter" class="form-label">Фільтр</label>
            <select id="filter" class="form-select">
                <option value="all">Усі логи</option>
                <option value="user">По користувачу</option>
                <option value="action">По дії</option>
            </select>
        </div>

        <div class="col-md-4" id="usernameGroup" style="display: none;">
            <label for="username" class="form-label">Ім'я користувача</label>
            <input type="text" id="username" class="form-control" />
        </div>

        <div class="col-md-5" id="actionGroup" style="display: none;">
            <label for="action" class="form-label">Дія (текст)</label>
            <input type="text" id="actionText" class="form-control" />
        </div>
    </form>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Користувач</th>
                <th>Дія</th>
                <th>Час</th>
            </tr>
        </thead>
        <tbody id="logTableBody">
            <tr><td colspan="4" class="text-center">Завантаження...</td></tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filter');
    const usernameInput = document.getElementById('username');
    const actionInput = document.getElementById('actionText');
    const tbody = document.getElementById('logTableBody');

    function fetchLogs(filter, term = '') {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `?controller=admin&action=ajaxLogs&filter=${filter}&term=${encodeURIComponent(term)}`);
        xhr.onload = function () {
            if (xhr.status === 200) {
                tbody.innerHTML = xhr.responseText;
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Помилка завантаження</td></tr>';
            }
        };
        xhr.onerror = function () {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Помилка з\'єднання</td></tr>';
        };
        xhr.send();
    }

    function onInputChange() {
        const filter = filterSelect.value;
        let term = '';
        if (filter === 'user') term = usernameInput.value.trim();
        else if (filter === 'action') term = actionInput.value.trim();
        fetchLogs(filter, term);
    }

    filterSelect.addEventListener('change', () => {
        const filter = filterSelect.value;

        document.getElementById('usernameGroup').style.display = (filter === 'user') ? 'block' : 'none';
        document.getElementById('actionGroup').style.display = (filter === 'action') ? 'block' : 'none';

        fetchLogs(filter);
    });

    if (usernameInput) usernameInput.addEventListener('input', onInputChange);
    if (actionInput) actionInput.addEventListener('input', onInputChange);

    fetchLogs('all');
});

</script>



</body>
</html>
