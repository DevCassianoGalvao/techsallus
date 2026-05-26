<?php
/* ─────────────────────────────────────────────────────────────
   api/leads.php — Captura de leads do formulário de demonstração
   POST FormData → valida → honeypot → rate limit → DB → Brevo
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';

Env::load($rootDir . '/.env');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido']);
    exit;
}

/* ── Campos principais ──────────────────────────────────────── */
$nome        = trim(strip_tags($_POST['nome']        ?? ''));
$instituicao = trim(strip_tags($_POST['instituicao'] ?? ''));
$cargo       = trim(strip_tags($_POST['cargo']       ?? ''));
$email       = trim($_POST['email']                  ?? '');
$whatsapp    = trim(strip_tags($_POST['whatsapp']    ?? ''));
$porte       = trim(strip_tags($_POST['porte']       ?? ''));

/* ── Validação ──────────────────────────────────────────────── */
$erros = [];
if (empty($nome))        $erros[] = 'Nome obrigatório';
if (empty($instituicao)) $erros[] = 'Instituição obrigatória';
if (empty($cargo))       $erros[] = 'Cargo obrigatório';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido';
if (empty($whatsapp))    $erros[] = 'WhatsApp obrigatório';
if (empty($porte))       $erros[] = 'Porte obrigatório';

if (!empty($erros)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erros' => $erros]);
    exit;
}

/* ── Honeypot — bots preenchem o campo "website", humanos não ── */
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true]); // fingir sucesso
    exit;
}

/* ── Rate limit por IP — máximo 3 submissões/hora ───────────── */
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

/* ── UTM params ─────────────────────────────────────────────── */
$utm_source   = trim(strip_tags($_POST['utm_source']   ?? ''));
$utm_medium   = trim(strip_tags($_POST['utm_medium']   ?? ''));
$utm_campaign = trim(strip_tags($_POST['utm_campaign'] ?? ''));
$ip_origem    = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

/* ── Inserir no banco ───────────────────────────────────────── */
try {
    $leadId = DB::insert(
        "INSERT INTO leads (nome, instituicao, cargo, email, whatsapp, porte, utm_source, utm_medium, utm_campaign, ip_origem)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$nome, $instituicao, $cargo, $email, $whatsapp, $porte, $utm_source, $utm_medium, $utm_campaign, $ip_origem]
    );

    /* ── Histórico de criação (non-fatal) ─────────────────────── */
    try {
        DB::query(
            "INSERT INTO lead_historico (lead_id, usuario_id, tipo, descricao) VALUES (?, NULL, 'criacao', 'Lead cadastrado via formulário')",
            [$leadId]
        );
    } catch (Exception $he) {
        error_log('Historico criacao: ' . $he->getMessage());
    }

    notificarBrevo($nome, $instituicao, $cargo, $email, $whatsapp, $porte);

    echo json_encode(['ok' => true, 'id' => $leadId]);

} catch (Exception $e) {
    error_log('Erro ao salvar lead: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Erro interno. Tente novamente.']);
}

/* ── Notificação Brevo ──────────────────────────────────────── */
function notificarBrevo(string $nome, string $inst, string $cargo, string $email, string $whatsapp, string $porte): void
{
    $apiKey    = Env::get('BREVO_API_KEY');
    $fromEmail = Env::get('BREVO_FROM_EMAIL');
    $fromName  = Env::get('BREVO_FROM_NAME');
    $toEmail   = Env::get('BREVO_TO_EMAIL');

    if (empty($apiKey) || empty($toEmail)) return;

    $body = json_encode([
        'sender'      => ['name' => $fromName, 'email' => $fromEmail],
        'to'          => [['email' => $toEmail, 'name' => 'Techsallus']],
        'subject'     => "🔔 Novo lead: {$nome} — {$inst}",
        'htmlContent' => "
            <div style='font-family:sans-serif;max-width:520px'>
              <h2 style='color:#094a86'>Novo lead recebido!</h2>
              <table style='width:100%;border-collapse:collapse'>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Nome</td><td style='padding:8px;border-bottom:1px solid #eee'><strong>{$nome}</strong></td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Instituição</td><td style='padding:8px;border-bottom:1px solid #eee'>{$inst}</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>Cargo</td><td style='padding:8px;border-bottom:1px solid #eee'>{$cargo}</td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>E-mail</td><td style='padding:8px;border-bottom:1px solid #eee'><a href='mailto:{$email}'>{$email}</a></td></tr>
                <tr><td style='padding:8px;border-bottom:1px solid #eee;color:#666'>WhatsApp</td><td style='padding:8px;border-bottom:1px solid #eee'><a href='https://wa.me/55{$whatsapp}'>{$whatsapp}</a></td></tr>
                <tr><td style='padding:8px;color:#666'>Porte</td><td style='padding:8px'>{$porte}</td></tr>
              </table>
              <p style='margin-top:20px'><a href='https://techsallus.com.br/admin/crm.php' style='background:#094a86;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>Ver no CRM</a></p>
            </div>
        ",
    ]);

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
        CURLOPT_TIMEOUT => 10,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
