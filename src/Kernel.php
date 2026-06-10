<?php declare(strict_types=1);

namespace App;

use App\Auth\Auth;
use App\Db\Connection;
use App\Db\Migrator;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Settings\Settings;
use App\Stores\ClientStore;
use App\Stores\InvoiceStore;
use App\Stores\LeadStore;
use App\Stores\PortfolioStore;
use App\Support\Brand;
use App\Support\Config;
use App\Support\I18n;
use App\Support\View;

final class Kernel
{
    public readonly string $base;
    /** @var array<class-string, object> */
    private array $instances = [];

    public function __construct(string $base)
    {
        $this->base = rtrim($base, '/');
    }

    public function boot(): void
    {
        Config::load($this->base . '/.env');

        // Hide error details from users; rely on the server log instead.
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        if (session_status() === PHP_SESSION_NONE) {
            $https = ($_SERVER['HTTPS'] ?? '') === 'on'
                || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
            session_name('devady_sess');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $https,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            session_start();
        }
        I18n::boot();
        $this->migrate();
    }

    private function migrate(): void
    {
        $migrator = new Migrator($this->db()->pdo(), $this->base . '/src/Db/migrations');
        $migrator->migrate();
    }

    public function handle(Request $request, Router $router): Response
    {
        if ($request->method === 'POST' && !$this->auth()->checkCsrf($request->input('_csrf'))) {
            return new Response('CSRF token missing or invalid.', 419, [
                'Content-Type' => 'text/plain; charset=utf-8',
            ]);
        }

        $response = $router->dispatch($request, $this);
        return $this->applySecurityHeaders($response, $request);
    }

    private function applySecurityHeaders(Response $response, Request $request): Response
    {
        $defaults = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Referrer-Policy'        => 'strict-origin-when-cross-origin',
            'Permissions-Policy'     => 'geolocation=(), microphone=(), camera=()',
        ];
        foreach ($defaults as $name => $value) {
            $response->headers[$name] ??= $value;
        }
        $https = ($request->server['HTTPS'] ?? '') === 'on'
            || ($request->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        if ($https) {
            $response->headers['Strict-Transport-Security'] ??= 'max-age=31536000; includeSubDomains';
        }
        return $response;
    }

    public function runMiddleware(string $name, Request $request): ?Response
    {
        return match ($name) {
            'auth' => empty($_SESSION['authed'])
                ? Response::redirect($this->url('/login'))
                : null,
            default => null,
        };
    }

    public function url(string $path = '/', array $query = []): string
    {
        $u = $path;
        if ($query) $u .= (str_contains($u, '?') ? '&' : '?') . http_build_query($query);
        return $u;
    }

    public function resolve(string $class): object
    {
        if (isset($this->instances[$class])) return $this->instances[$class];

        // Controllers receive the kernel via constructor.
        $instance = match (true) {
            str_starts_with($class, 'App\\Controllers\\') => new $class($this),
            default => new $class(),
        };
        return $this->instances[$class] = $instance;
    }

    // ---- Service accessors (cached) -------------------------------------

    public function db(): Connection
    {
        return $this->instances[Connection::class] ??= new Connection($this->base . '/storage/devady.db');
    }

    public function auth(): Auth
    {
        return $this->instances[Auth::class] ??= new Auth();
    }

    public function settings(): Settings
    {
        return $this->instances[Settings::class] ??= new Settings($this->db()->pdo());
    }

    public function clients(): ClientStore
    {
        return $this->instances[ClientStore::class] ??= new ClientStore($this->db()->pdo());
    }

    public function invoices(): InvoiceStore
    {
        return $this->instances[InvoiceStore::class] ??= new InvoiceStore($this->db()->pdo());
    }

    public function leads(): LeadStore
    {
        return $this->instances[LeadStore::class] ??= new LeadStore($this->db()->pdo());
    }

    public function portfolio(): PortfolioStore
    {
        return $this->instances[PortfolioStore::class] ??= new PortfolioStore($this->db()->pdo());
    }

    public function view(): View
    {
        return $this->instances[View::class] ??= new View($this->base . '/resources/views', $this);
    }

    public function brand(): string { return Brand::class; }
}
