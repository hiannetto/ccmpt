<?php

namespace Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler, $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => '#^' . preg_replace('/:([a-zA-Z_]+)/', '(?P<$1>[a-zA-Z0-9_-]+)', $path) . '$#',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch($method, $uri) {
        $method = strtoupper($method);

        // Formulários com arquivo não funcionam com PUT no PHP: o frontend envia
        // POST com _method=PUT (ou cabeçalho X-HTTP-Method-Override).
        if ($method === 'POST') {
            $override = $_POST['_method'] ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override && in_array(strtoupper($override), ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = strtoupper($override);
            }
        }

        $path = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';

        // Remove prefixo quando a API é servida a partir de um subdiretório
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($base !== '/' && $base !== '.' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $pathMatched = false;
        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            foreach ($route['middleware'] as $mw) {
                (new $mw())->handle();
            }

            [$controller, $action] = explode('@', $route['handler']);
            $class = "Controllers\\$controller";
            $instance = new $class();
            return call_user_func_array([$instance, $action], array_values($params));
        }

        if ($pathMatched) {
            Response::error('Método não permitido.', 405);
        }
        Response::error('Rota não encontrada.', 404);
    }
}
