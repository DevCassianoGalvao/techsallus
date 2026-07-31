<?php

require_once __DIR__ . '/DB.php';

class Schema
{
    private static bool $leadTrackingReady = false;

    public static function ensureLeadTrackingColumns(): void
    {
        if (self::$leadTrackingReady) {
            return;
        }

        foreach ([
            'tipo_instituicao' => "ALTER TABLE leads ADD COLUMN tipo_instituicao VARCHAR(100) DEFAULT NULL AFTER instituicao",
            'cidade'           => "ALTER TABLE leads ADD COLUMN cidade VARCHAR(120) DEFAULT NULL AFTER porte",
            'estado'           => "ALTER TABLE leads ADD COLUMN estado CHAR(2) DEFAULT NULL AFTER cidade",
            'utm_term'         => "ALTER TABLE leads ADD COLUMN utm_term VARCHAR(150) DEFAULT NULL AFTER utm_campaign",
            'utm_content'      => "ALTER TABLE leads ADD COLUMN utm_content VARCHAR(150) DEFAULT NULL AFTER utm_term",
            'perfil_operacao'    => "ALTER TABLE leads ADD COLUMN perfil_operacao VARCHAR(50) DEFAULT NULL AFTER tipo_instituicao",
            'principal_desafio'  => "ALTER TABLE leads ADD COLUMN principal_desafio VARCHAR(255) DEFAULT NULL AFTER perfil_operacao",
            'mensagem'           => "ALTER TABLE leads ADD COLUMN mensagem TEXT DEFAULT NULL AFTER principal_desafio",
        ] as $column => $sql) {
            $exists = DB::fetchOne(
                "SELECT COLUMN_NAME
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'leads'
                   AND COLUMN_NAME = ?",
                [$column]
            );
            if (!$exists) {
                DB::query($sql);
            }
        }

        // principal_desafio agora aceita multiplas selecoes (string separada por virgula) —
        // amplia a coluna mesmo em bancos onde ela ja existia como VARCHAR(50).
        DB::query("ALTER TABLE leads MODIFY principal_desafio VARCHAR(255) DEFAULT NULL");

        DB::query("ALTER TABLE leads MODIFY status ENUM('novo','contato','proposta','fechamento','arquivado') NOT NULL DEFAULT 'novo'");

        try {
            DB::query("ALTER TABLE lead_historico MODIFY tipo ENUM('criacao','mover','nota','movimentacao') NOT NULL");
        } catch (Exception $e) {
            // The history table may not exist yet during a fresh install.
        }

        self::$leadTrackingReady = true;
    }
}
