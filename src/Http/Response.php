<?php declare(strict_types=1);

namespace App\Http;

final class Response
{
    public function __construct(
        public string $body = '',
        public int $status = 200,
        /** @var array<string,string> */
        public array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status);
    }

    public static function text(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            (string)json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
        );
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    public static function notFound(string $body = 'Not Found'): self
    {
        return new self($body, 404);
    }

    public static function file(string $absPath, ?string $mime = null): self
    {
        $mime = $mime ?? (mime_content_type($absPath) ?: 'application/octet-stream');
        return new self(
            (string)file_get_contents($absPath),
            200,
            [
                'Content-Type'   => $mime,
                'Content-Length' => (string)filesize($absPath),
                'Cache-Control'  => 'private, max-age=300',
            ],
        );
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $k => $v) {
                header($k . ': ' . $v);
            }
        }
        echo $this->body;
    }
}
