<?php

namespace Core\Router;

use Utils\HttpStatusCode;

class Router
{
    private $routes = [];

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($route['callback'], $matches);
            }
        }
        http_response_code(HttpStatusCode::NOT_FOUND);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found']);
        exit();
    }

    public function get($pattern, $callback)
    {
        $this->addRoute('GET', $pattern, $callback);
    }

    public function post($pattern, $callback)
    {
        $this->addRoute('POST', $pattern, $callback);
    }

    public function put($pattern, $callback)
    {
        $this->addRoute('PUT', $pattern, $callback);
    }

    public function delete($pattern, $callback)
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    public function loadRoutesFromFile($filePath): void
    {
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception("Route file not found: $filePath");
        }
    }

    private function addRoute($method, $pattern, $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $this->convertPattern($pattern),
            'callback' => $callback,
        ];
    }

    private function convertPattern($pattern): string
    {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $pattern);
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }
}