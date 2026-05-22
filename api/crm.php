<?php
/* ─────────────────────────────────────────────────────────────
   GET  /api/crm.php        — Lista leads (admin autenticado)
   PATCH /api/crm.php?id=N  — Atualiza status de um lead
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../core/Auth.php';
if (!Auth::check()) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }

require_once __DIR__ . '/../core/DB.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // TODO: SELECT * FROM leads ORDER BY criado_em DESC LIMIT 100
    echo json_encode(['leads' => [], 'total' => 0]);
    exit;
}

if ($method === 'PATCH') {
    $id   = (int)($_GET['id'] ?? 0);
    $body = json_decode(file_get_contents('php://input'), true);
    // TODO: UPDATE leads SET status = ?, atualizado_em = NOW() WHERE id = ?
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
