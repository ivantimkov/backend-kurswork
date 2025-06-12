<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>📊 Статистика — Адмін панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Статистика</h2>
        <a href="?controller=admin&action=dashboard" class="btn btn-outline-secondary">← Назад</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Кількість користувачів</h5>
                    <p class="display-4"><?= htmlspecialchars($stats['users_count'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Кількість подій</h5>
                    <p class="display-4"><?= htmlspecialchars($stats['events_count'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Активні нагадування</h5>
                    <p class="display-4"><?= htmlspecialchars($stats['pending_reminders'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4>Статистика за останній місяць</h4>
        <canvas id="eventsChart" height="150"></canvas>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('eventsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($stats['last_month_labels'] ?? []) ?>,
            datasets: [{
                label: 'Події за день',
                data: <?= json_encode($stats['last_month_data'] ?? []) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)', // bootstrap primary blue
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, stepSize: 1 }
            },
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
</script>

</body>
</html>
