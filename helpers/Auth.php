<?php

class Auth
{
    public static function check()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: ?controller=auth&action=login");
            exit;
        }
    }

    public static function isAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }
}
