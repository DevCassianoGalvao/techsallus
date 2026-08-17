<?php
$_root = __DIR__;
require_once $_root . '/core/Security.php';
Security::headers();
Security::csrfToken();

require_once __DIR__ . '/config/i18n.php';
require_once $_root . '/core/Settings.php';
require_once __DIR__ . '/_partials/icons.php';

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

$_desafios = [
    tx('Faltas e agenda', 'No-shows and scheduling', 'Ausencias y agenda'),
    tx('Fluxo de atendimento', 'Care flow', 'Flujo de atención'),
    tx('Faturamento e glosas', 'Billing and denials', 'Facturación y glosas'),
    tx('Estoque e farmácia', 'Inventory and pharmacy', 'Inventario y farmacia'),
    tx('Custos e margem', 'Costs and margin', 'Costos y margen'),
    tx('Integrações', 'Integrations', 'Integraciones'),
];

$_campaigns = [
    [tx('Quero reduzir faltas e ociosidade', 'I want to reduce no-shows and idle time', 'Quiero reducir ausencias y ociosidad'), tx('Converse sobre confirmação, agenda e melhor aproveitamento da capacidade.', 'Talk about confirmations, scheduling and better use of capacity.', 'Converse sobre confirmación, agenda y mejor aprovechamiento de la capacidad.'), $_desafios[0]],
    [tx('Quero acelerar o atendimento', 'I want to speed up care', 'Quiero acelerar la atención'), tx('Mapeie filas, check-in, acolhimento, triagem e passagem entre setores.', 'Map queues, check-in, intake, triage and handoffs between departments.', 'Mapee filas, check-in, acogida, triaje y traspaso entre sectores.'), $_desafios[1]],
    [tx('Quero controlar faturamento e glosas', 'I want to control billing and denials', 'Quiero controlar facturación y glosas'), tx('Revise a jornada entre produção, guias, regras, repasses e recebimento.', 'Review the journey between production, claims, rules, payouts and collection.', 'Revise el recorrido entre producción, guías, reglas, repartos y cobro.'), $_desafios[2]],
    [tx('Quero conhecer meus custos', 'I want to know my costs', 'Quiero conocer mis costos'), tx('Entenda como analisar margem e resultado por especialidade, unidade e procedimento.', 'Understand how to analyze margin and results by specialty, unit and procedure.', 'Entienda cómo analizar margen y resultado por especialidad, unidad y procedimiento.'), $_desafios[4]],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Fale com a TechSallus | Gestão para Saúde', 'Talk to TechSallus | Healthcare Management', 'Hable con TechSallus | Gestión para Salud') ?></title>
<meta name="description" content="<?= tx('Conte qual gargalo mais impacta sua instituição e converse com a TechSallus sobre uma solução modular para atendimento, faturamento, gestão e custos.', 'Tell us which bottleneck impacts your institution most and talk to TechSallus about a modular solution for care, billing, management and costs.', 'Cuéntenos qué cuello de botella impacta más a su institución y converse con TechSallus sobre una solución modular para atención, facturación, gestión y costos.') ?>"/>
<link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link rel="stylesheet" href="/assets/css/main.css?v=20260731f"/>
<?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section class="subhero" style="padding-bottom:36px">
  <div class="wrap">
    <div style="max-width:760px;margin:0 auto;text-align:center">
      <span class="eyebrow reveal"><?= tx('Vamos começar pela sua prioridade', 'Let\'s start with your priority', 'Empecemos por su prioridad') ?></span>
      <h1 class="reveal"><?= tx('Qual gargalo mais impacta sua operação hoje?', 'Which bottleneck impacts your operation most today?', '¿Qué cuello de botella impacta más su operación hoy?') ?></h1>
      <p class="reveal" style="margin-left:auto;margin-right:auto"><?= tx('Conte um pouco sobre a instituição e escolha o desafio que mais pesa na rotina. A conversa começa por ele, não por uma lista de módulos.', 'Tell us a bit about your institution and choose the challenge that weighs most on your routine. The conversation starts there — not with a list of modules.', 'Cuéntenos un poco sobre la institución y elija el desafío que más pesa en la rutina. La conversación empieza por ahí, no por una lista de módulos.') ?></p>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div style="max-width:820px;margin:0 auto">
    <div class="form-card reveal" id="form-card">
      <form id="contactForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Security::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <div style="position:absolute;left:-9999px" aria-hidden="true">
          <label for="website"><?= tx('Não preencha', 'Do not fill', 'No completar') ?></label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-grid">
          <div class="field">
            <label><?= t('form_nome') ?> *</label>
            <input type="text" name="nome" required/>
            <span class="err"><?= tx('Informe seu nome completo.', 'Enter your full name.', 'Indique su nombre completo.') ?></span>
          </div>
          <div class="field">
            <label><?= t('form_instituicao') ?> *</label>
            <input type="text" name="instituicao" required/>
            <span class="err"><?= tx('Informe o nome da instituição.', 'Enter the institution\'s name.', 'Indique el nombre de la institución.') ?></span>
          </div>
          <div class="field">
            <label><?= t('form_perfil') ?> *</label>
            <select name="perfil_operacao" required>
              <option value=""><?= t('form_selecione') ?></option>
              <option><?= t('form_perfil_consultorio') ?></option>
              <option><?= t('form_perfil_clinica') ?></option>
              <option><?= t('form_perfil_hospital') ?></option>
              <option><?= t('form_perfil_pa') ?></option>
              <option><?= t('form_perfil_outro') ?></option>
            </select>
            <span class="err"><?= tx('Selecione o perfil da operação.', 'Select the operation profile.', 'Seleccione el perfil de la operación.') ?></span>
          </div>
          <div class="field">
            <label><?= t('form_email') ?> *</label>
            <input type="email" name="email" required/>
            <span class="err"><?= tx('Informe um e-mail válido.', 'Enter a valid email.', 'Indique un correo válido.') ?></span>
          </div>
          <div class="field full">
            <label><?= t('form_desafio') ?> * <span style="font-weight:400;color:var(--muted)">(<?= tx('pode escolher mais de um', 'you can pick more than one', 'puede elegir más de uno') ?>)</span></label>
            <input type="hidden" id="priorityValue" name="principal_desafio" required/>
            <div class="priority-pills">
              <?php foreach ($_desafios as $_d): ?>
                <button type="button" class="priority-pill" data-value="<?= htmlspecialchars($_d, ENT_QUOTES, 'UTF-8') ?>"><?= $_d ?></button>
              <?php endforeach; ?>
            </div>
            <span class="err"><?= tx('Selecione o principal desafio.', 'Select the main challenge.', 'Seleccione el principal desafío.') ?></span>
          </div>
          <div class="field">
            <label><?= t('form_whatsapp') ?> *</label>
            <input type="tel" name="whatsapp" required/>
            <span class="err"><?= tx('Informe um telefone com DDD.', 'Enter a phone number with area code.', 'Indique un teléfono con código de área.') ?></span>
          </div>
          <div class="field">&nbsp;</div>
          <div class="field full">
            <label><?= t('form_mensagem') ?></label>
            <textarea name="mensagem" placeholder="<?= t('form_mensagem_help') ?>"></textarea>
          </div>
        </div>

        <input type="hidden" name="utm_source" value="">
        <input type="hidden" name="utm_medium" value="">
        <input type="hidden" name="utm_campaign" value="">
        <input type="hidden" name="utm_term" value="">
        <input type="hidden" name="utm_content" value="">

        <p style="font-size:13.5px;color:var(--muted);margin-top:20px;line-height:1.6"><?= tx('Ao enviar seus dados, nossa equipe entrará em contato para entender a operação e indicar o caminho mais adequado para a sua realidade.', 'By sending your details, our team will get in touch to understand your operation and point out the best path for your reality.', 'Al enviar sus datos, nuestro equipo se pondrá en contacto para entender la operación e indicar el camino más adecuado para su realidad.') ?></p>
        <button type="submit" class="btn btn-primary" style="margin-top:24px"><?= t('form_submit') ?></button>
      </form>
      <div class="form-success" id="formSuccess">
        <div class="icon-badge" style="width:60px;height:60px;margin-bottom:20px"><svg class="icon" style="stroke:#22a35a;width:30px;height:30px" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg></div>
        <h3><?= t('form_success_title') ?></h3>
        <p style="color:var(--muted);margin-top:10px"><?= t('form_success_desc') ?></p>
      </div>
    </div>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Ou comece por aqui', 'Or start here', 'O empiece por aquí') ?></span>
      <h2 class="reveal"><?= tx('Chamada alternativa para campanhas.', 'Alternative call-to-action for campaigns.', 'Llamada alternativa para campañas.') ?></h2>
    </div>
    <div class="grid-4">
      <?php foreach ($_campaigns as [$_t, $_d, $_v]): ?>
        <button type="button" class="campaign-card reveal" onclick="selectPriority('<?= htmlspecialchars(addslashes($_v)) ?>')"><h4><?= $_t ?></h4><p><?= $_d ?></p></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731f"></script>
</body>
</html>
