<?php
/* ─────────────────────────────────────────────────────────────
   api/crm.php — CRM API (admin-only, JSON)
   POST actions:
     mover          — UPDATE leads SET status = ?
     notas          — SELECT lead_notas for a lead
     adicionar_nota — INSERT into lead_notas
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');

/* Auth guard */
Auth::start();
if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'erro' => 'Não autorizado']);
    exit;
}

/* Only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido']);
    exit;
}

/* Parse JSON body */
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = trim($input['action'] ?? '');

$allowedStatus = ['novo', 'contato', 'proposta', 'fechamento'];

switch ($action) {

    /* ── mover ───────────────────────────────────────────────── */
    case 'mover':
        $id     = (int)($input['id'] ?? 0);
        $status = trim($input['status'] ?? '');

        if (!$id || !in_array($status, $allowedStatus, true)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Dados inválidos']);
            exit;
        }

        try {
            DB::query(
                "UPDATE leads SET status = ?, atualizado_em = NOW() WHERE id = ?",
                [$status, $id]
            );
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            error_log('CRM mover: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'erro' => 'Erro interno']);
        }
        break;

    /* ── notas ───────────────────────────────────────────────── */
    case 'notas':
        $leadId = (int)($input['lead_id'] ?? 0);

        if (!$leadId) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'lead_id obrigatório']);
            exit;
        }

        try {
            $notas = DB::fetchAll(
                "SELECT ln.id, ln.nota, ln.criado_em, u.nome AS usuario_nome
                 FROM lead_notas ln
                 JOIN usuarios u ON u.id = ln.usuario_id
                 WHERE ln.lead_id = ?
                 ORDER BY ln.criado_em ASC",
                [$leadId]
            );
            echo json_encode(['ok' => true, 'notas' => $notas]);
        } catch (Exception $e) {
            error_log('CRM notas: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'erro' => 'Erro interno']);
        }
        break;

    /* ── adicionar_nota ─────────────────────────────────────── */
    case 'adicionar_nota':
        $leadId    = (int)($input['lead_id'] ?? 0);
        $nota      = trim($input['nota'] ?? '');
        $usuarioId = (int)(Auth::user()['id'] ?? 0);

        if (!$leadId || !$nota || !$usuarioId) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Dados inválidos']);
            exit;
        }

        try {
            $noteId = DB::insert(
                "INSERT INTO lead_notas (lead_id, usuario_id, nota) VALUES (?, ?, ?)",
                [$leadId, $usuarioId, $nota]
            );
            echo json_encode(['ok' => true, 'id' => $noteId]);
        } catch (Exception $e) {
            error_log('CRM adicionar_nota: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'erro' => 'Erro interno']);
        }
        break;

    default:
        http_response_code(422);
        echo json_encode(['ok' => false, 'erro' => 'Ação inválida']);
}
