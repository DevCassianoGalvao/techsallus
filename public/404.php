<?php
/* ─────────────────────────────────────────────────────────────
   public/404.php — Custom 404 page
   ───────────────────────────────────────────────────────────── */
$rootDir = dirname(__DIR__);
if (!file_exists($rootDir . '/core/Env.php')) {
    $rootDir = __DIR__;
}
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Security.php';
Env::load($rootDir . '/.env');
Security::headers();

http_response_code(404);

try {
    $recentes = DB::fetchAll(
        "SELECT titulo, slug FROM posts WHERE status='publicado' ORDER BY published_at DESC LIMIT 3"
    );
} catch (Exception $e) {
    $recentes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Página não encontrada | Techsallus</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css">
<style>
  .not-found {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 24px;
  }
  .not-found-code {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-size: 120px;
    font-weight: 800;
    color: #e2eaf3;
    line-height: 1;
    margin-bottom: 16px;
  }
  .not-found h1 {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-size: 28px;
    font-weight: 800;
    color: #0d1f35;
    margin-bottom: 12px;
  }
  .not-found p { color: #4a6080; font-size: 16px; margin-bottom: 32px; }
  .not-found-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
  .btn-home { background: #094a86; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; }
  .btn-home:hover { background: #073d70; }
  .btn-blog { background: transparent; color: #094a86; border: 1.5px solid #094a86; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; }
  .btn-blog:hover { background: #eff6ff; }
  .sugestoes { margin-top: 48px; }
  .sugestoes h2 { font-family: 'Bricolage Grotesque', sans-serif; font-size: 18px; font-weight: 700; color: #0d1f35; margin-bottom: 16px; }
  .sugestao-link { display: block; color: #094a86; text-decoration: none; font-size: 14px; margin-bottom: 8px; }
  .sugestao-link:hover { text-decoration: underline; }
</style>
</head>
<body>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<div class="not-found">
  <div>
    <div class="not-found-code">404</div>
    <h1>Página não encontrada</h1>
    <p>A página que você procura não existe ou foi removida.</p>
    <div class="not-found-actions">
      <a href="/" class="btn-home">Voltar ao início</a>
      <a href="/blog/" class="btn-blog">Ver o blog</a>
    </div>

    <?php if (!empty($recentes)): ?>
      <div class="sugestoes">
        <h2>Talvez você queira ler:</h2>
        <?php foreach ($recentes as $r): ?>
          <a href="/blog/<?= htmlspecialchars($r['slug']) ?>" class="sugestao-link">
            → <?= htmlspecialchars($r['titulo']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
