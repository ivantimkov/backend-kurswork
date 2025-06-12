<?php
// Запуск сесії (лише якщо вона ще не стартувала)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Підключення конфігурації та автозавантаження
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/Router.php';

// Визначення контролера та дії з URL (наприклад: ?controller=auth&action=login)
$controllerName = $_GET['controller'] ?? 'auth';
$actionName = $_GET['action'] ?? 'login';

// Запускаємо маршрутизатор
Router::route($controllerName, $actionName);
