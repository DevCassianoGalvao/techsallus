<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../core/Env.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Security.php';
Env::load(__DIR__ . '/../.env');
Security::headers();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $posts = DB::fetchAll(
            "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.published_at,
                    c.nome AS categoria_nome
             FROM posts p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.status = 'publicado'
             ORDER BY p.published_at DESC"
        );
        echo json_encode(['ok' => true, 'posts' => $posts]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'erro' => 'Erro ao listar posts']);
    }
    exit;
}

Auth::start();
Auth::require();

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido']);
    exit;
}

Security::requireCsrfJson($_POST);

function sanitizePostContent(string $html): string
{
    $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|textarea|select|link|meta)[^>]*>.*?</\1>#is', '', $html);
    $html = preg_replace('#</?(script|style|iframe|object|embed|form|input|button|textarea|select|link|meta)[^>]*>#is', '', $html);
    $html = preg_replace('/\son[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html);
    $html = preg_replace('/\s(href|src)\s*=\s*([\'"])\s*javascript:[^\'"]*\2/is', '', $html);
    $html = preg_replace('/\sstyle\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html);

    return $html ?? '';
}

try {
    $action = trim($_POST['action'] ?? '');

    if ($action === 'excluir') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Artigo invalido.']);
            exit;
        }

        $postAtual = DB::fetchOne("SELECT imagem_capa FROM posts WHERE id = ? LIMIT 1", [$id]);
        if (!$postAtual) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'erro' => 'Artigo nao encontrado.']);
            exit;
        }

        DB::query("DELETE FROM posts WHERE id = ?", [$id]);

        $imagem = $postAtual['imagem_capa'] ?? '';
        if ($imagem && str_starts_with($imagem, '/assets/uploads/posts/')) {
            $path = $_SERVER['DOCUMENT_ROOT'] . $imagem;
            if (is_file($path)) {
                @unlink($path);
            }
        }

        echo json_encode(['ok' => true]);
        exit;
    }

    $id        = (int)($_POST['id'] ?? 0);
    $titulo    = trim($_POST['titulo'] ?? '');
    $slug      = trim($_POST['slug'] ?? '');
    $catId     = (int)($_POST['categoria_id'] ?? 0) ?: null;
    $novaCategoria = trim($_POST['nova_categoria'] ?? '');
    $tags      = trim($_POST['tags'] ?? '');
    $resumo    = trim($_POST['resumo'] ?? '');
    $conteudo  = trim($_POST['conteudo'] ?? '');
    $conteudo = sanitizePostContent($conteudo);
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDesc  = trim($_POST['meta_desc'] ?? '');
    $status    = in_array($_POST['status'] ?? '', ['rascunho', 'publicado'], true)
        ? $_POST['status']
        : 'rascunho';

    if (!$titulo || !$slug) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erro' => 'Título e slug são obrigatórios.']);
        exit;
    }

    if ($novaCategoria !== '') {
        $catSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $novaCategoria)), '-'));
        $existente = DB::fetchOne("SELECT id FROM categorias WHERE slug = ? LIMIT 1", [$catSlug]);
        if ($existente) {
            $catId = (int)$existente['id'];
        } else {
            $catId = (int)DB::insert(
                "INSERT INTO categorias (nome, slug) VALUES (?, ?)",
                [$novaCategoria, $catSlug]
            );
        }
    }

    // Manter imagem atual por padrão
    $imagemCapa = null;
    if ($id) {
        $postAtual = DB::fetchOne("SELECT imagem_capa FROM posts WHERE id = ?", [$id]);
        $imagemCapa = $postAtual['imagem_capa'] ?? null;
    }

    // Só processa upload se um arquivo foi enviado
    if (!empty($_FILES['imagem_capa']['tmp_name']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imagem_capa'];

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Imagem maior que 5MB']);
            exit;
        }

        $tmpPath = $file['tmp_name'];

        // Validar MIME real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Tipo de imagem não permitido']);
            exit;
        }

        // Criar pasta se não existir
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $nomeArquivo = uniqid('post_', true) . '.webp';
        $destPath = $uploadDir . $nomeArquivo;

        $imgOriginal = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($tmpPath),
            'image/png'  => imagecreatefrompng($tmpPath),
            'image/webp' => imagecreatefromwebp($tmpPath),
            default      => null,
        };

        if (!$imgOriginal) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'erro' => 'Não foi possível processar a imagem']);
            exit;
        }

        $origW = imagesx($imgOriginal);
        $origH = imagesy($imgOriginal);
        $maxW = 1200;

        if ($origW > $maxW) {
            $novaH = (int)($origH * $maxW / $origW);
            $imgRedim = imagecreatetruecolor($maxW, $novaH);
            imagecopyresampled($imgRedim, $imgOriginal, 0, 0, 0, 0, $maxW, $novaH, $origW, $origH);
            imagedestroy($imgOriginal);
            $imgOriginal = $imgRedim;
        }

        imagewebp($imgOriginal, $destPath, 82);
        imagedestroy($imgOriginal);
        $imagemCapa = '/assets/uploads/posts/' . $nomeArquivo;
    }

    if (!empty($_FILES['imagem_capa']['error']) && $_FILES['imagem_capa']['error'] !== UPLOAD_ERR_NO_FILE) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'erro' => 'Erro no upload da imagem']);
        exit;
    }

    if ($id) {
        $postAtual = DB::fetchOne("SELECT published_at FROM posts WHERE id = ?", [$id]);
        $pubAt = $postAtual['published_at'] ?? null;
        if ($status === 'publicado' && !$pubAt) {
            $pubAt = date('Y-m-d H:i:s');
        }

        DB::query(
            "UPDATE posts SET titulo=?, slug=?, categoria_id=?, tags=?, resumo=?, conteudo=?,
             imagem_capa=?, meta_title=?, meta_desc=?, status=?, published_at=?, atualizado_em=NOW()
             WHERE id=?",
            [$titulo, $slug, $catId, $tags, $resumo, $conteudo, $imagemCapa,
             $metaTitle, $metaDesc, $status, $pubAt, $id]
        );
    } else {
        $pubAt = $status === 'publicado' ? date('Y-m-d H:i:s') : null;
        $id = (int)DB::insert(
            "INSERT INTO posts (titulo, slug, categoria_id, tags, resumo, conteudo,
             imagem_capa, meta_title, meta_desc, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$titulo, $slug, $catId, $tags, $resumo, $conteudo, $imagemCapa,
             $metaTitle, $metaDesc, $status, $pubAt]
        );
    }

    echo json_encode(['ok' => true, 'id' => $id, 'imagem_capa' => $imagemCapa]);
} catch (Exception $e) {
    error_log('Erro ao salvar post: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok'   => false,
        'erro' => 'Erro ao salvar: ' . $e->getMessage()
    ]);
}
