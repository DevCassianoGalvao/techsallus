<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus Blog — artigo individual
   ───────────────────────────────────────────────────────────── */
require_once __DIR__ . '/../../config/i18n.php';

$ALL_ARTICLES = [

  'como-reduzir-glosas-faturamento-hospitalar' => [
    'slug'    => 'como-reduzir-glosas-faturamento-hospitalar',
    'title'   => 'Como reduzir glosas no faturamento hospitalar e recuperar receita',
    'cat'     => 'Faturamento',
    'cat_slug'=> 'faturamento',
    'author'  => 'Equipe TechSallus',
    'date'    => '12 mai. 2025',
    'read'    => '8 min',
    'img_bg'  => '#e8f0f9',
    'img_accent' => '#094a86',
    'toc' => [
      'O que é uma glosa e por que ela acontece',
      'As 5 causas mais comuns de glosa',
      'Como o sistema ajuda a prevenir',
      'Processo de recursão de glosa',
      'Indicadores para monitorar',
    ],
    'content' => <<<HTML
<p>Glosas hospitalares representam, em média, entre 8% e 15% da receita bruta de uma instituição de saúde no Brasil. Para muitos hospitais e clínicas, esse valor pode ser a diferença entre equilíbrio financeiro e prejuízo. Entender suas causas e criar processos para reduzi-las é uma das tarefas mais importantes do gestor de faturamento.</p>

<h2 id="toc-1">O que é uma glosa e por que ela acontece</h2>
<p>Glosa é a recusa total ou parcial de pagamento de uma conta hospitalar por parte do convênio ou operadora de saúde. Ela ocorre quando a operadora identifica inconsistências, erros ou informações incompletas na guia enviada. Existem três tipos principais: glosa de auditoria (negada após revisão médica), glosa técnica (erro no preenchimento da guia) e glosa administrativa (falha no processo de autorização ou prazo).</p>
<blockquote>Uma única glosa não resolvida pode representar meses de trabalho desperdiçados. O problema se multiplica quando o processo de faturamento não tem controle sistematizado.</blockquote>

<h2 id="toc-2">As 5 causas mais comuns de glosa</h2>
<p>Em nossa experiência com centenas de instituições, identificamos as causas mais recorrentes:</p>
<ul>
  <li><strong>Codificação incorreta de procedimentos:</strong> uso de código TUSS inadequado para o procedimento realizado.</li>
  <li><strong>Divergência entre guia e prontuário:</strong> procedimentos registrados no atendimento não batem com o que foi faturado.</li>
  <li><strong>Falta de autorização prévia:</strong> procedimentos realizados sem a autorização obrigatória do convênio.</li>
  <li><strong>Prazo vencido de envio:</strong> guias enviadas fora do prazo estipulado pelo convênio.</li>
  <li><strong>Dados incompletos do beneficiário:</strong> número de carteirinha, nome ou data de nascimento com erros.</li>
</ul>

<h2 id="toc-3">Como o sistema ajuda a prevenir</h2>
<p>Um sistema de gestão hospitalar bem configurado pode reduzir glosas em até 60% ao automatizar os pontos críticos do processo. O TechSallus realiza validação em tempo real dos códigos TISS antes do envio da guia, alerta quando um procedimento não possui autorização registrada, e cruza automaticamente as informações do prontuário eletrônico com os dados do faturamento.</p>
<p>Além disso, o módulo de Regras de Negócio permite configurar, por convênio, os prazos de envio e os procedimentos que exigem autorização prévia. Assim, a equipe recebe alertas antes de cometer o erro, não depois.</p>

<h2 id="toc-4">Processo de recursão de glosa</h2>
<p>Mesmo com processos preventivos, algumas glosas são inevitáveis. O importante é ter um fluxo de recursão eficiente. O processo deve incluir: identificação da glosa por tipo e convênio, análise da viabilidade de recurso, coleta de documentação de suporte (prontuário, laudos, autorizações), envio dentro do prazo de recursão, e acompanhamento até resolução.</p>
<p>No TechSallus, o controle de glosas é integrado ao módulo financeiro: cada glosa gera uma pendência vinculada à conta do paciente, com status de acompanhamento e histórico de ações tomadas.</p>

<h2 id="toc-5">Indicadores para monitorar</h2>
<p>Acompanhe mensalmente: taxa de glosa por convênio (valor glosado / valor faturado), taxa de sucesso em recursões, tempo médio de resolução de glosa, e distribuição de glosas por tipo (técnica, auditoria, administrativa). Com esses dados, é possível identificar padrões e agir preventivamente onde os erros mais ocorrem.</p>
<p>O módulo de Business Intelligence do TechSallus disponibiliza esses indicadores em dashboards atualizados em tempo real, permitindo que gestores tomem decisões com base em dados, não em achismos.</p>
HTML,
  ],

  'agendamento-online-clinicas-vantagens' => [
    'slug'    => 'agendamento-online-clinicas-vantagens',
    'title'   => 'Agendamento online para clínicas: vantagens reais e como implementar',
    'cat'     => 'Agendamento',
    'cat_slug'=> 'agendamento',
    'author'  => 'Equipe TechSallus',
    'date'    => '05 mai. 2025',
    'read'    => '6 min',
    'img_bg'  => '#fff4eb',
    'img_accent' => '#ff7300',
    'toc' => [
      'Por que o agendamento online virou obrigação',
      'Vantagens operacionais concretas',
      'O que os pacientes esperam',
      'Integração com confirmação automática',
      'Como implementar sem traumatizar a equipe',
    ],
    'content' => <<<HTML
<p>Não faz muito tempo que agendamento online era visto como um diferencial sofisticado, restrito a grandes redes de saúde. Hoje, clínicas de qualquer porte que não oferecem essa opção perdem pacientes para a concorrência — silenciosamente, sem nenhuma reclamação formal.</p>

<h2 id="toc-1">Por que o agendamento online virou obrigação</h2>
<p>A pandemia acelerou em anos o que seria uma transformação gradual: o paciente passou a preferir resolver tudo pelo celular, fora do horário comercial. Pesquisas do setor indicam que mais de 60% dos pacientes preferem marcar consultas por plataformas digitais do que por telefone. Entre os menores de 40 anos, esse percentual supera 80%.</p>
<blockquote>O telefone que não atende às 22h custa uma consulta. O sistema que agenda 24 horas por dia não tira férias e não comete erros de comunicação.</blockquote>

<h2 id="toc-2">Vantagens operacionais concretas</h2>
<p>Além da conveniência para o paciente, o agendamento online traz ganhos reais para a operação da clínica. A recepção deixa de gastar horas ao telefone com marcações e remarcações, reduzindo custos de pessoal ou liberando a equipe para tarefas de maior valor. O número de faltas cai porque o sistema envia lembretes automaticamente — e o próprio paciente confirma a presença em um clique.</p>
<ul>
  <li>Redução de até 40% no volume de ligações recebidas pela recepção</li>
  <li>Queda de 25% a 35% na taxa de no-show quando combinado com confirmação automática</li>
  <li>Aumento na taxa de preenchimento da agenda (menos buracos de última hora)</li>
</ul>

<h2 id="toc-3">O que os pacientes esperam</h2>
<p>Um bom sistema de agendamento online não é apenas um formulário. O paciente espera ver os horários disponíveis em tempo real, escolher o profissional de sua preferência, receber uma confirmação imediata por e-mail ou WhatsApp, e poder remarcar ou cancelar sem precisar ligar. Qualquer fricção nesse processo faz o paciente desistir e buscar outra clínica.</p>

<h2 id="toc-4">Integração com confirmação automática</h2>
<p>O passo mais importante após implementar o agendamento online é automatizar a confirmação. No TechSallus, o sistema envia confirmações por e-mail e SMS no momento do agendamento, e lembretes 48h e 24h antes da consulta. O paciente responde e o sistema atualiza automaticamente o status na agenda — sem intervenção humana.</p>
<p>Isso elimina o trabalho manual de confirmação que, em muitas clínicas, ocupa uma funcionária de recepção durante horas por dia.</p>

<h2 id="toc-5">Como implementar sem traumatizar a equipe</h2>
<p>A resistência à mudança é real, especialmente em clínicas com equipes acostumadas ao processo tradicional. A implementação deve ser gradual: comece disponibilizando o agendamento online apenas para retornos ou para determinadas especialidades, colete feedback da equipe, ajuste o fluxo e depois amplie. Com o TechSallus, a agenda online e a agenda interna são a mesma — não existem dois sistemas para sincronizar. O que o paciente vê é reflexo exato do que a recepcionista vê.</p>
HTML,
  ],

  'prontuario-eletronico-obrigatorio-cfm' => [
    'slug'    => 'prontuario-eletronico-obrigatorio-cfm',
    'title'   => 'Prontuário eletrônico: o que a Resolução CFM N.1639-2002 exige na prática',
    'cat'     => 'Prontuário',
    'cat_slug'=> 'prontuario',
    'author'  => 'Equipe TechSallus',
    'date'    => '28 abr. 2025',
    'read'    => '10 min',
    'img_bg'  => '#eef4ff',
    'img_accent' => '#094a86',
    'toc' => [
      'O que diz a resolução CFM N.1639-2002',
      'Requisitos técnicos obrigatórios',
      'Assinatura digital e validade legal',
      'Tempo de guarda do prontuário eletrônico',
      'Auditoria e rastreabilidade',
    ],
    'content' => <<<HTML
<p>A Resolução CFM N.1639/2002 foi publicada há mais de duas décadas, mas ainda hoje é mal compreendida por gestores e profissionais de saúde. Muitos acreditam que ter qualquer sistema informatizado é suficiente para estar em conformidade. Não é. A resolução define critérios específicos que o sistema precisa atender — e ignorá-los pode resultar em problemas jurídicos sérios.</p>

<h2 id="toc-1">O que diz a resolução CFM N.1639-2002</h2>
<p>A resolução aprovou a informatização do processo de utilização dos serviços de saúde e o uso de sistemas informatizados para guarda e manuseio de prontuários de pacientes. Ela estabelece os requisitos mínimos para que um prontuário eletrônico tenha a mesma validade legal que o prontuário em papel. O CFM reconhece o prontuário eletrônico como documento oficial desde que cumpra as normas técnicas e de segurança previstas.</p>
<blockquote>O prontuário eletrônico só tem validade jurídica plena quando o sistema que o armazena atende integralmente aos requisitos da resolução CFM N.1639-2002 e da Resolução CFM N.1821/2007, que revogou parcialmente a primeira.</blockquote>

<h2 id="toc-2">Requisitos técnicos obrigatórios</h2>
<p>Entre os requisitos mais importantes estão: unicidade do prontuário por paciente (um único registro, não registros paralelos), autoria identificada de cada entrada (todo registro precisa estar associado ao profissional que o fez), imutabilidade dos registros (não é permitido alterar ou apagar informações já salvas, apenas adicionar adendos com identificação), e confidencialidade com controle de acesso granular.</p>
<ul>
  <li>Cada entrada deve ter data, hora e identificação do profissional</li>
  <li>Nenhum campo pode ser apagado — apenas corrigido com registro da correção</li>
  <li>O acesso deve ser controlado por nível de permissão</li>
  <li>Toda ação no sistema deve ser registrada em log de auditoria</li>
</ul>

<h2 id="toc-3">Assinatura digital e validade legal</h2>
<p>Para que o prontuário eletrônico substitua legalmente o papel, os documentos precisam ser assinados digitalmente por certificados ICP-Brasil. Isso garante a autoria e a integridade dos documentos. No TechSallus, os laudos e evoluções podem ser assinados digitalmente diretamente pelo sistema, com suporte a certificados A1 e A3.</p>

<h2 id="toc-4">Tempo de guarda do prontuário eletrônico</h2>
<p>O CFM determina que o prontuário do paciente adulto deve ser guardado por no mínimo 20 anos a partir do último atendimento. Para menores de idade, o prazo é de 20 anos após completarem 18 anos. Isso significa que dados inseridos hoje precisam ser acessíveis e íntegros por décadas. O sistema precisa garantir migração de dados em caso de mudança de tecnologia e backups periódicos verificados.</p>

<h2 id="toc-5">Auditoria e rastreabilidade</h2>
<p>A resolução exige que todos os acessos, modificações e visualizações ao prontuário sejam registrados. Esse log de auditoria é fundamental tanto para fins regulatórios quanto para defesa em processos judiciais. No TechSallus, o módulo de Segurança registra automaticamente cada ação no prontuário: quem acessou, quando, de qual dispositivo, e o que foi visualizado ou alterado. Esses logs são imutáveis e ficam disponíveis para consulta a qualquer momento.</p>
HTML,
  ],

  'indicadores-gestao-hospitalar-essenciais' => [
    'slug'    => 'indicadores-gestao-hospitalar-essenciais',
    'title'   => 'Os 10 indicadores de gestão hospitalar que todo gestor deveria acompanhar',
    'cat'     => 'Gestão',
    'cat_slug'=> 'gestao',
    'author'  => 'Equipe TechSallus',
    'date'    => '20 abr. 2025',
    'read'    => '12 min',
    'img_bg'  => '#f0faf4',
    'img_accent' => '#1a8a4a',
    'toc' => [
      'Por que medir é a base da gestão',
      'Indicadores financeiros essenciais',
      'Indicadores de produção e qualidade',
      'Indicadores de satisfação do paciente',
      'Como montar um painel de gestão',
    ],
    'content' => <<<HTML
<p>Gestão sem indicadores é intuição. E na saúde, decidir por intuição pode custar muito caro — tanto para as finanças da instituição quanto para a qualidade do atendimento. Os gestores hospitalares mais eficazes do Brasil têm em comum uma coisa: acompanham um conjunto enxuto de KPIs regularmente e agem quando algum sinal se desvia do esperado.</p>

<h2 id="toc-1">Por que medir é a base da gestão</h2>
<p>Muitos gestores acreditam que conhecem bem sua operação pelo tempo de experiência no setor. Esse conhecimento é valioso, mas não substitui dados. Um indicador ruim que passa despercebido por meses pode virar uma crise financeira. Um indicador bom que não está sendo medido pode ser replicado e gerar muito mais resultado. A medição regular cria consciência situacional — sem ela, você pilota no escuro.</p>
<blockquote>O que não é medido não pode ser gerenciado. Na gestão hospitalar, essa máxima tem consequências diretas no caixa e na qualidade do atendimento.</blockquote>

<h2 id="toc-2">Indicadores financeiros essenciais</h2>
<p>Os indicadores financeiros mais importantes são: taxa de glosa (valor glosado / valor faturado × 100), prazo médio de recebimento por convênio (quantos dias desde o faturamento até o pagamento), custo por atendimento (total de despesas / número de atendimentos), e margem líquida por especialidade. Esses quatro indicadores já revelam a maior parte dos problemas financeiros de uma instituição.</p>
<ul>
  <li><strong>Taxa de glosa saudável:</strong> abaixo de 5% — acima de 10% é sinal de crise</li>
  <li><strong>Prazo médio de recebimento:</strong> varia por convênio, mas acima de 45 dias compromete o fluxo de caixa</li>
  <li><strong>Custo por atendimento:</strong> deve ser monitorado por especialidade e por tipo de procedimento</li>
</ul>

<h2 id="toc-3">Indicadores de produção e qualidade</h2>
<p>No lado operacional, acompanhe: taxa de ocupação de leitos (para internação), taxa de utilização da agenda (percentual de horários preenchidos), giro de leito (número de pacientes por leito ao mês), taxa de no-show (percentual de agendamentos sem comparecimento), e tempo médio de espera. A taxa de ocupação ideal para internação fica entre 75% e 85% — abaixo disso, há ociosidade; acima, há risco de degradação da qualidade.</p>

<h2 id="toc-4">Indicadores de satisfação do paciente</h2>
<p>NPS (Net Promoter Score) hospitalar, taxa de retorno de pacientes, e índice de reclamações são indicadores de satisfação que cada vez mais influenciam a reputação e a captação de novos pacientes. Com a popularização dos sites de avaliação, uma instituição com NPS baixo perde pacientes de forma silenciosa. O módulo de pesquisa de satisfação do TechSallus (disponível no plano Premium) automatiza a coleta e o cálculo desses indicadores.</p>

<h2 id="toc-5">Como montar um painel de gestão</h2>
<p>O melhor painel de gestão é o que é visto e interpretado regularmente. Escolha no máximo 10 a 15 indicadores — mais que isso e o painel vira decoração. Defina metas para cada um, estabeleça alertas automáticos quando um indicador se desvia mais de X% da meta, e reserve um momento semanal ou quinzenal para revisão com a equipe de gestão. O módulo de Business Intelligence do TechSallus permite criar dashboards personalizados com os indicadores da sua realidade, com atualizações em tempo real e filtros por período, convênio ou especialidade.</p>
HTML,
  ],

  'tiss-padrao-intercambio-informacoes-saude' => [
    'slug'    => 'tiss-padrao-intercambio-informacoes-saude',
    'title'   => 'TISS na prática: guia completo para clínicas e hospitais',
    'cat'     => 'Faturamento',
    'cat_slug'=> 'faturamento',
    'author'  => 'Equipe TechSallus',
    'date'    => '14 abr. 2025',
    'read'    => '9 min',
    'img_bg'  => '#e8f0f9',
    'img_accent' => '#094a86',
    'toc' => [
      'O que é o padrão TISS',
      'Componentes da guia TISS',
      'Prazos e penalidades',
      'Erros mais comuns no preenchimento',
      'Versões do TISS e atualizações',
    ],
    'content' => <<<HTML
<p>Se sua clínica ou hospital atende algum plano de saúde, o padrão TISS faz parte da sua operação diária. A Troca de Informações em Saúde Suplementar foi criada pela ANS para padronizar a comunicação entre prestadores de serviços (clínicas, hospitais, laboratórios) e operadoras de planos de saúde. Entender seu funcionamento é fundamental para garantir o faturamento correto e evitar glosas desnecessárias.</p>

<h2 id="toc-1">O que é o padrão TISS</h2>
<p>O TISS é um padrão eletrônico definido pela Agência Nacional de Saúde Suplementar (ANS) para a troca de dados entre prestadores e operadoras. Ele define o formato das guias eletrônicas (XML) que devem ser enviadas para autorização e faturamento de procedimentos ambulatoriais, hospitalares e de SP/SADT (Serviço Profissional, Serviço Auxiliar de Diagnóstico e Terapia). A adesão é obrigatória para todos que atendem beneficiários de planos de saúde regulamentados pela ANS.</p>
<blockquote>O TISS não é um sistema — é um padrão. Seu sistema de gestão precisa gerar os arquivos XML no formato correto para cada tipo de guia.</blockquote>

<h2 id="toc-2">Componentes da guia TISS</h2>
<p>Existem quatro tipos principais de guia TISS: Guia de Consulta, Guia SP/SADT (para procedimentos diagnósticos e terapêuticos), Guia de Internação, e Guia de Resumo de Internação. Cada tipo tem campos obrigatórios específicos. Entre os campos mais críticos estão: número do beneficiário, CBO do profissional, código TUSS do procedimento, CID, data do atendimento, e dados do estabelecimento de saúde.</p>
<ul>
  <li>Código TUSS incorreto → glosa técnica automática</li>
  <li>CBO incompatível com o procedimento → recusa de autorização</li>
  <li>CID não compatível com procedimento → auditoria e possível glosa</li>
</ul>

<h2 id="toc-3">Prazos e penalidades</h2>
<p>Cada operadora define seus próprios prazos de envio de guias, mas a ANS estabelece limites máximos. Em geral, guias de consulta devem ser enviadas em até 30 dias após o atendimento; guias de internação têm prazos mais curtos. O descumprimento resulta em glosas administrativas automáticas, que não admitem recurso se o prazo foi realmente ultrapassado. Um sistema de alertas automáticos é essencial para garantir que nenhuma guia passe do prazo.</p>

<h2 id="toc-4">Erros mais comuns no preenchimento</h2>
<p>Após analisar os padrões de glosa em dezenas de instituições, identificamos os erros mais frequentes: código TUSS desatualizado (a tabela é revisada periodicamente), preenchimento incorreto do campo de CBO, ausência de registro de materiais e medicamentos utilizados em procedimentos cirúrgicos, inconsistência entre o código CID e o procedimento faturado, e dados do beneficiário com erros de digitação. O TechSallus valida automaticamente todos esses campos antes de gerar a guia.</p>

<h2 id="toc-5">Versões do TISS e atualizações</h2>
<p>O TISS é atualizado periodicamente pela ANS. Cada nova versão pode introduzir campos novos, modificar a estrutura de XML, ou alterar a tabela TUSS. Instituições que não atualizam seus sistemas perdem a compatibilidade com as operadoras — e as guias começam a ser rejeitadas por erro de formato. O TechSallus é atualizado automaticamente sempre que a ANS publica uma nova versão do padrão TISS, garantindo conformidade permanente sem trabalho manual.</p>
HTML,
  ],

  'lgpd-saude-dados-pacientes-conformidade' => [
    'slug'    => 'lgpd-saude-dados-pacientes-conformidade',
    'title'   => 'LGPD na saúde: o que muda na gestão de dados dos seus pacientes',
    'cat'     => 'Tecnologia',
    'cat_slug'=> 'tecnologia',
    'author'  => 'Equipe TechSallus',
    'date'    => '07 abr. 2025',
    'read'    => '11 min',
    'img_bg'  => '#f5f0ff',
    'img_accent' => '#6b4fa0',
    'toc' => [
      'Por que a LGPD é especialmente crítica na saúde',
      'Dados sensíveis: o que entra nessa categoria',
      'Bases legais para tratamento de dados de saúde',
      'Obrigações práticas do controlador',
      'Como o sistema ajuda na conformidade',
    ],
    'content' => <<<HTML
<p>A Lei Geral de Proteção de Dados (Lei 13.709/2018) entrou em vigor em setembro de 2020 e as sanções passaram a ser aplicadas a partir de agosto de 2021. Para o setor de saúde, a LGPD tem um peso especial: dados de saúde são classificados como dados sensíveis, o que significa requisitos mais rígidos e penalidades mais severas em caso de violação.</p>

<h2 id="toc-1">Por que a LGPD é especialmente crítica na saúde</h2>
<p>Clínicas, hospitais e laboratórios são controladores de dados por definição — coletam, armazenam, processam e compartilham dados pessoais de pacientes rotineiramente. Além disso, esses dados incluem informações de saúde, que a lei classifica como dados sensíveis. Isso significa que o tratamento desses dados exige consentimento específico do titular ou enquadramento em uma das hipóteses legais previstas na lei — e o controlador precisa conseguir comprovar qual hipótese está sendo utilizada.</p>
<blockquote>Dados de saúde revelam condições íntimas da pessoa, histórico de doenças, hábitos de vida. Uma violação não é apenas um problema jurídico — é um dano à dignidade do paciente.</blockquote>

<h2 id="toc-2">Dados sensíveis: o que entra nessa categoria</h2>
<p>Na saúde, praticamente todo dado coletado é sensível ou diretamente vinculado a dado sensível: diagnósticos, resultados de exames, prescrições médicas, histórico de internações, dados genéticos, informações sobre saúde mental, condições crônicas, e histórico de procedimentos cirúrgicos. Mesmo dados aparentemente simples, como o fato de um paciente frequentar uma determinada especialidade médica, podem revelar condições de saúde sensíveis.</p>
<ul>
  <li>Diagnósticos (CID) → dado sensível</li>
  <li>Resultados de exames → dado sensível</li>
  <li>Medicamentos prescritos → podem revelar condições sensíveis</li>
  <li>Frequência a especialidades (ex: oncologia, psiquiatria) → dado sensível indiretamente</li>
</ul>

<h2 id="toc-3">Bases legais para tratamento de dados de saúde</h2>
<p>O artigo 11 da LGPD define as hipóteses em que dados sensíveis podem ser tratados sem o consentimento explícito do titular. Para o setor de saúde, as mais relevantes são: tutela da saúde (tratamento por profissionais de saúde para atendimento ao paciente), cumprimento de obrigação legal ou regulatória (como envio de guias TISS à ANS), e exercício regular de direitos em processo judicial. O consentimento ainda pode ser a base legal em situações específicas, mas a tutela da saúde elimina a necessidade de consentimento para a maior parte do atendimento clínico.</p>

<h2 id="toc-4">Obrigações práticas do controlador</h2>
<p>As principais obrigações incluem: manter um Registro de Operações de Tratamento de Dados (inventário de dados), designar um Encarregado de Proteção de Dados (DPO), garantir o direito de acesso, correção e exclusão de dados pelo titular, implementar medidas técnicas e organizacionais de segurança, e notificar a ANPD e os titulares em caso de incidente de segurança. O prazo para notificação de incidente é de 72 horas após a detecção — o que exige processos claros e sistemas com logs de acesso para identificar rapidamente o que foi exposto.</p>

<h2 id="toc-5">Como o sistema ajuda na conformidade</h2>
<p>O TechSallus foi desenvolvido com privacidade por design: todos os dados são transmitidos com criptografia HTTPS, o banco de dados é criptografado em repouso, o controle de acesso granular garante que cada profissional só vê os dados que precisa para sua função, e o módulo de Segurança registra todos os acessos com log imutável. Para apoiar o atendimento de requisições de titulares (acesso, correção, portabilidade), o sistema permite exportar em formato estruturado todos os dados de um paciente específico — reduzindo para minutos um processo que poderia levar dias.</p>
HTML,
  ],

];

/* ── Roteamento ──────────────────────────────────────────────── */
$slug    = isset($_GET['slug']) ? preg_replace('/[^a-z0-9\-]/', '', $_GET['slug']) : '';
$article = $ALL_ARTICLES[$slug] ?? null;

if (!$article) {
  http_response_code(404);
  header('Location: /blog/');
  exit;
}

/* ── Artigos relacionados (mesma categoria, exceto o atual) ──── */
$related = array_filter($ALL_ARTICLES, fn($a) => $a['cat_slug'] === $article['cat_slug'] && $a['slug'] !== $slug);
if (count($related) < 3) {
  $others = array_filter($ALL_ARTICLES, fn($a) => $a['slug'] !== $slug && !isset($related[$a['slug']]));
  $related = array_merge($related, $others);
}
$related = array_slice(array_values($related), 0, 3);
?>
<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($article['title']) ?> — TechSallus Blog</title>
  <meta name="description" content="<?= htmlspecialchars(mb_substr(strip_tags($article['content']), 0, 160)) ?>"/>

  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg"/>
  <link rel="shortcut icon" href="/assets/img/favicon.svg"/>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="/assets/css/main.css"/>
  <link rel="stylesheet" href="/assets/css/blog.css"/>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <a href="/" aria-label="TechSallus – voltar ao site">
      <img src="/assets/img/logo.png" alt="Techsallus" class="nav-logo"/>
    </a>
    <div class="nav-links">
      <a href="/"><?= t('nav_home') ?></a>
      <a href="/#sobre"><?= t('nav_system') ?></a>
      <a href="/#modulos"><?= t('nav_modules') ?></a>
      <a href="/#planos"><?= t('nav_plans') ?></a>
      <a href="/#suporte"><?= t('nav_support') ?></a>
      <a href="/blog/" class="active"><?= t('nav_blog') ?></a>
    </div>
    <div class="nav-right">
      <div class="lang-switcher" id="lang-switcher">
        <button class="lang-btn" id="lang-btn" aria-haspopup="listbox" aria-expanded="false">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
          <span class="lang-current"><?= t('lang_label') ?></span>
          <svg class="lang-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <ul class="lang-dropdown" role="listbox" aria-label="Selecionar idioma">
          <li><a href="/setlang.php?lang=pt"<?= $LANG==='pt' ? ' class="lang-active"' : '' ?>><?= t('lang_pt') ?></a></li>
          <li><a href="/setlang.php?lang=en"<?= $LANG==='en' ? ' class="lang-active"' : '' ?>><?= t('lang_en') ?></a></li>
          <li><a href="/setlang.php?lang=es"<?= $LANG==='es' ? ' class="lang-active"' : '' ?>><?= t('lang_es') ?></a></li>
        </ul>
      </div>
      <a href="/#contato" class="nav-cta"><?= t('nav_cta') ?></a>
    </div>
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Abrir menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu de navegação">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Fechar menu">&#x2715;</button>
  <nav>
    <a href="/"><?= t('nav_home') ?></a>
    <a href="/#sobre"><?= t('nav_system') ?></a>
    <a href="/#modulos"><?= t('nav_modules') ?></a>
    <a href="/#planos"><?= t('nav_plans') ?></a>
    <a href="/#suporte"><?= t('nav_support') ?></a>
    <a href="/blog/"><?= t('nav_blog') ?></a>
    <a href="/#contato" style="color:#ff7300"><?= t('nav_demo') ?></a>
  </nav>
  <div class="mobile-lang">
    <a href="/setlang.php?lang=pt"<?= $LANG==='pt' ? ' class="lang-active"' : '' ?>>PT</a>
    <span class="mobile-lang-sep">|</span>
    <a href="/setlang.php?lang=en"<?= $LANG==='en' ? ' class="lang-active"' : '' ?>>EN</a>
    <span class="mobile-lang-sep">|</span>
    <a href="/setlang.php?lang=es"<?= $LANG==='es' ? ' class="lang-active"' : '' ?>>ES</a>
  </div>
</div>

<main>

<!-- ARTICLE HERO -->
<section class="article-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Navegação estrutural">
      <a href="/">Início</a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <a href="/blog/">Blog</a>
      <span class="breadcrumb-sep" aria-hidden="true">›</span>
      <span><?= htmlspecialchars($article['cat']) ?></span>
    </nav>
    <span class="article-cat-badge"><?= htmlspecialchars($article['cat']) ?></span>
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <div class="article-hero-meta">
      <span><?= htmlspecialchars($article['author']) ?></span>
      <span class="article-hero-meta-dot" aria-hidden="true"></span>
      <span><?= htmlspecialchars($article['date']) ?></span>
      <span class="article-hero-meta-dot" aria-hidden="true"></span>
      <span><?= htmlspecialchars($article['read']) ?> de leitura</span>
    </div>
  </div>
</section>

<!-- ARTICLE BODY -->
<div class="article-body-wrap">
  <div class="container">

    <!-- Cover illustration -->
    <div style="width:100%; height:360px; background:<?= $article['img_bg'] ?>; border-radius:12px; margin-bottom:48px; display:flex; align-items:center; justify-content:center;">
      <svg width="80" height="80" viewBox="0 0 80 80" fill="none" aria-hidden="true">
        <rect x="10" y="8" width="60" height="64" rx="6" fill="<?= $article['img_accent'] ?>" opacity="0.12"/>
        <rect x="20" y="20" width="40" height="5" rx="2.5" fill="<?= $article['img_accent'] ?>" opacity="0.5"/>
        <rect x="20" y="32" width="32" height="5" rx="2.5" fill="<?= $article['img_accent'] ?>" opacity="0.4"/>
        <rect x="20" y="44" width="36" height="5" rx="2.5" fill="<?= $article['img_accent'] ?>" opacity="0.3"/>
        <rect x="20" y="56" width="24" height="5" rx="2.5" fill="<?= $article['img_accent'] ?>" opacity="0.2"/>
      </svg>
    </div>

    <div class="article-layout">

      <!-- Content -->
      <div class="article-content">
        <div class="article-content-body">
          <?= $article['content'] ?>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="article-sidebar">

        <!-- TOC -->
        <div class="sidebar-toc">
          <div class="sidebar-toc-title">Neste artigo</div>
          <ol>
            <?php foreach ($article['toc'] as $item): ?>
              <li><a href="#"><?= htmlspecialchars($item) ?></a></li>
            <?php endforeach; ?>
          </ol>
        </div>

        <!-- CTA card -->
        <div class="sidebar-cta-card">
          <h3>Conheça o TechSallus</h3>
          <p>Veja como o sistema resolve na prática os desafios que você acabou de ler.</p>
          <a href="/#contato" class="btn-sidebar-cta">Agendar demonstração gratuita</a>
        </div>

      </aside>
    </div>
  </div>
</div>

<!-- RELATED ARTICLES -->
<?php if (!empty($related)): ?>
<section class="related-section">
  <div class="container">
    <h2>Artigos relacionados</h2>
    <div class="related-grid">
      <?php foreach ($related as $r): ?>
        <article class="article-card">
          <a href="/blog/artigo.php?slug=<?= htmlspecialchars($r['slug']) ?>" class="article-card-img" tabindex="-1" aria-hidden="true" style="background:<?= $r['img_bg'] ?>; display:flex; align-items:center; justify-content:center; height:160px; text-decoration:none;">
            <svg width="40" height="40" viewBox="0 0 56 56" fill="none" aria-hidden="true">
              <rect x="8" y="6" width="40" height="44" rx="4" fill="<?= $r['img_accent'] ?>" opacity="0.15"/>
              <rect x="14" y="14" width="28" height="3" rx="1.5" fill="<?= $r['img_accent'] ?>" opacity="0.5"/>
              <rect x="14" y="22" width="22" height="3" rx="1.5" fill="<?= $r['img_accent'] ?>" opacity="0.4"/>
              <rect x="14" y="30" width="26" height="3" rx="1.5" fill="<?= $r['img_accent'] ?>" opacity="0.3"/>
            </svg>
          </a>
          <div class="article-card-body">
            <span class="article-cat"><?= htmlspecialchars($r['cat']) ?></span>
            <h2><a href="/blog/artigo.php?slug=<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['title']) ?></a></h2>
            <div class="article-meta">
              <span><?= htmlspecialchars($r['date']) ?></span>
              <span class="article-meta-dot"></span>
              <span><?= htmlspecialchars($r['read']) ?> de leitura</span>
            </div>
            <a href="/blog/artigo.php?slug=<?= htmlspecialchars($r['slug']) ?>" class="article-read-link">
              Ler artigo
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                <path d="M2 7h10M8 3l4 4-4 4" stroke="#094a86" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

</main>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <img src="/assets/img/logo.png" alt="Techsallus" class="footer-logo"/>
        <p class="footer-tagline">Sistema de gestão hospitalar desde 1994</p>
      </div>
      <div>
        <div class="footer-col-title">Sistema</div>
        <div class="footer-links">
          <a href="/">Sistema</a>
          <a href="/#modulos">Módulos</a>
          <a href="/#planos">Planos</a>
          <a href="/#suporte">Suporte</a>
          <a href="/blog/">Blog</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Contato</div>
        <div class="footer-contact">
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
            <a href="mailto:faleconosco@techsallus.com.br">faleconosco@techsallus.com.br</a>
          </div>
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            <span>Rua Ewerton Visco, 290 · Ed. Boulevard Side, Salas 1601 · Salvador, Bahia</span>
          </div>
          <div class="footer-contact-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff7300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            <span>Segunda a sexta, 8h às 18h</span>
          </div>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Presente em</div>
        <div class="footer-states">
          <?php foreach (['São Paulo','Rio de Janeiro','Bahia','Espírito Santo','Rondônia','Maranhão','Sergipe','Alagoas'] as $s): ?>
            <div class="footer-state"><div class="footer-state-dot"></div><span class="footer-state-name"><?= htmlspecialchars($s) ?></span></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span class="footer-copy">© 2025 Techsallus. Todos os direitos reservados.</span>
      <a href="/admin/" class="footer-access">Acesso Restrito</a>
    </div>
  </div>
</footer>

<a href="https://wa.me/557181299624?text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20sistema%20de%20voc%C3%AAs"
   target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Fale conosco pelo WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" width="26" height="26" aria-hidden="true">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.974-1.304A9.963 9.963 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="none" stroke="white" stroke-width="1.5"/>
  </svg>
</a>

<script src="/assets/js/main.js"></script>
<script src="/assets/js/blog.js"></script>
</body>
</html>
