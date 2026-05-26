<?php
/* ─────────────────────────────────────────────────────────────
   public/blog/preview.php — Prévia de artigo (somente admins)
   Exibe o artigo como ficará público + banner laranja de aviso.
   ───────────────────────────────────────────────────────────── */

$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
Env::load($rootDir . '/.env');

Auth::start();
if (!Auth::check()) {
    header('Location: /admin/login.php');
    exit;
}

$slug = trim($_GET['slug'] ?? '');
if (!$slug) {
    header('Location: /admin/blog-posts.php');
    exit;
}

$post = null;
try {
    $post = DB::fetchOne(
        "SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.slug = ? LIMIT 1",
        [$slug]
    );
} catch (Exception $e) { /* sem DB ainda */ }

if (!$post) {
    http_response_code(404);
    header('Location: /404.php');
    exit;
}

/* ── Meta ───────────────────────────────────────────────────── */
$titulo       = $post['titulo']     ?? '';
$conteudo     = $post['conteudo']   ?? '';
$resumo       = $post['resumo']     ?? '';
$imagemCapa   = $post['imagem_capa'] ?? '';
$metaTitle    = $post['meta_title'] ?: $titulo;
$metaDesc     = $post['meta_desc']  ?: mb_substr(strip_tags($resumo), 0, 160);
$tags         = $post['tags']       ? array_map('trim', explode(',', $post['tags'])) : [];
$catNome      = $post['categoria_nome'] ?? '';
$catSlug      = $post['categoria_slug'] ?? '';
$statusPost   = $post['status'] ?? 'rascunho';

/* ── Data ───────────────────────────────────────────────────── */
$meses = ['','janeiro','fevereiro','março','abril','maio','junho',
           'julho','agosto','setembro','outubro','novembro','dezembro'];
$dtRaw = $post['published_at'] ?? $post['criado_em'];
$dt    = $dtRaw ? new DateTime($dtRaw) : null;
$dataFormatada = $dt
    ? $dt->format('d') . ' de ' . $meses[(int)$dt->format('n')] . ' de ' . $dt->format('Y')
    : '—';

/* ── Tempo de leitura ───────────────────────────────────────── */
$readingTime = max(1, (int) round(str_word_count(strip_tags($conteudo)) / 200));

/* ── Índice H2 ──────────────────────────────────────────────── */
$headings = [];
preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $conteudo, $matches);
foreach ($matches[1] as $i => $h) {
    $headings[] = ['idx' => $i + 1, 'text' => strip_tags($h)];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($metaTitle) ?> — Prévia</title>
  <meta name="robots" content="noindex,nofollow"/>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/blog.css"/>
  <style>
    .preview-banner {
      background: #ff7300;
      color: #fff;
      text-align: center;
      padding: 10px 20px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }
    .preview-banner .preview-badge {
      background: rgba(255,255,255,0.25);
      border-radius: 20px;
      padding: 2px 10px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }
    .preview-banner a {
      color: #fff;
      text-decoration: underline;
      font-weight: 600;
    }
    .preview-banner a:hover { opacity: 0.85; }
    .preview-status-draft { opacity: 0.6; font-style: italic; }
  </style>
</head>
<body>

<!-- Banner de prévia -->
<div class="preview-banner">
  <span class="preview-badge">PRÉVIA</span>
  <span>
    Este artigo está em modo
    <strong><?= $statusPost === 'publicado' ? 'publicado' : 'rascunho' ?></strong>
    e pode não estar visível no blog público.
  </span>
  <a href="/admin/blog-editor.php?slug=<?= urlencode($slug) ?>">← Voltar ao editor</a>
</div>

<?php include dirname(__DIR__) . '/_partials/nav.php'; ?>

<main class="blog-article-page">
  <div class="blog-article-container">

    <?php if (!empty($headings)): ?>
    <aside class="blog-article-index">
      <div class="article-index-title">Neste artigo</div>
      <nav>
        <ul class="article-index-list">
          <?php foreach ($headings as $h): ?>
            <li><a href="#secao-<?= $h['idx'] ?>" class="article-index-link"><?= htmlspecialchars($h['text']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </aside>
    <?php endif; ?>

    <article class="blog-article-content">
      <!-- Header do artigo -->
      <header class="article-header">
        <?php if ($catNome): ?>
          <div class="article-cat-badge"><?= htmlspecialchars($catNome) ?></div>
        <?php endif; ?>
        <h1 class="article-title"><?= htmlspecialchars($titulo) ?></h1>
        <?php if ($resumo): ?>
          <p class="article-lead"><?= htmlspecialchars($resumo) ?></p>
        <?php endif; ?>
        <div class="article-meta-row">
          <span class="article-date"><?= $dataFormatada ?></span>
          <span class="article-dot">·</span>
          <span class="article-reading"><?= $readingTime ?> min de leitura</span>
        </div>
      </header>

      <?php if ($imagemCapa): ?>
        <div class="article-cover">
          <img src="<?= htmlspecialchars($imagemCapa) ?>" alt="<?= htmlspecialchars($titulo) ?>" loading="lazy"/>
        </div>
      <?php endif; ?>

      <!-- Corpo do artigo -->
      <div class="article-body">
        <?= $conteudo ?>
      </div>

      <!-- Tags -->
      <?php if (!empty($tags)): ?>
        <div class="article-tags">
          <?php foreach ($tags as $tag): ?>
            <span class="article-tag"><?= htmlspecialchars(trim($tag)) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- CTA -->
      <div class="article-cta-block">
        <div class="article-cta-title">Pronto para transformar a gestão da sua clínica?</div>
        <p class="article-cta-desc">Fale com nossos especialistas e descubra como o TechSallus pode ajudar.</p>
        <a href="/#demo" class="btn-article-cta">Agendar demonstração gratuita</a>
      </div>

    </article>
  </div>
</main>

<?php include dirname(__DIR__) . '/_partials/footer.php'; ?>

<script>
/* Adiciona IDs às seções H2 para o índice */
(function () {
  var h2s = document.querySelectorAll('.article-body h2');
  h2s.forEach(function (h, i) { h.id = 'secao-' + (i + 1); });
})();
</script>
</body>
</html>
