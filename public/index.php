<?php
/* Auto-detect paths: dev (public/..) vs prod flat (htdocs root) */
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
  <title><?= tx('Sistema de Gestão para Clínicas e Hospitais | TechSallus', 'Management System for Clinics and Hospitals | TechSallus', 'Sistema de Gestión para Clínicas y Hospitales | TechSallus') ?></title>
  <meta name="description" content="<?= tx('Reduza faltas, agilize o atendimento e ganhe mais controle sobre faturamento, glosas, estoque e custos com uma gestão integrada para instituições de saúde.', 'Reduce no-shows, speed up care and gain more control over billing, denials, inventory and costs with integrated management for health institutions.', 'Reduzca ausencias, agilice la atención y gane más control sobre facturación, glosas, inventario y costos con una gestión integrada para instituciones de salud.') ?>"/>

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

<!-- ══════════════════════════════════════════════════════════════
     HERO
     ══════════════════════════════════════════════════════════════ -->
<section class="hero">
  <svg class="hero-grid-bg" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" aria-hidden="true">
    <defs>
      <pattern id="linegrid" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.13)" stroke-width="0.8"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#linegrid)"/>
  </svg>

  <div class="container">
    <div class="hero-inner">
      <div class="hero-copy">
        <div class="hero-label">
          <div class="hero-label-bar"></div>
          <span><?= tx('Gestão integrada para instituições de saúde', 'Integrated management for health institutions', 'Gestión integrada para instituciones de salud') ?></span>
        </div>

        <h1 class="hero-headline">
          <span class="hero-headline-orange"><?= tx('Menos faltas. Atendimento mais ágil.', 'Fewer no-shows. Faster care.', 'Menos ausencias. Atención más ágil.') ?></span>
          <span class="hero-headline-rest"><?= tx('Mais controle sobre o faturamento.', 'More control over billing.', 'Más control sobre la facturación.') ?></span>
        </h1>

        <p class="hero-sub">
          <?= tx('A TechSallus conecta agenda, recepção, assistência, faturamento e gestão para reduzir retrabalho, melhorar a jornada do paciente e dar mais previsibilidade à operação.', 'TechSallus connects scheduling, front desk, care, billing and management to reduce rework, improve the patient journey and bring more predictability to the operation.', 'TechSallus conecta agenda, recepción, asistencia, facturación y gestión para reducir el retrabajo, mejorar el recorrido del paciente y dar más previsibilidad a la operación.') ?>
        </p>

        <div class="hero-ctas">
          <a href="/contato" class="btn-primary"><?= tx('Quero melhorar minha operação', 'I want to improve my operation', 'Quiero mejorar mi operación') ?></a>
          <a href="#solucoes" class="btn-outline-white" data-scroll="solucoes"><?= tx('Conhecer as soluções', 'See the solutions', 'Conocer las soluciones') ?></a>
        </div>

        <p class="hero-caption"><?= tx('Solução modular para consultórios, clínicas, policlínicas, hospitais e pronto atendimento.', 'A modular solution for private practices, clinics, polyclinics, hospitals and urgent care.', 'Solución modular para consultorios, clínicas, policlínicas, hospitales y urgencias.') ?></p>
      </div>

      <div class="hero-visual">
        <div class="dashboard-frame">
          <div class="dashboard-inner">
            <div class="db-topbar">
              <div class="db-dots">
                <span class="db-dot orange"></span>
                <span class="db-dot dim"></span>
                <span class="db-dot dim"></span>
              </div>
              <div class="db-tabs">
                <span class="db-tab active"><?= tx('Agenda', 'Schedule', 'Agenda') ?></span>
                <span class="db-tab"><?= tx('Atendimento', 'Care', 'Atención') ?></span>
                <span class="db-tab"><?= tx('Financeiro', 'Finance', 'Finanzas') ?></span>
              </div>
              <div class="db-avatar-wrap">
                <div class="db-avatar">DM</div>
              </div>
            </div>
            <div class="db-stats">
              <div class="db-stat-card">
                <div class="db-stat-label"><?= tx('Agendamentos', 'Appointments', 'Citas') ?></div>
                <div class="db-stat-value">1.247</div>
                <div class="db-stat-tag">▲ +12% <?= tx('mês', 'month', 'mes') ?></div>
              </div>
              <div class="db-stat-card">
                <div class="db-stat-label"><?= tx('Atendimentos', 'Visits', 'Atenciones') ?></div>
                <div class="db-stat-value">863</div>
                <div class="db-stat-tag">▲ +8% <?= tx('mês', 'month', 'mes') ?></div>
              </div>
              <div class="db-stat-card">
                <div class="db-stat-label"><?= tx('Faturamento', 'Billing', 'Facturación') ?></div>
                <div class="db-stat-value">R$ 92k</div>
                <div class="db-stat-tag">▲ +15% <?= tx('mês', 'month', 'mes') ?></div>
              </div>
            </div>
            <div class="db-chart">
              <div class="db-chart-head">
                <span class="db-chart-title"><?= tx('Atendimentos por semana', 'Visits per week', 'Atenciones por semana') ?></span>
                <span class="db-chart-link"><?= tx('Ver relatório', 'View report', 'Ver informe') ?> →</span>
              </div>
              <div class="db-bars">
                <div class="db-bar" style="height:42%"></div>
                <div class="db-bar" style="height:68%"></div>
                <div class="db-bar" style="height:55%"></div>
                <div class="db-bar" style="height:84%"></div>
                <div class="db-bar" style="height:61%"></div>
                <div class="db-bar highlight" style="height:95%"></div>
                <div class="db-bar" style="height:74%"></div>
              </div>
              <div class="db-days">
                <span class="db-day">S</span><span class="db-day">T</span>
                <span class="db-day">Q</span><span class="db-day">Q</span>
                <span class="db-day">S</span><span class="db-day">S</span>
                <span class="db-day">D</span>
              </div>
            </div>
            <div class="db-appts">
              <div class="db-appts-title"><?= tx('Próximos agendamentos', 'Upcoming appointments', 'Próximas citas') ?></div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">M</div>
                  <span class="db-appt-name">Maria Santos</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">09:00</span>
                  <span class="db-appt-type"><?= tx('Consulta', 'Visit', 'Consulta') ?></span>
                </div>
              </div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">J</div>
                  <span class="db-appt-name">João Oliveira</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">09:45</span>
                  <span class="db-appt-type"><?= tx('Retorno', 'Follow-up', 'Retorno') ?></span>
                </div>
              </div>
              <div class="db-appt-row">
                <div class="db-appt-left">
                  <div class="db-appt-avatar">A</div>
                  <span class="db-appt-name">Ana Costa</span>
                </div>
                <div class="db-appt-right">
                  <span class="db-appt-time">10:30</span>
                  <span class="db-appt-type"><?= tx('Exame', 'Exam', 'Examen') ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     PROBLEMAS — "seu maior custo..."
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section">
  <div class="container">
    <span class="section-label"><?= tx('O desafio', 'The challenge', 'El desafío') ?></span>
    <h2 class="page-section-heading"><?= tx('Seu maior custo pode estar entre um setor e outro.', 'Your biggest cost may be sitting between departments.', 'Su mayor costo puede estar entre un sector y otro.') ?></h2>
    <p class="page-section-sub"><?= tx('Quando a informação não acompanha o paciente, a equipe repete tarefas, a espera aumenta e o faturamento fica mais vulnerável. A TechSallus organiza a operação em um fluxo conectado, da marcação à análise do resultado.', 'When information does not follow the patient, staff repeat tasks, waiting time grows and billing becomes more vulnerable. TechSallus organizes the operation into a connected flow, from booking to results analysis.', 'Cuando la información no acompaña al paciente, el equipo repite tareas, la espera aumenta y la facturación queda más vulnerable. TechSallus organiza la operación en un flujo conectado, desde la reserva hasta el análisis del resultado.') ?></p>

    <div class="card-grid card-grid-3">
      <div class="card">
        <div class="card-title"><?= tx('Agenda com faltas', 'No-show-prone scheduling', 'Agenda con ausencias') ?></div>
        <p class="card-desc"><?= tx('Confirmações manuais consomem a equipe e horários ociosos reduzem a capacidade de atendimento.', 'Manual confirmations tie up staff and idle time slots shrink care capacity.', 'Las confirmaciones manuales consumen al equipo y los horarios vacíos reducen la capacidad de atención.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Filas e demora', 'Queues and delays', 'Filas y demoras') ?></div>
        <p class="card-desc"><?= tx('Recepção, triagem, consultório e exames desconectados criam espera e pouca visibilidade sobre o fluxo.', 'Disconnected front desk, triage, exam rooms and testing create waiting time and little visibility into flow.', 'Recepción, triaje, consultorio y exámenes desconectados generan espera y poca visibilidad del flujo.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Faturamento vulnerável', 'Vulnerable billing', 'Facturación vulnerable') ?></div>
        <p class="card-desc"><?= tx('Guias inconsistentes, regras dispersas e glosas aumentam retrabalho e atrasam o ciclo de receita.', 'Inconsistent claims, scattered payer rules and denials increase rework and delay the revenue cycle.', 'Guías inconsistentes, reglas dispersas y glosas aumentan el retrabajo y retrasan el ciclo de ingresos.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Informação fragmentada', 'Fragmented information', 'Información fragmentada') ?></div>
        <p class="card-desc"><?= tx('Dados clínicos e operacionais em ambientes separados dificultam a continuidade do cuidado.', 'Clinical and operational data living in separate systems make care continuity harder.', 'Datos clínicos y operativos en entornos separados dificultan la continuidad del cuidado.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Perdas em materiais e medicamentos', 'Losses in supplies and medication', 'Pérdidas en materiales y medicamentos') ?></div>
        <p class="card-desc"><?= tx('Compras, estoque, farmácia e consumo sem integração reduzem rastreabilidade e controle.', 'Disconnected purchasing, inventory, pharmacy and consumption reduce traceability and control.', 'Compras, inventario, farmacia y consumo sin integración reducen trazabilidad y control.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Decisões sem visão de custo', 'Decisions without cost visibility', 'Decisiones sin visión de costos') ?></div>
        <p class="card-desc"><?= tx('Faturamento mostra movimento, mas não revela margem, ponto de equilíbrio ou rentabilidade.', 'Billing shows volume, but not margin, break-even point or profitability.', 'La facturación muestra movimiento, pero no revela margen, punto de equilibrio ni rentabilidad.') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     BENEFÍCIOS — "mais eficiência onde o paciente sente..."
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section page-section-tint">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Mais eficiência onde o paciente sente. Mais controle onde a gestão decide.', 'More efficiency where the patient feels it. More control where management decides.', 'Más eficiencia donde el paciente la siente. Más control donde la gestión decide.') ?></h2>
    <p class="page-section-sub"><?= tx('A tecnologia entra para resolver a rotina: ocupar melhor a agenda, fazer o paciente avançar com fluidez, dar continuidade à informação e proteger o resultado da instituição.', 'Technology steps in to solve the day-to-day: filling the schedule better, moving the patient forward smoothly, giving continuity to information and protecting the institution\'s results.', 'La tecnología entra para resolver la rutina: ocupar mejor la agenda, hacer que el paciente avance con fluidez, dar continuidad a la información y proteger el resultado de la institución.') ?></p>

    <div class="card-grid card-grid-3">
      <div class="card">
        <div class="card-title"><?= tx('Aproveite melhor a agenda', 'Make better use of your schedule', 'Aproveche mejor la agenda') ?></div>
        <p class="card-desc"><?= tx('Agendamento integrado e confirmações por WhatsApp ajudam a reduzir esforço manual e favorecem uma ocupação mais previsível.', 'Integrated scheduling and WhatsApp confirmations reduce manual effort and support more predictable occupancy.', 'La agenda integrada y las confirmaciones por WhatsApp ayudan a reducir el esfuerzo manual y favorecen una ocupación más previsible.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Acelere a chegada ao atendimento', 'Speed up arrival to care', 'Acelere la llegada a la atención') ?></div>
        <p class="card-desc"><?= tx('Totem, recepção, distribuição de senhas, chamadas e acolhimento trabalham no mesmo fluxo.', 'Kiosk, front desk, ticket distribution, calling and intake all work within the same flow.', 'Totem, recepción, distribución de turnos, llamadas y acogida trabajan en el mismo flujo.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Dê continuidade ao cuidado', 'Give continuity to care', 'Dé continuidad al cuidado') ?></div>
        <p class="card-desc"><?= tx('Prontuário, laudos, imagens, prescrições e dados assistenciais acompanham a jornada do paciente.', 'Medical records, reports, images, prescriptions and clinical data follow the patient\'s journey.', 'Historia clínica, informes, imágenes, prescripciones y datos asistenciales acompañan el recorrido del paciente.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Fature com mais consistência', 'Bill with more consistency', 'Facture con más consistencia') ?></div>
        <p class="card-desc"><?= tx('TISS, TUSS, regras de convênios, guias, glosas e repasses dão mais controle ao ciclo de receita.', 'TISS, TUSS, payer rules, claims, denials and physician payouts give more control to the revenue cycle.', 'TISS, TUSS, reglas de convenios, guías, glosas y repartos dan más control al ciclo de ingresos.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Controle suprimentos e medicamentos', 'Control supplies and medication', 'Controle insumos y medicamentos') ?></div>
        <p class="card-desc"><?= tx('Compras, estoque, farmácia, lotes e dispensação se conectam à operação assistencial.', 'Purchasing, inventory, pharmacy, batches and dispensing connect to clinical operations.', 'Compras, inventario, farmacia, lotes y dispensación se conectan a la operación asistencial.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Decida com visão de custo e margem', 'Decide with cost and margin visibility', 'Decida con visión de costo y margen') ?></div>
        <p class="card-desc"><?= tx('Financeiro, BI e Apure Custos transformam dados da operação em informação gerencial.', 'Finance, BI and Apure Custos turn operational data into management information.', 'Financiero, BI y Apure Custos transforman los datos de la operación en información gerencial.') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     JORNADA — "da marcação ao resultado..."
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Da marcação ao resultado, cada etapa conversa com a próxima.', 'From booking to results, every step talks to the next.', 'De la reserva al resultado, cada etapa conversa con la siguiente.') ?></h2>
    <p class="page-section-sub"><?= tx('Em vez de departamentos trabalhando como ilhas, a TechSallus conecta a jornada do paciente às rotinas assistenciais, administrativas e financeiras.', 'Instead of departments working as isolated islands, TechSallus connects the patient journey to clinical, administrative and financial routines.', 'En vez de departamentos trabajando como islas, TechSallus conecta el recorrido del paciente a las rutinas asistenciales, administrativas y financieras.') ?></p>

    <div class="numbered-steps">
      <div class="step-item">
        <div class="step-num">1</div>
        <p class="step-text"><strong><?= tx('Agendar e confirmar.', 'Schedule and confirm.', 'Agendar y confirmar.') ?></strong> <?= tx('Organize a agenda e automatize confirmações por WhatsApp, conforme o escopo contratado.', 'Organize the schedule and automate WhatsApp confirmations, according to the contracted scope.', 'Organice la agenda y automatice confirmaciones por WhatsApp, según el alcance contratado.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <p class="step-text"><strong><?= tx('Receber e direcionar.', 'Receive and direct.', 'Recibir y dirigir.') ?></strong> <?= tx('Recepção, totem, senhas, chamadas e triagem ajudam o paciente a avançar com mais fluidez.', 'Front desk, kiosk, tickets, calling and triage help the patient move forward more smoothly.', 'Recepción, totem, turnos, llamadas y triaje ayudan al paciente a avanzar con más fluidez.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <p class="step-text"><strong><?= tx('Atender e registrar.', 'Care and record.', 'Atender y registrar.') ?></strong> <?= tx('Prontuário, evolução, laudos e protocolos ficam disponíveis no ponto de cuidado.', 'Medical records, progress notes, reports and protocols are available at the point of care.', 'Historia clínica, evolución, informes y protocolos están disponibles en el punto de cuidado.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">4</div>
        <p class="step-text"><strong><?= tx('Examinar, prescrever e dispensar.', 'Examine, prescribe and dispense.', 'Examinar, prescribir y dispensar.') ?></strong> <?= tx('Imagens, prescrições, farmácia, estoque e rotinas assistenciais se conectam à jornada.', 'Images, prescriptions, pharmacy, inventory and clinical routines connect to the journey.', 'Imágenes, prescripciones, farmacia, inventario y rutinas asistenciales se conectan al recorrido.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">5</div>
        <p class="step-text"><strong><?= tx('Faturar e acompanhar.', 'Bill and track.', 'Facturar y acompañar.') ?></strong> <?= tx('Guias, convênios, TISS, glosas e repasses sustentam um ciclo mais controlado.', 'Claims, payers, TISS, denials and payouts sustain a more controlled cycle.', 'Guías, convenios, TISS, glosas y repartos sostienen un ciclo más controlado.') ?></p>
      </div>
      <div class="step-item">
        <div class="step-num">6</div>
        <p class="step-text"><strong><?= tx('Analisar e decidir.', 'Analyze and decide.', 'Analizar y decidir.') ?></strong> <?= tx('Indicadores e custos mostram onde a operação gera resultado e onde precisa agir.', 'Indicators and costs show where the operation generates results and where it needs to act.', 'Indicadores y costos muestran dónde la operación genera resultado y dónde necesita actuar.') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     SOLUÇÕES POR PERFIL — #solucoes
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section page-section-tint" id="solucoes">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Uma solução para cada nível de complexidade.', 'A solution for every level of complexity.', 'Una solución para cada nivel de complejidad.') ?></h2>
    <p class="page-section-sub"><?= tx('Comece pelo que sua instituição precisa agora e amplie o escopo sem montar um quebra-cabeça de sistemas e fornecedores.', 'Start with what your institution needs now and expand the scope without piecing together a puzzle of systems and vendors.', 'Comience por lo que su institución necesita ahora y amplíe el alcance sin armar un rompecabezas de sistemas y proveedores.') ?></p>

    <div class="card-grid card-grid-2">
      <div class="card">
        <div class="card-title"><?= tx('Consultórios', 'Private practices', 'Consultorios') ?></div>
        <p class="card-desc"><?= tx('Mais tempo para atender e menos tarefas manuais entre agenda, prontuário, laudos e faturamento.', 'More time to see patients and fewer manual tasks between scheduling, records, reports and billing.', 'Más tiempo para atender y menos tareas manuales entre agenda, historia clínica, informes y facturación.') ?></p>
        <p style="margin-top:16px"><a href="/consultorios" class="btn-orange"><?= tx('Conhecer solução para consultórios', 'See the solution for private practices', 'Conocer la solución para consultorios') ?></a></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Clínicas e policlínicas', 'Clinics and polyclinics', 'Clínicas y policlínicas') ?></div>
        <p class="card-desc"><?= tx('Mais fluidez para alto volume de pacientes, procedimentos, exames, estoque, farmácia e gestão.', 'More fluidity for high patient volume, procedures, exams, inventory, pharmacy and management.', 'Más fluidez para alto volumen de pacientes, procedimientos, exámenes, inventario, farmacia y gestión.') ?></p>
        <p style="margin-top:16px"><a href="/clinicas" class="btn-orange"><?= tx('Conhecer solução para clínicas', 'See the solution for clinics', 'Conocer la solución para clínicas') ?></a></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Hospitais e pronto atendimento', 'Hospitals and urgent care', 'Hospitales y urgencias') ?></div>
        <p class="card-desc"><?= tx('Coordenação de urgência, internação, centro cirúrgico, prescrição, beira leito e ciclo de receita.', 'Coordination of urgency, admission, surgical center, prescriptions, bedside care and revenue cycle.', 'Coordinación de urgencia, internación, centro quirúrgico, prescripción, cabecera y ciclo de ingresos.') ?></p>
        <p style="margin-top:16px"><a href="/hospitais" class="btn-orange"><?= tx('Conhecer solução hospitalar', 'See the hospital solution', 'Conocer la solución hospitalaria') ?></a></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Gestão de custos', 'Cost management', 'Gestión de costos') ?></div>
        <p class="card-desc"><?= tx('Visão de custo, margem e resultado para priorizar melhorias e tomar decisões com mais segurança.', 'Cost, margin and results visibility to prioritize improvements and make decisions with more confidence.', 'Visión de costo, margen y resultado para priorizar mejoras y tomar decisiones con más seguridad.') ?></p>
        <p style="margin-top:16px"><a href="/apure-custos" class="btn-orange"><?= tx('Conhecer o Apure Custos', 'See Apure Custos', 'Conocer Apure Custos') ?></a></p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     MODULAR / INTEGRADA
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Modular por necessidade. Integrada por princípio.', 'Modular by need. Integrated by principle.', 'Modular por necesidad. Integrada por principio.') ?></h2>
    <p class="page-section-sub"><?= tx('A TechSallus permite compor a solução de acordo com as prioridades da instituição e evoluir à medida que a operação cresce. A modularidade reduz investimento desnecessário; a integração evita que cada novo recurso se transforme em mais uma ilha de informação.', 'TechSallus lets you build the solution according to the institution\'s priorities and evolve as the operation grows. Modularity avoids unnecessary investment; integration keeps every new capability from becoming another island of information.', 'TechSallus permite componer la solución de acuerdo con las prioridades de la institución y evolucionar a medida que la operación crece. La modularidad reduce la inversión innecesaria; la integración evita que cada nuevo recurso se convierta en otra isla de información.') ?></p>

    <div class="list-columns">
      <div class="list-column">
        <div class="list-column-title"><?= tx('Comece pela prioridade de hoje', 'Start with today\'s priority', 'Comience por la prioridad de hoy') ?></div>
        <ul>
          <li><?= tx('Agenda e absenteísmo', 'Scheduling and no-shows', 'Agenda y ausentismo') ?></li>
          <li><?= tx('Fluxo de atendimento', 'Care flow', 'Flujo de atención') ?></li>
          <li><?= tx('Faturamento e glosas', 'Billing and denials', 'Facturación y glosas') ?></li>
          <li><?= tx('Estoque e farmácia', 'Inventory and pharmacy', 'Inventario y farmacia') ?></li>
        </ul>
      </div>
      <div class="list-column">
        <div class="list-column-title"><?= tx('Evolua para uma gestão completa', 'Evolve to full management', 'Evolucione a una gestión completa') ?></div>
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

<!-- ══════════════════════════════════════════════════════════════
     APURE CUSTOS TEASER
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section page-section-dark">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Faturamento mostra volume. Custos mostram resultado.', 'Billing shows volume. Costs show results.', 'La facturación muestra volumen. Los costos muestran resultado.') ?></h2>
    <p class="page-section-sub"><?= tx('O Apure Custos reúne produção, materiais, pessoal, depreciação, custos gerais e repasses médicos para revelar custos e resultados por especialidade, paciente, unidade, produto e procedimento.', 'Apure Custos brings together production, materials, personnel, depreciation, overhead and physician payouts to reveal costs and results by specialty, patient, unit, product and procedure.', 'Apure Custos reúne producción, materiales, personal, depreciación, costos generales y repartos médicos para revelar costos y resultados por especialidad, paciente, unidad, producto y procedimiento.') ?></p>

    <div class="card-grid card-grid-2">
      <div class="card">
        <div class="card-title"><?= tx('Mensure custos', 'Measure costs', 'Mida costos') ?></div>
        <p class="card-desc"><?= tx('Conheça o custo real da operação e acompanhe sua evolução.', 'Know the real cost of the operation and track its evolution.', 'Conozca el costo real de la operación y siga su evolución.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Enxergue o ponto de equilíbrio', 'See your break-even point', 'Visualice el punto de equilibrio') ?></div>
        <p class="card-desc"><?= tx('Entenda quanto cada unidade, serviço ou especialidade precisa produzir para se sustentar.', 'Understand how much each unit, service or specialty needs to produce to sustain itself.', 'Entienda cuánto necesita producir cada unidad, servicio o especialidad para sostenerse.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Compare resultados', 'Compare results', 'Compare resultados') ?></div>
        <p class="card-desc"><?= tx('Analise especialidades, pacientes, unidades, produtos e procedimentos sob a mesma lógica.', 'Analyze specialties, patients, units, products and procedures under the same logic.', 'Analice especialidades, pacientes, unidades, productos y procedimientos bajo la misma lógica.') ?></p>
      </div>
      <div class="card">
        <div class="card-title"><?= tx('Avalie investimentos', 'Evaluate investments', 'Evalúe inversiones') ?></div>
        <p class="card-desc"><?= tx('Use dados de custo e resultado para apoiar expansão, negociação e priorização.', 'Use cost and results data to support expansion, negotiation and prioritization.', 'Use datos de costo y resultado para apoyar expansión, negociación y priorización.') ?></p>
      </div>
    </div>

    <p style="margin-top:40px"><a href="/apure-custos" class="btn-primary"><?= tx('Conhecer o Apure Custos', 'See Apure Custos', 'Conocer Apure Custos') ?></a></p>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     TECNOLOGIA TEASER
     ══════════════════════════════════════════════════════════════ -->
<section class="page-section">
  <div class="container">
    <h2 class="page-section-heading"><?= tx('Profundidade técnica sem complicar a rotina.', 'Technical depth without complicating the routine.', 'Profundidad técnica sin complicar la rutina.') ?></h2>
    <p class="page-section-sub"><?= tx('Prontuário eletrônico, faturamento TISS/TUSS, PACS/DICOM, integrações HL7, infraestrutura em nuvem, backups e painéis de BI formam a base técnica da solução. Para a equipe, isso significa menos digitação duplicada, mais continuidade da informação e uma operação mais conectada.', 'Electronic medical records, TISS/TUSS billing, PACS/DICOM, HL7 integrations, cloud infrastructure, backups and BI dashboards form the technical base of the solution. For the team, this means less duplicate data entry, more continuity of information and a more connected operation.', 'Historia clínica electrónica, facturación TISS/TUSS, PACS/DICOM, integraciones HL7, infraestructura en la nube, copias de seguridad y paneles de BI forman la base técnica de la solución. Para el equipo, esto significa menos digitación duplicada, más continuidad de la información y una operación más conectada.') ?></p>
    <p><a href="/tecnologia" class="btn-orange"><?= tx('Ver tecnologia e integrações', 'See technology and integrations', 'Ver tecnología e integraciones') ?></a></p>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     CTA FINAL
     ══════════════════════════════════════════════════════════════ -->
<section class="cta-bar cta-bar-dark">
  <div class="container">
    <h2 class="cta-bar-heading"><?= tx('Qual gargalo mais impacta sua operação hoje?', 'Which bottleneck impacts your operation most today?', '¿Qué cuello de botella impacta más su operación hoy?') ?></h2>
    <p class="page-section-sub" style="margin:-8px auto 24px"><?= tx('Faltas, filas, glosas, falta de controle sobre materiais ou pouca visibilidade de custos? Comece pela prioridade mais urgente e entenda quais módulos fazem sentido para o momento atual da sua instituição.', 'No-shows, queues, denials, lack of control over supplies or little visibility into costs? Start with the most urgent priority and understand which modules make sense for your institution right now.', '¿Ausencias, filas, glosas, falta de control sobre materiales o poca visibilidad de costos? Comience por la prioridad más urgente y entienda qué módulos tienen sentido para el momento actual de su institución.') ?></p>
    <div class="cta-bar-actions">
      <a href="/contato" class="btn-primary"><?= tx('Falar com um especialista', 'Talk to a specialist', 'Hablar con un especialista') ?></a>
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
