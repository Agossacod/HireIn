<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    // Configuration de connexion (host, base, utilisateur, etc.).
    /** @var array<string, mixed> */
    private array $config;

    // Recoit la configuration chargee depuis le fichier de config.
    /** @param array<string, mixed> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    // Ouvre la connexion PDO vers MySQL et retourne l'objet de connexion.
    public function connect(): PDO
    {
        // Construit la chaine DSN utilisee par PDO.
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config['driver'],
            $this->config['host'],
            $this->config['port'],
            $this->config['database'],
            $this->config['charset']
        );

        try {
            return new PDO(
                $dsn,
                (string) $this->config['username'],
                (string) $this->config['password'],
                [
                    // Laisse PDO lever des erreurs explicites pour faciliter le debug.
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Retourne les resultats SQL sous forme de tableaux associatifs.
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $exception) {
            http_response_code(500);
            exit('Database connection failed: ' . $exception->getMessage());
        }
    }
}
