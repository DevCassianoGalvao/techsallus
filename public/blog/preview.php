<?php
$rootDir = dirname(dirname(__DIR__));
if (!file_exists($rootDir . '/core/Env.php')) {
    $rootDir = dirname(__DIR__);
}

require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
require_once $rootDir . '/core/Security.php';

Env::load($rootDir . '/.env');
Security::headers();
Auth::start();

if (!Auth::check()) {
    header('Location: ' . Security::url('/admin/login.php'));
    exit;
}

$slug = trim($_GET['slug'] ?? '');
if (!$slug) {
    header('Location: ' . Security::url('/admin/blog-posts.php'));
    exit;
}

try {
    $post = DB::fetchOne(
        "SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.slug = ? LIMIT 1",
        [$slug]
    );
} catch (Exception $e) {
    $post = null;
}

if (!$post) {
    http_response_code(404);
    header('Location: ' . Security::url('/404.php'));
    exit;
}

$titulo = $post['titulo'] ?? '';
$conteudo = $post['conteudo'] ?? '';
$resumo = $post['resumo'] ?? '';
$imagemCapa = $post['imagem_capa'] ?? '';
$metaTitle = $post['meta_title'] ?: $titulo;
$metaDesc = $post['meta_desc'] ?: substr(strip_tags($resumo), 0, 160);
$tags = $post['tags'] ? array_map('trim', explode(',', $post['tags'])) : [];
$catNome = $post['categoria_nome'] ?? '';
$statusPost = $post['status'] ?? 'rascunho';

$meses = ['', 'janeiro', 'fevereiro', 'marco', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
$dtRaw = $post['published_at'] ?? $post['criado_em'];
$dt = $dtRaw ? new DateTime($dtRaw) : null;
$dataFormatada = $dt ? $dt->format('d') . ' de ' . $meses[(int)$dt->format('n')] . ' de ' . $dt->format('Y') : '-';
$readingTime = max(1, (int) round(str_word_count(strip_tags($conteudo)) / 200));

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
  <title><?= htmlspecialchars($metaTitle) ?> - Prévia</title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>"/>
  <meta name="robots" content="noindex,nofollow"/>
  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
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
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 2000;
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
    .preview-page .nav { top: 38px; }
    .preview-page .article-hero { padding-top: 158px; }
    .article-preview-lead {
      max-width: 920px;
      margin: -6px 0 20px;
      font-family: 'DM Sans', sans-serif;
      font-size: 18px;
      line-height: 1.65;
      color: #4a6080;
    }
    .article-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 36px;
    }
    .article-tag {
      font-family: 'DM Sans', sans-serif;
      font-size: 12px;
      color: #094a86;
      background: #e8f0f9;
      border-radius: 999px;
      padding: 6px 10px;
    }
    @media (max-width: 768px) {
      .preview-banner { align-items: flex-start; gap: 8px; }
      .preview-page .nav { top: 58px; }
      .preview-page .article-hero { padding-top: 178px; }
    }
  </style>
</head>
<body class="preview-page">

<div class="preview-banner">
  <span class="preview-badge">Prévia</span>
  <span>Este artigo está em modo <strong><?= $statusPost === 'publicado' ? 'publicado' : 'rascunho' ?></strong> e pode não estar visível no blog público.</span>
  <a href="/admin/blog-editor.php?slug=<?= urlencode($slug) ?>">Voltar ao editor</a>
</div>

<?php include dirname(__DIR__) . '/_partials/nav.php'; ?>

<main>
  <section class="article-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Navegação estrutural">
        <a href="/">Início</a>
        <span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>
        <a href="/blog/">Blog</a>
        <?php if ($catNome): ?>
          <span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>
          <span><?= htmlspecialchars($catNome) ?></span>
        <?php endif; ?>
      </nav>
      <?php if ($catNome): ?>
        <span class="article-cat-badge"><?= htmlspecialchars($catNome) ?></span>
      <?php endif; ?>
      <h1><?= htmlspecialchars($titulo) ?></h1>
      <?php if ($resumo): ?>
        <p class="article-preview-lead"><?= htmlspecialchars($resumo) ?></p>
      <?php endif; ?>
      <div class="article-hero-meta">
        <span>Equipe TechSallus</span>
        <span class="article-hero-meta-dot" aria-hidden="true"></span>
        <span><?= htmlspecialchars($dataFormatada) ?></span>
        <span class="article-hero-meta-dot" aria-hidden="true"></span>
        <span><?= $readingTime ?> min de leitura</span>
      </div>
    </div>
  </section>

  <div class="article-body-wrap">
    <div class="container">
      <?php if ($imagemCapa): ?>
        <div style="width:100%;border-radius:12px;margin-bottom:48px;overflow:hidden;">
          <img src="<?= htmlspecialchars($imagemCapa) ?>"
               alt="<?= htmlspecialchars($titulo) ?>"
               width="1200" height="500" loading="eager"
               style="width:100%;height:360px;object-fit:cover;display:block;">
        </div>
      <?php endif; ?>

      <div class="article-layout">
        <div class="article-content">
          <div class="article-content-body">
            <?= $conteudo ?>
          </div>

          <?php if (!empty($tags)): ?>
            <div class="article-tags">
              <?php foreach ($tags as $tag): ?>
                <span class="article-tag"><?= htmlspecialchars($tag) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <aside class="article-sidebar">
          <?php if (!empty($headings)): ?>
            <div class="sidebar-toc">
              <div class="sidebar-toc-title">Neste artigo</div>
              <ol>
                <?php foreach ($headings as $h): ?>
                  <li><a href="#secao-<?= $h['idx'] ?>"><?= htmlspecialchars($h['text']) ?></a></li>
                <?php endforeach; ?>
              </ol>
            </div>
          <?php endif; ?>

          <div class="sidebar-cta-card">
            <h3>Conheça o TechSallus</h3>
            <p>Veja como o sistema resolve na prática os desafios que você acabou de ler.</p>
            <a href="/#demo" class="btn-sidebar-cta sidebar-cta-btn">Agendar demonstração</a>
          </div>
        </aside>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/_partials/footer.php'; ?>

<script>
(function () {
  var h2s = document.querySelectorAll('.article-content-body h2');
  h2s.forEach(function (h, i) { h.id = 'secao-' + (i + 1); });
})();
</script>
</body>
</html>
