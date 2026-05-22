<?php
require_once __DIR__ . '/../core/Auth.php';

Auth::start();
if (Auth::check()) {
    header('Location: /admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (Auth::login($email, $password)) {
        header('Location: /admin/');
        exit;
    }
    $error = 'E-mail ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — TechSallus Admin</title>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/admin.css"/>
</head>
<body class="admin-login-page">
  <div class="admin-login-box">
    <img src="/assets/img/logo.png" alt="Techsallus" class="admin-login-logo"/>
    <h1 class="admin-login-title">Área restrita</h1>
    <?php if ($error): ?>
      <div class="admin-alert admin-alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="admin-login-form">
      <div class="form-field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required autocomplete="email" placeholder="admin@techsallus.com.br"/>
      </div>
      <div class="form-field">
        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••"/>
      </div>
      <button type="submit" class="btn-submit">Entrar</button>
    </form>
    <a href="/" class="admin-login-back">← Voltar ao site</a>
  </div>
</body>
</html>
