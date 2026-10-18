<?php

declare(strict_types=1);

use Dotenv\Dotenv;

function loadEnv(string $path): void
{
    if (! is_readable($path)) {
        return;
    }

    $root = dirname($path);
    $file = basename($path);

    Dotenv::createImmutable($root, $file)->safeLoad();
}

/**
 * Leitura de variável de ambiente (padrão phpdotenv v5: $_ENV / $_SERVER).
 */
function env(string $key, ?string $default = null): string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default ?? '';
    }

    $value = (string) $value;

    if ($value === '' && $default !== null) {
        return $default;
    }

    return $value;
}
