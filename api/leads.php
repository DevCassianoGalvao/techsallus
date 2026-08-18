<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Schema.php';
require_once $rootDir . '/core/Security.php';

Env::load($rootDir . '/.env');
Security::headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Metodo nao permitido']);
    exit;
}

Security::requireCsrfJson($_POST);

$nome              = trim(strip_tags($_POST['nome']              ?? ''));
$instituicao       = trim(strip_tags($_POST['instituicao']       ?? ''));
$perfil_operacao   = trim(strip_tags($_POST['perfil_operacao']   ?? ''));
$principal_desafio = trim(strip_tags($_POST['principal_desafio'] ?? ''));
$email             = trim($_POST['email']                        ?? '');
$whatsapp          = trim(strip_tags($_POST['whatsapp']          ?? ''));
$mensagem          = trim(strip_tags($_POST['mensagem']          ?? ''));

// Legacy fields — kept for backward compatibility with the old form's data shape, no longer collected by the site.
$tipo_instituicao = trim(strip_tags($_POST['tipo_instituicao'] ?? ''));
$cargo            = trim(strip_tags($_POST['cargo']            ?? ''));
$porte            = trim(strip_tags($_POST['porte']            ?? ''));
$cidade           = trim(strip_tags($_POST['cidade']           ?? ''));
$estado           = trim(strip_tags($_POST['estado']           ?? ''));

$erros = [];
if (empty($nome))              $erros[] = 'Nome obrigatorio';
if (empty($instituicao))       $erros[] = 'Instituicao obrigatoria';
if (empty($perfil_operacao))   $erros[] = 'Perfil da operacao obrigatorio';
if (empty($principal_desafio)) $erros[] = 'Principal desafio obrigatorio';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail invalido';
if (empty($whatsapp))          $erros[] = 'WhatsApp obrigatorio';

if (!empty($erros)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erros' => $erros]);
    exit;
}

if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true]);
    exit;
}

$ip       = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ipHash   = hash('sha256', $ip);
$cacheDir = sys_get_temp_dir() . '/techsallus_rl/';

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0700, true);
}

$cacheFile = $cacheDir . $ipHash . '.json';
$limite    = 3;
$janela    = 3600;
$agora     = time();

$dados = file_exists($cacheFile)
    ? (json_decode(file_get_contents($cacheFile), true) ?: ['count' => 0, 'first' => $agora])
    : ['count' => 0, 'first' => $agora];

if ($agora - $dados['first'] > $janela) {
    $dados = ['count' => 0, 'first' => $agora];
}

if ($dados['count'] >= $limite) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'erro' => 'Muitas tentativas. Tente novamente em alguns minutos.']);
    exit;
}

$dados['count']++;
file_put_contents($cacheFile, json_encode($dados), LOCK_EX);

$utm_source   = trim(strip_tags($_POST['utm_source']   ?? ''));
$utm_medium   = trim(strip_tags($_POST['utm_medium']   ?? ''));
$utm_campaign = trim(strip_tags($_POST['utm_campaign'] ?? ''));
$utm_term     = trim(strip_tags($_POST['utm_term']     ?? ''));
$utm_content  = trim(strip_tags($_POST['utm_content']  ?? ''));
$ip_origem    = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

try {
    Schema::ensureLeadTrackingColumns();

    $leadId = DB::insert(
        "INSERT INTO leads
         (nome, instituicao, tipo_instituicao, perfil_operacao, principal_desafio, cargo, email, whatsapp, mensagem, porte,
          cidade, estado, utm_source, utm_medium, utm_campaign, utm_term, utm_content, ip_origem)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$nome, $instituicao, $tipo_instituicao, $perfil_operacao, $principal_desafio, $cargo, $email, $whatsapp, $mensagem,
         $porte, $cidade, $estado, $utm_source, $utm_medium, $utm_campaign, $utm_term, $utm_content, $ip_origem]
    );

    try {
        DB::query(
            "INSERT INTO lead_historico (lead_id, usuario_id, tipo, descricao) VALUES (?, NULL, 'criacao', 'Lead cadastrado via formulario')",
            [$leadId]
        );
    } catch (Exception $he) {
        error_log('Historico criacao: ' . $he->getMessage());
    }

    notificarBrevo($nome, $instituicao, $perfil_operacao, $principal_desafio, $email, $whatsapp, $mensagem);

    echo json_encode(['ok' => true, 'id' => $leadId]);

} catch (Exception $e) {
    error_log('Erro ao salvar lead: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Erro interno. Tente novamente.']);
}

function notificarBrevo(string $nome, string $inst, string $perfil, string $desafio, string $email, string $whatsapp, string $mensagem): void
{
    $apiKey   = Env::get('BREVO_API_KEY');
    $fromEmail= Env::get('BREVO_FROM_EMAIL');
    $fromName = Env::get('BREVO_FROM_NAME', 'Techsallus');
    $toEmail  = Env::get('BREVO_TO_EMAIL');

    if (empty($apiKey)) {
        error_log('[Brevo] ABORTADO: BREVO_API_KEY nao configurada no .env');
        return;
    }

    if (empty($toEmail)) {
        error_log('[Brevo] ABORTADO: BREVO_TO_EMAIL nao configurado no .env');
        return;
    }

    if (empty($fromEmail)) {
        error_log('[Brevo] ABORTADO: BREVO_FROM_EMAIL nao configurado no .env');
        return;
    }

    $nomeHtml     = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
    $instHtml     = htmlspecialchars($inst, ENT_QUOTES, 'UTF-8');
    $perfilHtml   = htmlspecialchars($perfil, ENT_QUOTES, 'UTF-8');
    $desafioHtml  = htmlspecialchars($desafio, ENT_QUOTES, 'UTF-8');
    $emailHtml    = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $whatsappHtml = htmlspecialchars($whatsapp, ENT_QUOTES, 'UTF-8');
    $mensagemHtml = nl2br(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'));
    $whatsappLink = preg_replace('/\D+/', '', $whatsapp);
    $mensagemRow  = $mensagem !== ''
        ? "<tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Mensagem</td><td style='padding:8px;border-bottom:1px solid #eee'>{$mensagemHtml}</td></tr>"
        : '';

    $payload = [
        'sender'      => ['name' => $fromName, 'email' => $fromEmail],
        'to'          => [['email' => $toEmail, 'name'  => 'Techsallus']],
        'subject'     => "Novo lead: {$nome} - {$inst}",
        'htmlContent' => "
            <div style='font-family:sans-serif;max-width:520px'>
              <h2 style='color:#094a86'>Novo lead recebido!</h2>
              <table style='width:100%;border-collapse:collapse'>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666;width:130px'>Nome</td><td style='padding:8px;border-bottom:1px solid #eee'><strong>{$nomeHtml}</strong></td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Instituicao</td><td style='padding:8px;border-bottom:1px solid #eee'>{$instHtml}</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Perfil da operacao</td><td style='padding:8px;border-bottom:1px solid #eee'>{$perfilHtml}</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Principal desafio</td><td style='padding:8px;border-bottom:1px solid #eee'>{$desafioHtml}</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>E-mail</td><td style='padding:8px;border-bottom:1px solid #eee'><a href='mailto:{$emailHtml}'>{$emailHtml}</a></td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>WhatsApp</td><td style='padding:8px;border-bottom:1px solid #eee'><a href='https://wa.me/55{$whatsappLink}'>{$whatsappHtml}</a></td></tr>
                {$mensagemRow}
              </table>
              <p style='margin-top:20px'>
                <a href='" . rtrim(Env::get('BASE_URL', 'https://techsallus.com.br'), '/') . "/admin/crm.php'
                   style='background:#094a86;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600'>
                  Ver no Kanban
                </a>
              </p>
            </div>
        ",
    ];

    $body = json_encode($payload);

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json',
        ],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        error_log('[Brevo] cURL Erro: ' . $curlErr);
    }

    if ($httpCode !== 201) {
        error_log('[Brevo] FALHOU - Esperado 201, recebeu ' . $httpCode);
        error_log('[Brevo] Resposta: ' . $response);
    }
}
