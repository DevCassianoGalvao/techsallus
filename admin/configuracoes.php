<?php
define('ADMIN_PAGE', true);

require_once __DIR__ . '/../core/Env.php';
require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Settings.php';

Env::load(__DIR__ . '/../.env');
Auth::start();
Auth::require();

$pageTitle = 'Configurações';
$activeNav = 'configuracoes';
$adminUser = Auth::user();
$saved = false;
$error = '';
$scriptName = 'Painel de Configurações';
$positions = [
    'head' => 'Head',
    'body' => 'Body',
    'footer' => 'Footer',
];

DB::query("
    CREATE TABLE IF NOT EXISTS scripts_injecao (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        posicao ENUM('head','body','footer') NOT NULL,
        nome VARCHAR(100) NOT NULL,
        conteudo TEXT NOT NULL,
        ativo TINYINT(1) DEFAULT 1,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!Security::verifyCsrf()) {
            throw new RuntimeException('Sessao expirada. Recarregue a pagina e tente novamente.');
        }
        Settings::set('whatsapp_numero', trim($_POST['whatsapp_numero'] ?? ''));
        Settings::set('whatsapp_mensagem', trim($_POST['whatsapp_mensagem'] ?? ''));

        foreach ($positions as $pos => $label) {
            $content = trim($_POST['script_' . $pos] ?? '');
            $active = $content !== '' ? 1 : 0;
            $existing = DB::fetchOne(
                "SELECT id FROM scripts_injecao WHERE posicao = ? AND nome = ? LIMIT 1",
                [$pos, $scriptName]
            );

            if ($existing) {
                DB::query(
                    "UPDATE scripts_injecao SET conteudo = ?, ativo = ? WHERE id = ?",
                    [$content, $active, $existing['id']]
                );
            } else {
                DB::insert(
                    "INSERT INTO scripts_injecao (posicao, nome, conteudo, ativo) VALUES (?, ?, ?, ?)",
                    [$pos, $scriptName, $content, $active]
                );
            }
        }

        $saved = true;
    } catch (Exception $e) {
        $error = 'Erro ao salvar configurações: ' . $e->getMessage();
    }
}

$whatsappNumero = Settings::get('whatsapp_numero', '557181299624');
$whatsappMensagem = Settings::get('whatsapp_mensagem', 'Ola, gostaria de mais informacoes sobre o sistema de voces');
$scripts = [];
$counts = [];
foreach ($positions as $pos => $label) {
    $row = DB::fetchOne(
        "SELECT conteudo FROM scripts_injecao WHERE posicao = ? AND nome = ? LIMIT 1",
        [$pos, $scriptName]
    );
    $scripts[$pos] = $row['conteudo'] ?? '';
    $counts[$pos] = (int)(DB::fetchOne(
        "SELECT COUNT(*) AS total FROM scripts_injecao WHERE posicao = ? AND ativo = 1 AND conteudo != ''",
        [$pos]
    )['total'] ?? 0);
}

$totalScripts = array_sum($counts);

require_once __DIR__ . '/_header.php';
?>

<?php if ($saved): ?>
  <div class="editor-alert-ok" style="margin-bottom:20px">Configurações salvas com sucesso.</div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="editor-alert-err" style="margin-bottom:20px"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="stats-grid settings-stats-grid">
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$totalScripts ?></div>
    <div class="stat-label-admin">Scripts ativos</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$counts['head'] ?></div>
    <div class="stat-label-admin">No head</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$counts['body'] ?></div>
    <div class="stat-label-admin">No body</div>
  </div>
  <div class="stat-card-admin">
    <div class="stat-num-admin"><?= (int)$counts['footer'] ?></div>
    <div class="stat-label-admin">No rodapé</div>
  </div>
</div>

<form method="POST" class="settings-layout">
  <?= Security::csrfField() ?>
  <div class="admin-card settings-main-card">
    <h2 class="admin-card-title">WhatsApp</h2>
    <div class="editor-field">
      <label class="editor-label" for="whatsapp_numero">Número do botão flutuante</label>
      <input
        class="editor-input"
        id="whatsapp_numero"
        name="whatsapp_numero"
        type="text"
        value="<?= htmlspecialchars($whatsappNumero) ?>"
        placeholder="5571999999999"
      >
      <small class="editor-help">Use DDI + DDD + número. Exemplo: 5571999999999. O site usa esse número no botão flutuante.</small>
    </div>

    <div class="editor-field">
      <label class="editor-label" for="whatsapp_mensagem">Mensagem inicial do WhatsApp</label>
      <textarea
        class="editor-textarea"
        id="whatsapp_mensagem"
        name="whatsapp_mensagem"
        rows="3"
        placeholder="Ola, gostaria de mais informacoes sobre o sistema de voces"
      ><?= htmlspecialchars($whatsappMensagem) ?></textarea>
      <small class="editor-help">Essa frase vai pronta no campo de mensagem quando o visitante clicar no botão flutuante.</small>
    </div>

    <h2 class="admin-card-title settings-section-title">Gerador de links UTM</h2>
    <div class="utm-generator">
      <div class="editor-field">
        <label class="editor-label" for="utm_base_url">URL de destino</label>
        <input class="editor-input" id="utm_base_url" type="url" value="https://techsallus.com.br/">
      </div>
      <div class="utm-grid">
        <div class="editor-field">
          <label class="editor-label" for="utm_gen_source">Origem</label>
          <input class="editor-input" id="utm_gen_source" type="text" placeholder="google, facebook, instagram">
        </div>
        <div class="editor-field">
          <label class="editor-label" for="utm_gen_medium">Midia</label>
          <input class="editor-input" id="utm_gen_medium" type="text" placeholder="cpc, social, email">
        </div>
        <div class="editor-field">
          <label class="editor-label" for="utm_gen_campaign">Campanha</label>
          <input class="editor-input" id="utm_gen_campaign" type="text" placeholder="clinicas_bahia_maio">
        </div>
        <div class="editor-field">
          <label class="editor-label" for="utm_gen_term">Termo</label>
          <input class="editor-input" id="utm_gen_term" type="text" placeholder="gestao hospitalar">
        </div>
        <div class="editor-field">
          <label class="editor-label" for="utm_gen_content">Conteudo</label>
          <input class="editor-input" id="utm_gen_content" type="text" placeholder="banner_a, botao_blog">
        </div>
      </div>
      <div class="editor-field">
        <label class="editor-label" for="utm_result">Link gerado</label>
        <div class="utm-result-row">
          <input class="editor-input" id="utm_result" type="text" readonly>
          <button type="button" class="btn-preview" id="copy_utm_link">Copiar</button>
        </div>
        <small class="editor-help">Use esse link em anuncios, posts, e-mails e botoes. O formulario salva esses dados no lead.</small>
      </div>
    </div>

    <h2 class="admin-card-title settings-section-title">Painel de scripts</h2>

    <div class="settings-script-block">
      <label class="editor-label" for="script_head">Scripts no &lt;head&gt;</label>
      <textarea class="editor-textarea settings-code" id="script_head" name="script_head" spellcheck="false" placeholder="Meta Pixel, Google Tag Manager head, tags de verificação..."><?= htmlspecialchars($scripts['head']) ?></textarea>
      <small class="editor-help">Use para Meta Pixel, Google Ads, GTM head, Search Console e verificações.</small>
    </div>

    <div class="settings-script-block">
      <label class="editor-label" for="script_body">Scripts no início do &lt;body&gt;</label>
      <textarea class="editor-textarea settings-code" id="script_body" name="script_body" spellcheck="false" placeholder="GTM noscript, pixels que precisam abrir no body..."><?= htmlspecialchars($scripts['body']) ?></textarea>
      <small class="editor-help">Use para GTM noscript e códigos que exigem instalação logo após abrir o body.</small>
    </div>

    <div class="settings-script-block">
      <label class="editor-label" for="script_footer">Scripts no rodapé</label>
      <textarea class="editor-textarea settings-code" id="script_footer" name="script_footer" spellcheck="false" placeholder="Chat, remarketing, scripts de conversão..."><?= htmlspecialchars($scripts['footer']) ?></textarea>
      <small class="editor-help">Use para chats, remarketing e scripts que podem carregar no final da página.</small>
    </div>

    <div class="editor-actions">
      <button type="submit" class="btn-save">Salvar configurações</button>
      <a href="/" target="_blank" class="btn-preview">Ver site</a>
    </div>
  </div>

  <aside class="admin-card settings-side-card">
    <h2 class="admin-card-title">Onde colar cada código</h2>
    <div class="settings-help-list">
      <div><strong>Meta Pixel:</strong><span>normalmente no Head.</span></div>
      <div><strong>Google Ads:</strong><span>tag global no Head; evento pode ir no Footer.</span></div>
      <div><strong>Google Tag Manager:</strong><span>script no Head e noscript no Body.</span></div>
      <div><strong>Chat / CRM:</strong><span>geralmente no Footer.</span></div>
      <div><strong>WhatsApp:</strong><span>troque apenas o número. O link é gerado sozinho.</span></div>
    </div>
  </aside>
</form>

<script>
(function () {
  const fields = {
    base: document.getElementById('utm_base_url'),
    utm_source: document.getElementById('utm_gen_source'),
    utm_medium: document.getElementById('utm_gen_medium'),
    utm_campaign: document.getElementById('utm_gen_campaign'),
    utm_term: document.getElementById('utm_gen_term'),
    utm_content: document.getElementById('utm_gen_content')
  };
  const result = document.getElementById('utm_result');
  const copyBtn = document.getElementById('copy_utm_link');

  function slugValue(value) {
    return value.trim().toLowerCase().replace(/\s+/g, '_');
  }

  function buildUrl() {
    let url;
    try {
      url = new URL(fields.base.value || 'https://techsallus.com.br/');
    } catch (e) {
      result.value = '';
      return;
    }

    Object.keys(fields).forEach(function (key) {
      if (key === 'base') return;
      const value = slugValue(fields[key].value);
      if (value) url.searchParams.set(key, value);
      else url.searchParams.delete(key);
    });
    result.value = url.toString();
  }

  Object.values(fields).forEach(function (field) {
    field.addEventListener('input', buildUrl);
  });

  copyBtn.addEventListener('click', async function () {
    buildUrl();
    if (!result.value) return;
    await navigator.clipboard.writeText(result.value);
    copyBtn.textContent = 'Copiado';
    setTimeout(function () { copyBtn.textContent = 'Copiar'; }, 1600);
  });

  buildUrl();
})();
</script>

<?php require_once __DIR__ . '/_footer.php'; ?>
