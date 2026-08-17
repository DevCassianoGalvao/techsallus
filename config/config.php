<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus — Configuração central
   ───────────────────────────────────────────────────────────── */

define('DB_HOST',   $_ENV['DB_HOST']   ?? getenv('DB_HOST')   ?: 'localhost');
define('DB_NAME',   $_ENV['DB_NAME']   ?? getenv('DB_NAME')   ?: 'techsallus');
define('DB_USER',   $_ENV['DB_USER']   ?? getenv('DB_USER')   ?: 'root');
define('DB_PASS',   $_ENV['DB_PASS']   ?? getenv('DB_PASS')   ?: '');
define('DB_CHARSET','utf8mb4');

define('APP_SECRET', $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'change-me-in-production');
define('APP_ENV',    $_ENV['APP_ENV']    ?? getenv('APP_ENV')    ?: 'production');

define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: '');

define('SESSION_LIFETIME', 3600);
define('BASE_URL', $_ENV['BASE_URL'] ?? getenv('BASE_URL') ?: 'https://techsallus.com.br');
