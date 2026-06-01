<?php declare(strict_types=1);

namespace App\Auth;

use App\Support\Config;

final class Auth
{
    public function check(string $password): bool
    {
        $expected = Config::get('APP_PASSWORD');
        if (!$expected) return false;
        return hash_equals($expected, $password);
    }

    public function login(): void
    {
        session_regenerate_id(true);
        $_SESSION['authed'] = true;
        $_SESSION['authed_at'] = time();
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public function authed(): bool
    {
        return !empty($_SESSION['authed']);
    }
}
