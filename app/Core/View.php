<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    // Charge une vue enfant, puis l'injecte dans le layout principal.
    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = []): void
    {
        $viewsPath = dirname(__DIR__) . '/Views';
        $viewFile = $viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        // Rend les variables du tableau accessibles dans la vue (ex: $title, $offers).
        extract($data, EXTR_SKIP);

        // Capture le HTML de la vue enfant dans $content.
        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        // Applique le layout commun (entete, pied de page, styles).
        require $viewsPath . '/layout.php';
    }
}
