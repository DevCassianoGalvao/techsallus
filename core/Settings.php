<?php

require_once __DIR__ . '/Env.php';
require_once __DIR__ . '/DB.php';

Env::load(dirname(__DIR__) . '/.env');

class Settings
{
    private static bool $ready = false;

    private static function ensureTable(): void
    {
        if (self::$ready) {
            return;
        }

        DB::query("
            CREATE TABLE IF NOT EXISTS configuracoes (
                chave VARCHAR(100) PRIMARY KEY,
                valor TEXT DEFAULT NULL,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        self::$ready = true;
    }

    public static function get(string $key, string $default = ''): string
    {
        try {
            self::ensureTable();
            $row = DB::fetchOne("SELECT valor FROM configuracoes WHERE chave = ? LIMIT 1", [$key]);
            return $row ? (string)$row['valor'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }

    public static function set(string $key, string $value): void
    {
        self::ensureTable();
        DB::query(
            "INSERT INTO configuracoes (chave, valor) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()",
            [$key, $value]
        );
    }

    public static function whatsappNumber(): string
    {
        $number = preg_replace('/\D+/', '', self::get('whatsapp_numero', '557181299624'));
        return $number ?: '557181299624';
    }

    public static function whatsappMessage(): string
    {
        $message = trim(self::get('whatsapp_mensagem', 'Ola, gostaria de mais informacoes sobre o sistema de voces'));
        return $message ?: 'Ola, gostaria de mais informacoes sobre o sistema de voces';
    }

    public static function whatsappUrl(?string $message = null): string
    {
        $message = $message ?? self::whatsappMessage();
        return 'https://wa.me/' . self::whatsappNumber() . '?text=' . rawurlencode($message);
    }
}
