<?php declare(strict_types=1);

use App\Controllers\AssetController;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\ContactController;
use App\Controllers\DashboardController;
use App\Controllers\InvoiceController;
use App\Controllers\LandingController;
use App\Controllers\LeadController;
use App\Controllers\SettingsController;
use App\Http\Request;
use App\Http\Router;
use App\Kernel;

$base = dirname(__DIR__);

// Autoload — composer if installed, otherwise a tiny fallback so the app boots
// even before `composer install` (with reduced features — no dompdf).
if (is_file($base . '/vendor/autoload.php')) {
    require $base . '/vendor/autoload.php';
} else {
    spl_autoload_register(function (string $class) use ($base) {
        if (!str_starts_with($class, 'App\\')) return;
        $rel = str_replace('\\', '/', substr($class, 4));
        $file = $base . '/src/' . $rel . '.php';
        if (is_file($file)) require $file;
    });
    require $base . '/src/Support/helpers.php';
}

$kernel = new Kernel($base);
$kernel->boot();

$router = new Router();

// --- Public ---------------------------------------------------------------
$router->get ('/',             [LandingController::class, 'index']);
$router->post('/contact',      [ContactController::class, 'submit']);
$router->get ('/i/{token}',    [InvoiceController::class, 'publicShow']);

// --- Auth -----------------------------------------------------------------
$router->get ('/login',        [AuthController::class, 'loginForm']);
$router->post('/login',        [AuthController::class, 'login']);
$router->post('/logout',       [AuthController::class, 'logout']);
$router->get ('/logout',       [AuthController::class, 'logout']);

// --- Asset (signature / stamp images) -------------------------------------
$router->get ('/asset',        [AssetController::class, 'serve'], ['auth']);

// --- Admin: dashboard & invoices ------------------------------------------
$router->get ('/admin',                          [DashboardController::class, 'index'],  ['auth']);
$router->get ('/admin/invoices/new',             [InvoiceController::class,   'showForm'], ['auth']);
$router->post('/admin/invoices/new',             [InvoiceController::class,   'save'],     ['auth']);
$router->get ('/admin/invoices/{id}',            [InvoiceController::class,   'show'],     ['auth']);
$router->get ('/admin/invoices/{id}/edit',       [InvoiceController::class,   'showForm'], ['auth']);
$router->post('/admin/invoices/{id}/edit',       [InvoiceController::class,   'save'],     ['auth']);
$router->post('/admin/invoices/{id}/delete',     [InvoiceController::class,   'delete'],   ['auth']);
$router->post('/admin/invoices/{id}/sign',       [InvoiceController::class,   'sign'],     ['auth']);
$router->post('/admin/invoices/{id}/unsign',     [InvoiceController::class,   'unsign'],   ['auth']);
$router->post('/admin/invoices/{id}/share',      [InvoiceController::class,   'share'],    ['auth']);
$router->post('/admin/invoices/{id}/revoke',     [InvoiceController::class,   'revoke'],   ['auth']);
$router->get ('/admin/invoices/{id}/pdf',        [InvoiceController::class,   'pdf'],      ['auth']);

// --- Admin: clients -------------------------------------------------------
$router->get ('/admin/clients',                  [ClientController::class, 'index'],    ['auth']);
$router->get ('/admin/clients/new',              [ClientController::class, 'showForm'], ['auth']);
$router->post('/admin/clients/new',              [ClientController::class, 'save'],     ['auth']);
$router->get ('/admin/clients/{id}/edit',        [ClientController::class, 'showForm'], ['auth']);
$router->post('/admin/clients/{id}/edit',        [ClientController::class, 'save'],     ['auth']);
$router->post('/admin/clients/{id}/delete',      [ClientController::class, 'delete'],   ['auth']);

// --- Admin: leads ---------------------------------------------------------
$router->get ('/admin/leads',                    [LeadController::class, 'index'],   ['auth']);
$router->get ('/admin/leads/{id}',               [LeadController::class, 'show'],    ['auth']);
$router->post('/admin/leads/{id}/convert',       [LeadController::class, 'convert'], ['auth']);
$router->post('/admin/leads/{id}/delete',        [LeadController::class, 'delete'],  ['auth']);

// --- Admin: settings ------------------------------------------------------
$router->get ('/admin/settings', [SettingsController::class, 'show'],   ['auth']);
$router->post('/admin/settings', [SettingsController::class, 'update'], ['auth']);

$response = $kernel->handle(Request::capture(), $router);
$response->send();
