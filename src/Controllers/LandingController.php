<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class LandingController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('landing/index', [
            'settings' => $this->kernel->settings()->all(),
            'authed'   => $this->kernel->auth()->authed(),
            'sent'     => ($request->query['sent']  ?? null) === '1',
            'err'      => $request->query['err']    ?? null,
        ]);
    }
}
