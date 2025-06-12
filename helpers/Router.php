<?php

class Router
{
    public static function route($controllerName, $actionName)
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();

                if (method_exists($controller, $actionName)) {
                    call_user_func([$controller, $actionName]);
                } else {
                    self::error("Метод $actionName не знайдено у контролері $controllerClass.");
                }
            } else {
                self::error("Контролер $controllerClass не знайдено.");
            }
        } else {
            self::error("Файл контролера $controllerFile не існує.");
        }
    }

    private static function error($message)
    {
        http_response_code(404);
        echo "<h1>404 - Не знайдено</h1>";
        echo "<p>$message</p>";
    }
}
