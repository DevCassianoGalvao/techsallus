<?php

header('Content-Type: text/html; charset=UTF-8');

$rootDir = __DIR__;
require_once $rootDir . '/core/Env.php';
Env::load($rootDir . '/.env');

if (Env::get('INSTALL_ENABLED', 'false') !== 'true') {
    http_response_code(404);
    exit('Instalador desativado.');
}

$results = [];
$error   = null;

$token      = $_GET['token'] ?? '';
$validToken = Env::get('INSTALL_TOKEN', 'techsallus-install-2026');
if ($token !== $validToken) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:#a32d2d;padding:40px">Acesso negado. Use ?token=SEU_TOKEN</h2>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $host = Env::get('DB_HOST', 'localhost');
        $name = Env::get('DB_NAME');
        $user = Env::get('DB_USER');
        $pass = Env::get('DB_PASS');

        $pdo = new PDO(
            "mysql:host={$host};charset=utf8mb4",
            $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$name}`");
        $results[] = ['ok', "Banco '{$name}' criado/verificado"];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `usuarios` (
                `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `nome`       VARCHAR(100) NOT NULL,
                `email`      VARCHAR(150) NOT NULL UNIQUE,
                `senha_hash` VARCHAR(255) NOT NULL,
                `criado_em`  DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela usuarios criada'];

        /* Adicionar colunas de recuperação de senha (idempotente) */
        foreach (['reset_token VARCHAR(64) DEFAULT NULL', 'reset_expires_at DATETIME DEFAULT NULL'] as $col) {
            $colName = explode(' ', $col)[0];
            $exists  = $pdo->query("SHOW COLUMNS FROM `usuarios` LIKE '{$colName}'")->rowCount();
            if (!$exists) {
                $pdo->exec("ALTER TABLE `usuarios` ADD COLUMN {$col}");
            }
        }
        $results[] = ['ok', 'Colunas reset_token / reset_expires_at verificadas'];

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute(['admin@techsallus.com.br']);
        if (!$stmt->fetch()) {
            $hash = password_hash('Techsallus@2026', PASSWORD_BCRYPT);
            $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)")
                ->execute(['Administrador', 'admin@techsallus.com.br', $hash]);
            $results[] = ['ok', 'Admin criado — email: admin@techsallus.com.br | senha: Techsallus@2026'];
        } else {
            $results[] = ['info', 'Usuário admin já existe'];
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `leads` (
                `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `nome`          VARCHAR(150) NOT NULL,
                `instituicao`   VARCHAR(200) NOT NULL,
                `tipo_instituicao` VARCHAR(100) DEFAULT NULL,
                `cargo`         VARCHAR(100) NOT NULL,
                `email`         VARCHAR(150) NOT NULL,
                `whatsapp`      VARCHAR(20)  NOT NULL,
                `porte`         VARCHAR(50)  NOT NULL,
                `cidade`        VARCHAR(120) DEFAULT NULL,
                `estado`        CHAR(2) DEFAULT NULL,
                `status`        ENUM('novo','contato','proposta','fechamento','arquivado') NOT NULL DEFAULT 'novo',
                `notas`         TEXT,
                `utm_source`    VARCHAR(100) DEFAULT NULL,
                `utm_medium`    VARCHAR(100) DEFAULT NULL,
                `utm_campaign`  VARCHAR(100) DEFAULT NULL,
                `utm_term`      VARCHAR(150) DEFAULT NULL,
                `utm_content`   VARCHAR(150) DEFAULT NULL,
                `ip_origem`     VARCHAR(45)  DEFAULT NULL,
                `criado_em`     DATETIME DEFAULT CURRENT_TIMESTAMP,
                `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_status` (`status`),
                INDEX `idx_criado_em` (`criado_em`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela leads criada'];

        foreach ([
            'tipo_instituicao VARCHAR(100) DEFAULT NULL AFTER instituicao',
            'perfil_operacao VARCHAR(50) DEFAULT NULL AFTER tipo_instituicao',
            'principal_desafio VARCHAR(255) DEFAULT NULL AFTER perfil_operacao',
            'mensagem TEXT DEFAULT NULL AFTER principal_desafio',
            'cidade VARCHAR(120) DEFAULT NULL AFTER porte',
            'estado CHAR(2) DEFAULT NULL AFTER cidade',
            'utm_term VARCHAR(150) DEFAULT NULL AFTER utm_campaign',
            'utm_content VARCHAR(150) DEFAULT NULL AFTER utm_term',
        ] as $col) {
            $colName = explode(' ', $col)[0];
            $exists = $pdo->query("SHOW COLUMNS FROM `leads` LIKE '{$colName}'")->rowCount();
            if (!$exists) {
                $pdo->exec("ALTER TABLE `leads` ADD COLUMN {$col}");
            }
        }
        $pdo->exec("ALTER TABLE `leads` MODIFY `status` ENUM('novo','contato','proposta','fechamento','arquivado') NOT NULL DEFAULT 'novo'");
        $results[] = ['ok', 'Colunas de leads verificadas'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `lead_notas` (
                `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `lead_id`    INT UNSIGNED NOT NULL,
                `usuario_id` INT UNSIGNED NOT NULL,
                `nota`       TEXT NOT NULL,
                `criado_em`  DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`lead_id`)    REFERENCES `leads`(`id`)    ON DELETE CASCADE,
                FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela lead_notas criada'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `lead_historico` (
                `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `lead_id`    INT UNSIGNED NOT NULL,
                `usuario_id` INT UNSIGNED DEFAULT NULL,
                `tipo`       ENUM('criacao','mover','nota','movimentacao') NOT NULL,
                `descricao`  VARCHAR(300) NOT NULL,
                `criado_em`  DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`lead_id`)    REFERENCES `leads`(`id`)    ON DELETE CASCADE,
                FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
                INDEX `idx_lead_id` (`lead_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $pdo->exec("ALTER TABLE `lead_historico` MODIFY `tipo` ENUM('criacao','mover','nota','movimentacao') NOT NULL");
        $results[] = ['ok', 'Tabela lead_historico criada'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `categorias` (
                `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL UNIQUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela categorias criada'];

        $stmtCat = $pdo->prepare("INSERT IGNORE INTO categorias (nome, slug) VALUES (?, ?)");
        foreach ([
            ['Agendamento', 'agendamento'],
            ['Faturamento',  'faturamento'],
            ['Prontuário',   'prontuario'],
            ['Gestão',       'gestao'],
            ['Tecnologia',   'tecnologia'],
        ] as $cat) { $stmtCat->execute($cat); }
        $results[] = ['ok', '5 categorias padrão inseridas'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `posts` (
                `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `titulo`        VARCHAR(250) NOT NULL,
                `slug`          VARCHAR(191) NOT NULL UNIQUE,
                `categoria_id`  INT UNSIGNED DEFAULT NULL,
                `tags`          VARCHAR(500) DEFAULT NULL,
                `resumo`        TEXT,
                `conteudo`      LONGTEXT,
                `imagem_capa`   VARCHAR(300) DEFAULT NULL,
                `meta_title`    VARCHAR(160) DEFAULT NULL,
                `meta_desc`     VARCHAR(320) DEFAULT NULL,
                `status`        ENUM('rascunho','publicado') NOT NULL DEFAULT 'rascunho',
                `published_at`  DATETIME DEFAULT NULL,
                `criado_em`     DATETIME DEFAULT CURRENT_TIMESTAMP,
                `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL,
                INDEX `idx_status` (`status`),
                INDEX `idx_slug`   (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela posts criada'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `scripts_injecao` (
                `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `posicao`   ENUM('head','body','footer') NOT NULL,
                `nome`      VARCHAR(100) NOT NULL,
                `conteudo`  TEXT NOT NULL,
                `ativo`     TINYINT(1) DEFAULT 1,
                `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela scripts_injecao criada'];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `configuracoes` (
                `chave`        VARCHAR(100) PRIMARY KEY,
                `valor`        TEXT DEFAULT NULL,
                `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $results[] = ['ok', 'Tabela configuracoes criada'];

        $results[] = ['success', '✓ Instalação concluída! Apague install.php antes de ir para produção.'];

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Techsallus — Instalador</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f8fc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
  .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 560px; width: 100%; box-shadow: 0 4px 32px rgba(9,74,134,0.10); }
  h1 { font-size: 22px; color: #0d1f35; margin-bottom: 6px; }
  .sub { font-size: 14px; color: #4a6080; margin-bottom: 28px; }
  .result { padding: 10px 14px; border-radius: 8px; margin-bottom: 8px; font-size: 13px; }
  .ok      { background: #eaf3de; color: #27500a; }
  .info    { background: #e6f1fb; color: #0c447c; }
  .success { background: #eaf3de; color: #27500a; font-weight: 700; font-size: 14px; padding: 14px; }
  .error   { background: #fcebeb; color: #a32d2d; padding: 14px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
  button { width: 100%; background: #094a86; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 20px; }
  button:hover { background: #073d70; }
  .warn { background: #faeeda; color: #854f0b; padding: 12px 14px; border-radius: 8px; font-size: 12px; margin-top: 20px; }
</style>
</head>
<body>
<div class="card">
  <h1>🔧 Instalador Techsallus</h1>
  <p class="sub">Cria todas as tabelas do banco de dados e o usuário admin padrão.</p>

  <?php if ($error): ?>
    <div class="error"><strong>Erro:</strong> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php foreach ($results as [$type, $msg]): ?>
    <div class="result <?= $type ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endforeach; ?>

  <?php if (empty($results)): ?>
    <form method="POST">
      <button type="submit">Instalar banco de dados</button>
    </form>
  <?php endif; ?>

  <div class="warn">⚠ Apague <code>install.php</code> imediatamente após a instalação.</div>
</div>
</body>
</html>
