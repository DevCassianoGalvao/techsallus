<?php
/* ─────────────────────────────────────────────────────────────
   api/exportar-leads.php — Exportação de leads para CSV (admin)
   GET params: busca, porte, de, ate  (mesmos filtros do CRM)
   ───────────────────────────────────────────────────────────── */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');

Auth::start();
if (!Auth::check()) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'erro' => 'Não autorizado']);
    exit;
}

/* ── Filtros (mesmos do CRM) ────────────────────────────────── */
$busca = trim($_GET['busca'] ?? '');
$porte = trim($_GET['porte'] ?? '');
$de    = trim($_GET['de']    ?? '');
$ate   = trim($_GET['ate']   ?? '');

$where  = ['1=1'];
$params = [];

if ($busca) {
    $where[]  = '(nome LIKE ? OR email LIKE ? OR instituicao LIKE ?)';
    $like     = '%' . $busca . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
if ($porte) {
    $where[]  = 'porte = ?';
    $params[] = $porte;
}
if ($de) {
    $where[]  = 'DATE(criado_em) >= ?';
    $params[] = $de;
}
if ($ate) {
    $where[]  = 'DATE(criado_em) <= ?';
    $params[] = $ate;
}

$sql = 'SELECT id, nome, instituicao, cargo, email, whatsapp, porte, status,
               utm_source, utm_medium, utm_campaign, ip_origem, criado_em, atualizado_em
        FROM leads WHERE ' . implode(' AND ', $where) . ' ORDER BY criado_em DESC';

try {
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
    'ID', 'Nome', 'Instituição', 'Cargo', 'E-mail', 'WhatsApp',
    'Porte', 'Status', 'UTM Source', 'UTM Medium', 'UTM Campaign',
    'IP Origem', 'Cadastrado em', 'Atualizado em',
], ';');

/* Dados */
foreach ($leads as $lead) {
    fputcsv($out, [
        $lead['id'],
        $lead['nome'],
        $lead['instituicao'],
        $lead['cargo'],
        $lead['email'],
        $lead['whatsapp'],
        $lead['porte'],
        $lead['status'],
        $lead['utm_source']   ?? '',
        $lead['utm_medium']   ?? '',
        $lead['utm_campaign'] ?? '',
        $lead['ip_origem']    ?? '',
        $lead['criado_em'],
        $lead['atualizado_em'],
    ], ';');
}

fclose($out);
