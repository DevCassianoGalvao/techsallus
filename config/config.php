<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus — Configuração central
   ───────────────────────────────────────────────────────────── */

define('DB_HOST',   getenv('DB_HOST')   ?: 'localhost');
define('DB_NAME',   getenv('DB_NAME')   ?: 'techsallus');
define('DB_USER',   getenv('DB_USER')   ?: 'root');
define('DB_PASS',   getenv('DB_PASS')   ?: '');
define('DB_CHARSET','utf8mb4');

define('APP_SECRET', getenv('APP_SECRET') ?: 'change-me-in-production');
define('APP_ENV',    getenv('APP_ENV')    ?: 'production');

define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');

define('SESSION_LIFETIME', 3600);
define('BASE_URL', getenv('BASE_URL') ?: 'https://techsallus.com.br');
