<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class AssetController extends Controller
{
    public function serve(Request $request): Response
    {
        $type = (string)$request->input('type', '');
        if (!in_array($type, ['signature', 'stamp'], true)) {
            return Response::notFound();
        }
        $rel = $this->kernel->settings()->get('company_' . $type . '_file');
        if (!$rel) return Response::notFound();

        $root = realpath($this->kernel->base . '/storage');
        $abs  = $root ? realpath($root . '/' . $rel) : false;
        if (!$abs || !str_starts_with($abs, $root . DIRECTORY_SEPARATOR) || !is_file($abs)) {
            return Response::notFound();
        }
        return Response::file($abs);
    }
}
