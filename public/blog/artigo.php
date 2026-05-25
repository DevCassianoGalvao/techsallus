<?php
/* ─────────────────────────────────────────────────────────────
   public/blog/artigo.php — Artigo individual
   Roteado via .htaccess: /blog/meu-slug → ?slug=meu-slug
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
Env::load($rootDir . '/.env');

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: /blog/');
    exit;
}

/* ── Artigo ──────────────────────────────────────────────────── */
try {
    $post = DB::fetchOne(
        "SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.slug = ? AND p.status = 'publicado'
         LIMIT 1",
        [$slug]
    );
} catch (Exception $e) {
    $post = null;
}

if (!$post) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit;
}

/* ── Artigos relacionados ────────────────────────────────────── */
try {
    $relacionados = DB::fetchAll(
        "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.published_at, c.nome AS categoria_nome
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.status = 'publicado' AND p.id != ? AND p.categoria_id = ?
         ORDER BY p.published_at DESC LIMIT 3",
        [$post['id'], $post['categoria_id']]
    );

    if (count($relacionados) < 3) {
        $idsExcluir   = array_merge([$post['id']], array_column($relacionados, 'id'));
        $placeholders = implode(',', array_fill(0, count($idsExcluir), '?'));
        $extras = DB::fetchAll(
            "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.published_at, c.nome AS categoria_nome
             FROM posts p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.status = 'publicado' AND p.id NOT IN ({$placeholders})
             ORDER BY p.published_at DESC
             LIMIT " . (3 - count($relacionados)),
            $idsExcluir
        );
        $relacionados = array_merge($relacionados, $extras);
    }
} catch (Exception $e) {
    $relacionados = [];
}

/* ── Índice do artigo (H2s) ──────────────────────────────────── */
preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $post['conteudo'] ?? '', $matches);
$indice = $matches[1] ?? [];

/* ── Schema JSON-LD ──────────────────────────────────────────── */
$schema = json_encode([
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $post['titulo'],
    'description'   => $post['resumo'],
    'datePublished' => $post['published_at'],
    'dateModified'  => $post['atualizado_em'],
    'author'        => ['@type' => 'Organization', 'name' => 'Techsallus'],
    'publisher'     => [
        '@type' => 'Organization',
        'name'  => 'Techsallus',
        'logo'  => ['@type' => 'ImageObject', 'url' => 'https://techsallus.com.br/assets/img/logo.svg'],
    ],
    'image' => $post['imagem_capa'] ? 'https://techsallus.com.br' . $post['imagem_capa'] : null,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

/* ── Scripts injetados ───────────────────────────────────────── */
function getScripts(string $pos): string {
    try {
        return implode("\n", array_column(
            DB::fetchAll("SELECT conteudo FROM scripts_injecao WHERE posicao=? AND ativo=1", [$pos]),
            'conteudo'
        ));
    } catch (Exception $e) { return ''; }
}

/* ── Tempo de leitura ────────────────────────────────────────── */
$leitura = max(1, (int)(str_word_count(strip_tags($post['conteudo'])) / 200));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($post['meta_title'] ?: $post['titulo'] . ' | Techsallus') ?></title>
<meta name="description" content="<?= htmlspecialchars($post['meta_desc'] ?: $post['resumo']) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($post['titulo']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($post['resumo']) ?>">
<meta property="og:type"        content="article">
<?php if ($post['imagem_capa']): ?>
<meta property="og:image" content="https://techsallus.com.br<?= htmlspecialchars($post['imagem_capa']) ?>">
<?php endif; ?>
<link rel="canonical" href="https://techsallus.com.br/blog/<?= htmlspecialchars($post['slug']) ?>">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<script type="application/ld+json"><?= $schema ?></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/blog.css">
<?= getScripts('head') ?>
</head>
<body>

<?php include __DIR__ . '/../_partials/nav.php'; ?>

<!-- Header do artigo -->
<header class="artigo-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Navegação estrutural">
      <a href="/">Início</a>
      <span aria-hidden="true">›</span>
      <a href="/blog/">Blog</a>
      <?php if ($post['categoria_nome']): ?>
        <span aria-hidden="true">›</span>
        <a href="/blog/?categoria=<?= urlencode($post['categoria_slug']) ?>"><?= htmlspecialchars($post['categoria_nome']) ?></a>
      <?php endif; ?>
    </nav>

    <?php if ($post['categoria_nome']): ?>
      <span class="artigo-cat-badge"><?= htmlspecialchars($post['categoria_nome']) ?></span>
    <?php endif; ?>

    <h1><?= htmlspecialchars($post['titulo']) ?></h1>

    <div class="artigo-meta">
      <?php if ($post['published_at']): ?>
        <span>📅 <?= date('d \d\e F \d\e Y', strtotime($post['published_at'])) ?></span>
      <?php endif; ?>
      <span>⏱ <?= $leitura ?> min de leitura</span>
    </div>
  </div>
</header>

<!-- Imagem de capa -->
<?php if ($post['imagem_capa']): ?>
  <div class="artigo-capa">
    <div class="container">
      <img src="<?= htmlspecialchars($post['imagem_capa']) ?>"
           alt="<?= htmlspecialchars($post['titulo']) ?>"
           width="1200" height="500" loading="eager">
    </div>
  </div>
<?php endif; ?>

<!-- Conteúdo + Sidebar -->
<section class="artigo-body">
  <div class="container">
    <div class="artigo-layout">

      <article class="artigo-content">
        <?= $post['conteudo'] ?>
      </article>

      <aside class="artigo-sidebar">
        <?php if (!empty($indice)): ?>
          <div class="sidebar-card">
            <h3 class="sidebar-card-title">Neste artigo</h3>
            <ul class="artigo-indice">
              <?php foreach ($indice as $i => $titulo): ?>
                <li><a href="#secao-<?= $i ?>"><?= strip_tags($titulo) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="sidebar-cta">
          <div class="sidebar-cta-icon">🏥</div>
          <h3>Quer ver o TechSallus na prática?</h3>
          <p>Agende uma demonstração gratuita em 30 minutos.</p>
          <a href="/#demo" class="sidebar-cta-btn">Agendar demonstração</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- Artigos relacionados -->
<?php if (!empty($relacionados)): ?>
  <section class="artigos-relacionados">
    <div class="container">
      <h2>Artigos relacionados</h2>
      <div class="blog-grid blog-grid-3">
        <?php foreach ($relacionados as $rel): ?>
          <article class="blog-card">
            <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>" class="blog-card-img-link">
              <?php if ($rel['imagem_capa']): ?>
                <img src="<?= htmlspecialchars($rel['imagem_capa']) ?>" alt="<?= htmlspecialchars($rel['titulo']) ?>" loading="lazy" width="400" height="200">
              <?php else: ?>
                <div class="blog-card-img-placeholder"></div>
              <?php endif; ?>
              <?php if ($rel['categoria_nome']): ?>
                <span class="blog-card-cat"><?= htmlspecialchars($rel['categoria_nome']) ?></span>
              <?php endif; ?>
            </a>
            <div class="blog-card-body">
              <h3 class="blog-card-title">
                <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>"><?= htmlspecialchars($rel['titulo']) ?></a>
              </h3>
              <div class="blog-card-footer">
                <span class="blog-card-date"><?= $rel['published_at'] ? date('d/m/Y', strtotime($rel['published_at'])) : '' ?></span>
                <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>" class="blog-card-link">Ler →</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
<?= getScripts('body') ?>
<?= getScripts('footer') ?>

<script src="/assets/js/main.js"></script>
<script>
/* Adicionar IDs nos H2s para o índice */
document.querySelectorAll('.artigo-content h2').forEach(function(el, i) {
  el.id = 'secao-' + i;
});

/* Highlight do índice conforme scroll */
var links  = document.querySelectorAll('.artigo-indice a');
var secoes = document.querySelectorAll('.artigo-content h2');

if (links.length && secoes.length) {
  var obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        links.forEach(function(l) { l.classList.remove('active'); });
        var ativo = document.querySelector('.artigo-indice a[href="#' + entry.target.id + '"]');
        if (ativo) ativo.classList.add('active');
      }
    });
  }, { rootMargin: '-20% 0px -70% 0px' });
  secoes.forEach(function(s) { obs.observe(s); });
}
</script>
</body>
</html>
