<?php
// includes/env.php
// Loads .env file from the project root and defines constants.
// Must be included ONCE at the very top of db.php.

$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    die('<b>Configuration Error:</b> .env file not found. Please copy .env.example to .env and fill in your values.');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    // Skip comments
    if (str_starts_with(trim($line), '#')) continue;

    if (str_contains($line, '=')) {
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Remove surrounding quotes if present
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Set as environment variable & super-global
        if (!array_key_exists($key, $_ENV)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Helper function — get an env value with an optional default.
 * Usage: env('DB_HOST', 'localhost')
 */
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);
        return ($value !== false && $value !== null) ? $value : $default;
    }
}
