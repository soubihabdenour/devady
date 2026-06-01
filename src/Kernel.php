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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        Config::load($this->base . '/.env');
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
        return $router->dispatch($request, $this);
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

    public function view(): View
    {
        return $this->instances[View::class] ??= new View($this->base . '/resources/views', $this);
    }

    public function brand(): string { return Brand::class; }
}
