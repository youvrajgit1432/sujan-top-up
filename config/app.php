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

if (!function_exists('game_fallback_image')) {
    /**
     * Central fallback map for game/service card images.
     *
     * Used only when the stored image is empty, missing or unusable. Local
     * first-party SVGs are always preferred; a game without a local asset
     * falls back to a stable remote placeholder. The final emergency
     * fallback is the generic local placeholder returned at the end.
     *
     * @param string $gameKey  Game type/key from the database (e.g. 'pubg').
     * @param string $gameName Human-readable game name (secondary hint).
     */
    function game_fallback_image(string $gameKey, string $gameName = ''): string
    {
        // Real, stable HTTPS artwork for each service (validated: HTTP 200,
        // Content-Type image/*). Kept in ONE place so no URLs live in index.php.
        // These are hotlink-friendly upload.wikimedia.org assets (Wikimedia
        // Commons / Wikipedia). Swap any URL here if a source ever changes.
        $map = [
            'pubg'         => 'https://upload.wikimedia.org/wikipedia/en/4/44/PlayerUnknown%27s_Battlegrounds_Mobile.webp',
            'freefire'     => 'https://upload.wikimedia.org/wikipedia/en/c/c5/Logo_of_Garena_Free_Fire.png',
            'mobilelegend' => 'https://upload.wikimedia.org/wikipedia/en/a/a0/Mobile_Legends_Bang_Bang_2025_logo.png',
            'tiktok'       => 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Tiktok_logo.png',
            'efootball'    => 'https://upload.wikimedia.org/wikipedia/commons/e/ee/EFootball_logo.svg',
            'clash'        => 'https://upload.wikimedia.org/wikipedia/en/5/59/Clash_of_Clans_Logo.png',
            'netflix'      => 'https://upload.wikimedia.org/wikipedia/commons/6/69/Netflix_logo.svg',
            'spotify'      => 'https://upload.wikimedia.org/wikipedia/commons/9/99/Black_Spotify_logo_with_text.svg',
            'prime'        => 'https://upload.wikimedia.org/wikipedia/commons/9/90/Prime_Video_logo_%282024%29.svg',
        ];

        // Normalise the key/name into space-separated lowercase words so both
        // 'pubg_global', 'pubg-global' and 'Pubg Global' match the same rule.
        $haystack = strtolower($gameKey . ' ' . $gameName);
        $haystack = (string) preg_replace('/[^a-z0-9]+/', ' ', $haystack);

        // Ordered most-specific first, so aliases like 'pubg global'/'pubg'
        // collapse onto one key.
        $rules = [
            'pubg global'     => 'pubg',
            'pubg mobile'     => 'pubg',
            'pubgmobile'      => 'pubg',
            'pubg'            => 'pubg',
            'free fire'       => 'freefire',
            'freefire'        => 'freefire',
            'mobile legends'  => 'mobilelegend',
            'mobile legend'   => 'mobilelegend',
            'mobilelegend'    => 'mobilelegend',
            'mlbb'            => 'mobilelegend',
            'efootball'       => 'efootball',
            'clash of clans'  => 'clash',
            'clashofclans'    => 'clash',
            'clash'           => 'clash',
            'tiktok'          => 'tiktok',
            'netflix'         => 'netflix',
            'spotify'         => 'spotify',
            'prime video'     => 'prime',
            'prime'           => 'prime',
        ];

        foreach ($rules as $needle => $key) {
            if (str_contains($haystack, $needle) && isset($map[$key])) {
                return $map[$key];
            }
        }

        // Generic safe local placeholder (final emergency fallback only).
        return 'assets/img/games/generic.svg';
    }
}

if (!function_exists('game_image_src')) {
    /**
     * Resolve a stored image path for display.
     *
     * Priority:
     *   A. explicit http(s):// URL          -> returned validated, unchanged
     *   B. existing first-party asset       -> assets/...
     *   C. existing legacy upload          -> uploads/...
     *   D. game-specific configured fallback
     *   E. generic local placeholder
     *
     * A missing/blank/broken database image therefore never renders as an
     * empty dark box. This also supports admin-supplied external image URLs
     * by never prepending 'uploads/' to an http(s) URL.
     */
    function game_image_src($path, string $gameKey = '', string $gameName = ''): string
    {
        $path = trim((string) $path);

        // A. External URL - validate and return unchanged.
        if ($path !== '' && preg_match('#^https?://#i', $path) === 1) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false
                ? $path
                : game_fallback_image($gameKey, $gameName);
        }

        if ($path === '') {
            return game_fallback_image($gameKey, $gameName);
        }

        $relative = ltrim($path, '/\\');

        // B. First-party asset.
        if (str_starts_with($relative, 'assets/')) {
            return is_file(SUJAN_ROOT . '/' . $relative)
                ? $relative
                : game_fallback_image($gameKey, $gameName);
        }

        // C. Legacy uploads/ path (optionally with a '../' prefix).
        $relative = (string) preg_replace('#^(\.\./|\.\.\\\\|[\/\\\\])+#', '', $relative);
        if (str_starts_with($relative, 'uploads/')) {
            return is_file(SUJAN_ROOT . '/' . $relative)
                ? $relative
                : game_fallback_image($gameKey, $gameName);
        }

        // D. Bare filename or other relative path - assume uploads/.
        $candidate = 'uploads/' . ltrim($relative, '/\\');
        if (is_file(SUJAN_ROOT . '/' . $candidate)) {
            return $candidate;
        }

        // E. Nothing usable - game-specific or generic fallback.
        return game_fallback_image($gameKey, $gameName);
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