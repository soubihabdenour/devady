<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;
use App\Kernel;

abstract class Controller
{
    public function __construct(protected Kernel $kernel) {}

    protected function view(string $template, array $data = []): Response
    {
        return $this->kernel->view()->render($template, $data);
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect($path);
    }
}
