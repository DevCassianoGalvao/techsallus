<?php
$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Security.php';

Env::load($rootDir . '/.env');
Security::headers();

$mensagem = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrf()) {
        $mensagem = 'Sessao expirada. Recarregue a pagina e tente novamente.';
        $tipo = 'error';
    } else {
        $email = trim($_POST['email'] ?? '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $usuario = DB::fetchOne(
                    "SELECT id, nome FROM usuarios WHERE email = ? LIMIT 1",
                    [$email]
                );

                if ($usuario) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', time() + 3600);

                    DB::query(
                        "UPDATE usuarios SET reset_token = ?, reset_expires_at = ? WHERE id = ?",
                        [$token, $expires, $usuario['id']]
                    );

                    $link = "https://techsallus.com.br/admin/redefinir-senha.php?token={$token}";
                    $apiKey = Env::get('BREVO_API_KEY');

                    if ($apiKey) {
                        $body = json_encode([
                            'sender' => ['name' => 'Techsallus', 'email' => Env::get('BREVO_FROM_EMAIL')],
                            'to' => [['email' => $email, 'name' => $usuario['nome']]],
                            'subject' => 'Redefinicao de senha - Techsallus Admin',
                            'htmlContent' => "
                                <div style='font-family:sans-serif;max-width:480px'>
                                  <h2 style='color:#094a86'>Redefinicao de senha</h2>
                                  <p>Ola, " . htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') . "!</p>
                                  <p>Clique no botao abaixo para redefinir sua senha. O link expira em 1 hora.</p>
                                  <p style='margin:24px 0'>
                                    <a href='{$link}' style='background:#094a86;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600'>Redefinir senha</a>
                                  </p>
                                  <p style='color:#94a3b8;font-size:12px'>Se nao solicitou, ignore este e-mail.</p>
                                </div>
                            ",
                        ]);

                        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
                        curl_setopt_array($ch, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $body,
                            CURLOPT_HTTPHEADER => [
                                'accept: application/json',
                                'api-key: ' . $apiKey,
                                'content-type: application/json',
                            ],
                            CURLOPT_TIMEOUT => 10,
                        ]);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                }
            } catch (Exception $e) {
                error_log('Esqueci senha: ' . $e->getMessage());
            }

            $mensagem = 'Se este e-mail estiver cadastrado, voce recebera as instrucoes em instantes.';
            $tipo = 'success';
        } else {
            $mensagem = 'E-mail invalido.';
            $tipo = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Esqueci minha senha - Techsallus</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: #f5f8fc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .card { background: #fff; border-radius: 20px; padding: 48px 40px; width: 100%; max-width: 420px; box-shadow: 0 4px 40px rgba(9,74,134,0.10); }
  .brand { font-family: 'Bricolage Grotesque', sans-serif; font-size: 22px; font-weight: 800; color: #094a86; margin-bottom: 6px; }
  .brand span { color: #ff7300; }
  h1 { font-family: 'Bricolage Grotesque', sans-serif; font-size: 18px; font-weight: 700; color: #0d1f35; margin-bottom: 6px; }
  p  { font-size: 14px; color: #4a6080; margin-bottom: 24px; line-height: 1.55; }
  label { display: block; font-size: 13px; font-weight: 600; color: #0d1f35; margin-bottom: 6px; }
  input { width: 100%; border: 1px solid #e2eaf3; border-radius: 8px; padding: 11px 14px; font-family: 'DM Sans', sans-serif; font-size: 14px; margin-bottom: 16px; outline: none; color: #0d1f35; }
  input:focus { border-color: #094a86; box-shadow: 0 0 0 3px rgba(9,74,134,0.08); }
  button { width: 100%; background: #094a86; color: #fff; border: none; border-radius: 8px; padding: 13px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; }
  button:hover { background: #073d70; }
  .msg-success { background: #eaf3de; color: #27500a; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 16px; }
  .msg-error { background: #fcebeb; color: #a32d2d; border-radius: 8px; padding: 12px 14px; font-size: 13px; margin-bottom: 16px; }
  .voltar { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #4a6080; text-decoration: none; }
  .voltar:hover { color: #094a86; }
</style>
</head>
<body>
<div class="card">
  <div class="brand">tech<span>sallus</span></div>
  <h1>Esqueci minha senha</h1>
  <p>Informe seu e-mail e enviaremos as instrucoes para redefinir sua senha.</p>

  <?php if ($mensagem): ?>
    <div class="msg-<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($mensagem) ?></div>
  <?php endif; ?>

  <?php if ($tipo !== 'success'): ?>
    <form method="POST">
      <?= Security::csrfField() ?>
      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" required placeholder="seu@email.com.br" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <button type="submit">Enviar instrucoes</button>
    </form>
  <?php endif; ?>

  <a href="/admin/login.php" class="voltar">Voltar ao login</a>
</div>
</body>
</html>
