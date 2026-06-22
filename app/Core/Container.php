<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Conteneur de dépendances simple pour la gestion des services.
 * Initialise les services une fois et les réutilise.
 */
final class Container
{
    /** @var array<string, mixed> */
    private static array $services = [];

    /**
     * Enregistre un service dans le conteneur.
     *
     * @param string $key Clé du service
     * @param mixed $value Valeur ou callback
     */
    public static function set(string $key, mixed $value): void
    {
        self::$services[$key] = $value;
    }

    /**
     * Récupère un service du conteneur.
     *
     * @param string $key Clé du service
     * @return mixed Le service demandé
     */
    public static function get(string $key): mixed
    {
        if (!isset(self::$services[$key])) {
            throw new \RuntimeException("Service '{$key}' not found in container");
        }

        $value = self::$services[$key];

        // Si c'est un callback, on l'exécute pour créer le service
        if (is_callable($value)) {
            self::$services[$key] = $value();
        }

        return self::$services[$key];
    }

    /**
     * Vérifie si un service existe.
     *
     * @param string $key Clé du service
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset(self::$services[$key]);
    }
}
