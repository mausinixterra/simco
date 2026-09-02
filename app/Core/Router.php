<?php

declare(strict_types=1);

namespace App\Core;

use Exception;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public static function load(string $file): self
    {
        $router = new static;
        require $file;
        return $router;
    }

    public function get(string $uri, array $action): void
    {
        $uri = trim($uri, '/');
        $this->routes['GET'][$uri] = $action;
    }

    // --- NEW METHOD ---
    public function post(string $uri, array $action): void
    {
        $uri = trim($uri, '/');
        $this->routes['POST'][$uri] = $action;
    }

    public function direct(string $uri, string $requestMethod): void
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Check if a route exists for the given method and URI
        if (isset($this->routes[$requestMethod]) && array_key_exists($uri, $this->routes[$requestMethod])) {
            $this->callAction(
                ...$this->routes[$requestMethod][$uri]
            );
            return;
        }

        http_response_code(404);
        require __DIR__ . '/../Views/errors/404.php';
    }

    protected function callAction(string $controller, string $method): void
    {
        if (!class_exists($controller)) {
            throw new Exception("Controller not found: {$controller}");
        }

        $controllerInstance = new $controller;

        if (!method_exists($controllerInstance, $method)) {
            throw new Exception(
                "The method {$method} does not exist in the controller {$controller}."
            );
        }

        $controllerInstance->$method();
    }
}