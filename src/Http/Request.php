<?php declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        /** @var array<string,string|array> */
        public readonly array $query,
        /** @var array<string,string|array> */
        public readonly array $post,
        /** @var array<string,array> */
        public readonly array $files,
        /** @var array<string,string> */
        public readonly array $cookies,
        /** @var array<string,string> */
        public readonly array $server,
        /** @var array<string,string> */
        public array $routeParams = [],
    ) {}

    public static function capture(): self
    {
        $uri  = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        return new self(
            strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            rtrim($path, '/') ?: '/',
            $_GET ?? [],
            $_POST ?? [],
            $_FILES ?? [],
            $_COOKIE ?? [],
            // @phpstan-ignore-next-line — $_SERVER is mixed but we expect strings.
            array_map(fn($v) => is_array($v) ? '' : (string)$v, $_SERVER ?? []),
        );
    }

    public function isPost(): bool { return $this->method === 'POST'; }

    public function input(string $key, ?string $default = null): ?string
    {
        $v = $this->post[$key] ?? $this->query[$key] ?? null;
        return is_string($v) ? $v : $default;
    }

    public function param(string $key, ?string $default = null): ?string
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}
