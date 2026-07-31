<?php
/* ─────────────────────────────────────────────────────────────
   admin/index.php — Dashboard
   ───────────────────────────────────────────────────────────── */
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
require_once $rootDir . '/core/Schema.php';
Env::load($rootDir . '/.env');
Auth::require();

$adminUser = Auth::user();
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

/* ── Stats ─────────────────────────────────────────────────── */
$dbOk        = false;
$totalLeads  = 0;
$byStatus    = [];
$byPorte     = [];
$byCargo     = [];
$byCidade    = [];
$byEstado    = [];
$byOrigem    = [];
$byCampanha  = [];
$byUtmCombo  = [];
$recentLeads = [];
$statusMap   = [
    'novo'        => ['label' => 'Novo',        'n' => 0],
    'contato'     => ['label' => 'Contato',     'n' => 0],
    'proposta'    => ['label' => 'Proposta',     'n' => 0],
    'fechamento'  => ['label' => 'Fechamento',   'n' => 0],
];

try {
    Schema::ensureLeadTrackingColumns();

    $totalLeads = (int)(DB::fetchOne("SELECT COUNT(*) AS n FROM leads")['n'] ?? 0);

    $rows = DB::fetchAll("SELECT status, COUNT(*) AS n FROM leads GROUP BY status");
    foreach ($rows as $r) {
        if (isset($statusMap[$r['status']])) {
            $statusMap[$r['status']]['n'] = (int)$r['n'];
        }
    }

    $byPorte = DB::fetchAll(
        "SELECT porte, COUNT(*) AS n FROM leads GROUP BY porte ORDER BY n DESC LIMIT 8"
    );

    $byCargo = DB::fetchAll(
        "SELECT cargo, COUNT(*) AS n FROM leads WHERE cargo IS NOT NULL AND cargo != '' GROUP BY cargo ORDER BY n DESC LIMIT 8"
    );

    $byCidade = DB::fetchAll(
        "SELECT cidade, estado, COUNT(*) AS n FROM leads WHERE cidade IS NOT NULL AND cidade != '' GROUP BY cidade, estado ORDER BY n DESC LIMIT 8"
    );

    $byEstado = DB::fetchAll(
        "SELECT estado, COUNT(*) AS n FROM leads WHERE estado IS NOT NULL AND estado != '' GROUP BY estado ORDER BY n DESC LIMIT 8"
    );

    $byOrigem = DB::fetchAll(
        "SELECT COALESCE(NULLIF(utm_source, ''), 'Sem UTM') AS origem, COUNT(*) AS n
         FROM leads GROUP BY origem ORDER BY n DESC LIMIT 8"
    );

    $byCampanha = DB::fetchAll(
        "SELECT COALESCE(NULLIF(utm_campaign, ''), 'Sem campanha') AS campanha, COUNT(*) AS n
         FROM leads GROUP BY campanha ORDER BY n DESC LIMIT 8"
    );

    $byUtmCombo = DB::fetchAll(
        "SELECT
            COALESCE(NULLIF(utm_source, ''), 'Sem UTM') AS origem,
            COALESCE(NULLIF(utm_medium, ''), '-') AS midia,
            COALESCE(NULLIF(utm_campaign, ''), '-') AS campanha,
            COUNT(*) AS n
         FROM leads
         GROUP BY origem, midia, campanha
         ORDER BY n DESC
         LIMIT 8"
    );

    $recentLeads = DB::fetchAll(
        "SELECT id, nome, instituicao, cargo, cidade, estado, porte, status, utm_source, utm_campaign, criado_em FROM leads ORDER BY criado_em DESC LIMIT 8"
    );

    $dbOk = true;
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

$emAndamento = $statusMap['contato']['n'] + $statusMap['proposta']['n'];

/* ── Pipeline bar width helper ────────────────────────────── */
function barWidth(int $n, int $total): int {
    return $total > 0 ? (int)round($n / $total * 100) : 0;
}

include __DIR__ . '/_header.php';
?>

<?php if (!$dbOk): ?>
  <div class="admin-empty-state">
    <div class="admin-empty-icon">⚠️</div>
    <h3>Banco de dados não conectado</h3>
    <p>Execute o <a href="/install.php?token=techsallus-install-2026">instalador</a> para configurar as tabelas.</p>
    <?php if (!empty($dbError)): ?>
      <pre class="admin-error-detail"><?= htmlspecialchars($dbError) ?></pre>
    <?php endif; ?>
  </div>

<?php else: ?>

  <!-- Stats grid -->
  <div class="stats-grid">
    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#eff6ff">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-num-admin"><?= $totalLeads ?></div>
      <div class="stat-label-admin">Total de leads</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#f1f5f9">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="stat-num-admin"><?= $statusMap['novo']['n'] ?></div>
      <div class="stat-label-admin">Aguardando contato</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#fffbeb">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <div class="stat-num-admin"><?= $emAndamento ?></div>
      <div class="stat-label-admin">Em andamento</div>
    </div>

    <div class="stat-card-admin">
      <div class="stat-icon" style="background:#ecfdf5">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="stat-num-admin"><?= $statusMap['fechamento']['n'] ?></div>
      <div class="stat-label-admin">Fechamentos</div>
    </div>
  </div>

  <!-- Dashboard grid -->
  <div class="dashboard-grid">

    <!-- Pipeline -->
    <div class="admin-card">
      <h2 class="admin-card-title">Pipeline de vendas</h2>
      <?php foreach ($statusMap as $key => $s): ?>
        <div class="pipeline-item">
          <div class="pipeline-label">
            <span><?= $s['label'] ?></span>
            <span><?= $s['n'] ?> lead<?= $s['n'] !== 1 ? 's' : '' ?></span>
          </div>
          <div class="pipeline-bar">
            <div class="pipeline-bar-fill pipeline-bar-<?= $key ?>" style="width:<?= barWidth($s['n'], $totalLeads) ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Porte -->
    <div class="admin-card">
      <h2 class="admin-card-title">Leads por porte</h2>
      <?php if (empty($byPorte)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byPorte as $p): ?>
          <div class="pipeline-item">
            <div class="pipeline-label">
              <span><?= htmlspecialchars($p['porte']) ?></span>
              <span><?= (int)$p['n'] ?></span>
            </div>
            <div class="pipeline-bar">
              <div class="pipeline-bar-fill" style="background:#094a86;width:<?= barWidth((int)$p['n'], $totalLeads) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Cargos que mais procuram</h2>
      <?php if (empty($byCargo)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byCargo as $row): ?>
          <div class="pipeline-item">
            <div class="pipeline-label">
              <span><?= htmlspecialchars($row['cargo']) ?></span>
              <span><?= (int)$row['n'] ?></span>
            </div>
            <div class="pipeline-bar">
              <div class="pipeline-bar-fill" style="background:#ff7300;width:<?= barWidth((int)$row['n'], $totalLeads) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Estados com mais leads</h2>
      <?php if (empty($byEstado)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byEstado as $row): ?>
          <div class="metric-row">
            <span><?= htmlspecialchars($row['estado']) ?></span>
            <strong><?= (int)$row['n'] ?></strong>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Cidades com mais leads</h2>
      <?php if (empty($byCidade)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byCidade as $row): ?>
          <div class="metric-row">
            <span><?= htmlspecialchars($row['cidade'] . ' / ' . $row['estado']) ?></span>
            <strong><?= (int)$row['n'] ?></strong>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Origem dos leads</h2>
      <?php if (empty($byOrigem)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byOrigem as $row): ?>
          <div class="pipeline-item">
            <div class="pipeline-label">
              <span><?= htmlspecialchars($row['origem']) ?></span>
              <span><?= (int)$row['n'] ?></span>
            </div>
            <div class="pipeline-bar">
              <div class="pipeline-bar-fill" style="background:#2563eb;width:<?= barWidth((int)$row['n'], $totalLeads) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Campanhas com mais leads</h2>
      <?php if (empty($byCampanha)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byCampanha as $row): ?>
          <div class="pipeline-item">
            <div class="pipeline-label">
              <span><?= htmlspecialchars($row['campanha']) ?></span>
              <span><?= (int)$row['n'] ?></span>
            </div>
            <div class="pipeline-bar">
              <div class="pipeline-bar-fill" style="background:#059669;width:<?= barWidth((int)$row['n'], $totalLeads) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2 class="admin-card-title">Combinações UTM</h2>
      <?php if (empty($byUtmCombo)): ?>
        <p class="admin-muted">Nenhum dado ainda.</p>
      <?php else: ?>
        <?php foreach ($byUtmCombo as $row): ?>
          <div class="metric-row metric-row-stack">
            <span>
              <strong><?= htmlspecialchars($row['origem']) ?></strong>
              <small><?= htmlspecialchars($row['midia'] . ' / ' . $row['campanha']) ?></small>
            </span>
            <strong><?= (int)$row['n'] ?></strong>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <!-- Recent leads table -->
  <div class="admin-card" style="margin-top:20px">
    <h2 class="admin-card-title">Últimos leads recebidos</h2>
    <?php if (empty($recentLeads)): ?>
      <p class="admin-muted">Nenhum lead cadastrado ainda.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="leads-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Instituição</th>
              <th>Cargo</th>
              <th>Cidade/UF</th>
              <th>Porte</th>
              <th>Status</th>
              <th>UTM</th>
              <th>Data</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentLeads as $lead): ?>
              <tr>
                <td class="td-nome"><?= htmlspecialchars($lead['nome']) ?></td>
                <td><?= htmlspecialchars($lead['instituicao']) ?></td>
                <td><?= htmlspecialchars($lead['cargo'] ?? '-') ?></td>
                <td><?= htmlspecialchars(($lead['cidade'] ?? '-') . ' / ' . ($lead['estado'] ?? '-')) ?></td>
                <td><?= htmlspecialchars($lead['porte']) ?></td>
                <td><span class="badge badge-<?= $lead['status'] ?>"><?= $lead['status'] ?></span></td>
                <td><?= htmlspecialchars(($lead['utm_source'] ?: '-') . ' / ' . ($lead['utm_campaign'] ?: '-')) ?></td>
                <td><?= date('d/m/Y', strtotime($lead['criado_em'])) ?></td>
                <td><a href="/admin/crm.php" class="table-action-link">Ver Kanban →</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/_footer.php'; ?>
