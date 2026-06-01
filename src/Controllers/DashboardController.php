<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('admin/dashboard', [
            'invoices' => $this->kernel->invoices()->all(),
            'totals'   => $this->kernel->invoices()->totals(),
        ]);
    }
}
