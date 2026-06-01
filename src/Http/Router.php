<?php declare(strict_types=1);

namespace App\Http;

use App\Kernel;

final class Router
{
    /** @var list<array{method:string,pattern:string,regex:string,vars:list<string>,handler:callable|array,middleware:list<string>}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function any(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('*', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, callable|array $handler, array $middleware): self
    {
        $pattern = '/' . trim($path, '/');
        $vars    = [];
        $regex   = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function ($m) use (&$vars) {
            $vars[] = $m[1];
            return '([^/]+)';
        }, $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = compact('method', 'pattern', 'regex', 'vars', 'handler', 'middleware');
        return $this;
    }

    public function dispatch(Request $request, Kernel $kernel): Response
    {
        $path = rtrim($request->path, '/') ?: '/';
        foreach ($this->routes as $route) {
            if ($route['method'] !== '*' && $route['method'] !== $request->method) {
                continue;
            }
            if (!preg_match($route['regex'], $path, $m)) {
                continue;
            }
            $params = [];
            foreach ($route['vars'] as $i => $name) {
                $params[$name] = $m[$i + 1];
            }
            $request->routeParams = $params;

            foreach ($route['middleware'] as $mw) {
                $result = $kernel->runMiddleware($mw, $request);
                if ($result instanceof Response) return $result;
            }

            $handler = $route['handler'];
            if (is_array($handler) && is_string($handler[0])) {
                $instance = $kernel->resolve($handler[0]);
                $handler  = [$instance, $handler[1]];
            }
            $result = $handler($request, $kernel);
            return $result instanceof Response ? $result : Response::html((string)$result);
        }
        return Response::notFound('404 — route not found');
    }
}
