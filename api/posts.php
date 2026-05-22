<?php
/* ─────────────────────────────────────────────────────────────
   GET    /api/posts.php          — Lista posts publicados
   POST   /api/posts.php          — Cria post (admin)
   PUT    /api/posts.php?slug=X   — Atualiza post (admin)
   DELETE /api/posts.php?slug=X   — Remove post (admin)
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../core/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // TODO: SELECT slug, title, cat, author, date, excerpt FROM posts WHERE published = 1 ORDER BY created_at DESC
    echo json_encode(['posts' => []]);
    exit;
}

Auth::requireAuth();

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    // TODO: inserir post no banco
    http_response_code(201);
    echo json_encode(['ok' => true]);
    exit;
}

if ($method === 'PUT') {
    $slug = $_GET['slug'] ?? '';
    $body = json_decode(file_get_contents('php://input'), true);
    // TODO: atualizar post por slug
    echo json_encode(['ok' => true]);
    exit;
}

if ($method === 'DELETE') {
    $slug = $_GET['slug'] ?? '';
    // TODO: marcar como deletado (soft delete)
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
