<?php

date_default_timezone_set('Europe/Kyiv');

file_put_contents(__DIR__ . '/log.txt', date('Y-m-d H:i:s') . " - 📩 Скрипт запущено\n", FILE_APPEND);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $pdo = Database::getConnection();

    // Витяг подій, для яких настав час нагадування
    $stmt = $pdo->prepare("
        SELECT e.*, u.email, u.username 
        FROM events e
        JOIN users u ON e.user_id = u.id
        WHERE e.reminder_time IS NOT NULL 
          AND e.reminder_time <= NOW()
          AND e.notified = 0
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($events)) {
        file_put_contents(__DIR__ . '/log.txt', date('Y-m-d H:i:s') . " - ℹ️ Немає подій для нагадування\n", FILE_APPEND);
        exit;
    }

    foreach ($events as $event) {
        $mail = new PHPMailer(true);

        try {
            // SMTP конфігурація
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vanatimkov@gmail.com';        // свій Gmail
            $mail->Password   = 'hyaq pccw lcxd luxl';          // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Основні параметри
            $mail->setFrom('vanatimkov@gmail.com', '📅 Нагадування');
            $mail->addAddress($event['email'], $event['username']);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = "⏰ Нагадування: {$event['title']}";
            $mail->Body = "
                <h3>Привіт, {$event['username']}!</h3>
                <p>Це нагадування про вашу подію: <strong>{$event['title']}</strong>.</p>
                <p>🗓 Дата події: <strong>{$event['event_date']}</strong></p>
                <p>📋 Опис: {$event['description']}</p>
                <hr>
                <small>Це автоматичне повідомлення. Ви отримали його, бо зареєстровані в ReminderApp.</small>
            ";
            $mail->AltBody = "Привіт, {$event['username']}! Це нагадування про подію '{$event['title']}' ({$event['event_date']}).";

            $mail->send();

            // Логування успішного надсилання
            file_put_contents(__DIR__ . '/log.txt', date('Y-m-d H:i:s') . " - ✅ Надіслано на: {$event['email']}\n", FILE_APPEND);

            // Оновлення в БД
            $update = $pdo->prepare("UPDATE events SET notified = 1 WHERE id = ?");
            $update->execute([$event['id']]);

        } catch (Exception $e) {
            $errorMessage = date('Y-m-d H:i:s') . " - ❌ Помилка email на {$event['email']}: {$mail->ErrorInfo}\n";
            file_put_contents(__DIR__ . '/error_log.txt', $errorMessage, FILE_APPEND);
        }
    }

} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " - ❌ Загальна помилка: {$e->getMessage()}\n";
    file_put_contents(__DIR__ . '/error_log.txt', $errorMessage, FILE_APPEND);
}
