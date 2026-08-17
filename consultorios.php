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

$_benefits = [
    ['calendarCheck', tx('Agenda mais previsível', 'A more predictable schedule', 'Agenda más previsible'), tx('Organize horários e use confirmações por WhatsApp para diminuir o esforço manual e apoiar a ocupação.', 'Organize time slots and use WhatsApp confirmations to reduce manual effort and support occupancy.', 'Organice horarios y use confirmaciones por WhatsApp para disminuir el esfuerzo manual y apoyar la ocupación.')],
    ['heartPulse', tx('Atendimento com continuidade', 'Care with continuity', 'Atención con continuidad'), tx('Recepção, consultório, prontuário e laudos compartilham a mesma informação.', 'Front desk, exam room, medical records and reports share the same information.', 'Recepción, consultorio, historia clínica e informes comparten la misma información.')],
    ['receiptCheck', tx('Faturamento mais organizado', 'More organized billing', 'Facturación más organizada'), tx('TISS, TUSS, regras de convênios e repasses ficam vinculados à produção.', 'TISS, TUSS, payer rules and payouts stay linked to production.', 'TISS, TUSS, reglas de convenios y repartos quedan vinculados a la producción.')],
    ['cloud', tx('Infraestrutura administrada', 'Managed infrastructure', 'Infraestructura administrada'), tx('Ambiente em nuvem, manutenção, atualizações, backups e SLA conforme a modalidade contratada.', 'Cloud environment, maintenance, updates, backups and SLA according to the contracted plan.', 'Entorno en la nube, mantenimiento, actualizaciones, copias de seguridad y SLA según la modalidad contratada.')],
];

$_journey = [
    [tx('Antes da consulta', 'Before the visit', 'Antes de la consulta'), tx('A agenda organiza os horários e a confirmação via WhatsApp ajuda a reduzir contatos manuais e faltas evitáveis.', 'The schedule organizes time slots and WhatsApp confirmation helps reduce manual contacts and avoidable no-shows.', 'La agenda organiza los horarios y la confirmación por WhatsApp ayuda a reducir contactos manuales y ausencias evitables.')],
    [tx('Na chegada', 'On arrival', 'A la llegada'), tx('A recepção localiza o agendamento e dá continuidade ao atendimento sem recadastrar informações desnecessariamente.', 'The front desk finds the appointment and continues care without unnecessarily re-entering information.', 'La recepción localiza la cita y da continuidad a la atención sin recapturar información innecesariamente.')],
    [tx('Durante o atendimento', 'During the visit', 'Durante la atención'), tx('O profissional registra evolução no prontuário eletrônico, consulta dados e produz laudos complementares no mesmo ambiente.', 'The professional records progress in the electronic medical record, checks data and produces complementary reports in the same environment.', 'El profesional registra la evolución en la historia clínica electrónica, consulta datos y produce informes complementarios en el mismo entorno.')],
    [tx('Depois da consulta', 'After the visit', 'Después de la consulta'), tx('Procedimentos, regras de convênios, faturamento TISS e repasses médicos seguem conectados à produção realizada.', 'Procedures, payer rules, TISS billing and physician payouts stay connected to the production performed.', 'Procedimientos, reglas de convenios, facturación TISS y repartos médicos siguen conectados a la producción realizada.')],
];

$_resources = [
    [tx('Agenda e relacionamento', 'Scheduling and relationship', 'Agenda y relación'), [
        tx('Agendamento e organização da rotina do consultório.', 'Scheduling and organization of the practice\'s routine.', 'Agenda y organización de la rutina del consultorio.'),
        tx('Confirmação por WhatsApp com chatbot e assistência de IA, quando contratada.', 'WhatsApp confirmation with chatbot and AI assistance, when contracted.', 'Confirmación por WhatsApp con chatbot y asistencia de IA, cuando se contrata.'),
        tx('Chamada de pacientes por Smart TV, quando contratada.', 'Patient calling via Smart TV, when contracted.', 'Llamada de pacientes por Smart TV, cuando se contrata.'),
    ]],
    [tx('Atendimento e prontuário', 'Care and medical records', 'Atención e historia clínica'), [
        tx('Atendimento pela recepção ou diretamente pelo consultório.', 'Check-in via front desk or directly at the exam room.', 'Atención por recepción o directamente por el consultorio.'),
        tx('Prontuário eletrônico orientado pelo método SOAP.', 'Electronic medical record based on the SOAP method.', 'Historia clínica electrónica orientada por el método SOAP.'),
        tx('Laudos complementares para diferentes especialidades.', 'Complementary reports for different specialties.', 'Informes complementarios para diferentes especialidades.'),
    ]],
    [tx('Faturamento e repasses', 'Billing and payouts', 'Facturación y repartos'), [
        tx('Faturamento no padrão TISS.', 'TISS-standard billing.', 'Facturación en el estándar TISS.'),
        tx('Base TUSS de procedimentos, serviços, materiais e medicamentos.', 'TUSS database of procedures, services, materials and medications.', 'Base TUSS de procedimientos, servicios, materiales y medicamentos.'),
        tx('Regras de convênios, preços e repasses médicos.', 'Payer rules, pricing and physician payouts.', 'Reglas de convenios, precios y repartos médicos.'),
    ]],
    [tx('Acesso e infraestrutura', 'Access and infrastructure', 'Acceso e infraestructura'), [
        tx('Acesso multiplataforma conforme os requisitos da solução.', 'Multi-platform access according to the solution\'s requirements.', 'Acceso multiplataforma según los requisitos de la solución.'),
        tx('Infraestrutura em nuvem no Brasil, configuração e manutenção.', 'Cloud infrastructure in Brazil, configuration and maintenance.', 'Infraestructura en la nube en Brasil, configuración y mantenimiento.'),
        tx('Atualizações, backups e nível de serviço conforme contrato.', 'Updates, backups and service level according to contract.', 'Actualizaciones, copias de seguridad y nivel de servicio según contrato.'),
    ]],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Sistema para Consultórios: Agenda, Prontuário e TISS | TechSallus', 'System for Private Practices: Scheduling, Records and Billing | TechSallus', 'Sistema para Consultorios: Agenda, Historia Clínica y Facturación | TechSallus') ?></title>
<meta name="description" content="<?= tx('Organize agenda, confirmação, atendimento, prontuário, laudos, faturamento TISS, regras de convênios e repasses em uma solução modular.', 'Organize scheduling, confirmations, care, medical records, reports, TISS billing, payer rules and payouts in one modular solution.', 'Organice agenda, confirmación, atención, historia clínica, informes, facturación TISS, reglas de convenios y repartos en una solución modular.') ?>"/>
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

<section class="subhero">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Rotina simples, informação conectada', 'A simple routine, connected information', 'Rutina simple, información conectada') ?></span>
    <h1 class="reveal"><?= tx('Mais tempo para atender. Menos tarefas manuais entre a agenda e o faturamento.', 'More time to see patients. Fewer manual tasks between scheduling and billing.', 'Más tiempo para atender. Menos tareas manuales entre la agenda y la facturación.') ?></h1>
    <p class="reveal"><?= tx('Reúna agendamento, atendimento, prontuário, laudos, faturamento e repasses em um fluxo único, com recursos que acompanham a rotina do consultório sem adicionar complexidade desnecessária.', 'Bring scheduling, care, medical records, reports, billing and payouts together in a single flow, with features that follow the practice\'s routine without adding unnecessary complexity.', 'Reúna agenda, atención, historia clínica, informes, facturación y repartos en un flujo único, con recursos que acompañan la rutina del consultorio sin agregar complejidad innecesaria.') ?></p>
    <div class="hero-ctas reveal"><a href="/contato" class="btn btn-primary"><?= tx('Quero organizar meu consultório', 'I want to organize my practice', 'Quiero organizar mi consultorio') ?></a></div>
    <div class="hero-tag reveal"><?= tx('Solução web, modular e dimensionada para a realidade da sua operação', 'A web-based, modular solution sized to fit your operation\'s reality', 'Solución web, modular y dimensionada para la realidad de su operación') ?></div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('O problema real', 'The real problem', 'El problema real') ?></span>
      <h2 class="reveal"><?= tx('Seu consultório precisa de tempo, não de mais controles paralelos.', 'Your practice needs time, not more parallel controls.', 'Su consultorio necesita tiempo, no más controles paralelos.') ?></h2>
      <p class="reveal"><?= tx('Confirmar consultas uma a uma, procurar informações em sistemas diferentes e revisar o faturamento no fim do dia consome horas que poderiam estar voltadas ao paciente.', 'Confirming appointments one by one, searching for information across different systems and reviewing billing at the end of the day consumes hours that could be spent on the patient.', 'Confirmar consultas una por una, buscar información en sistemas diferentes y revisar la facturación al final del día consume horas que podrían dedicarse al paciente.') ?></p>
    </div>
    <div class="grid-4">
      <?php foreach ($_benefits as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Como funciona na prática', 'How it works in practice', 'Cómo funciona en la práctica') ?></span>
      <h2 class="reveal"><?= tx('Uma jornada mais leve para a equipe e para o paciente.', 'A lighter journey for the team and the patient.', 'Un recorrido más liviano para el equipo y el paciente.') ?></h2>
    </div>
    <div class="journey">
      <?php foreach ($_journey as $_n => [$_t, $_d]): ?>
        <div class="j-step reveal"><div class="j-num"><?= $_n + 1 ?></div><div class="j-body"><h3><?= $_t ?></h3><p><?= $_d ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('Recursos disponíveis', 'Available features', 'Recursos disponibles') ?></span>
      <h2 class="reveal"><?= tx('Tudo o que o consultório precisa, em um único ambiente.', 'Everything the practice needs, in a single environment.', 'Todo lo que el consultorio necesita, en un solo entorno.') ?></h2>
    </div>
    <div class="resource-table">
      <?php foreach ($_resources as [$_h, $_items]): ?>
        <div class="resource-row reveal"><div class="rr-head"><?= $_h ?></div><div class="rr-body"><ul><?php foreach ($_items as $_it): ?><li><?= $_it ?></li><?php endforeach; ?></ul></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section band-dark" id="cta">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Comece pelo que mais toma tempo da sua equipe', 'Start with what takes the most time from your team', 'Comience por lo que más tiempo le quita a su equipo') ?></span>
    <h2 class="reveal"><?= tx('Agenda, prontuário ou faturamento: você escolhe por onde começar.', 'Scheduling, medical records or billing: you choose where to start.', 'Agenda, historia clínica o facturación: usted elige por dónde empezar.') ?></h2>
    <p class="reveal"><?= tx('A solução pode ser dimensionada de acordo com a prioridade atual do consultório.', 'The solution can be sized according to the practice\'s current priority.', 'La solución puede dimensionarse según la prioridad actual del consultorio.') ?></p>
    <a href="/contato" class="btn btn-primary reveal"><?= tx('Solicitar uma demonstração para consultórios', 'Request a demo for private practices', 'Solicitar una demostración para consultorios') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731f"></script>
</body>
</html>
