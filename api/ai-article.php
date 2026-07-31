<?php
/* ─────────────────────────────────────────────────────────────
   api/ai-article.php — Gera artigo via OpenAI GPT-4o mini
   POST: { tema, tom, pontos, categoria }
   Retorna JSON: { titulo, slug, resumo, conteudo, meta_title, meta_desc }
   ───────────────────────────────────────────────────────────── */
require_once __DIR__ . '/../core/Env.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Security.php';
Env::load(__DIR__ . '/../.env');
Security::headers();

/* Só admin autenticado */
Auth::start();
if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

Security::requireCsrfJson($_POST);

$apiKey = Env::get('OPENAI_API_KEY');
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'OPENAI_API_KEY não configurada no .env']);
    exit;
}

$tema      = trim($_POST['tema']      ?? '');
$tom       = trim($_POST['tom']       ?? 'profissional e didático');
$pontos    = trim($_POST['pontos']    ?? '');
$categoria = trim($_POST['categoria'] ?? 'Gestão em Saúde');

if (!$tema) {
    http_response_code(400);
    echo json_encode(['error' => 'Informe o tema do artigo']);
    exit;
}

/* ── Prompt ─────────────────────────────────────────────────── */
$pontosLine = $pontos ? "\nPontos obrigatórios a cobrir:\n{$pontos}" : '';

$systemPrompt = <<<PROMPT
Você é um especialista em tecnologia para saúde, gestão hospitalar e sistemas de prontuário eletrônico (HIS/PEP).
Escreva artigos de blog completos, precisos e otimizados para SEO, voltados para profissionais de saúde (diretores, médicos, gestores de clínicas e hospitais).
Sempre use linguagem {$tom}.
PROMPT;

$userPrompt = <<<PROMPT
Escreva um artigo de blog completo sobre o tema abaixo para o blog da TechSallus, empresa de software de gestão para saúde.

Tema: {$tema}
Categoria: {$categoria}{$pontosLine}

REGRAS OBRIGATÓRIAS:
1. Retorne EXCLUSIVAMENTE um objeto JSON válido, sem markdown, sem texto antes ou depois.
2. O JSON deve ter exatamente estas chaves:
   - "titulo": string (título atraente, máx 90 chars)
   - "slug": string (slug URL amigável, só minúsculas, hífens, sem acentos)
   - "resumo": string (lead de 2-3 frases, máx 320 chars, sem spoilers do conteúdo)
   - "conteudo": string (HTML completo do artigo, use <h2>, <p>, <ul>/<li>, <strong>; mínimo 800 palavras; NÃO inclua <h1> nem o título)
   - "meta_title": string (título SEO, máx 60 chars)
   - "meta_desc": string (descrição SEO, máx 155 chars)
3. O conteúdo deve ter pelo menos 4 seções com <h2>.
4. Mencione brevemente a TechSallus como solução em pelo menos uma seção (de forma natural, não forçada).
5. Linguagem: português brasileiro.
PROMPT;

/* ── Chamada OpenAI ─────────────────────────────────────────── */
$payload = json_encode([
    'model'       => 'gpt-4o-mini',
    'temperature' => 0.7,
    'max_tokens'  => 3000,
    'messages'    => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user',   'content' => $userPrompt],
    ],
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 90,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com OpenAI: ' . $curlErr]);
    exit;
}

$data = json_decode($response, true);

if ($httpCode !== 200 || empty($data['choices'][0]['message']['content'])) {
    $msg = $data['error']['message'] ?? 'Erro desconhecido da API';
    http_response_code(502);
    echo json_encode(['error' => 'OpenAI retornou erro: ' . $msg]);
    exit;
}

$raw = trim($data['choices'][0]['message']['content']);

/* Remove possível markdown fence ```json ... ``` */
$raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
$raw = preg_replace('/\s*```$/', '', $raw);

$article = json_decode($raw, true);

if (!$article || !isset($article['titulo'], $article['conteudo'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Resposta da IA em formato inesperado. Tente novamente.', 'raw' => $raw]);
    exit;
}

/* Sanitiza slug */
if (empty($article['slug'])) {
    $article['slug'] = preg_replace('/[^a-z0-9]+/', '-', strtolower($article['titulo']));
    $article['slug'] = trim($article['slug'], '-');
}

echo json_encode($article, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
