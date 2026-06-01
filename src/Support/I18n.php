<?php declare(strict_types=1);

namespace App\Support;

final class I18n
{
    public const DEFAULT_LANG    = 'en';
    public const AVAILABLE       = ['en', 'fr', 'ar'];
    public const COOKIE          = 'lang';
    public const COOKIE_TTL_DAYS = 365;

    private static string $current = self::DEFAULT_LANG;
    /** @var array<string,string> */
    private static array $strings  = [];
    /** @var array<string,string> */
    private static array $fallback = [];

    public static function boot(): void
    {
        $lang = self::DEFAULT_LANG;

        if (isset($_GET['lang']) && self::isValid((string)$_GET['lang'])) {
            $lang = (string)$_GET['lang'];
            self::setCookie($lang);
        } elseif (isset($_COOKIE[self::COOKIE]) && self::isValid((string)$_COOKIE[self::COOKIE])) {
            $lang = (string)$_COOKIE[self::COOKIE];
        } elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = self::pickFromAcceptLanguage((string)$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        self::$current  = $lang;
        self::$fallback = self::load(self::DEFAULT_LANG);
        self::$strings  = $lang === self::DEFAULT_LANG ? self::$fallback : self::load($lang);
    }

    public static function current(): string { return self::$current; }
    public static function dir(): string { return self::$current === 'ar' ? 'rtl' : 'ltr'; }
    public static function htmlLang(): string { return self::$current; }

    public static function nativeName(string $lang): string
    {
        return ['en' => 'EN', 'fr' => 'FR', 'ar' => 'عربي'][$lang] ?? strtoupper($lang);
    }

    public static function t(string $key, array $vars = []): string
    {
        $s = self::$strings[$key] ?? self::$fallback[$key] ?? $key;
        if ($vars) {
            $repl = [];
            foreach ($vars as $k => $v) {
                $repl['{' . $k . '}'] = (string)$v;
            }
            $s = strtr($s, $repl);
        }
        return $s;
    }

    /** Build a URL that switches to $lang while preserving the current path + query. */
    public static function switchUrl(string $lang): string
    {
        $uri  = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $qs   = $_GET ?? [];
        $qs['lang'] = $lang;
        return $path . '?' . http_build_query($qs);
    }

    public static function isValid(string $lang): bool
    {
        return in_array($lang, self::AVAILABLE, true);
    }

    private static function setCookie(string $lang): void
    {
        if (headers_sent()) return;
        setcookie(self::COOKIE, $lang, [
            'expires'  => time() + 86400 * self::COOKIE_TTL_DAYS,
            'path'     => '/',
            'samesite' => 'Lax',
            'httponly' => false,
        ]);
        $_COOKIE[self::COOKIE] = $lang;
    }

    private static function pickFromAcceptLanguage(string $header): string
    {
        foreach (explode(',', $header) as $part) {
            $code = strtolower(trim(explode(';', $part, 2)[0]));
            $code = substr($code, 0, 2);
            if (self::isValid($code)) return $code;
        }
        return self::DEFAULT_LANG;
    }

    /** @return array<string,string> */
    private static function load(string $lang): array
    {
        $file = __DIR__ . '/i18n/' . $lang . '.php';
        if (!is_file($file)) return [];
        $data = require $file;
        return is_array($data) ? $data : [];
    }
}
