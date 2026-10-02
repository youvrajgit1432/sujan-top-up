<?php
/**
 * Sujan Top-Up - Application bootstrap.
 *
 * Loads environment configuration (.env if present), the Composer
 * autoloader, and error handling. It does NOT define application constants;
 * include config/app.php for those.
 *
 * Every entry point should include config/app.php (directly or through the
 * dbcon.php / db_connection.php compatibility shims), which in turn loads
 * this bootstrap.
 */

declare(strict_types=1);

if (!defined('SUJAN_ROOT')) {
    define('SUJAN_ROOT', dirname(__DIR__));
}

/**
 * Minimal .env parser (no external dependency required).
 * Real environment variables always win over .env values.
 */
if (!function_exists('sujan_load_env')) {
    function sujan_load_env(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim(trim($value), "\"'");

            if ($key === '') {
                continue;
            }

            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

sujan_load_env(SUJAN_ROOT . '/.env');

if (!function_exists('env')) {
    /**
     * Read an environment value with a default fallback.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

// Composer autoloader (PHPMailer and any future dependencies).
$sujanAutoload = SUJAN_ROOT . '/vendor/autoload.php';
if (is_file($sujanAutoload)) {
    require_once $sujanAutoload;
}

// ---------------------------------------------------------------------------
// Error handling: never leak stack traces to visitors in production, but
// always log them so the error gate can be inspected.
// ---------------------------------------------------------------------------
error_reporting(E_ALL);

$sujanLogDir = SUJAN_ROOT . '/logs';
if (!is_dir($sujanLogDir)) {
    @mkdir($sujanLogDir, 0775, true);
}
ini_set('log_errors', '1');
ini_set('error_log', $sujanLogDir . '/php-error.log');
ini_set('display_errors', env_bool('APP_DEBUG', false) ? '1' : '0');