<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\UserRepository;
use RuntimeException;

abstract class Controller
{
    // Methode utilitaire commune pour afficher une vue avec ses donnees.
    /** @param array<string, mixed> $data */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function userRepository(): UserRepository
    {
        if (!Container::has('db')) {
            throw new RuntimeException('Connexion base de donnees indisponible.');
        }

        return new UserRepository(Container::get('db'));
    }

    /**
     * @param array<string, string> $old
     */
    protected function storeOld(array $old): void
    {
        $_SESSION['old'] = $old;
    }

    /**
     * @return array{type:string,message:string}|null
     */
    protected function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }

    /**
     * @return array<string, string>
     */
    protected function pullOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        return is_array($old) ? $old : [];
    }

    protected function flashError(string $message): void
    {
        $_SESSION['flash'] = ['type' => 'error', 'message' => $message];
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
