<?php
/* ─────────────────────────────────────────────────────────────
   admin/_header.php — Shared admin header partial
   Guard: parent must define ADMIN_PAGE before including.
   Parent must have already: loaded Env, DB, Auth and called Auth::require().
   Parent sets: $pageTitle (string), $activeNav (string), $adminUser (array),
                $extraHead (optional string of extra <head> tags).
   ───────────────────────────────────────────────────────────── */
if (!defined('ADMIN_PAGE')) { http_response_code(403); exit; }
if (class_exists('Security')) {
    Security::headers();
}

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
  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/admin.css?v=20260731c"/>
  <meta name="csrf-token" content="<?= htmlspecialchars(Security::csrfToken()) ?>">
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
        Kanban
      </a>
      <a href="/admin/contatos.php" class="admin-nav-link<?= $activeNav === 'contatos' ? ' active' : '' ?>">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Contatos
      </a>
      <a href="/admin/configuracoes.php" class="admin-nav-link<?= $activeNav === 'configuracoes' ? ' active' : '' ?>">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82V22a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 20.4a1.65 1.65 0 0 0-1.82-.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33H2a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 3.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 6.04 4.3l.06.06A1.65 1.65 0 0 0 9 3.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .33-1.82V2a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15 3.6a1.65 1.65 0 0 0 1.82.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 20.4 9c0 .38.13.74.36 1.03.23.3.56.52.94.64H22a2 2 0 1 1 0 4h-.09A1.65 1.65 0 0 0 20.4 15z"/></svg>
        Configurações
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
