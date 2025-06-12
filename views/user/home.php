<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Головна</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid bg-white border-bottom py-2 mb-3">
    <div class="d-flex justify-content-end">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <a class="btn btn-outline-primary me-2" href="?controller=admin&action=dashboard">Адмін панель</a>
<?php endif; ?>
<a class="btn btn-outline-primary me-2" href="?controller=note&action=index">Нотатки</a>
<a class="btn btn-outline-primary me-2" href="?controller=friend&action=friend">Друзі</a>

        <a class="btn btn-outline-primary me-2" href="?controller=user&action=calendar">Календар</a>
        <a class="btn btn-outline-primary me-2" href="?controller=forum&action=index">Форум</a>
        <a class="btn btn-outline-primary me-2" href="?controller=user&action=profile">Профіль</a>
        <a class="btn btn-outline-danger" href="?controller=auth&action=logout">Вийти</a>
    </div>
</div>
<div class="container mt-2">
    <h2>👋 Привіт, <?= htmlspecialchars($user['username']) ?>!</h2>
    <p>Це твоя головна сторінка. Тут з’являться твої події.</p>

    <!-- Кнопка для створення нової події -->
    <button id="openCreateEvent" class="btn btn-success mb-3">Створити нову подію</button>
<!-- Фільтри -->
<div class="card p-3 mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" id="filterTitle" class="form-control" placeholder="Пошук за назвою">
        </div>
        <div class="col-md-4">
            <input type="date" id="filterDate" class="form-control" placeholder="Фільтр за датою">
        </div>
        <div class="col-md-4">
            <select id="filterOwner" class="form-select">
                <option value="">Всі події</option>
                <option value="own">Мої події</option>
                <option value="friend">Події від друзів</option>
            </select>
        </div>
    </div>
</div>

    <!-- Список подій -->
    <ul id="eventsList" class="list-group mb-3">
        <!-- Тут JS буде підвантажувати події -->
    </ul>

</div>

<!-- Модальне вікно для створення / редагування події -->
<div class="modal" tabindex="-1" id="eventModal" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Створити подію</h5>
        <button type="button" class="btn-close" id="closeModal"></button>
      </div>
      <div class="modal-body">
        <form id="eventForm">
            <input type="hidden" id="eventId" value="" />
            <div class="mb-3">
                <label for="title" class="form-label">Назва</label>
                <input type="text" id="title" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="event_date" class="form-label">Дата події</label>
                <input type="datetime-local" id="event_date" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Час нагадування</label>
                <input type="datetime-local" id="reminder_time" class="form-control" />
            </div>
            <div class="mb-3">
    <label for="friends" class="form-label">Додати друзів до події (необов’язково)</label>
    <select id="friends" class="form-select" multiple>
        <!-- Друзі будуть підвантажені JS -->
    </select>
    <div class="form-text">Утримуйте Ctrl або Cmd для вибору кількох друзів</div>
</div>

            <button type="submit" class="btn btn-primary">Зберегти</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

// Елементи
const modal = document.getElementById('eventModal');
const openBtn = document.getElementById('openCreateEvent');
const closeModalBtn = document.getElementById('closeModal');
const eventForm = document.getElementById('eventForm');
const eventsList = document.getElementById('eventsList');
const modalTitle = document.querySelector('.modal-title');


function loadFriends() {
    fetch('?controller=friend&action=listJson')
        .then(res => res.json())
        .then(data => {
            const friendsSelect = document.getElementById('friends');
            friendsSelect.innerHTML = '';
            if (data.friends && data.friends.length > 0) {
                data.friends.forEach(friend => {
                    const option = document.createElement('option');
                    option.value = friend.id;
                    option.textContent = friend.full_name;
                    friendsSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.textContent = 'У вас немає друзів';
                option.disabled = true;
                friendsSelect.appendChild(option);
            }
        })
        .catch(() => {
            console.error('Не вдалося завантажити друзів');
        });
    
}
// Відкриття модалки для створення нової події
function openModal() {
    modal.style.display = 'block';
    modalTitle.textContent = 'Створити подію';
    eventForm.reset();
    document.getElementById('eventId').value = '';
    loadFriends();

}

// Відкриття модалки для редагування події
function openModalEdit(event) {
    modal.style.display = 'block';
    modalTitle.textContent = 'Редагувати подію';
    document.getElementById('eventId').value = event.id;
    document.getElementById('title').value = event.title;
    document.getElementById('description').value = event.description || '';
    document.getElementById('event_date').value = formatDateForInput(event.event_date);
    document.getElementById('reminder_time').value = event.reminder_time ? formatDateForInput(event.reminder_time) : '';
    loadFriends();

}

// Закриття модалки
function closeModal() {
    modal.style.display = 'none';
}

// Форматування дати у формат для datetime-local input (YYYY-MM-DDTHH:mm)
function formatDateForInput(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    if (isNaN(d)) return '';
    const pad = n => n.toString().padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

// Безпечне вставлення тексту (escape HTML)
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Завантаження подій з сервера
function loadEvents() {
    fetch('?controller=event&action=listJson')
        .then(res => res.json())
        .then(data => {
            eventsList.innerHTML = '';

            const titleFilter = document.getElementById('filterTitle').value.toLowerCase();
            const dateFilter = document.getElementById('filterDate').value;
            const ownerFilter = document.getElementById('filterOwner').value;

            let events = data.events || [];

            // Фільтрація
            events = events.filter(event => {
                const matchTitle = event.title.toLowerCase().includes(titleFilter);
                const matchDate = !dateFilter || event.event_date.startsWith(dateFilter);
                const matchOwner = !ownerFilter ||
                    (ownerFilter === 'own' && event.is_owner) ||
                    (ownerFilter === 'friend' && !event.is_owner);

                return matchTitle && matchDate && matchOwner;
            });

            if (events.length === 0) {
                eventsList.innerHTML = '<li class="list-group-item">Подій немає</li>';
                return;
            }

            events.forEach(event => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap';

                let friendNote = '';
                if (event.is_owner === false && event.added_by_friend_name) {
                    friendNote = `<br><small class="text-info">Ваш друг ${escapeHtml(event.added_by_friend_name)} додав вас до події</small>`;
                }

                li.innerHTML = `
                    <div style="max-width: 70%;">
                        <strong>${escapeHtml(event.title)}</strong><br>
                        <small>${escapeHtml(event.description)}</small><br>
                        <small>Дата події: ${escapeHtml(event.event_date)}</small><br>
                        <small>Нагадування: ${escapeHtml(event.reminder_time || '-')}</small>
                        ${friendNote}
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <button class="btn btn-sm btn-primary me-2 editBtn">Редагувати</button>
                        <button class="btn btn-sm btn-danger deleteBtn">Видалити</button>
                    </div>
                `;

                li.querySelector('.editBtn').addEventListener('click', () => openModalEdit(event));
                li.querySelector('.deleteBtn').addEventListener('click', () => {
                    if (confirm('Ви дійсно хочете видалити цю подію?')) {
                        fetch('?controller=event&action=delete', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({id: event.id})
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.status === 'success') {
                                loadEvents();
                            } else {
                                alert(result.message || 'Помилка видалення');
                            }
                        })
                        .catch(() => alert('Помилка зв’язку з сервером'));
                    }
                });

                eventsList.appendChild(li);
            });
        })
        .catch(() => {
            eventsList.innerHTML = '<li class="list-group-item text-danger">Не вдалося завантажити події</li>';
        });
}


// Події кнопок
openBtn.addEventListener('click', openModal);
closeModalBtn.addEventListener('click', closeModal);

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}

// Відправка форми створення/редагування події
eventForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('eventId').value;
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    const event_date = document.getElementById('event_date').value;
    const reminder_time = document.getElementById('reminder_time').value;
    const friendsSelect = document.getElementById('friends');
const selectedFriends = Array.from(friendsSelect.selectedOptions).map(opt => opt.value);

    if(!title || !event_date) {
        alert('Будь ласка, заповніть обов’язкові поля: назва та дата події.');
        return;
    }

    fetch('?controller=event&action=save', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, title, description, event_date, reminder_time, friend_ids: selectedFriends})
})
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Подія успішно збережена!');
            closeModal();
            loadEvents();
        } else {
            alert(data.message || 'Помилка збереження події.');
        }
    })
    .catch(() => alert('Помилка зв’язку з сервером.'));
});


// Оновлення подій при зміні фільтрів
document.getElementById('filterTitle').addEventListener('input', loadEvents);
document.getElementById('filterDate').addEventListener('change', loadEvents);
document.getElementById('filterOwner').addEventListener('change', loadEvents);

 

// Завантаження подій при старті
window.onload = loadEvents;

</script>


</body>
</html>
