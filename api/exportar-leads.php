<?php
/* ─────────────────────────────────────────────────────────────
   api/exportar-leads.php — Exportação de leads para CSV (admin)
   GET params: busca, porte, de, ate  (mesmos filtros do CRM)
   ───────────────────────────────────────────────────────────── */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
require_once $rootDir . '/core/Schema.php';
Env::load($rootDir . '/.env');

Auth::start();
if (!Auth::check()) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'erro' => 'Não autorizado']);
    exit;
}

/* ── Filtros (CRM + Contatos) ───────────────────────────────── */
$busca   = trim($_GET['busca']   ?? '');
$estado  = trim($_GET['estado']  ?? '');
$tipo    = trim($_GET['tipo']    ?? '');
$porte   = trim($_GET['porte']   ?? '');
$periodo = trim($_GET['periodo'] ?? '');
$de      = trim($_GET['de']      ?? '');
$ate     = trim($_GET['ate']     ?? '');

$where  = ['1=1'];
$params = [];

if ($busca) {
    $where[]  = '(nome LIKE ? OR email LIKE ? OR instituicao LIKE ? OR cidade LIKE ?)';
    $like     = '%' . $busca . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
if ($estado) {
    $where[]  = 'estado = ?';
    $params[] = $estado;
}
if ($tipo) {
    $where[]  = 'tipo_instituicao = ?';
    $params[] = $tipo;
}
if ($porte) {
    $where[]  = 'porte = ?';
    $params[] = $porte;
}
if ($periodo === 'hoje') {
    $where[] = 'DATE(criado_em) = CURDATE()';
} elseif ($periodo === 'semana') {
    $where[] = 'criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
} elseif ($periodo === 'mes') {
    $where[] = 'criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
}
if ($de) {
    $where[]  = 'DATE(criado_em) >= ?';
    $params[] = $de;
}
if ($ate) {
    $where[]  = 'DATE(criado_em) <= ?';
    $params[] = $ate;
}

try {
    Schema::ensureLeadTrackingColumns();

    $sql = 'SELECT id, nome, instituicao, tipo_instituicao, cargo, cidade, estado, email, whatsapp, porte, status,
                   utm_source, utm_medium, utm_campaign, utm_term, utm_content, ip_origem, criado_em, atualizado_em
            FROM leads WHERE ' . implode(' AND ', $where) . ' ORDER BY criado_em DESC';
    $leads = DB::fetchAll($sql, $params);
} catch (Exception $e) {
    error_log('Exportar leads: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'erro' => 'Erro interno ao buscar leads']);
    exit;
}

/* ── Output CSV com BOM UTF-8 (compatível com Excel) ─────────── */
$filename = 'leads_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');

/* BOM para detecção de UTF-8 pelo Excel */
fwrite($out, "\xEF\xBB\xBF");

/* Cabeçalho */
fputcsv($out, [
    'ID', 'Nome', 'Instituição', 'Tipo de Instituição', 'Cargo', 'Cidade', 'Estado', 'E-mail', 'WhatsApp',
    'Porte', 'Status', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Term', 'UTM Content',
    'IP Origem', 'Cadastrado em', 'Atualizado em',
], ';');

/* Dados */
foreach ($leads as $lead) {
    fputcsv($out, [
        $lead['id'],
        $lead['nome'],
        $lead['instituicao'],
        $lead['tipo_instituicao'] ?? '',
        $lead['cargo'],
        $lead['cidade'] ?? '',
        $lead['estado'] ?? '',
        $lead['email'],
        $lead['whatsapp'],
        $lead['porte'],
        $lead['status'],
        $lead['utm_source']   ?? '',
        $lead['utm_medium']   ?? '',
        $lead['utm_campaign'] ?? '',
        $lead['utm_term']     ?? '',
        $lead['utm_content']  ?? '',
        $lead['ip_origem']    ?? '',
        $lead['criado_em'],
        $lead['atualizado_em'],
    ], ';');
}

fclose($out);
