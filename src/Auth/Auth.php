<?php declare(strict_types=1);

namespace App\Auth;

use App\Support\Config;

final class Auth
{
    public function check(string $password): bool
    {
        if ($password === '') return false;

        // Prefer a bcrypt/argon hash if configured; fall back to plaintext for
        // legacy installs. Both branches use constant-time comparison.
        $hash = (string)Config::get('APP_PASSWORD_HASH', '');
        if ($hash !== '') {
            return password_verify($password, $hash);
        }

        $expected = (string)Config::get('APP_PASSWORD', '');
        if ($expected === '') return false;
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

    /** Get (and lazily create) the per-session CSRF token. */
    public function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['csrf'];
    }

    public function checkCsrf(?string $token): bool
    {
        if (!is_string($token) || $token === '' || empty($_SESSION['csrf'])) {
            return false;
        }
        return hash_equals((string)$_SESSION['csrf'], $token);
    }
}
