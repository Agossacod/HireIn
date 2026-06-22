<?php

declare(strict_types=1);

// Configuration generale de l'application avec valeurs par defaut.
return [
    'name' => getenv('APP_NAME') ?: 'HireIn',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => (getenv('APP_DEBUG') ?: 'true') === 'true',
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
];
