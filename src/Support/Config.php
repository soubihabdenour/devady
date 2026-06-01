<?php declare(strict_types=1);

namespace App\Support;

final class Config
{
    /** @var array<string,string> */
    private static array $env = [];

    public static function load(string $envFile): void
    {
        if (!is_file($envFile)) return;
        foreach ((array)file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim((string)$line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            self::$env[trim($k)] = trim($v, " \t\"'");
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::$env[$key] ?? getenv($key) ?: $default;
    }
}
