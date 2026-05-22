<?php
/* ─────────────────────────────────────────────────────────────
   POST /api/ia-gerar.php — Gera rascunho de artigo via OpenAI
   Requer autenticação de admin.
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../core/Auth.php';
if (!Auth::check()) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once __DIR__ . '/../core/OpenAI.php';

$body  = json_decode(file_get_contents('php://input'), true);
$topic = trim($body['topic'] ?? '');
$cat   = trim($body['cat']   ?? 'Gestão');

if (!$topic) {
    http_response_code(422);
    echo json_encode(['error' => 'Informe o tema (topic) do artigo.']);
    exit;
}

$openai = new OpenAI();
$draft  = $openai->generateBlogDraft($topic, $cat);

if (!$draft) {
    http_response_code(503);
    echo json_encode(['error' => 'Geração indisponível. Verifique a chave de API OpenAI em config/config.php.']);
    exit;
}

echo json_encode(['ok' => true, 'draft' => $draft]);
