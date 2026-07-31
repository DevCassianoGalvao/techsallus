<?php

class Security
{
    public static function basePath(): string
    {
        $configured = getenv('BASE_PATH') ?: ($_SERVER['BASE_PATH'] ?? '');
        if ($configured !== '') {
            return rtrim('/' . trim((string)$configured, '/'), '/');
        }

        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        foreach (['/admin/', '/api/', '/blog/'] as $marker) {
            $pos = strpos($script, $marker);
            if ($pos !== false) {
                return rtrim(substr($script, 0, $pos), '/');
            }
        }

        $dir = trim(str_replace('\\', '/', dirname($script)), '/');
        return $dir === '' ? '' : '/' . $dir;
    }

    public static function url(string $path): string
    {
        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        return self::basePath() . '/' . ltrim($path, '/');
    }

    public static function headers(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'");
        self::rewriteOutputForBasePath();
    }

    public static function rewriteOutputForBasePath(): void
    {
        $base = self::basePath();
        if ($base === '' || defined('APP_BASE_REWRITE_STARTED')) {
            return;
        }

        define('APP_BASE_REWRITE_STARTED', true);
        ob_start(static function (string $html) use ($base): string {
            return str_replace(
                ['href="/', 'src="/', 'action="/', "href='/", "src='/", "action='/", "fetch('/", 'fetch("/'],
                ['href="' . $base . '/', 'src="' . $base . '/', 'action="' . $base . '/', "href='" . $base . '/', "src='" . $base . '/', "action='" . $base . '/', "fetch('" . $base . '/', 'fetch("' . $base . '/'],
                $html
            );
        });
    }

    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verifyCsrf(?array $data = null): bool
    {
        self::startSession();
        $data = $data ?? $_POST;
        $sent = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $token = $_SESSION['csrf_token'] ?? '';
        return is_string($sent) && is_string($token) && $sent !== '' && hash_equals($token, $sent);
    }

    public static function requireCsrfJson(?array $data = null): void
    {
        if (!self::verifyCsrf($data)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'erro' => 'Sessao expirada. Recarregue a pagina e tente novamente.']);
            exit;
        }
    }
}
