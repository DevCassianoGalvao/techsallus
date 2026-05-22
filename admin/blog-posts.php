<?php
require_once __DIR__ . '/../core/Auth.php';
Auth::requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blog · Artigos — TechSallus Admin</title>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/admin.css"/>
</head>
<body class="admin-body">

<aside class="admin-sidebar">
  <div class="admin-sidebar-logo"><img src="/assets/img/logo.png" alt="Techsallus"/></div>
  <nav class="admin-nav">
    <a href="/admin/" class="admin-nav-link">Dashboard</a>
    <a href="/admin/crm.php" class="admin-nav-link">CRM · Leads</a>
    <a href="/admin/blog-posts.php" class="admin-nav-link active">Blog · Artigos</a>
    <a href="/admin/blog-editor.php" class="admin-nav-link">Novo Artigo</a>
  </nav>
  <div class="admin-sidebar-footer">
    <a href="/admin/login.php?logout=1" class="admin-logout-link">Sair</a>
  </div>
</aside>

<main class="admin-main">
  <header class="admin-topbar">
    <h1 class="admin-page-title">Blog · Artigos</h1>
    <a href="/admin/blog-editor.php" class="btn-orange" style="font-size:14px;height:40px;padding:0 20px">+ Novo artigo</a>
  </header>
  <div class="admin-content">
    <div class="admin-placeholder-card">
      <p>Gerenciamento de artigos do blog.</p>
      <p>Integração com banco de dados e geração por IA pendentes de implementação.</p>
    </div>
  </div>
</main>

</body>
</html>
