<?php

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $usuario): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
    }

    public static function logout(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function check(): bool
    {
        self::start();
        return !empty($_SESSION['usuario_id']);
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /admin/login.php');
            exit;
        }
    }

    public static function user(): array
    {
        self::start();
        return [
            'id'    => $_SESSION['usuario_id']    ?? null,
            'nome'  => $_SESSION['usuario_nome']  ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
        ];
    }
}
