<?php
define('ADMIN_PAGE', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/core/Env.php';
require_once $rootDir . '/core/DB.php';
require_once $rootDir . '/core/Auth.php';
require_once $rootDir . '/core/Schema.php';
Env::load($rootDir . '/.env');
Auth::require();
Schema::ensureLeadTrackingColumns();

$adminUser = Auth::user();
$pageTitle = 'Contatos';
$activeNav = 'contatos';

$busca   = trim($_GET['busca']   ?? '');
$estado  = trim($_GET['estado']  ?? '');
$tipo    = trim($_GET['tipo']    ?? '');
$porte   = trim($_GET['porte']   ?? '');
$periodo = trim($_GET['periodo'] ?? '');

$where  = ['1=1'];
$params = [];

if ($busca) {
    $t = "%{$busca}%";
    $where[] = "(nome LIKE ? OR instituicao LIKE ? OR email LIKE ? OR cidade LIKE ?)";
    $params = array_merge($params, [$t, $t, $t, $t]);
}
if ($estado) {
    $where[] = "estado = ?";
    $params[] = $estado;
}
if ($tipo) {
    $where[] = "tipo_instituicao = ?";
    $params[] = $tipo;
}
if ($porte) {
    $where[] = "porte = ?";
    $params[] = $porte;
}
if ($periodo === 'hoje') {
    $where[] = "DATE(criado_em) = CURDATE()";
} elseif ($periodo === 'semana') {
    $where[] = "criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($periodo === 'mes') {
    $where[] = "criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

$whereSQL = implode(' AND ', $where);
$total = (int)(DB::fetchOne("SELECT COUNT(*) AS t FROM leads WHERE {$whereSQL}", $params)['t'] ?? 0);

$porPagina = 25;
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$offset = ($pagina - 1) * $porPagina;
$totalPags = (int)ceil($total / $porPagina);

$contatos = DB::fetchAll(
    "SELECT * FROM leads WHERE {$whereSQL} ORDER BY criado_em DESC LIMIT {$porPagina} OFFSET {$offset}",
    $params
);

$estados = DB::fetchAll("SELECT DISTINCT estado FROM leads WHERE estado IS NOT NULL AND estado != '' ORDER BY estado");
$tipos = DB::fetchAll("SELECT DISTINCT tipo_instituicao FROM leads WHERE tipo_instituicao IS NOT NULL AND tipo_instituicao != '' ORDER BY tipo_instituicao");

$queryStr = http_build_query(array_filter([
    'busca'   => $busca,
    'estado'  => $estado,
    'tipo'    => $tipo,
    'porte'   => $porte,
    'periodo' => $periodo,
]));
$exportUrl = '/api/exportar-leads.php' . ($queryStr ? '?' . $queryStr : '');

$topbarExtra = '<div class="topbar-actions">
  <span class="admin-muted">' . $total . ' contato' . ($total !== 1 ? 's' : '') . '</span>
  <a href="' . htmlspecialchars($exportUrl) . '" class="btn-export">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="7 10 12 15 17 10"/>
      <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    Exportar CSV
  </a>
</div>';

include __DIR__ . '/_header.php';
?>

<div class="crm-filters">
  <form method="GET" class="filter-form" action="/admin/contatos.php">
    <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Nome, e-mail, instituição, cidade..." class="filter-input">

    <select name="estado" class="filter-select">
      <option value="">Todos os estados</option>
      <?php foreach ($estados as $e): ?>
        <option value="<?= htmlspecialchars($e['estado']) ?>" <?= $estado === $e['estado'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['estado']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="tipo" class="filter-select">
      <option value="">Todos os tipos</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= htmlspecialchars($t['tipo_instituicao']) ?>" <?= $tipo === $t['tipo_instituicao'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($t['tipo_instituicao']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="porte" class="filter-select">
      <option value="">Todos os portes</option>
      <option value="1-2" <?= $porte === '1-2' ? 'selected' : '' ?>>1-2 profissionais</option>
      <option value="3-9" <?= $porte === '3-9' ? 'selected' : '' ?>>3-9 profissionais</option>
      <option value="10-30" <?= $porte === '10-30' ? 'selected' : '' ?>>10-30 profissionais</option>
      <option value="31-100" <?= $porte === '31-100' ? 'selected' : '' ?>>31-100 profissionais</option>
      <option value="100+" <?= $porte === '100+' ? 'selected' : '' ?>>100+ profissionais</option>
    </select>

    <select name="periodo" class="filter-select">
      <option value="">Qualquer período</option>
      <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
      <option value="semana" <?= $periodo === 'semana' ? 'selected' : '' ?>>Últimos 7 dias</option>
      <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>Últimos 30 dias</option>
    </select>

    <button type="submit" class="filter-btn">Filtrar</button>
    <?php if ($busca || $estado || $tipo || $porte || $periodo): ?>
      <a href="/admin/contatos.php" class="filter-clear">Limpar</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <div class="table-wrap">
    <table class="leads-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Cargo</th>
          <th>Tipo</th>
          <th>Instituição</th>
          <th>Cidade/UF</th>
          <th>Porte</th>
          <th>E-mail</th>
          <th>WhatsApp</th>
          <th>Status</th>
          <th>UTM</th>
          <th>Data</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contatos as $c): ?>
          <tr>
            <td class="td-nome"><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['cargo']) ?></td>
            <td><?= htmlspecialchars($c['tipo_instituicao'] ?? '-') ?></td>
            <td><?= htmlspecialchars($c['instituicao']) ?></td>
            <td><?= htmlspecialchars(($c['cidade'] ?? '-') . ' / ' . ($c['estado'] ?? '-')) ?></td>
            <td><?= htmlspecialchars($c['porte']) ?></td>
            <td>
              <a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:#094a86">
                <?= htmlspecialchars($c['email']) ?>
              </a>
            </td>
            <td>
              <a href="https://wa.me/55<?= preg_replace('/\D/', '', $c['whatsapp']) ?>" target="_blank" rel="noopener noreferrer" style="color:#25d366;text-decoration:none">
                <?= htmlspecialchars($c['whatsapp']) ?>
              </a>
            </td>
            <td><span class="badge badge-<?= htmlspecialchars($c['status']) ?>"><?= ucfirst($c['status']) ?></span></td>
            <td style="font-size:11px;color:#64748b">
              <?= htmlspecialchars(($c['utm_source'] ?: '-') . ' / ' . ($c['utm_campaign'] ?: '-')) ?>
            </td>
            <td style="font-size:12px;white-space:nowrap"><?= date('d/m/Y', strtotime($c['criado_em'])) ?></td>
            <td class="td-actions">
              <button type="button" class="btn-row-action btn-edit-contato" title="Editar"
                      data-id="<?= (int)$c['id'] ?>"
                      data-nome="<?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>"
                      data-cargo="<?= htmlspecialchars($c['cargo'] ?? '', ENT_QUOTES) ?>"
                      data-tipo_instituicao="<?= htmlspecialchars($c['tipo_instituicao'] ?? '', ENT_QUOTES) ?>"
                      data-instituicao="<?= htmlspecialchars($c['instituicao'] ?? '', ENT_QUOTES) ?>"
                      data-perfil_operacao="<?= htmlspecialchars($c['perfil_operacao'] ?? '', ENT_QUOTES) ?>"
                      data-principal_desafio="<?= htmlspecialchars($c['principal_desafio'] ?? '', ENT_QUOTES) ?>"
                      data-email="<?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES) ?>"
                      data-whatsapp="<?= htmlspecialchars($c['whatsapp'] ?? '', ENT_QUOTES) ?>"
                      data-porte="<?= htmlspecialchars($c['porte'] ?? '', ENT_QUOTES) ?>"
                      data-cidade="<?= htmlspecialchars($c['cidade'] ?? '', ENT_QUOTES) ?>"
                      data-estado="<?= htmlspecialchars($c['estado'] ?? '', ENT_QUOTES) ?>"
                      data-mensagem="<?= htmlspecialchars($c['mensagem'] ?? '', ENT_QUOTES) ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </button>
              <button type="button" class="btn-row-action danger btn-delete-contato" title="Excluir" data-id="<?= (int)$c['id'] ?>" data-nome="<?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($contatos)): ?>
          <tr>
            <td colspan="12" style="text-align:center;color:#94a3b8;padding:40px">Nenhum contato encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPags > 1): ?>
    <div style="display:flex;justify-content:center;gap:6px;padding:20px 0 4px">
      <?php if ($pagina > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])) ?>" class="btn-export">←</a>
      <?php endif; ?>

      <?php for ($i = max(1, $pagina - 2); $i <= min($totalPags, $pagina + 2); $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" class="btn-export" style="<?= $i === $pagina ? 'background:#094a86;color:#fff;border-color:#094a86' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($pagina < $totalPags): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])) ?>" class="btn-export">→</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="edit-modal" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
  <div class="modal-box" style="max-width:640px">
    <div class="modal-header">
      <div>
        <div class="modal-title" id="edit-modal-title">Editar contato</div>
        <div class="modal-sub">Altere os dados e salve.</div>
      </div>
      <button type="button" class="modal-close" id="edit-modal-close" aria-label="Fechar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="edit-contato-form">
        <input type="hidden" id="edit-id" name="id">
        <div class="edit-grid">
          <div class="edit-field"><label>Nome</label><input type="text" id="edit-nome" name="nome"></div>
          <div class="edit-field"><label>Cargo</label><input type="text" id="edit-cargo" name="cargo"></div>
          <div class="edit-field"><label>Instituição</label><input type="text" id="edit-instituicao" name="instituicao"></div>
          <div class="edit-field"><label>Tipo de instituição</label><input type="text" id="edit-tipo_instituicao" name="tipo_instituicao"></div>
          <div class="edit-field"><label>Perfil da operação</label><input type="text" id="edit-perfil_operacao" name="perfil_operacao"></div>
          <div class="edit-field"><label>Porte</label><input type="text" id="edit-porte" name="porte"></div>
          <div class="edit-field"><label>E-mail</label><input type="email" id="edit-email" name="email"></div>
          <div class="edit-field"><label>WhatsApp</label><input type="text" id="edit-whatsapp" name="whatsapp"></div>
          <div class="edit-field"><label>Cidade</label><input type="text" id="edit-cidade" name="cidade"></div>
          <div class="edit-field"><label>Estado (UF)</label><input type="text" id="edit-estado" name="estado" maxlength="2"></div>
          <div class="edit-field full"><label>Principal desafio</label><input type="text" id="edit-principal_desafio" name="principal_desafio"></div>
          <div class="edit-field full"><label>Mensagem</label><textarea id="edit-mensagem" name="mensagem"></textarea></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-modal-cancel" id="edit-modal-cancel">Cancelar</button>
      <button type="button" class="btn-modal-save" id="edit-modal-save">Salvar</button>
    </div>
  </div>
</div>

<script>
(function () {
  var editModal   = document.getElementById('edit-modal');
  var editForm    = document.getElementById('edit-contato-form');
  var editFields  = ['nome', 'cargo', 'instituicao', 'tipo_instituicao', 'perfil_operacao', 'porte', 'email', 'whatsapp', 'cidade', 'estado', 'principal_desafio', 'mensagem'];

  function openEditModal(btn) {
    document.getElementById('edit-id').value = btn.dataset.id;
    editFields.forEach(function (f) {
      var el = document.getElementById('edit-' + f);
      if (el) el.value = btn.dataset[f] || '';
    });
    editModal.classList.add('open');
  }
  function closeEditModal() { editModal.classList.remove('open'); }

  document.querySelectorAll('.btn-edit-contato').forEach(function (btn) {
    btn.addEventListener('click', function () { openEditModal(btn); });
  });
  document.getElementById('edit-modal-close').addEventListener('click', closeEditModal);
  document.getElementById('edit-modal-cancel').addEventListener('click', closeEditModal);
  editModal.addEventListener('click', function (e) { if (e.target === editModal) closeEditModal(); });

  function postCrm(action, data) {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var payload = Object.assign({ action: action }, data);
    return fetch('/api/crm.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf ? csrf.content : '' },
      body: JSON.stringify(payload),
    }).then(function (r) { return r.json(); });
  }

  document.getElementById('edit-modal-save').addEventListener('click', function () {
    var saveBtn = this;
    var data = { id: document.getElementById('edit-id').value };
    editFields.forEach(function (f) {
      var el = document.getElementById('edit-' + f);
      if (el) data[f] = el.value;
    });
    saveBtn.disabled = true;
    saveBtn.textContent = 'Salvando...';
    postCrm('editar', data).then(function (res) {
      saveBtn.disabled = false;
      saveBtn.textContent = 'Salvar';
      if (res.ok) {
        window.location.reload();
      } else {
        alert(res.erro || 'Erro ao salvar.');
      }
    }).catch(function () {
      saveBtn.disabled = false;
      saveBtn.textContent = 'Salvar';
      alert('Erro ao salvar.');
    });
  });

  document.querySelectorAll('.btn-delete-contato').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var nome = btn.dataset.nome || 'este contato';
      if (!confirm('Excluir ' + nome + '? Essa ação não pode ser desfeita.')) return;
      postCrm('excluir', { id: btn.dataset.id }).then(function (res) {
        if (res.ok) {
          window.location.reload();
        } else {
          alert(res.erro || 'Erro ao excluir.');
        }
      }).catch(function () { alert('Erro ao excluir.'); });
    });
  });
})();
</script>

<?php include __DIR__ . '/_footer.php'; ?>
