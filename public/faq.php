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

$_faqs = [
    [tx('A TechSallus atende apenas hospitais?', 'Does TechSallus only serve hospitals?', '¿TechSallus atiende solo a hospitales?'), tx('Não. A solução pode ser dimensionada para consultórios, clínicas, policlínicas, hospitais e pronto atendimento de diferentes portes.', 'No. The solution can be sized for private practices, clinics, polyclinics, hospitals and urgent care of different sizes.', 'No. La solución puede dimensionarse para consultorios, clínicas, policlínicas, hospitales y urgencias de diferentes portes.')],
    [tx('É necessário contratar todos os módulos?', 'Do I need to contract all modules?', '¿Es necesario contratar todos los módulos?'), tx('Não. A TechSallus é modular. O escopo pode começar pelas prioridades mais importantes e evoluir conforme a necessidade e a complexidade da operação.', 'No. TechSallus is modular. Scope can start with the most important priorities and evolve according to the operation\'s needs and complexity.', 'No. TechSallus es modular. El alcance puede comenzar por las prioridades más importantes y evolucionar según la necesidad y la complejidad de la operación.')],
    [tx('A solução pode ajudar a reduzir o absenteísmo?', 'Can the solution help reduce no-shows?', '¿La solución puede ayudar a reducir el ausentismo?'), tx('A agenda integrada e a confirmação de atendimentos por WhatsApp ajudam a reduzir o trabalho manual e favorecem uma ocupação mais previsível. O resultado depende do perfil da operação e da forma de uso.', 'Integrated scheduling and WhatsApp appointment confirmation help reduce manual work and support more predictable occupancy. Results depend on the operation\'s profile and how it is used.', 'La agenda integrada y la confirmación de citas por WhatsApp ayudan a reducir el trabajo manual y favorecen una ocupación más previsible. El resultado depende del perfil de la operación y de la forma de uso.')],
    [tx('Como a TechSallus apoia o faturamento?', 'How does TechSallus support billing?', '¿Cómo apoya TechSallus la facturación?'), tx('A solução contempla faturamento TISS, base TUSS, regras de convênios, geração de guias, controle de glosas e repasses médicos, conforme o escopo contratado.', 'The solution covers TISS billing, the TUSS database, payer rules, claim generation, denial control and physician payouts, according to the contracted scope.', 'La solución contempla facturación TISS, base TUSS, reglas de convenios, generación de guías, control de glosas y repartos médicos, según el alcance contratado.')],
    [tx('Há recursos para exames de imagem e integração com equipamentos?', 'Are there features for imaging exams and equipment integration?', '¿Hay recursos para exámenes de imagen e integración con equipos?'), tx('Sim. A solução contempla PACS/DICOM, integração com RIS e capacidades de interoperabilidade via HL7. As interfaces específicas são avaliadas no projeto.', 'Yes. The solution covers PACS/DICOM, RIS integration and HL7 interoperability capabilities. Specific interfaces are evaluated during the project.', 'Sí. La solución contempla PACS/DICOM, integración con RIS y capacidades de interoperabilidad vía HL7. Las interfaces específicas se evalúan en el proyecto.')],
    [tx('O prontuário eletrônico é certificado?', 'Is the electronic medical record certified?', '¿La historia clínica electrónica está certificada?'), tx('O PEP TechSallus possui certificação S-RES SBIS no escopo Clínica/Ambulatório apresentado na página técnica. A versão e o estágio aplicáveis devem acompanhar a documentação vigente da solução.', 'The TechSallus PEP holds S-RES SBIS certification in the Clinic/Outpatient scope shown on the technology page. The applicable version and stage should follow the solution\'s current documentation.', 'El PEP TechSallus cuenta con certificación S-RES SBIS en el alcance Clínica/Ambulatorio presentado en la página técnica. La versión y la etapa aplicables deben acompañar la documentación vigente de la solución.')],
    [tx('A TechSallus funciona em nuvem?', 'Does TechSallus run in the cloud?', '¿TechSallus funciona en la nube?'), tx('Há modalidades com infraestrutura em nuvem no Brasil e opções de ambiente dedicado ou virtual. A arquitetura é definida de acordo com o perfil, o porte e o escopo da instituição.', 'There are plans with cloud infrastructure in Brazil and dedicated or virtual environment options. Architecture is defined according to the institution\'s profile, size and scope.', 'Hay modalidades con infraestructura en la nube en Brasil y opciones de entorno dedicado o virtual. La arquitectura se define según el perfil, el porte y el alcance de la institución.')],
    [tx('Como funcionam backups e disponibilidade?', 'How do backups and availability work?', '¿Cómo funcionan las copias de seguridad y la disponibilidad?'), tx('As modalidades podem incluir configuração, manutenção, atualizações, rotinas de backup e SLA de disponibilidade. Os detalhes são definidos na proposta e no contrato.', 'Plans can include configuration, maintenance, updates, backup routines and an availability SLA. Details are defined in the proposal and contract.', 'Las modalidades pueden incluir configuración, mantenimiento, actualizaciones, rutinas de copia de seguridad y SLA de disponibilidad. Los detalles se definen en la propuesta y el contrato.')],
    [tx('A solução também controla estoque e farmácia?', 'Does the solution also control inventory and pharmacy?', '¿La solución también controla inventario y farmacia?'), tx('Sim. Para clínicas e hospitais, o escopo pode incluir compras, estoque, farmácia, lotes, etiquetagem e integrações de dispensação ou automação.', 'Yes. For clinics and hospitals, scope can include purchasing, inventory, pharmacy, batches, labeling and dispensing or automation integrations.', 'Sí. Para clínicas y hospitales, el alcance puede incluir compras, inventario, farmacia, lotes, etiquetado e integraciones de dispensación o automatización.')],
    [tx('Como a TechSallus ajuda na gestão de custos?', 'How does TechSallus help with cost management?', '¿Cómo ayuda TechSallus en la gestión de costos?'), tx('O Apure Custos consolida produção, materiais, pessoal, depreciação, custos gerais e repasses para analisar custos e resultados por especialidade, paciente, unidade, produto e procedimento.', 'Apure Custos consolidates production, materials, personnel, depreciation, overhead and payouts to analyze costs and results by specialty, patient, unit, product and procedure.', 'Apure Custos consolida producción, materiales, personal, depreciación, costos generales y repartos para analizar costos y resultados por especialidad, paciente, unidad, producto y procedimiento.')],
    [tx('É possível integrar com sistemas que a instituição já utiliza?', 'Is it possible to integrate with systems the institution already uses?', '¿Es posible integrar con sistemas que la institución ya utiliza?'), tx('A TechSallus trabalha com padrões e integrações para saúde, incluindo HL7, PACS/DICOM, RIS, HIS e LIS. A viabilidade de cada integração é avaliada conforme os sistemas e equipamentos envolvidos.', 'TechSallus works with healthcare standards and integrations, including HL7, PACS/DICOM, RIS, HIS and LIS. The viability of each integration is assessed based on the systems and equipment involved.', 'TechSallus trabaja con estándares e integraciones para salud, incluyendo HL7, PACS/DICOM, RIS, HIS y LIS. La viabilidad de cada integración se evalúa según los sistemas y equipos involucrados.')],
    [tx('Como é definido o investimento?', 'How is the investment defined?', '¿Cómo se define la inversión?'), tx('O investimento depende do perfil da instituição, dos módulos, das integrações, da infraestrutura e dos serviços necessários. A definição começa com o mapeamento do escopo.', 'The investment depends on the institution\'s profile, the modules, integrations, infrastructure and services needed. The definition starts with mapping the scope.', 'La inversión depende del perfil de la institución, de los módulos, las integraciones, la infraestructura y los servicios necesarios. La definición comienza con el mapeo del alcance.')],
];
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= tx('Perguntas Frequentes sobre a TechSallus', 'Frequently Asked Questions about TechSallus', 'Preguntas Frecuentes sobre TechSallus') ?></title>
<meta name="description" content="<?= tx('Tire dúvidas sobre modularidade, tipos de instituição, absenteísmo, faturamento TISS/TUSS, integrações, nuvem, prontuário e gestão de custos.', 'Get answers about modularity, institution types, no-shows, TISS/TUSS billing, integrations, cloud, medical records and cost management.', 'Resuelva dudas sobre modularidad, tipos de institución, ausentismo, facturación TISS/TUSS, integraciones, nube, historia clínica y gestión de costos.') ?>"/>
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

<section class="subhero">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Dúvidas sobre a solução', 'Questions about the solution', 'Dudas sobre la solución') ?></span>
    <h1 class="reveal"><?= tx('Perguntas Frequentes', 'Frequently Asked Questions', 'Preguntas Frecuentes') ?></h1>
    <p class="reveal"><?= tx('Respostas diretas sobre modularidade, tipos de instituição, faturamento, integrações e gestão de custos.', 'Direct answers about modularity, institution types, billing, integrations and cost management.', 'Respuestas directas sobre modularidad, tipos de institución, facturación, integraciones y gestión de costos.') ?></p>
  </div>
</section>

<section class="section" style="padding-top:20px">
  <div class="wrap" style="max-width:860px">
    <?php foreach ($_faqs as $_i => [$_q, $_a]): ?>
      <div class="faq-item reveal">
        <button type="button" class="faq-q"><?= $_q ?><?= ic('plusMinus') ?></button>
        <div class="faq-a"><p><?= $_a ?></p></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section tint" id="cta">
  <div class="wrap">
    <span class="eyebrow reveal"><?= tx('Ainda tenho uma dúvida', 'I still have a question', 'Todavía tengo una duda') ?></span>
    <h2 class="reveal"><?= tx('Fale direto com um especialista.', 'Talk directly to a specialist.', 'Hable directamente con un especialista.') ?></h2>
    <p class="reveal"><?= tx('Conte sobre a sua operação e receba orientação sobre o melhor caminho para a sua instituição.', 'Tell us about your operation and get guidance on the best path for your institution.', 'Cuéntenos sobre su operación y reciba orientación sobre el mejor camino para su institución.') ?></p>
    <a href="/contato" class="btn btn-primary reveal"><?= tx('Falar com um especialista', 'Talk to a specialist', 'Hablar con un especialista') ?></a>
  </div>
</section>

</main>

<?php include __DIR__ . '/_partials/footer.php'; ?>
<script src="/assets/js/main.js?v=20260731b"></script>
</body>
</html>
