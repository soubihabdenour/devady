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
        $pw = (string)$request->input('password', '');
        if ($this->kernel->auth()->check($pw)) {
            $this->kernel->auth()->login();
            return $this->redirect('/admin');
        }
        return $this->view('admin/login', ['error' => 'Incorrect password.']);
    }

    public function logout(Request $request): Response
    {
        $this->kernel->auth()->logout();
        return $this->redirect('/');
    }
}
