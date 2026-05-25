<?php
/* ─────────────────────────────────────────────────────────────
   public/blog/index.php — Blog público — listagem com paginação
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
Env::load($rootDir . '/.env');

/* ── Parâmetros ──────────────────────────────────────────────── */
$categoriaSlug = trim($_GET['categoria'] ?? '');
$busca         = trim($_GET['busca']     ?? '');
$pagina        = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina     = 6;
$offset        = ($pagina - 1) * $porPagina;

/* ── Query dinâmica ──────────────────────────────────────────── */
$where  = ["p.status = 'publicado'"];
$params = [];

if ($categoriaSlug) {
    $where[]  = "c.slug = ?";
    $params[] = $categoriaSlug;
}
if ($busca) {
    $where[]  = "(p.titulo LIKE ? OR p.resumo LIKE ? OR p.tags LIKE ?)";
    $termo    = "%{$busca}%";
    $params   = array_merge($params, [$termo, $termo, $termo]);
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

/* ── Dados do banco ──────────────────────────────────────────── */
try {
    $total = (int)(DB::fetchOne(
        "SELECT COUNT(*) AS total FROM posts p LEFT JOIN categorias c ON c.id = p.categoria_id {$whereSQL}",
        $params
    )['total'] ?? 0);

    $totalPaginas = (int)ceil($total / $porPagina);

    $posts = DB::fetchAll(
        "SELECT p.id, p.titulo, p.slug, p.resumo, p.imagem_capa, p.tags, p.published_at,
                c.nome AS categoria_nome, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         {$whereSQL}
         ORDER BY p.published_at DESC
         LIMIT {$porPagina} OFFSET {$offset}",
        $params
    );

    $categorias = DB::fetchAll(
        "SELECT c.id, c.nome, c.slug, COUNT(p.id) AS total
         FROM categorias c
         LEFT JOIN posts p ON p.categoria_id = c.id AND p.status = 'publicado'
         GROUP BY c.id
         HAVING total > 0
         ORDER BY c.nome"
    );
} catch (Exception $e) {
    $posts = $categorias = [];
    $total = $totalPaginas = 0;
}

/* ── SEO ─────────────────────────────────────────────────────── */
$metaTitle = $categoriaSlug
    ? "Blog sobre " . ucfirst($categoriaSlug) . " | Techsallus"
    : "Blog | Gestão hospitalar e tecnologia para saúde | Techsallus";
$metaDesc  = "Conteúdo prático sobre gestão e tecnologia para saúde. Tudo que você precisa saber para gerir melhor sua clínica ou hospital.";

/* ── Scripts injetados ───────────────────────────────────────── */
function getScripts(string $pos): string {
    try {
        return implode("\n", array_column(
            DB::fetchAll("SELECT conteudo FROM scripts_injecao WHERE posicao=? AND ativo=1", [$pos]),
            'conteudo'
        ));
    } catch (Exception $e) { return ''; }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($metaTitle) ?>">
<meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta property="og:type"        content="website">
<link rel="canonical" href="https://techsallus.com.br/blog/">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/blog.css">
<?= getScripts('head') ?>
</head>
<body>

<?php include __DIR__ . '/../_partials/nav.php'; ?>

<!-- Hero do blog -->
<section class="blog-hero">
  <div class="container">
    <span class="section-label">Blog Techsallus</span>
    <h1>Conteúdo prático sobre gestão e tecnologia para saúde</h1>
    <p>Tudo que você precisa saber para gerir melhor sua clínica ou hospital — com exemplos reais, linguagem acessível e foco na prática.</p>

    <form class="blog-search" method="GET" action="/blog/">
      <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar artigos...">
      <button type="submit" aria-label="Buscar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </form>
  </div>
</section>

<!-- Filtros de categoria -->
<div class="blog-filters">
  <div class="container">
    <div class="filter-pills">
      <a href="/blog/" class="pill-filter <?= !$categoriaSlug ? 'active' : '' ?>">Todos</a>
      <?php foreach ($categorias as $cat): ?>
        <a href="/blog/?categoria=<?= urlencode($cat['slug']) ?>"
           class="pill-filter <?= $categoriaSlug === $cat['slug'] ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['nome']) ?>
          <span class="pill-count"><?= $cat['total'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Grid de artigos -->
<section class="blog-grid-section">
  <div class="container">

    <?php if ($busca): ?>
      <p class="search-result-info">
        <?= $total ?> resultado<?= $total != 1 ? 's' : '' ?> para
        "<strong><?= htmlspecialchars($busca) ?></strong>"
        <a href="/blog/" style="margin-left:8px;color:#ff7300;font-size:13px">Limpar busca</a>
      </p>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
      <div class="blog-empty">
        <div style="font-size:48px;margin-bottom:16px">📭</div>
        <h2>Nenhum artigo encontrado</h2>
        <p>Tente outros termos ou <a href="/blog/">veja todos os artigos</a>.</p>
      </div>
    <?php else: ?>

      <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
          <article class="blog-card">
            <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="blog-card-img-link">
              <?php if ($post['imagem_capa']): ?>
                <img src="<?= htmlspecialchars($post['imagem_capa']) ?>"
                     alt="<?= htmlspecialchars($post['titulo']) ?>"
                     loading="lazy" width="800" height="400">
              <?php else: ?>
                <div class="blog-card-img-placeholder">
                  <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c8daf0" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                </div>
              <?php endif; ?>
              <?php if ($post['categoria_nome']): ?>
                <span class="blog-card-cat"><?= htmlspecialchars($post['categoria_nome']) ?></span>
              <?php endif; ?>
            </a>
            <div class="blog-card-body">
              <h2 class="blog-card-title">
                <a href="/blog/<?= htmlspecialchars($post['slug']) ?>">
                  <?= htmlspecialchars($post['titulo']) ?>
                </a>
              </h2>
              <?php if ($post['resumo']): ?>
                <p class="blog-card-resumo"><?= htmlspecialchars($post['resumo']) ?></p>
              <?php endif; ?>
              <div class="blog-card-footer">
                <span class="blog-card-date">
                  <?= $post['published_at'] ? date('d/m/Y', strtotime($post['published_at'])) : '' ?>
                </span>
                <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="blog-card-link">
                  Ler artigo →
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Paginação -->
      <?php if ($totalPaginas > 1): ?>
        <nav class="blog-pagination" aria-label="Paginação">
          <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?><?= $categoriaSlug ? '&categoria='.$categoriaSlug : '' ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>" class="page-btn">← Anterior</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?><?= $categoriaSlug ? '&categoria='.$categoriaSlug : '' ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>"
               class="page-btn <?= $i === $pagina ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($pagina < $totalPaginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?><?= $categoriaSlug ? '&categoria='.$categoriaSlug : '' ?><?= $busca ? '&busca='.urlencode($busca) : '' ?>" class="page-btn">Próxima →</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../_partials/footer.php'; ?>
<?= getScripts('body') ?>
<?= getScripts('footer') ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
