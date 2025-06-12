<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Календар моїх подій</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Події інших користувачів */
        .foreign-event {
            background-color: #fcd5ce !important;
            border: 1px solid #f5a09b !important;
            color: #000 !important;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📅 Ваш календар подій</h2>
            <a href="?controller=user&action=home" class="btn btn-outline-secondary">← На головну</a>
        </div>

        <div id="calendar"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'uk',
                initialView: 'dayGridMonth',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today:    'Сьогодні',
                    month:    'Місяць',
                    week:     'Тиждень',
                    day:      'День',
                    list:     'Список'
                },
                events: {
                    url: '?controller=user&action=getUserEvents',
                    failure: function () {
                        alert('❌ Не вдалося завантажити події!');
                    }
                },
                eventClassNames: function(arg) {
                    const currentUsername = '<?php echo $_SESSION["user"]["username"]; ?>';
                    if (arg.event.extendedProps.owner_name && arg.event.extendedProps.owner_name !== currentUsername) {
                        return ['foreign-event'];
                    }
                    return [];
                },
                eventClick: function(info) {
                    const event = info.event;
                    const owner = event.extendedProps.owner_name || 'ви';
                    alert(`📌 Подія: ${event.title}
🗓 Дата: ${event.start.toLocaleString()}
👤 Автор: ${owner}`);
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
