<?php
/* ─────────────────────────────────────────────────────────────
   POST /api/leads.php — Recebe lead do formulário e salva no DB
   ───────────────────────────────────────────────────────────── */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? ''));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method Not Allowed']); exit; }

require_once __DIR__ . '/../core/DB.php';

$body = json_decode(file_get_contents('php://input'), true);

$nome        = trim($body['nome']        ?? '');
$instituicao = trim($body['instituicao'] ?? '');
$cargo       = trim($body['cargo']       ?? '');
$email       = trim($body['email']       ?? '');
$whatsapp    = trim($body['whatsapp']    ?? '');
$prof        = trim($body['prof']        ?? '');

$errors = [];
if (!$nome)                         $errors[] = 'nome obrigatório';
if (!$instituicao)                  $errors[] = 'instituição obrigatória';
if (!$cargo)                        $errors[] = 'cargo obrigatório';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'e-mail inválido';
if (!$whatsapp)                     $errors[] = 'whatsapp obrigatório';
if (!$prof)                         $errors[] = 'profissionais obrigatório';

if ($errors) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// TODO: inserir no banco de dados
// $db  = DB::get();
// $sql = 'INSERT INTO leads (nome, instituicao, cargo, email, whatsapp, profissionais, criado_em) VALUES (?,?,?,?,?,?,NOW())';
// $db->prepare($sql)->execute([$nome, $instituicao, $cargo, $email, $whatsapp, $prof]);

http_response_code(201);
echo json_encode(['ok' => true, 'message' => 'Lead recebido com sucesso.']);
