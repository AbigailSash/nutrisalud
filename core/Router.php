<?php
// core/Router.php

class Router {
    public function run($url) {
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $urlParts = explode('/', $url);

        $controllerName = ucfirst($urlParts[0]) . 'Controller';
        $methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
        $params = array_slice($urlParts, 2);

        $controllerFile = '../app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "Error: El método $methodName no existe en $controllerName.";
            }
        } else {
            echo "Error 404: El controlador $controllerName no se encontró en la ruta $controllerFile.";
        }
    }
}
