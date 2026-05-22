<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus — Autenticação de admin (stub)
   ───────────────────────────────────────────────────────────── */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/DB.php';

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function check(): bool
    {
        self::start();
        return !empty($_SESSION['admin_id']);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /admin/login.php');
            exit;
        }
    }

    public static function login(string $email, string $password): bool
    {
        // TODO: implementar validação contra banco de dados
        // Exemplo: SELECT * FROM admins WHERE email = ? AND is_active = 1
        return false;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
        header('Location: /admin/login.php');
        exit;
    }

    public static function currentUser(): ?array
    {
        self::start();
        return $_SESSION['admin_user'] ?? null;
    }
}
