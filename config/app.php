<?php
/**
 * Sujan Top-Up - Application configuration.
 *
 * Central, configurable application metadata. Values are read from the
 * environment (.env) with safe, non-personal demo defaults so the project
 * can run locally without any private configuration.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (!defined('APP_NAME')) {
    define('APP_NAME', (string) env('APP_NAME', 'Sujan Top-Up'));
    define('APP_URL', (string) env('APP_URL', 'http://localhost/sujan'));
    define('APP_ENV', (string) env('APP_ENV', 'local'));
    define('APP_DEBUG', env_bool('APP_DEBUG', false));

    // Public demo contact details. These are placeholders only - override
    // them in .env for your own deployment. Never commit real details.
    // The phone is deliberately masked (non-dialable) and the WhatsApp number
    // is intentionally empty so the demo never opens a chat to a fake number.
    define('SUPPORT_EMAIL', (string) env('SUPPORT_EMAIL', 'demo@example.test'));
    define('SUPPORT_PHONE', (string) env('SUPPORT_PHONE', '+977-98XXXXXXXX'));
    define('WHATSAPP_NUMBER', (string) env('WHATSAPP_NUMBER', ''));

    // Manual / offline payment instructions shown on the demo checkout.
    define('PAYMENT_INSTRUCTIONS', (string) env(
        'PAYMENT_INSTRUCTIONS',
        'Configure your payment account in local settings.'
    ));
}

if (!function_exists('e')) {
    /** Escape a value for safe HTML output. */
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('game_image_src')) {
    /**
     * Resolve a stored image path for display.
     *
     * Legacy rows store paths relative to uploads/ (sometimes with a
     * '../uploads/' prefix), while the public demo seeds first-party paths
     * under assets/. This normalises both so no asset 404s.
     */
    function game_image_src($path): string
    {
        $path = ltrim((string) $path, '/\\');
        if ($path === '') {
            return 'assets/img/games/pubg.svg';
        }
        if (str_starts_with($path, 'assets/')) {
            return $path;
        }
        $path = preg_replace('#^\.\./uploads/#', '', $path);
        $path = preg_replace('#^uploads/#', '', $path);
        return 'uploads/' . $path;
    }
}

if (!function_exists('whatsapp_configured')) {
    /**
     * Whether a real WhatsApp number is configured.
     *
     * The demo default is empty, so outbound WhatsApp links are disabled
     * instead of pointing at a placeholder / fake number.
     */
    function whatsapp_configured(): bool
    {
        $digits = preg_replace('/\D+/', '', (string) WHATSAPP_NUMBER);
        return $digits !== null && $digits !== '' && preg_match('/^\d{10,15}$/', $digits) === 1;
    }
}

if (!function_exists('whatsapp_client_number')) {
    /** Digits-only WhatsApp number for client-side links ('' when disabled). */
    function whatsapp_client_number(): string
    {
        return whatsapp_configured() ? (string) preg_replace('/\D+/', '', WHATSAPP_NUMBER) : '';
    }
}

if (!function_exists('phone_link')) {
    /**
     * Build a tel: link. Returns a neutral '#' when only a masked placeholder
     * is configured, so no outbound call is possible from the demo.
     */
    function phone_link(): string
    {
        $raw = (string) SUPPORT_PHONE;
        if ($raw === '' || stripos($raw, 'X') !== false) {
            return '#';
        }
        $digits = preg_replace('/\D+/', '', $raw);
        if ($digits === null || $digits === '') {
            return '#';
        }
        if (!str_starts_with($digits, '977')) {
            $digits = '977' . $digits;
        }
        return 'tel:+' . $digits;
    }
}

if (!function_exists('whatsapp_link')) {
    /**
     * Build a wa.me link using the configured WhatsApp number, or a neutral
     * '#' when no real number is configured (demo mode).
     */
    function whatsapp_link(string $text = ''): string
    {
        if (!whatsapp_configured()) {
            return '#';
        }
        $url = 'https://wa.me/' . preg_replace('/\D+/', '', WHATSAPP_NUMBER);
        if ($text !== '') {
            $url .= '?text=' . rawurlencode($text);
        }
        return $url;
    }
}

if (!function_exists('mail_configured')) {
    /**
     * Whether SMTP delivery is configured. When false, features such as
     * OTP and password-reset email degrade gracefully instead of crashing.
     */
    function mail_configured(): bool
    {
        return env('MAIL_HOST') !== null
            && env('MAIL_USERNAME') !== null
            && env('MAIL_PASSWORD') !== null;
    }
}