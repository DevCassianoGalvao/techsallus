<?php
/* ─────────────────────────────────────────────────────────────
   admin/_header.php — Shared admin header partial
   Guard: parent must define ADMIN_PAGE before including.
   Parent must have already: loaded Env, DB, Auth and called Auth::require().
   Parent sets: $pageTitle (string), $activeNav (string), $adminUser (array),
                $extraHead (optional string of extra <head> tags).
   ───────────────────────────────────────────────────────────── */
if (!defined('ADMIN_PAGE')) { http_response_code(403); exit; }

$pageTitle = $pageTitle ?? 'Admin';
$activeNav = $activeNav ?? '';
$adminUser = $adminUser ?? ['nome' => '', 'email' => ''];
$extraHead = $extraHead ?? '';

$initials = strtoupper(substr(trim($adminUser['nome']), 0, 1) ?: 'A');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle) ?> — TechSallus Admin</title>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/admin.css"/>
  <?= $extraHead ?>
</head>
<body class="admin-body">
<div class="admin-layout">

  <aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
      <img src="/assets/img/logo.png" alt="Techsallus"/>
    </div>

    <nav class="admin-nav">
      <a href="/admin/" class="admin-nav-link<?= $activeNav === 'dashboard' ? ' active' : '' ?>">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
      <a href="/admin/crm.php" class="admin-nav-link<?= $activeNav === 'crm' ? ' active' : '' ?>">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        CRM · Leads
      </a>
      <a href="/admin/blog-posts.php" class="admin-nav-link<?= $activeNav === 'blog' ? ' active' : '' ?>">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Blog · Artigos
      </a>
    </nav>

    <div class="admin-sidebar-footer">
      <div class="admin-user">
        <div class="admin-user-avatar"><?= $initials ?></div>
        <div class="admin-user-info">
          <span class="admin-user-name"><?= htmlspecialchars($adminUser['nome']) ?></span>
          <span class="admin-user-email"><?= htmlspecialchars($adminUser['email']) ?></span>
        </div>
      </div>
      <a href="/admin/logout.php" class="admin-logout-link">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sair
      </a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <h1 class="admin-page-title"><?= htmlspecialchars($pageTitle) ?></h1>
      <?= $topbarExtra ?? '' ?>
    </header>
    <div class="admin-content">
