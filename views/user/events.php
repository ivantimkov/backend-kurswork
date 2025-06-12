<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8" />
<title>Мої події</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Мої події</h2>
    <button id="addEventBtn" class="btn btn-primary mb-3">Додати подію</button>
    <div id="eventsList"></div>

    <!-- Модальне вікно для редагування -->
    <div class="modal" tabindex="-1" id="eventModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Подія</h5>
            <button type="button" class="btn-close" id="closeModal"></button>
          </div>
          <div class="modal-body">
            <form id="eventForm">
                <input type="hidden" id="eventId" />
                <div class="mb-3">
                    <label>Назва</label>
                    <input type="text" id="title" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label>Опис</label>
                    <textarea id="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Дата події</label>
                    <input type="datetime-local" id="event_date" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label>Час нагадування</label>
                    <input type="datetime-local" id="reminder_time" class="form-control" />
                </div>
                <button type="submit" class="btn btn-success">Зберегти</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <a href="?controller=auth&action=logout" class="btn btn-secondary mt-3">Вийти</a>
</div>

<script>
const modal = document.getElementById('eventModal');
const addEventBtn = document.getElementById('addEventBtn');
const closeModalBtn = document.getElementById('closeModal');
const eventForm = document.getElementById('eventForm');
const eventsList = document.getElementById('eventsList');

function openModal(event = null) {
    modal.style.display = 'block';
    if (event) {
        document.getElementById('eventId').value = event.id;
        document.getElementById('title').value = event.title;
        document.getElementById('description').value = event.description;
        document.getElementById('event_date').value = event.event_date.replace(' ', 'T');
        document.getElementById('reminder_time').value = event.reminder_time ? event.reminder_time.replace(' ', 'T') : '';
    } else {
        eventForm.reset();
        document.getElementById('eventId').value = '';
    }
}

function closeModal() {
    modal.style.display = 'none';
}

function loadEvents() {
    fetch('?controller=event&action=list')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            renderEvents(data.data);
        } else {
            eventsList.innerHTML = '<p>Помилка завантаження подій</p>';
        }
    });
}

function renderEvents(events) {
    if (!events.length) {
        eventsList.innerHTML = '<p>Подій немає</p>';
        return;
    }
    let html = '<ul class="list-group">';
    events.forEach(e => {
        html += `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>${e.title}</strong><br/>
                <small>${e.event_date.replace('T', ' ')}</small>
            </div>
            <div>
                <button class="btn btn-sm btn-primary me-2" onclick="editEvent(${e.id})">Редагувати</button>
                <button class="btn btn-sm btn-danger" onclick="deleteEvent(${e.id})">Видалити</button>
            </div>
        </li>`;
    });
    html += '</ul>';
    eventsList.innerHTML = html;
}

function editEvent(id) {
    fetch(`?controller=event&action=list`)
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            const event = data.data.find(ev => ev.id == id);
            if(event) {
                openModal(event);
            }
        }
    });
}

function deleteEvent(id) {
    if(!confirm('Ви впевнені, що хочете видалити подію?')) return;
    fetch('?controller=event&action=delete', {
        method: 'POST',
        body: JSON.stringify({id}),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            loadEvents();
        } else {
            alert(data.message);
        }
    });
}

eventForm.addEventListener('submit', e => {
    e.preventDefault();
    const id = document.getElementById('eventId').value;
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const event_date = document.getElementById('event_date').value;
    const reminder_time = document.getElementById('reminder_time').value;

    fetch('?controller=event&action=save', {
        method: 'POST',
        body: JSON.stringify({id, title, description, event_date, reminder_time}),
        headers: {'Content-Type': 'application/json'}
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            closeModal();
            loadEvents();
        } else {
            alert(data.message);
        }
    });
});

addEventBtn.addEventListener('click', () => openModal());
closeModalBtn.addEventListener('click', closeModal);

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}

loadEvents();
</script>
</body>
</html>
