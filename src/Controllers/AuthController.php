<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

final class AuthController extends Controller
{
    public function loginForm(Request $request): Response
    {
        if ($this->kernel->auth()->authed()) {
            return $this->redirect('/admin');
        }
        return $this->view('admin/login', ['error' => null]);
    }

    public function login(Request $request): Response
    {
        // Lock the form for a minute after 5 failures from this session/IP.
        $now      = time();
        $attempts = (int)($_SESSION['login_attempts'] ?? 0);
        $lockedAt = (int)($_SESSION['login_locked_at'] ?? 0);
        if ($attempts >= 5 && ($now - $lockedAt) < 60) {
            $wait = 60 - ($now - $lockedAt);
            return $this->view('admin/login', ['error' => "Too many attempts. Try again in {$wait}s."]);
        }
        if ($attempts >= 5) {
            $_SESSION['login_attempts'] = 0;
        }

        $pw = (string)$request->input('password', '');
        if ($this->kernel->auth()->check($pw)) {
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_at']);
            $this->kernel->auth()->login();
            return $this->redirect('/admin');
        }

        $_SESSION['login_attempts'] = $attempts + 1;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_at'] = $now;
        }
        usleep(250_000); // slow down credential stuffing
        return $this->view('admin/login', ['error' => 'Incorrect password.']);
    }

    public function logout(Request $request): Response
    {
        $this->kernel->auth()->logout();
        return $this->redirect('/');
    }
}
