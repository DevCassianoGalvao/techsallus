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

$_problems = [
    ['calendarX', tx('Agenda com faltas', 'No-show-prone scheduling', 'Agenda con ausencias'), tx('Confirmações manuais consomem a equipe e horários ociosos reduzem a capacidade de atendimento.', 'Manual confirmations tie up staff and idle time slots shrink care capacity.', 'Las confirmaciones manuales consumen al equipo y los horarios vacíos reducen la capacidad de atención.')],
    ['clock', tx('Filas e demora', 'Queues and delays', 'Filas y demoras'), tx('Recepção, triagem, consultório e exames desconectados criam espera e pouca visibilidade sobre o fluxo.', 'Disconnected front desk, triage, exam rooms and testing create waiting time and little visibility into flow.', 'Recepción, triaje, consultorio y exámenes desconectados generan espera y poca visibilidad del flujo.')],
    ['receiptAlert', tx('Faturamento vulnerável', 'Vulnerable billing', 'Facturación vulnerable'), tx('Guias inconsistentes, regras dispersas e glosas aumentam retrabalho e atrasam o ciclo de receita.', 'Inconsistent claims, scattered payer rules and denials increase rework and delay the revenue cycle.', 'Guías inconsistentes, reglas dispersas y glosas aumentan el retrabajo y retrasan el ciclo de ingresos.')],
    ['layers', tx('Informação fragmentada', 'Fragmented information', 'Información fragmentada'), tx('Dados clínicos e operacionais em ambientes separados dificultam a continuidade do cuidado.', 'Clinical and operational data living in separate systems make care continuity harder.', 'Datos clínicos y operativos en entornos separados dificultan la continuidad del cuidado.')],
    ['box', tx('Perdas em materiais e medicamentos', 'Losses in supplies and medication', 'Pérdidas en materiales y medicamentos'), tx('Compras, estoque, farmácia e consumo sem integração reduzem rastreabilidade e controle.', 'Disconnected purchasing, inventory, pharmacy and consumption reduce traceability and control.', 'Compras, inventario, farmacia y consumo sin integración reducen trazabilidad y control.')],
    ['chartDown', tx('Decisões sem visão de custo', 'Decisions without cost visibility', 'Decisiones sin visión de costos'), tx('Faturamento mostra movimento, mas não revela margem, ponto de equilíbrio ou rentabilidade.', 'Billing shows volume, but not margin, break-even point or profitability.', 'La facturación muestra movimiento, pero no revela margen, punto de equilibrio ni rentabilidad.')],
];

$_benefits = [
    ['calendarCheck', tx('Aproveite melhor a agenda', 'Make better use of your schedule', 'Aproveche mejor la agenda'), tx('Agendamento integrado e confirmações por WhatsApp ajudam a reduzir esforço manual e favorecem uma ocupação mais previsível.', 'Integrated scheduling and WhatsApp confirmations reduce manual effort and support more predictable occupancy.', 'La agenda integrada y las confirmaciones por WhatsApp ayudan a reducir el esfuerzo manual y favorecen una ocupación más previsible.')],
    ['zap', tx('Acelere a chegada ao atendimento', 'Speed up arrival to care', 'Acelere la llegada a la atención'), tx('Totem, recepção, distribuição de senhas, chamadas e acolhimento trabalham no mesmo fluxo.', 'Kiosk, front desk, ticket distribution, calling and intake all work within the same flow.', 'Totem, recepción, distribución de turnos, llamadas y acogida trabajan en el mismo flujo.')],
    ['heartPulse', tx('Dê continuidade ao cuidado', 'Give continuity to care', 'Dé continuidad al cuidado'), tx('Prontuário, laudos, imagens, prescrições e dados assistenciais acompanham a jornada do paciente.', 'Medical records, reports, images, prescriptions and clinical data follow the patient\'s journey.', 'Historia clínica, informes, imágenes, prescripciones y datos asistenciales acompañan el recorrido del paciente.')],
    ['receiptCheck', tx('Fature com mais consistência', 'Bill with more consistency', 'Facture con más consistencia'), tx('TISS, TUSS, regras de convênios, guias, glosas e repasses dão mais controle ao ciclo de receita.', 'TISS, TUSS, payer rules, claims, denials and physician payouts give more control to the revenue cycle.', 'TISS, TUSS, reglas de convenios, guías, glosas y repartos dan más control al ciclo de ingresos.')],
    ['boxCheck', tx('Controle suprimentos e medicamentos', 'Control supplies and medication', 'Controle insumos y medicamentos'), tx('Compras, estoque, farmácia, lotes e dispensação se conectam à operação assistencial.', 'Purchasing, inventory, pharmacy, batches and dispensing connect to clinical operations.', 'Compras, inventario, farmacia, lotes y dispensación se conectan a la operación asistencial.')],
    ['chartUp', tx('Decida com visão de custo e margem', 'Decide with cost and margin visibility', 'Decida con visión de costo y margen'), tx('Financeiro, BI e Apure Custos transformam dados da operação em informação gerencial.', 'Finance, BI and Apure Custos turn operational data into management information.', 'Financiero, BI y Apure Custos transforman los datos de la operación en información gerencial.')],
];

$_journey = [
    [tx('Agendar e confirmar', 'Schedule and confirm', 'Agendar y confirmar'), tx('Organize a agenda e automatize confirmações por WhatsApp, conforme o escopo contratado.', 'Organize the schedule and automate WhatsApp confirmations, according to the contracted scope.', 'Organice la agenda y automatice confirmaciones por WhatsApp, según el alcance contratado.')],
    [tx('Receber e direcionar', 'Receive and direct', 'Recibir y dirigir'), tx('Recepção, totem, senhas, chamadas e triagem ajudam o paciente a avançar com mais fluidez.', 'Front desk, kiosk, tickets, calling and triage help the patient move forward more smoothly.', 'Recepción, totem, turnos, llamadas y triaje ayudan al paciente a avanzar con más fluidez.')],
    [tx('Atender e registrar', 'Care and record', 'Atender y registrar'), tx('Prontuário, evolução, laudos e protocolos ficam disponíveis no ponto de cuidado.', 'Medical records, progress notes, reports and protocols are available at the point of care.', 'Historia clínica, evolución, informes y protocolos están disponibles en el punto de cuidado.')],
    [tx('Examinar, prescrever e dispensar', 'Examine, prescribe and dispense', 'Examinar, prescribir y dispensar'), tx('Imagens, prescrições, farmácia, estoque e rotinas assistenciais se conectam à jornada.', 'Images, prescriptions, pharmacy, inventory and clinical routines connect to the journey.', 'Imágenes, prescripciones, farmacia, inventario y rutinas asistenciales se conectan al recorrido.')],
    [tx('Faturar e acompanhar', 'Bill and track', 'Facturar y acompañar'), tx('Guias, convênios, TISS, glosas e repasses sustentam um ciclo mais controlado.', 'Claims, payers, TISS, denials and payouts sustain a more controlled cycle.', 'Guías, convenios, TISS, glosas y repartos sostienen un ciclo más controlado.')],
    [tx('Analisar e decidir', 'Analyze and decide', 'Analizar y decidir'), tx('Indicadores e custos mostram onde a operação gera resultado e onde precisa agir.', 'Indicators and costs show where the operation generates results and where it needs to act.', 'Indicadores y costos muestran dónde la operación genera resultado y dónde necesita actuar.')],
];

$_segments = [
    [tx('Consultórios', 'Private practices', 'Consultorios'), tx('Mais tempo para atender e menos tarefas manuais entre agenda, prontuário, laudos e faturamento.', 'More time to see patients and fewer manual tasks between scheduling, records, reports and billing.', 'Más tiempo para atender y menos tareas manuales entre agenda, historia clínica, informes y facturación.'), tx('Conhecer solução para consultórios', 'See the solution for private practices', 'Conocer la solución para consultorios'), '/consultorios'],
    [tx('Clínicas e policlínicas', 'Clinics and polyclinics', 'Clínicas y policlínicas'), tx('Mais fluidez para alto volume de pacientes, procedimentos, exames, estoque, farmácia e gestão.', 'More fluidity for high patient volume, procedures, exams, inventory, pharmacy and management.', 'Más fluidez para alto volumen de pacientes, procedimientos, exámenes, inventario, farmacia y gestión.'), tx('Conhecer solução para clínicas', 'See the solution for clinics', 'Conocer la solución para clínicas'), '/clinicas'],
    [tx('Hospitais e pronto atendimento', 'Hospitals and urgent care', 'Hospitales y urgencias'), tx('Coordenação de urgência, internação, centro cirúrgico, prescrição, beira leito e ciclo de receita.', 'Coordination of urgency, admission, surgical center, prescriptions, bedside care and revenue cycle.', 'Coordinación de urgencia, internación, centro quirúrgico, prescripción, cabecera y ciclo de ingresos.'), tx('Conhecer solução hospitalar', 'See the hospital solution', 'Conocer la solución hospitalaria'), '/hospitais'],
    [tx('Gestão de custos', 'Cost management', 'Gestión de costos'), tx('Visão de custo, margem e resultado para priorizar melhorias e tomar decisões com mais segurança.', 'Cost, margin and results visibility to prioritize improvements and make decisions with more confidence.', 'Visión de costo, margen y resultado para priorizar mejoras y tomar decisiones con más seguridad.'), tx('Conhecer o Apure Custos', 'See Apure Custos', 'Conocer Apure Custos'), '/apure-custos'],
];

$_apure = [
    ['chartUp', tx('Mensure custos', 'Measure costs', 'Mida costos'), tx('Conheça o custo real da operação e acompanhe sua evolução.', 'Know the real cost of the operation and track its evolution.', 'Conozca el costo real de la operación y siga su evolución.')],
    ['chartDown', tx('Enxergue o ponto de equilíbrio', 'See your break-even point', 'Visualice el punto de equilibrio'), tx('Entenda quanto cada unidade, serviço ou especialidade precisa produzir para se sustentar.', 'Understand how much each unit, service or specialty needs to produce to sustain itself.', 'Entienda cuánto necesita producir cada unidad, servicio o especialidad para sostenerse.')],
    ['layers', tx('Compare resultados', 'Compare results', 'Compare resultados'), tx('Analise especialidades, pacientes, unidades, produtos e procedimentos sob a mesma lógica.', 'Analyze specialties, patients, units, products and procedures under the same logic.', 'Analice especialidades, pacientes, unidades, productos y procedimientos bajo la misma lógica.')],
    ['receiptCheck', tx('Avalie investimentos', 'Evaluate investments', 'Evalúe inversiones'), tx('Use dados de custo e resultado para apoiar expansão, negociação e priorização.', 'Use cost and results data to support expansion, negotiation and prioritization.', 'Use datos de costo y resultado para apoyar expansión, negociación y priorización.')],
];

$_badges = ['Prontuário SOAP', 'TISS / TUSS', 'PACS / DICOM', 'HL7', 'HIS / LIS', tx('Nuvem no Brasil', 'Cloud in Brazil', 'Nube en Brasil'), tx('Backups & SLA', 'Backups & SLA', 'Backups y SLA'), tx('Painéis Power BI', 'Power BI dashboards', 'Paneles Power BI')];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Sistema de Gestão para Clínicas e Hospitais | TechSallus', 'Management System for Clinics and Hospitals | TechSallus', 'Sistema de Gestión para Clínicas y Hospitales | TechSallus') ?></title>
<meta name="description" content="<?= tx('Reduza faltas, agilize o atendimento e ganhe mais controle sobre faturamento, glosas, estoque e custos com uma gestão integrada para instituições de saúde.', 'Reduce no-shows, speed up care and gain more control over billing, denials, inventory and costs with integrated management for health institutions.', 'Reduzca ausencias, agilice la atención y gane más control sobre facturación, glosas, inventario y costos con una gestión integrada para instituciones de salud.') ?>"/>
<link rel="icon" type="image/png" href="/assets/img/favicon.png"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link rel="stylesheet" href="/assets/css/main.css?v=20260731b"/>
<?= getScripts('head') ?>
</head>
<body>
<?= getScripts('body') ?>

<?php include __DIR__ . '/_partials/nav.php'; ?>

<main>

<section id="hero">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <span class="eyebrow reveal"><?= tx('Gestão integrada para instituições de saúde', 'Integrated management for health institutions', 'Gestión integrada para instituciones de salud') ?></span>
      <h1 class="reveal"><?= tx('Menos faltas. Atendimento', 'Fewer no-shows. Care that\'s', 'Menos ausencias. Atención') ?> <span><?= tx('mais ágil', 'faster', 'más ágil') ?></span>. <?= tx('Mais controle sobre o faturamento.', 'More control over billing.', 'Más control sobre la facturación.') ?></h1>
      <p class="reveal"><?= tx('A TechSallus conecta agenda, recepção, assistência, faturamento e gestão para reduzir retrabalho, melhorar a jornada do paciente e dar mais previsibilidade à operação.', 'TechSallus connects scheduling, front desk, care, billing and management to reduce rework, improve the patient journey and bring more predictability to the operation.', 'TechSallus conecta agenda, recepción, asistencia, facturación y gestión para reducir el retrabajo, mejorar el recorrido del paciente y dar más previsibilidad a la operación.') ?></p>
      <div class="hero-ctas reveal">
        <a href="/contato" class="btn btn-primary"><?= tx('Quero melhorar minha operação', 'I want to improve my operation', 'Quiero mejorar mi operación') ?></a>
        <a href="#segmentos" class="btn btn-ghost"><?= tx('Conhecer as soluções', 'See the solutions', 'Conocer las soluciones') ?></a>
      </div>
      <div class="hero-tag reveal"><?= tx('Solução modular para consultórios, clínicas, policlínicas, hospitais e pronto atendimento', 'A modular solution for private practices, clinics, polyclinics, hospitals and urgent care', 'Solución modular para consultorios, clínicas, policlínicas, hospitales y urgencias') ?></div>
    </div>
    <div class="hero-visual reveal">
      <div class="dash-card">
        <div class="dash-dots"><span></span><span></span><span></span></div>
        <div class="dash-title"><?= tx('OCUPAÇÃO DA AGENDA HOJE', 'TODAY\'S SCHEDULE OCCUPANCY', 'OCUPACIÓN DE LA AGENDA HOY') ?></div>
        <div class="dash-bar-label"><span><?= tx('Consultas confirmadas', 'Confirmed visits', 'Consultas confirmadas') ?></span><span>92%</span></div>
        <div class="dash-bar-track"><div class="dash-bar-fill" id="dashBar"></div></div>
        <div class="dash-row"><span class="dash-dot" style="background:#22a35a"></span><span class="dash-row-text"><?= tx('Consulta com Dra. Marcondes', 'Visit with Dr. Marcondes', 'Consulta con la Dra. Marcondes') ?></span><span class="dash-row-time">09:00</span></div>
        <div class="dash-row"><span class="dash-dot" style="background:var(--orange)"></span><span class="dash-row-text"><?= tx('Consulta com Dr. Almeida', 'Visit with Dr. Almeida', 'Consulta con el Dr. Almeida') ?></span><span class="dash-row-time">09:30</span></div>
        <div class="dash-row"><span class="dash-dot" style="background:#22a35a"></span><span class="dash-row-text"><?= tx('Retorno com Dra. Souza', 'Follow-up with Dr. Souza', 'Retorno con la Dra. Souza') ?></span><span class="dash-row-time">10:00</span></div>
      </div>
      <div class="float-card fc-top">
        <div class="fc-ico" style="background:#fff2e6"><svg class="icon" style="stroke:var(--orange);width:18px;height:18px" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8M21 7v6h-6"/></svg></div>
        <div><div class="fc-label"><?= tx('Faturamento TISS', 'TISS billing', 'Facturación TISS') ?></div><div class="fc-value">+18%</div></div>
      </div>
      <div class="float-card fc-bottom">
        <div class="fc-ico" style="background:#e9f8ee"><svg class="icon" style="stroke:#22a35a;width:18px;height:18px" viewBox="0 0 24 24"><path d="M3 7l6 6 4-4 8 8M21 17v-6h-6"/></svg></div>
        <div><div class="fc-label"><?= tx('Glosas', 'Denials', 'Glosas') ?></div><div class="fc-value">-32%</div></div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="problemas">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('O que está travando a operação', 'What\'s holding the operation back', 'Qué está frenando la operación') ?></span>
      <h2 class="reveal"><?= tx('Seu maior custo pode estar entre um setor e outro.', 'Your biggest cost may be sitting between departments.', 'Su mayor costo puede estar entre un sector y otro.') ?></h2>
      <p class="reveal"><?= tx('Quando a informação não acompanha o paciente, a equipe repete tarefas, a espera aumenta e o faturamento fica mais vulnerável.', 'When information does not follow the patient, staff repeat tasks, waiting time grows and billing becomes more vulnerable.', 'Cuando la información no acompaña al paciente, el equipo repite tareas, la espera aumenta y la facturación queda más vulnerable.') ?></p>
    </div>
    <div class="grid-6">
      <?php foreach ($_problems as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Como a TechSallus ajuda', 'How TechSallus helps', 'Cómo ayuda TechSallus') ?></span>
      <h2 class="reveal"><?= tx('Mais eficiência onde o paciente sente. Mais controle onde a gestão decide.', 'More efficiency where the patient feels it. More control where management decides.', 'Más eficiencia donde el paciente la siente. Más control donde la gestión decide.') ?></h2>
      <p class="reveal"><?= tx('A tecnologia entra para resolver a rotina: ocupar melhor a agenda, fazer o paciente avançar com fluidez, dar continuidade à informação e proteger o resultado da instituição.', 'Technology steps in to solve the day-to-day: filling the schedule better, moving the patient forward smoothly, giving continuity to information and protecting the institution\'s results.', 'La tecnología entra para resolver la rutina: ocupar mejor la agenda, hacer que el paciente avance con fluidez, dar continuidad a la información y proteger el resultado de la institución.') ?></p>
    </div>
    <div class="grid-6">
      <?php foreach ($_benefits as [$_i, $_t, $_d]): ?>
        <div class="card reveal"><div class="icon-badge"><?= ic($_i) ?></div><h3><?= $_t ?></h3><p><?= $_d ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('O fluxo conectado', 'The connected flow', 'El flujo conectado') ?></span>
      <h2 class="reveal"><?= tx('Da marcação ao resultado, cada etapa conversa com a próxima.', 'From booking to results, every step talks to the next.', 'De la reserva al resultado, cada etapa conversa con la siguiente.') ?></h2>
      <p class="reveal"><?= tx('Em vez de departamentos trabalhando como ilhas, a TechSallus conecta a jornada do paciente às rotinas assistenciais, administrativas e financeiras.', 'Instead of departments working as isolated islands, TechSallus connects the patient journey to clinical, administrative and financial routines.', 'En vez de departamentos trabajando como islas, TechSallus conecta el recorrido del paciente a las rutinas asistenciales, administrativas y financieras.') ?></p>
    </div>
    <div class="journey">
      <?php foreach ($_journey as $_n => [$_t, $_d]): ?>
        <div class="j-step reveal"><div class="j-num"><?= $_n + 1 ?></div><div class="j-body"><h3><?= $_t ?></h3><p><?= $_d ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section tint" id="segmentos">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('Para cada tipo de operação', 'For every type of operation', 'Para cada tipo de operación') ?></span>
      <h2 class="reveal"><?= tx('Uma solução para cada nível de complexidade.', 'A solution for every level of complexity.', 'Una solución para cada nivel de complejidad.') ?></h2>
      <p class="reveal"><?= tx('Comece pelo que sua instituição precisa agora e amplie o escopo sem montar um quebra-cabeça de sistemas e fornecedores.', 'Start with what your institution needs now and expand the scope without piecing together a puzzle of systems and vendors.', 'Comience por lo que su institución necesita ahora y amplíe el alcance sin armar un rompecabezas de sistemas y proveedores.') ?></p>
    </div>
    <div class="grid-4">
      <?php foreach ($_segments as $_n => [$_t, $_d, $_c, $_href]): ?>
        <a href="<?= htmlspecialchars($_href) ?>" class="seg-card reveal">
          <div class="seg-num">0<?= $_n + 1 ?></div>
          <h3><?= $_t ?></h3>
          <p><?= $_d ?></p>
          <span class="seg-cta"><?= $_c ?><?= ic('arrowRight') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <span class="eyebrow reveal"><?= tx('Cresça no seu ritmo', 'Grow at your own pace', 'Crezca a su ritmo') ?></span>
      <h2 class="reveal"><?= tx('Modular por necessidade. Integrada por princípio.', 'Modular by need. Integrated by principle.', 'Modular por necesidad. Integrada por principio.') ?></h2>
      <p class="reveal"><?= tx('A modularidade reduz investimento desnecessário; a integração evita que cada novo recurso se transforme em mais uma ilha de informação.', 'Modularity avoids unnecessary investment; integration keeps every new capability from becoming another island of information.', 'La modularidad reduce la inversión innecesaria; la integración evita que cada nuevo recurso se convierta en otra isla de información.') ?></p>
    </div>
    <div class="mod-grid">
      <div class="mod-col reveal">
        <h3><?= tx('Comece pela prioridade de hoje', 'Start with today\'s priority', 'Comience por la prioridad de hoy') ?></h3>
        <ul>
          <li><?= tx('Agenda e absenteísmo', 'Scheduling and no-shows', 'Agenda y ausentismo') ?></li>
          <li><?= tx('Fluxo de atendimento', 'Care flow', 'Flujo de atención') ?></li>
          <li><?= tx('Faturamento e glosas', 'Billing and denials', 'Facturación y glosas') ?></li>
          <li><?= tx('Estoque e farmácia', 'Inventory and pharmacy', 'Inventario y farmacia') ?></li>
        </ul>
      </div>
      <div class="mod-col filled reveal">
        <h3><?= tx('Evolua para uma gestão completa', 'Evolve to full management', 'Evolucione a una gestión completa') ?></h3>
        <ul>
          <li><?= tx('Prontuário e assistência', 'Medical records and care', 'Historia clínica y asistencia') ?></li>
          <li><?= tx('Financeiro e repasses', 'Finance and payouts', 'Financiero y repartos') ?></li>
          <li><?= tx('BI e indicadores', 'BI and dashboards', 'BI e indicadores') ?></li>
          <li><?= tx('Custos, margem e resultado', 'Costs, margin and results', 'Costos, margen y resultado') ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section tint">
  <div class="wrap apure-wrap">
    <div>
      <span class="eyebrow reveal">TechSallus Gestão | Apure Custos</span>
      <h2 class="reveal" style="font-size:clamp(30px,3.2vw,40px);margin-top:18px"><?= tx('Faturamento mostra volume. Custos mostram resultado.', 'Billing shows volume. Costs show results.', 'La facturación muestra volumen. Los costos muestran resultado.') ?></h2>
      <p class="reveal" style="color:var(--muted);font-size:17px;margin-top:16px;line-height:1.65"><?= tx('O Apure Custos reúne produção, materiais, pessoal, depreciação, custos gerais e repasses médicos para revelar custos e resultados por especialidade, paciente, unidade, produto e procedimento.', 'Apure Custos brings together production, materials, personnel, depreciation, overhead and physician payouts to reveal costs and results by specialty, patient, unit, product and procedure.', 'Apure Custos reúne producción, materiales, personal, depreciación, costos generales y repartos médicos para revelar costos y resultados por especialidad, paciente, unidad, producto y procedimiento.') ?></p>
      <div class="apure-list">
        <?php foreach ($_apure as [$_i, $_t, $_d]): ?>
          <div class="apure-item reveal"><div class="icon-badge"><?= ic($_i) ?></div><div><h4><?= $_t ?></h4><p><?= $_d ?></p></div></div>
        <?php endforeach; ?>
      </div>
      <a href="/apure-custos" class="btn btn-primary reveal" style="margin-top:34px"><?= tx('Conhecer o Apure Custos', 'See Apure Custos', 'Conocer Apure Custos') ?></a>
    </div>
    <div class="apure-visual reveal">
      <div class="apure-stat"><span class="label"><?= tx('Resultado por especialidade', 'Results by specialty', 'Resultado por especialidad') ?></span><span class="val">+24%</span></div>
      <div class="apure-stat"><span class="label"><?= tx('Ponto de equilíbrio', 'Break-even point', 'Punto de equilibrio') ?></span><span class="val">68%</span></div>
      <div class="apure-stat"><span class="label"><?= tx('Custo por procedimento', 'Cost per procedure', 'Costo por procedimiento') ?></span><span class="val">-15%</span></div>
      <div class="apure-stat"><span class="label"><?= tx('Margem por unidade', 'Margin per unit', 'Margen por unidad') ?></span><span class="val">31%</span></div>
    </div>
  </div>
</section>

<section class="section band-dark" id="tech">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow reveal"><?= tx('Tecnologia e integrações', 'Technology and integrations', 'Tecnología e integraciones') ?></span>
      <h2 class="reveal"><?= tx('Profundidade técnica sem complicar a rotina.', 'Technical depth without complicating the routine.', 'Profundidad técnica sin complicar la rutina.') ?></h2>
      <p class="reveal"><?= tx('Prontuário eletrônico, faturamento TISS/TUSS, PACS/DICOM, integrações HL7, infraestrutura em nuvem, backups e painéis de BI formam a base técnica da solução. Para a equipe, isso significa menos digitação duplicada, mais continuidade da informação e uma operação mais conectada.', 'Electronic medical records, TISS/TUSS billing, PACS/DICOM, HL7 integrations, cloud infrastructure, backups and BI dashboards form the technical base of the solution. For the team, this means less duplicate data entry, more continuity of information and a more connected operation.', 'Historia clínica electrónica, facturación TISS/TUSS, PACS/DICOM, integraciones HL7, infraestructura en la nube, copias de seguridad y paneles de BI forman la base técnica de la solución. Para el equipo, esto significa menos digitación duplicada, más continuidad de la información y una operación más conectada.') ?></p>
    </div>
    <div class="badges">
      <?php foreach ($_badges as $_b): ?><span class="badge-chip reveal"><?= $_b ?></span><?php endforeach; ?>
    </div>
    <a href="/tecnologia" class="btn btn-dark reveal"><?= tx('Ver tecnologia e integrações', 'See technology and integrations', 'Ver tecnología e integraciones') ?></a>
  </div>
</section>

<section class="section" id="cta">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Vamos começar pela sua prioridade', 'Let\'s start with your priority', 'Empecemos por su prioridad') ?></span>
    <h2 class="reveal"><?= tx('Qual gargalo mais impacta sua operação hoje?', 'Which bottleneck impacts your operation most today?', '¿Qué cuello de botella impacta más su operación hoy?') ?></h2>
    <p class="reveal"><?= tx('Faltas, filas, glosas, falta de controle sobre materiais ou pouca visibilidade de custos? Comece pela prioridade mais urgente e entenda quais módulos fazem sentido para o momento atual da sua instituição.', 'No-shows, queues, denials, lack of control over supplies or little visibility into costs? Start with the most urgent priority and understand which modules make sense for your institution right now.', '¿Ausencias, filas, glosas, falta de control sobre materiales o poca visibilidad de costos? Comience por la prioridad más urgente y entienda qué módulos tienen sentido para el momento actual de su institución.') ?></p>
    <a href="/contato" class="btn btn-primary reveal"><?= tx('Falar com um especialista', 'Talk to a specialist', 'Hablar con un especialista') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731b"></script>
</body>
</html>
