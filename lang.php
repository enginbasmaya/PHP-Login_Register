<?php

declare(strict_types=1);

/**
 * Lang — Language Manager
 *
 * Usage:
 *   Lang::init();          // Load language from session (default: 'en')
 *   Lang::t('login_title') // Get translated string
 *   Lang::toggle()         // Switch language and redirect
 *   Lang::current()        // Current language code: 'en' | 'tr'
 *
 * Adding a new language:
 *   1. Create lang/xx.php (copy tr.php, translate the values)
 *   2. Add 'xx' to self::SUPPORTED_LANGS
 *   3. Done — no other changes needed.
 *
 * To enable an external translation API (DeepL, Google Translate, etc.):
 *   → Implement fetchFromApi() and set TRANSLATION_API_ENABLED=true in .env
 */
class Lang
{
    // ── Supported languages ──────────────────────────────────────────────────
    private const SUPPORTED_LANGS = ['en', 'tr'];
    private const DEFAULT_LANG    = 'en';
    private const SESSION_KEY     = 'app_lang';

    // ── State ────────────────────────────────────────────────────────────────
    private static string $current     = self::DEFAULT_LANG;
    private static array  $strings     = [];
    private static bool   $initialized = false;

    // ────────────────────────────────────────────────────────────────────────

    /**
     * Initialize language. Call at the top of every page after session starts.
     */
    public static function init(): void
    {
        if (self::$initialized) return;

        // URL parameter takes priority (?lang=tr)
        if (isset($_GET['lang']) && in_array($_GET['lang'], self::SUPPORTED_LANGS, true)) {
            $_SESSION[self::SESSION_KEY] = $_GET['lang'];
        }

        self::$current = $_SESSION[self::SESSION_KEY] ?? self::DEFAULT_LANG;

        // Sanitize — reject invalid values stored in session
        if (!in_array(self::$current, self::SUPPORTED_LANGS, true)) {
            self::$current = self::DEFAULT_LANG;
        }

        self::$strings     = self::load(self::$current);
        self::$initialized = true;
    }

    /**
     * Get a translated string by key. Falls back to the key itself if not found.
     * Supports sprintf formatting: Lang::t('err_lockout', 30)
     */
    public static function t(string $key, mixed ...$args): string
    {
        $value = self::$strings[$key] ?? $key;
        return $args ? sprintf($value, ...$args) : $value;
    }

    /**
     * Return the current language code: 'en' | 'tr'
     */
    public static function current(): string
    {
        return self::$current;
    }

    /**
     * Toggle language and redirect back to the same page.
     */
    public static function toggle(): void
    {
        $langs = self::SUPPORTED_LANGS;

        // Read directly from session — self::$current may still be the default
        // value if init() has not run yet when toggle() is called.
        $activeLang = $_SESSION[self::SESSION_KEY] ?? self::DEFAULT_LANG;
        $current    = array_search($activeLang, $langs, true);
        $next       = $langs[($current + 1) % count($langs)];

        $_SESSION[self::SESSION_KEY] = $next;

        // Redirect to the same page without the query string
        $url = strtok($_SERVER['REQUEST_URI'], '?');
        header('Location: ' . $url);
        exit;
    }

    /**
     * Render the language toggle button HTML.
     */
    public static function toggleButton(): string
    {
        return sprintf(
            '<a href="?action=lang_toggle" class="lang-btn" title="%s">%s %s</a>',
            htmlspecialchars(self::t('lang_toggle')),
            self::t('lang_toggle_icon'),
            htmlspecialchars(self::t('lang_toggle'))
        );
    }

    // ── Private ──────────────────────────────────────────────────────────────

    /**
     * Load the translation file for the given language code.
     * Falls back to the default language if the file is missing.
     */
    private static function load(string $lang): array
    {
        // ── External API support (future) ─────────────────────────────────
        // if (self::useApi()) {
        //     return self::fetchFromApi($lang);
        // }

        $file = __DIR__ . "/lang/{$lang}.php";

        if (!file_exists($file)) {
            // Fallback to default language
            $file = __DIR__ . '/lang/' . self::DEFAULT_LANG . '.php';
        }

        return require $file;
    }

    /**
     * Whether to use an external translation API.
     * Controlled via TRANSLATION_API_ENABLED in .env / environment.
     */
    private static function useApi(): bool
    {
        return ($_ENV['TRANSLATION_API_ENABLED'] ?? 'false') === 'true';
    }

    /**
     * Fetch a translation pack from an external API.
     * Wire up DeepL, Google Translate, LibreTranslate, or your own backend here.
     *
     * @param string $lang  Target language code ('en', 'tr', 'de' …)
     * @return array        Key-value translation map
     */
    private static function fetchFromApi(string $lang): array
    {
        // Example implementation:
        // $apiKey  = $_ENV['TRANSLATION_API_KEY'];
        // $baseUrl = $_ENV['TRANSLATION_API_URL'];
        //
        // $response = file_get_contents("{$baseUrl}/translations/{$lang}?key={$apiKey}");
        // return json_decode($response, true) ?? [];

        return []; // Not yet implemented
    }
}
