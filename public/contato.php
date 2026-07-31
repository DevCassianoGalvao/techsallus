<?php
$_root = file_exists(__DIR__ . '/../core/Settings.php') ? dirname(__DIR__) : __DIR__;
require_once $_root . '/core/Security.php';
Security::headers();
Security::csrfToken();

$_cfg = file_exists(__DIR__ . '/../config/i18n.php')
    ? __DIR__ . '/../config/i18n.php'
    : __DIR__ . '/config/i18n.php';
require_once $_cfg;
require_once $_root . '/core/Settings.php';

function getScripts(string $pos): string {
    try {
        return implode("\n", array_column(
            DB::fetchAll("SELECT conteudo FROM scripts_injecao WHERE posicao=? AND ativo=1 AND conteudo != ''", [$pos]),
            'conteudo'
        ));
    } catch (Exception $e) {
        return '';
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= tx('Fale com a TechSallus | Gestão para Saúde', 'Talk to TechSallus | Healthcare Management', 'Hable con TechSallus | Gestión para Salud') ?></title>
  <meta name="description" content="<?= tx('Conte qual gargalo mais impacta sua instituição e converse com a TechSallus sobre uma solução modular para atendimento, faturamento, gestão e custos.', 'Tell us which bottleneck impacts your institution most and talk to TechSallus about a modular solution for care, billing, management and costs.', 'Cuéntenos qué cuello de botella impacta más a su institución y converse con TechSallus sobre una solución modular para atención, facturación, gestión y costos.') ?>"/>

  <link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
  <link rel="shortcut icon" href="/assets/img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/assets/css/main.css?v=20260731a"/>
  <?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="page-hero">
  <svg class="hero-grid-bg" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" aria-hidden="true">
    <defs><pattern id="linegrid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.13)" stroke-width="0.8"/></pattern></defs>
    <rect width="100%" height="100%" fill="url(#linegrid)"/>
  </svg>
  <div class="container">
    <div class="page-hero-inner">
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Vamos começar pela sua prioridade', 'Let\'s start with your priority', 'Empecemos por su prioridad') ?></span></div>
      <h1 class="hero-headline"><?= tx('Qual gargalo mais impacta sua operação hoje?', 'Which bottleneck impacts your operation most today?', '¿Qué cuello de botella impacta más su operación hoy?') ?></h1>
      <p class="page-hero-sub"><?= tx('Conte um pouco sobre a instituição e escolha o desafio que mais pesa na rotina. A conversa começa por ele — e não por uma lista de módulos.', 'Tell us a bit about your institution and choose the challenge that weighs most on your routine. The conversation starts there — not with a list of modules.', 'Cuéntenos un poco sobre la institución y elija el desafío que más pesa en la rutina. La conversación empieza por ahí, no por una lista de módulos.') ?></p>
    </div>
  </div>
</section>

<section class="form-section">
  <div class="container">
    <div class="form-layout" id="form-section-inner">
      <div class="form-container">
        <span class="form-section-label"><?= tx('Formulário', 'Form', 'Formulario') ?></span>
        <h2 class="form-heading"><?= tx('Solicitar contato', 'Request contact', 'Solicitar contacto') ?></h2>
        <p class="form-sub"><?= tx('Ao enviar seus dados, nossa equipe entrará em contato para entender a operação e indicar o caminho mais adequado para a sua realidade.', 'By sending your details, our team will get in touch to understand your operation and point out the best path for your reality.', 'Al enviar sus datos, nuestro equipo se pondrá en contacto para entender la operación e indicar el camino más adecuado para su realidad.') ?></p>

        <form id="contato-form" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Security::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
          <div style="position:absolute;left:-9999px" aria-hidden="true">
            <label for="website"><?= tx('Não preencha', 'Do not fill', 'No completar') ?></label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="form-field">
            <label for="nome"><?= t('form_nome') ?></label>
            <input type="text" id="nome" name="nome" placeholder="<?= t('form_nome_help') ?>">
          </div>
          <div class="form-field">
            <label for="instituicao"><?= t('form_instituicao') ?></label>
            <input type="text" id="instituicao" name="instituicao" placeholder="<?= t('form_instituicao_help') ?>">
          </div>
          <div class="form-field">
            <label for="perfil_operacao"><?= t('form_perfil') ?></label>
            <select id="perfil_operacao" name="perfil_operacao">
              <option value=""><?= t('form_selecione') ?></option>
              <option value="consultorio"><?= t('form_perfil_consultorio') ?></option>
              <option value="clinica"><?= t('form_perfil_clinica') ?></option>
              <option value="hospital"><?= t('form_perfil_hospital') ?></option>
              <option value="pronto_atendimento"><?= t('form_perfil_pa') ?></option>
              <option value="outro"><?= t('form_perfil_outro') ?></option>
            </select>
          </div>
          <div class="form-field">
            <label for="principal_desafio"><?= t('form_desafio') ?></label>
            <select id="principal_desafio" name="principal_desafio">
              <option value=""><?= t('form_selecione') ?></option>
              <option value="faltas_agenda"><?= t('form_desafio_faltas') ?></option>
              <option value="fluxo_atendimento"><?= t('form_desafio_fluxo') ?></option>
              <option value="faturamento_glosas"><?= t('form_desafio_faturamento') ?></option>
              <option value="estoque_farmacia"><?= t('form_desafio_estoque') ?></option>
              <option value="custos_margem"><?= t('form_desafio_custos') ?></option>
              <option value="integracoes"><?= t('form_desafio_integracoes') ?></option>
            </select>
          </div>
          <div class="form-field">
            <label for="email"><?= t('form_email') ?></label>
            <input type="email" id="email" name="email" placeholder="<?= t('form_email_help') ?>">
          </div>
          <div class="form-field">
            <label for="whatsapp"><?= t('form_whatsapp') ?></label>
            <input type="text" id="whatsapp" name="whatsapp" placeholder="<?= t('form_whatsapp_help') ?>">
          </div>
          <div class="form-field">
            <label for="mensagem"><?= t('form_mensagem') ?></label>
            <textarea id="mensagem" name="mensagem" rows="4" placeholder="<?= t('form_mensagem_help') ?>" style="width:100%;font-family:'DM Sans',sans-serif;font-size:15px;color:#0d1f35;border:1px solid #e2eaf3;border-radius:6px;padding:10px 14px;resize:vertical"></textarea>
          </div>

          <input type="hidden" id="utm_source" name="utm_source" value="">
          <input type="hidden" id="utm_medium" name="utm_medium" value="">
          <input type="hidden" id="utm_campaign" name="utm_campaign" value="">
          <input type="hidden" id="utm_term" name="utm_term" value="">
          <input type="hidden" id="utm_content" name="utm_content" value="">

          <button type="submit" class="btn-submit"><?= t('form_submit') ?></button>
        </form>
      </div>

      <div class="trust-col" style="background:#f5f8fc">
        <div class="trust-checks">
          <div class="trust-check">
            <div class="trust-check-icon"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-6" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <span><?= tx('Consultórios, clínicas, policlínicas, hospitais e pronto atendimento', 'Private practices, clinics, polyclinics, hospitals and urgent care', 'Consultorios, clínicas, policlínicas, hospitales y urgencias') ?></span>
          </div>
          <div class="trust-check">
            <div class="trust-check-icon"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-6" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <span><?= tx('Escopo modular, dimensionado para a sua prioridade', 'Modular scope, sized to your priority', 'Alcance modular, dimensionado para su prioridad') ?></span>
          </div>
          <div class="trust-check">
            <div class="trust-check-icon"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-6" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <span><?= tx('Resposta de um especialista para entender sua operação', 'A specialist response to understand your operation', 'Respuesta de un especialista para entender su operación') ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="/assets/js/main.js?v=20260731a"></script>
</body>
</html>
