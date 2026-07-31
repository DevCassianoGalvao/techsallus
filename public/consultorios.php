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
  <title><?= tx('Sistema para Consultórios: Agenda, Prontuário e TISS | TechSallus', 'System for Private Practices: Scheduling, Records and Billing | TechSallus', 'Sistema para Consultorios: Agenda, Historia Clínica y Facturación | TechSallus') ?></title>
  <meta name="description" content="<?= tx('Organize agenda, confirmação, atendimento, prontuário, laudos, faturamento TISS, regras de convênios e repasses em uma solução modular.', 'Organize scheduling, confirmations, care, medical records, reports, TISS billing, payer rules and payouts in one modular solution.', 'Organice agenda, confirmación, atención, historia clínica, informes, facturación TISS, reglas de convenios y repartos en una solución modular.') ?>"/>

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
      <div class="hero-label"><div class="hero-label-bar"></div><span><?= tx('Rotina simples, informação conectada', 'A simple routine, connected information', 'Rutina simple, información conectada') ?></span></div>
      <h1 class="hero-headline"><?= tx('Mais tempo para atender. Menos tarefas manuais entre a agenda e o faturamento.', 'More time to see patients. Fewer manual tasks between scheduling and billing.', 'Más tiempo para atender. Menos tareas manuales entre la agenda y la facturación.') ?></h1>
      <p class="page-hero-sub"><?= tx('Reúna agendamento, atendimento, prontuário, laudos, faturamento e repasses em um fluxo único, com recursos que acompanham a rotina do consultório sem adicionar complexidade desnecessária.', 'Bring scheduling, care, medical records, reports, billing and payouts together in a single flow, with features that follow the practice\'s routine without adding unnecessary complexity.', 'Reúna agenda, atención, historia clínica, informes, facturación y repartos en un flujo único, con recursos que acompañan la rutina del consultorio sin agregar complejidad innecesaria.') ?></p>
      <div class="hero-ctas">
        <a href="/contato" class="btn-primary"><?= tx('Quero organizar meu consultório', 'I want to organize my practice', 'Quiero organizar mi consultorio') ?></a>
      </div>
      <p class="hero-caption"><?= tx('Solução web, modular e dimensionada para a realidade da sua operação.', 'A web-based, modular solution sized to fit your operation\'s reality.', 'Solución web, modular y dimensionada para la realidad de su operación.') ?></p>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Seu consultório precisa de tempo, não de mais controles paralelos.', 'Your practice needs time, not more parallel controls.', 'Su consultorio necesita tiempo, no más controles paralelos.') ?></h2>
    <p class="page-section-sub"><?= tx('Confirmar consultas uma a uma, procurar informações em sistemas diferentes e revisar o faturamento no fim do dia consome horas que poderiam estar voltadas ao paciente. A TechSallus conecta essas etapas para simplificar a rotina e reduzir retrabalho.', 'Confirming appointments one by one, searching for information across different systems and reviewing billing at the end of the day consumes hours that could be spent on the patient. TechSallus connects these steps to simplify the routine and reduce rework.', 'Confirmar consultas una por una, buscar información en sistemas diferentes y revisar la facturación al final del día consume horas que podrían dedicarse al paciente. TechSallus conecta estas etapas para simplificar la rutina y reducir el retrabajo.') ?></p>
    <div class="card-grid card-grid-2">
      <div class="card">
        <div class="card-title"><?= tx('Agenda mais previsível', 'A more predictable schedule', 'Agenda más previsible') ?></div>
        <p class="card-desc"><?= tx('Organize horários e use confirmações por WhatsApp para diminuir o esforço manual e apoiar a ocupação.', 'Organize time slots and use WhatsApp confirmations to reduce manual effort and support occupancy.', 'Organice horarios y use confirmaciones por WhatsApp para disminuir el esfuerzo manual y apoyar la ocupación.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Atendimento com continuidade', 'Care with continuity', 'Atención con continuidad') ?></div>
        <p class="card-desc"><?= tx('Recepção, consultório, prontuário e laudos compartilham a mesma informação.', 'Front desk, exam room, medical records and reports share the same information.', 'Recepción, consultorio, historia clínica e informes comparten la misma información.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Faturamento mais organizado', 'More organized billing', 'Facturación más organizada') ?></div>
        <p class="card-desc"><?= tx('TISS, TUSS, regras de convênios e repasses ficam vinculados à produção.', 'TISS, TUSS, payer rules and payouts stay linked to production.', 'TISS, TUSS, reglas de convenios y repartos quedan vinculados a la producción.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Infraestrutura administrada', 'Managed infrastructure', 'Infraestructura administrada') ?></div>
        <p class="card-desc"><?= tx('Ambiente em nuvem, manutenção, atualizações, backups e SLA conforme a modalidade contratada.', 'Cloud environment, maintenance, updates, backups and SLA according to the contracted plan.', 'Entorno en la nube, mantenimiento, actualizaciones, copias de seguridad y SLA según la modalidad contratada.') ?></p>
      </div>
    </div>
  </div>
</section>

<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Uma jornada mais leve para a equipe e para o paciente.', 'A lighter journey for the team and the patient.', 'Un recorrido más liviano para el equipo y el paciente.') ?></h2>
    <div class="numbered-steps">
      <div class="step-item">
        <div class="step-num">1</div>
        <p class="step-text"><strong><?= tx('Antes da consulta.', 'Before the visit.', 'Antes de la consulta.') ?></strong> <?= tx('A agenda organiza os horários e a confirmação via WhatsApp ajuda a reduzir contatos manuais e faltas evitáveis.', 'The schedule organizes time slots and WhatsApp confirmation helps reduce manual contacts and avoidable no-shows.', 'La agenda organiza los horarios y la confirmación por WhatsApp ayuda a reducir contactos manuales y ausencias evitables.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <p class="step-text"><strong><?= tx('Na chegada.', 'On arrival.', 'A la llegada.') ?></strong> <?= tx('A recepção localiza o agendamento e dá continuidade ao atendimento sem recadastrar informações desnecessariamente.', 'The front desk finds the appointment and continues care without unnecessarily re-entering information.', 'La recepción localiza la cita y da continuidad a la atención sin recapturar información innecesariamente.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <p class="step-text"><strong><?= tx('Durante o atendimento.', 'During the visit.', 'Durante la atención.') ?></strong> <?= tx('O profissional registra evolução no prontuário eletrônico, consulta dados e produz laudos complementares no mesmo ambiente.', 'The professional records progress in the electronic medical record, checks data and produces complementary reports in the same environment.', 'El profesional registra la evolución en la historia clínica electrónica, consulta datos y produce informes complementarios en el mismo entorno.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">4</div>
        <p class="step-text"><strong><?= tx('Depois da consulta.', 'After the visit.', 'Después de la consulta.') ?></strong> <?= tx('Procedimentos, regras de convênios, faturamento TISS e repasses médicos seguem conectados à produção realizada.', 'Procedures, payer rules, TISS billing and physician payouts stay connected to the production performed.', 'Procedimientos, reglas de convenios, facturación TISS y repartos médicos siguen conectados a la producción realizada.') ?></p>
      </div>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></h2>
    <div class="table-wrap">
      <table class="resource-table">
        <thead><tr><th><?= tx('Frente da operação', 'Area of operation', 'Frente de la operación') ?></th><th><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></th></tr></thead>
        <tbody>
          <tr>
            <td><?= tx('Agenda e relacionamento', 'Scheduling and relationship', 'Agenda y relación') ?></td>
            <td><ul>
              <li><?= tx('Agendamento e organização da rotina do consultório.', 'Scheduling and organization of the practice\'s routine.', 'Agenda y organización de la rutina del consultorio.') ?></li>
              <li><?= tx('Confirmação por WhatsApp com chatbot e assistência de IA, quando contratada.', 'WhatsApp confirmation with chatbot and AI assistance, when contracted.', 'Confirmación por WhatsApp con chatbot y asistencia de IA, cuando se contrata.') ?></li>
              <li><?= tx('Chamada de pacientes por Smart TV, quando contratada.', 'Patient calling via Smart TV, when contracted.', 'Llamada de pacientes por Smart TV, cuando se contrata.') ?></li>
            </ul></td>
          </tr>
          <tr>
            <td><?= tx('Atendimento e prontuário', 'Care and medical records', 'Atención e historia clínica') ?></td>
            <td><ul>
              <li><?= tx('Atendimento pela recepção ou diretamente pelo consultório.', 'Check-in via front desk or directly at the exam room.', 'Atención por recepción o directamente por el consultorio.') ?></li>
              <li><?= tx('Prontuário eletrônico orientado pelo método SOAP.', 'Electronic medical record based on the SOAP method.', 'Historia clínica electrónica orientada por el método SOAP.') ?></li>
              <li><?= tx('Laudos complementares para diferentes especialidades.', 'Complementary reports for different specialties.', 'Informes complementarios para diferentes especialidades.') ?></li>
            </ul></td>
          </tr>
          <tr>
            <td><?= tx('Faturamento e repasses', 'Billing and payouts', 'Facturación y repartos') ?></td>
            <td><ul>
              <li><?= tx('Faturamento no padrão TISS.', 'TISS-standard billing.', 'Facturación en el estándar TISS.') ?></li>
              <li><?= tx('Base TUSS de procedimentos, serviços, materiais e medicamentos.', 'TUSS database of procedures, services, materials and medications.', 'Base TUSS de procedimientos, servicios, materiales y medicamentos.') ?></li>
              <li><?= tx('Regras de convênios, preços e repasses médicos.', 'Payer rules, pricing and physician payouts.', 'Reglas de convenios, precios y repartos médicos.') ?></li>
            </ul></td>
          </tr>
          <tr>
            <td><?= tx('Acesso e infraestrutura', 'Access and infrastructure', 'Acceso e infraestructura') ?></td>
            <td><ul>
              <li><?= tx('Acesso multiplataforma conforme os requisitos da solução.', 'Multi-platform access according to the solution\'s requirements.', 'Acceso multiplataforma según los requisitos de la solución.') ?></li>
              <li><?= tx('Infraestrutura em nuvem no Brasil, configuração e manutenção.', 'Cloud infrastructure in Brazil, configuration and maintenance.', 'Infraestructura en la nube en Brasil, configuración y mantenimiento.') ?></li>
              <li><?= tx('Atualizações, backups e nível de serviço conforme contrato.', 'Updates, backups and service level according to contract.', 'Actualizaciones, copias de seguridad y nivel de servicio según contrato.') ?></li>
            </ul></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="cta-bar">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Comece pelo que mais toma tempo da sua equipe', 'Start with what takes the most time from your team', 'Comience por lo que más tiempo le quita a su equipo') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('Agenda, prontuário ou faturamento: a solução pode ser dimensionada de acordo com a prioridade atual do consultório.', 'Scheduling, medical records or billing: the solution can be sized according to the practice\'s current priority.', 'Agenda, historia clínica o facturación: la solución puede dimensionarse según la prioridad actual del consultorio.') ?></p>
    <div class="cta-bar-actions">
      <a href="/contato" class="btn-primary"><?= tx('Solicitar uma demonstração para consultórios', 'Request a demo for private practices', 'Solicitar una demostración para consultorios') ?></a>
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
