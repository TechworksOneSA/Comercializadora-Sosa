<?php

class Router {
    private $routes = [
        "GET"  => [],
        "POST" => [],
    ];

    // Registrar rutas GET
    public function get(string $path, string $action) {
        $this->routes["GET"][$path] = $action;
    }

    // Registrar rutas POST
    public function post(string $path, string $action) {
        $this->routes["POST"][$path] = $action;
    }

    public function dispatch() {
        $method = $_SERVER["REQUEST_METHOD"];
        $uri    = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        // Remover /index.php si está presente
        $uri = str_replace('/index.php', '', $uri);

        // Normalizar si APP_URL != '/'
        if (defined('APP_URL') && APP_URL !== '' && APP_URL !== '/' && str_starts_with($uri, APP_URL)) {
            $uri = substr($uri, strlen(APP_URL));
            if ($uri === '') {
                $uri = '/';
            }
        }
        
        // Si la URI está vacía después de normalizar, ponerla en /
        if ($uri === '') {
            $uri = '/';
        }

        // Si no hay rutas para el método, 404
        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo "404 - Ruta no encontrada";
            return;
        }

        // Recorremos TODAS las rutas registradas para ese método
        foreach ($this->routes[$method] as $route => $action) {

            /**
             * Soportar rutas con parámetros:
             *  Ejemplo de definición:
             *    $router->get('/admin/productos/editar/{id}', 'ProductosController@editar');
             *
             *  Internamente convertimos:
             *    /admin/productos/editar/{id}
             *  a un regex:
             *    #^/admin/productos/editar/([0-9a-zA-Z_-]+)$#
             */
            $pattern = preg_replace(
                '#\{[a-zA-Z_][a-zA-Z0-9_]*\}#',
                '([0-9a-zA-Z_-]+)',
                $route
            );

            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // $matches[0] es la ruta completa; los demás son parámetros
                array_shift($matches);

                // Acción tipo "ProductosController@editar"
                [$controller, $methodName] = explode("@", $action);

                // Instanciar y ejecutar con parámetros
                $instance = new $controller();
                call_user_func_array([$instance, $methodName], $matches);
                return;
            }
        }

        // Si ninguna ruta hizo match
        http_response_code(404);
        echo "404 - Ruta no encontrada";
    }
}
