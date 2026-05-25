<?php
/* ─────────────────────────────────────────────────────────────
   admin/redefinir-senha.php — Redefinição de senha via token
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
Env::load($rootDir . '/.env');

$token   = trim($_GET['token'] ?? '');
$erro    = '';
$sucesso = false;

/* ── Validar token ───────────────────────────────────────────── */
try {
    $usuario = $token ? DB::fetchOne(
        "SELECT id, nome FROM usuarios WHERE reset_token = ? AND reset_expires_at > NOW() LIMIT 1",
        [$token]
    ) : null;
} catch (Exception $e) {
    $usuario = null;
}

if (!$usuario) {
    $erro = 'Link inválido ou expirado. Solicite um novo.';
}

/* ── POST — atualizar senha ──────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
    $senha    = $_POST['senha']    ?? '';
    $confirma = $_POST['confirma'] ?? '';

    if (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        try {
            $hash = password_hash($senha, PASSWORD_BCRYPT);
            DB::query(
                "UPDATE usuarios SET senha_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?",
                [$hash, $usuario['id']]
            );
            $sucesso = true;
        } catch (Exception $e) {
            $erro = 'Erro interno. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Redefinir senha — Techsallus</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: #f5f8fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .card { background: #fff; border-radius: 20px; padding: 48px 40px; width: 100%; max-width: 420px; box-shadow: 0 4px 40px rgba(9,74,134,0.10); }
  .brand { font-family: 'Bricolage Grotesque', sans-serif; font-size: 22px; font-weight: 800; color: #094a86; margin-bottom: 20px; }
  .brand span { color: #ff7300; }
  h1 { font-family: 'Bricolage Grotesque', sans-serif; font-size: 18px; font-weight: 700; color: #0d1f35; margin-bottom: 20px; }
  label { display: block; font-size: 13px; font-weight: 600; color: #0d1f35; margin-bottom: 6px; }
  input { width: 100%; border: 1px solid #e2eaf3; border-radius: 8px; padding: 11px 14px; font-family: 'DM Sans', sans-serif; font-size: 14px; margin-bottom: 16px; outline: none; color: #0d1f35; }
  input:focus { border-color: #094a86; box-shadow: 0 0 0 3px rgba(9,74,134,0.08); }
  button { width: 100%; background: #094a86; color: #fff; border: none; border-radius: 8px; padding: 13px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; }
  button:hover { background: #073d70; }
  .msg-success { background: #eaf3de; color: #27500a; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 16px; }
  .msg-error   { background: #fcebeb; color: #a32d2d; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 16px; }
  .voltar { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #094a86; text-decoration: none; }
  .voltar:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="card">
  <div class="brand">tech<span>sallus</span></div>

  <?php if ($sucesso): ?>
    <div class="msg-success">✓ Senha redefinida com sucesso!</div>
    <a href="/admin/login.php" class="voltar" style="display:block;text-align:center;margin-top:0;font-weight:600">Fazer login →</a>

  <?php elseif ($erro && !$usuario): ?>
    <h1>Link inválido</h1>
    <div class="msg-error"><?= htmlspecialchars($erro) ?></div>
    <a href="/admin/esqueci-senha.php" class="voltar">← Solicitar novo link</a>

  <?php else: ?>
    <h1>Redefinir senha</h1>
    <?php if ($erro): ?>
      <div class="msg-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label for="senha">Nova senha</label>
      <input type="password" id="senha" name="senha" required minlength="8" placeholder="Mínimo 8 caracteres">
      <label for="confirma">Confirmar nova senha</label>
      <input type="password" id="confirma" name="confirma" required placeholder="Repita a senha">
      <button type="submit">Redefinir senha</button>
    </form>
    <a href="/admin/login.php" class="voltar">← Voltar ao login</a>
  <?php endif; ?>
</div>
</body>
</html>
