<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'reminder_app');
define('DB_USER', 'root');
define('DB_PASS', '1234');

class Database
{
    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO(
                    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8",
                    DB_USER,
                    DB_PASS
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Помилка підключення: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}

?>
