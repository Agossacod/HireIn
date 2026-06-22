<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    // Tableau des routes par methode HTTP (GET/POST).
    /** @var array<string, array<string, callable|array{0: class-string, 1: string}>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    // Enregistre une route GET et son handler.
    /** @param callable|array{0: class-string, 1: string} $handler */
    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    // Enregistre une route POST et son handler.
    /** @param callable|array{0: class-string, 1: string} $handler */
    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    // Trouve la route demandee puis execute le code associe.
    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $normalizedPath = $this->normalize($path);

        $handlers = $this->routes[$method] ?? [];
        $handler = $handlers[$normalizedPath] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        if (is_callable($handler)) {
            $handler();
            return;
        }

        [$class, $action] = $handler;
        $controller = new $class();
        $controller->{$action}();
    }

    // Uniformise les chemins pour eviter les differences de format (/offres/ vs /offres).
    private function normalize(string $path): string
    {
        $trimmed = trim($path);
        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        return '/' . trim($trimmed, '/');
    }
}
