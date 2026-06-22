<?php

declare(strict_types=1);

namespace App\Core;

final class AuthMiddleware
{
    /**
     * Exige une authentification, optionnellement un role specifique.
     *
     * @return array<string, mixed>
     */
    public static function requireAuth(?string $role = null, string $redirect = '/espace-entreprise', string $message = 'Connexion requise.'): array
    {
        $auth = $_SESSION['auth'] ?? null;
        if (!is_array($auth)) {
            self::flashError($message);
            self::redirect($redirect);
        }

        if ($role !== null && ($auth['role'] ?? '') !== $role) {
            self::flashError($message);
            self::redirect($redirect);
        }

        return $auth;
    }

    public static function requireGuest(string $redirect = '/'): void
    {
        $auth = $_SESSION['auth'] ?? null;
        if (is_array($auth)) {
            self::redirect($redirect);
        }
    }

    private static function flashError(string $message): void
    {
        $_SESSION['flash'] = ['type' => 'error', 'message' => $message];
    }

    private static function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
