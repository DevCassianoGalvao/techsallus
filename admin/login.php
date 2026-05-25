<?php
/* ─────────────────────────────────────────────────────────────
   admin/login.php — Admin authentication page
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');

Auth::start();
if (Auth::check()) {
    header('Location: /admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $usuario = DB::fetchOne(
                "SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ? LIMIT 1",
                [$email]
            );
            if ($usuario && password_verify($password, $usuario['senha_hash'])) {
                Auth::login($usuario);
                header('Location: /admin/');
                exit;
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
        }
    }
    $error = 'E-mail ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entrar — TechSallus Admin</title>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/admin.css"/>
</head>
<body class="admin-login-page">
  <div class="admin-login-box">
    <img src="/assets/img/logo.png" alt="Techsallus" class="admin-login-logo"/>
    <h1 class="admin-login-title">Área restrita</h1>

    <?php if ($error): ?>
      <div class="admin-alert admin-alert-error" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="admin-login-form" novalidate>
      <div class="form-field">
        <label for="email">E-mail</label>
        <input
          type="email"
          id="email"
          name="email"
          required
          autocomplete="email"
          placeholder="admin@techsallus.com.br"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        />
      </div>
      <div class="form-field">
        <label for="password">Senha</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          autocomplete="current-password"
          placeholder="••••••••"
        />
      </div>
      <button type="submit" class="btn-submit">Entrar</button>
    </form>

    <a href="/admin/esqueci-senha.php" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:#4a6080;text-decoration:none">
      Esqueci minha senha
    </a>

    <a href="/" class="admin-login-back">← Voltar ao site</a>
  </div>
</body>
</html>
