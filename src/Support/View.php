<?php declare(strict_types=1);

namespace App\Support;

use App\Http\Response;
use App\Kernel;
use RuntimeException;

/**
 * Minimal PHP-based template renderer.
 * Views live under resources/views/ and receive $kernel + data array as locals.
 */
final class View
{
    public function __construct(private string $dir, private Kernel $kernel) {}

    public function render(string $template, array $data = []): Response
    {
        return Response::html($this->renderToString($template, $data));
    }

    public function renderToString(string $template, array $data = []): string
    {
        $path = $this->dir . '/' . $template . '.php';
        if (!is_file($path)) {
            throw new RuntimeException("View not found: $template ($path)");
        }
        $kernel = $this->kernel;
        $brand  = Brand::class;
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string)ob_get_clean();
    }
}
