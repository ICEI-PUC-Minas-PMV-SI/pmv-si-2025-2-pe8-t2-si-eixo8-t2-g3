2 Plano de Inteligência Competitiva (IC)
========================================

2.1 Identificação de Necessidades de Inteligência Competitiva
-------------------------------------------------------------

Este capítulo tem como objetivo identificar as decisões estratégicas
mais críticas enfrentadas pela Delta Coding, derivar os tópicos de
inteligência necessários para embasá-las e mapear as fontes de
informação requeridas. O processo segue a metodologia de *Key
Intelligence Topics* (KITs) e *Key Intelligence Questions* (KIQs) para
garantir que o esforço de coleta de informação seja focado, eficiente e
direcionado para apoiar a tomada de decisão.

### 2.1.1 Mapeamento das decisões críticas

1.  **Decisão de Portfólio e Precificação:** Deve a empresa expandir seu
    portfólio para incluir linhas de equipamentos laser, de maior valor
    agregado?

2.  **Decisão Comercial (Upsell/Cross-sell):** Deve a empresa criar e
    oferecer formalmente contratos de manutenção preventiva com preços
    fixos para seus clientes?

3.  **Decisão de Canais:** Deve a empresa investir mais pesadamente no
    canal digital (e-commerce e marketplaces) em detrimento da força de
    vendas tradicional?

4.  **Decisão de Expansão Geográfica:** Deve a empresa expandir suas
    operações para atender outros municipios além da região
    metropolitana de Belo Horizonte?

### 2.1.2 Escolha de uma decisão-chave

**Decisão Escolhida:** "Deve a Delta Coding criar e oferecer formalmente
**contratos de manutenção preventiva com preços fixos (Planos de
Manutenção)** para sua base de clientes?"  
**Razão da Escolha:**

-   O sistema integrado será uma ferramenta **crítica** para
    viabilizá-la e gerenciá-la com eficiência (controlando agendamentos,
    custos, e evitando o "esquecimento" dos clientes), mas a decisão em
    si é estratégica e comercial.

-   **Alta Relevância:** Impacta diretamente a receita recorrente, a
    fidelização de clientes e a previsibilidade financeira, mitigando a
    sazonalidade.

-   **Resolve uma Fraqueza:** Ataca diretamente a "Dependência de
    comunicação via WhatsApp", pois os contratos seriam gerenciados no
    novo sistema.

-   **Explora uma Oportunidade:** Alinha-se perfeitamente com a
    oportunidade de "Upsell de contratos de manutenção" e "Potencial de
    expansão através de contratos recorrentes" identificadas na SWOT.

### 2.1.3 Definição do KIT (Key Intelligence Topic)

O **Tópico-Chave de Inteligência** é o assunto macro sobre o qual a
inteligência será produzida para subsidiar a decisão escolhida.

**KIT:** "A viabilidade e o potencial de lucratividade da implementação
de um programa de **planos anuais de manutenção preventiva** como um
novo modelo de negócio para a Delta Coding."

### 2.1.4 Formulação das KIQs (Key Intelligence Questions)

1.  **KIQ 1 (Interno/Operacional):** Quais são os **custos reais**
    envolvidos na execução de uma visita de manutenção preventiva (mão
    de obra técnica, deslocamento, peças consumíveis)? Qual é a
    capacidade ociosa atual da equipe técnica para absorver essa nova
    demanda sem afetar os serviços corretivos?

2.  **KIQ 2 (Econômico/Modelo):** Qual modelo de precificação (ex:
    percentual do valor do equipamento, preço fixo por ticket, tier de
    planos: básico, premium) seria mais competitivo e lucrativo para a
    Delta Coding, considerando sua estrutura de custos e o mercado?

3.  **KIQ 3 (Estoque/Planejamento e Previsão):** Qual é o nível ótimo de
    peças e insumos em estoque necessário para garantir a execução das
    manutenções preventivas sem gerar excesso de capital imobilizado?
    Como prever a demanda de peças com base no histórico de falhas e nas
    características dos equipamentos atendidos?

4.  **KIQ 4 (Logístico/Regional):** Como a distribuição geográfica dos
    clientes impacta o tempo de deslocamento e os custos logísticos das
    manutenções preventivas? Quais regiões apresentam maior concentração
    de equipamentos e poderiam justificar bases técnicas regionais ou
    parcerias locais?

5.  **KIQ 5 (Tempo médio/Tipos de Máquinas):** Qual é o tempo médio de
    execução de uma manutenção preventiva por tipo de máquina? Existem
    variações significativas de tempo e complexidade entre modelos que
    impactem o dimensionamento da equipe e a precificação do serviço?

### 2.1.5 Justificativa da relevância do KIT e das KIQs para a empresa

Responder a essas perguntas é fundamental para evitar que a Delta Coding
tome uma decisão baseada apenas em suposições. Um programa de manutenção
mal estruturado pode:

-   Ser precificado abaixo do custo, gerando prejuízo.

-   Não ser atraente para os clientes, resultando em baixa adesão.

-   Sobrecarregar a equipe técnica, prejudicando o atendimento
    corretivo.

-   Falhar em criar a desejada receita recorrente estável.

Portanto, o KIT e as KIQs são diretamente relevantes para:

-   **Aumentar a Receita Recorrente:** Criando uma fonte de receita
    previsível e estável.

-   **Fidelizar Clientes:** Transformando clientes de "vendas pontuais"
    em "parceiros de longo prazo".

-   **Otimizar a Operação:** Permitindo um planejamento mais eficiente
    da agenda dos técnicos e da gestão de estoque de peças.

**Mitigar Riscos:** Evitando entrar em um novo modelo de negócio sem
entender plenamente os custos, a concorrência e a demanda do cliente.

2.2 Identificação das Necessidades de Informação
------------------------------------------------

<table>
<thead>
<tr class="header">
<th><strong>KIQ</strong></th>
<th><strong>Necessidades de Informação</strong></th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>KIQ 1 (Interno/Operacional)</td>
<td>- Tempo médio entre opened_at e closed_at para ordens de serviço.<br />
- Custos totais registrados em total_amount.<br />
- Lista de peças utilizadas (parts_list).<br />
- Distribuição de chamados por técnico (technician_name).</td>
</tr>
<tr class="even">
<td>KIQ 2 (Econômico/Modelo)</td>
<td>- Valor médio de ordens de serviço (total_amount).<br />
- Frequência de abertura de chamados por cliente (customer_id).<br />
- Tipos de problemas mais recorrentes (problem_description).</td>
</tr>
<tr class="odd">
<td>KIQ 3 (Estoque/Planejamento e Previsão)</td>
<td>- Peças mais utilizadas nas manutenções (parts_list).<br />
- Frequência de uso das peças por cliente e por tipo de serviço.<br />
- Histórico de serviços realizados (services_done).</td>
</tr>
<tr class="even">
<td>KIQ 4 (Logístico/Regional)</td>
<td>- Localização dos clientes (address).<br />
- Quantidade de ordens abertas por região (CUSTOMERS.address + SERVICE_ORDERS.customer_id).<br />
- Relação entre distância (endereço) e tempo médio de atendimento (opened_at → closed_at).</td>
</tr>
<tr class="odd">
<td>KIQ 5 (Tempo médio/Tipos de Máquinas)</td>
<td>- Tempo médio de fechamento por tipo de problema (problem_description + diferença opened_at / closed_at).<br />
- Correlação entre complexidade do serviço (services_done) e duração.<br />
- Comparação de tempos entre diferentes ordens com problemas semelhantes.</td>
</tr>
</tbody>
</table>

2.3 Especificação de Requisitos Informacionais
----------------------------------------------

Esta seção traduz as necessidades estratégicas expressas nas KIQs em
informações, indicadores e funcionalidades que o sistema deverá prover
para suportar a decisão-chave (planos de manutenção preventiva).

### 2.3.1 Vinculação às KIQs (o que o sistema precisa entregar)

| **KIQ**                                     | **Saídas/Informações que o sistema/BI deve gerar**                                                                                                                                                         | **Uso na decisão**                                                                          |
|---------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------|
| **KIQ 1 (Interno/Operacional)**             | Relatório de ordens de serviço com **tempo médio de atendimento** (opened\_at → closed\_at), **valores totais por OS** (total\_amount), e distribuição de atendimentos por **técnico** (technician\_name). | Avaliar produtividade da equipe e impacto financeiro das manutenções preventivas.           |
| **KIQ 2 (Econômico/Modelo)**                | Relatório com **valor médio das ordens de serviço** (total\_amount), **quantidade de chamados por cliente** (customer\_id), e **problemas mais recorrentes** (problem\_description).                       | Ajustar modelos de contrato e identificar perfis de clientes mais rentáveis.                |
| **KIQ 3 (Estoque/Planejamento e Previsão)** | Listagem consolidada das **peças utilizadas** (parts\_list), frequência de uso por ordem de serviço, e histórico de **serviços realizados** (services\_done).                                              | Planejar estoque mínimo necessário e prever demanda de insumos.                             |
| **KIQ 4 (Logístico/Regional)**              | Relatório de **ordens de serviço por região** (CUSTOMERS.address), tempo médio de atendimento por localidade (opened\_at → closed\_at), e quantidade de chamados por cliente em cada região.               | Avaliar custo logístico e identificar necessidade de bases regionais.                       |
| **KIQ 5 (Tempo médio/Tipos de Máquinas)**   | Relatórios com **tempo médio de atendimento por tipo de problema** (problem\_description), comparativo entre **serviços realizados** (services\_done) e duração (opened\_at → closed\_at).                 | Definir parâmetros de SLA por tipo de máquina/problema e ajustar dimensionamento da equipe. |

### 2.3.2 KPIs do programa (definições e medições)

| **KPI**                                   | **Definição/Fórmula**                                 | **Fonte**                   | **Periodicidade** | **Responsável** | **Meta inicial**             |
|-------------------------------------------|-------------------------------------------------------|-----------------------------|-------------------|-----------------|------------------------------|
| **Tempo médio de atendimento (TMA)**      | Média do tempo entre opened\_at e closed\_at          | SERVICE\_ORDERS             | Mensal            | Operação        | ≤ 48h                        |
| **Taxa de resolução na 1ª visita (FTFR)** | Nº de OS resolvidas em uma visita ÷ total de OS × 100 | SERVICE\_ORDERS             | Mensal            | Operação        | ≥ 80%                        |
| **Ticket médio (R$)**                     | Σ total\_amount ÷ nº de OS concluídas                 | SERVICE\_ORDERS             | Mensal            | Financeiro      | Definir após 1º ciclo        |
| **Frequência de chamados por cliente**    | Nº de OS por customer\_id ÷ total de clientes         | CUSTOMERS + SERVICE\_ORDERS | Trimestral        | Comercial       | Identificar top 10% clientes |
| **Uso de peças por OS**                   | Nº de peças listadas em parts\_list ÷ total de OS     | SERVICE\_ORDERS             | Mensal            | Operação        | Monitorar tendência          |

### 2.3.3 Requisitos funcionais e não funcionais

**Requisitos funcionais (com prioridade):**

| **ID** | **Descrição**                                                           | **Relacionado** | **Prioridade** |
|--------|-------------------------------------------------------------------------|-----------------|----------------|
| R1     | Gestão de contratos de pla                                              | KIQ4            | MVP            |
| R2     | Agenda de preventivas com regras de SLA re replanejamento               | KIQ2            | MVP            |
| R3     | Portal do técnico                                                       | KIQ3            | MVP            |
| R4     | Custeio por visita (horas, km, peças) e rateio por contrato             | KIQ3            | MVP            |
| R5     | Yampi (clientes/produtos) e Whatsapp Business/api (notificações/anexos) | KIQ1/2          | MVP            |
| R6     | Dashboards Executivo/Operação/Qualidade (KPIs da 2.3.2)                 | KIQ 1–4         | MVP            |
| R7     | Alertas (SLA a vencer, estoque crítico)                                 | KIQ2/3          | Fase 2         |
| R8     | Catálogo de dados + trilhas de auditoria                                | Todas           | Fase 2         |

**Requisitos não funcionais:**

| **Categoria**   | **Especificação**                                                                                                                                   |
|-----------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| Segurança       | Perfis de acesso por função; verificação em 2 etapas; conexão segura (HTTPS) e dados cifrados; registro de acessos (auditoria).                     |
| Disponibilidade | Se o sistema cair, voltar a funcionar em até 8 horas; perder no máximo os dados do último dia; backups diários testados; monitoramento com alertas. |
| Desempenho      | Painéis com até 12 meses de dados abrindo em até 5 segundos; importações feitas à noite ou com atualização frequente ao longo do dia.               |
| Compliance      | Atender à LGPD; revisar riscos de privacidade antes de entrar no ar; contratos com fornecedores que tratam dados.                                   |

### 

### 2.3.4 Protótipo dos painéis (wireframes ou descrição)

O sistema terá dois módulos principais, cada um baseado em um CRUD:
Clientes e Ordens de Serviço.

Módulo de Gestão de Clientes (CRUD Clientes)

Tela: Clientes

1.  Cabeçalho (Header)

    -   Logo da Delta Coding (canto superior esquerdo).

    -   Menu de Navegação: Dashboard \| Clientes (ativo) \| Ordens de
        Serviço \| Relatórios.

    -   Perfil do Usuário (canto superior direito) com opções de
        logout/configurações.

2.  Área de Ações

    -   Título da Página: *"Gestão de Clientes"*.

    -   Botão Primário: + Novo Cliente → abre formulário de cadastro.

    -   Barra de Pesquisa: *"Buscar por nome, documento ou email"*.

3.  Tabela de Clientes

    -   Colunas:

        -   Nome (name)

        -   Documento (document)

        -   Email (email)

        -   Telefone (phone)

        -   Endereço (address)

        -   Ações: Ver, Editar, Excluir

    -   A ação *Ver* abre a página de detalhes do cliente, listando
        também todas as ordens de serviço relacionadas.

Módulo de Gestão de Ordens de Serviço (CRUD OS)

Tela: Ordens de Serviço

1.  Cabeçalho (Header)

    -   Mesmo padrão do módulo de clientes.

    -   Item ativo: *Ordens de Serviço*.

2.  Área de Ações

    -   Título da Página: *"Gestão de Ordens de Serviço"*.

    -   Botão Primário: + Nova OS → abre formulário para criar uma nova
        OS vinculada a um cliente.

    -   Barra de Pesquisa: *"Buscar por cliente, status ou data"*.

    -   Filtros: Status (Aberta, Em andamento, Concluída).

3.  Tabela de Ordens de Serviço

    -   Colunas:

        -   Nº OS (id)

        -   Cliente (customer\_name\_snapshot)

        -   Status (status)

        -   Aberta em (opened\_at)

        -   Fechada em (closed\_at)

        -   Valor Total (total\_amount)

        -   Técnico (technician\_name)

        -   Ações: Ver, Editar, Excluir

Tela de Detalhes da Ordem de Serviço

1.  Cabeçalho da OS

    -   Botão "Voltar" → retorna à lista de OS.

    -   Título: *"OS \#ID – \[Nome do Cliente\]"*.

2.  Seções de Informações

    -   Cliente: Nome, telefone, email (campos de snapshot).

    -   Descrição do Problema (problem\_description).

    -   Serviços Executados (services\_done).

    -   Peças Utilizadas (parts\_list).

    -   Status e Datas: status, opened\_at, closed\_at.

    -   Valor Total (total\_amount).

    -   Técnico Responsável (technician\_name).

3.  Ações

    -   Editar OS: abre formulário de edição.

    -   Finalizar OS: altera status para concluído e preenche
        closed\_at.

**  
**

2.4 Critérios de aceite e sucesso (MVP)
---------------------------------------

Considera-se que o MVP atende ao escopo quando for possível:

\(i\) **cadastrar, editar e consultar contratos de plano** (modalidades,
vigência, cobertura, preço e anexos);

\(ii\) **planejar preventivas** com definição de SLA e replanejamento;

\(iii\) **registrar visitas técnicas** (checklist, fotos, peças e
assinatura do cliente) pelo portal/app do técnico;

\(iv\) **apurar o custo direto por visita** (hora, deslocamento e peças)
vinculado ao contrato;

\(v\) **enviar avisos** ao cliente (ex.: confirmação/lembrança de
visita) via WhatsApp **Business**;

\(vi\) **visualizar no BI** os **KPIs da Seção 2.3.2** em painéis
Executivo, Operação e Qualidade.

**Metas de sucesso (primeiros 90 dias).**

-   Taxa de adesão aos planos ≥ 10% da base elegível;

-   SLA cumprido ≥ 85%;

-   FTFR (taxa de resolução na 1ª visita) ≥ 75%;

-   Margem média por plano ≥ 30%;

-   Taxa de cancelamento e não renovação ≤ 3%/mês.

2.5 Compliance e Segurança do Sistema
-------------------------------------

Este capítulo aborda as estratégias e implementações de *compliance* e
segurança adotadas no desenvolvimento do **Delta Coding Service
Manager**, com ênfase na conformidade com a **Lei Geral de Proteção de
Dados (LGPD)**, Lei nº 13.709/2018. As medidas descritas a seguir visam
garantir a integridade, a confidencialidade e a disponibilidade dos
dados pessoais, bem como a transparência no relacionamento com os
titulares.

### 2.5.1 Conformidade com a Lei Geral de Proteção de Dados (LGPD)

O sistema foi projetado para atuar em consonância com os princípios da
LGPD, especialmente o **princípio da finalidade**, o **princípio da
adequação**, o **princípio da necessidade** e o **princípio da
transparência**. O tratamento de dados pessoais, como nome, documento,
e-mail, telefone e endereço, é justificado pela necessidade de prestação
dos serviços de ordens de serviço e contato com o cliente.

-   **2.5.1.1 Consentimento e Transparência:** No processo de cadastro
    de cliente, o sistema implementa um mecanismo de consentimento
    informado, no qual o titular dos dados é convidado a aceitar
    explicitamente os **Termos de Uso** e a **Política de Privacidade**
    da empresa. Esses documentos detalham a finalidade da coleta, o tipo
    de dados coletados e os direitos do titular.

-   **2.5.1.2 Direitos do Titular:** Para garantir o exercício dos
    direitos previstos na LGPD, o sistema oferece funcionalidades que
    permitem ao cliente solicitar:

    -   **Acesso:** Visualização completa de todos os dados pessoais
        registrados em seu cadastro por meio da ação **Ver** no Módulo
        de Clientes.

    -   **Correção:** Possibilidade de edição dos dados incorretos,
        incompletos ou desatualizados.

    -   **Exclusão:** Implementação de um fluxo de solicitação de
        exclusão de dados, assegurando que as informações sejam
        removidas de forma segura, ressalvadas as obrigações legais de
        retenção.

### 2.5.2 Medidas de Segurança da Informação

Para proteger os dados contra acessos não autorizados, perdas ou
alterações ilícitas, o sistema incorpora medidas técnicas e
organizacionais robustas, alinhadas às melhores práticas de segurança da
informação.

-   **2.5.2.1 Controles de Acesso:** O acesso aos dados é restrito a
    usuários autorizados por meio de autenticação segura e perfis de
    permissão. Essa abordagem garante que cada usuário tenha acesso
    apenas às informações essenciais para a execução de suas funções.

-   **2.5.2.2 Criptografia de Dados:** Dados sensíveis, como números de
    documentos e senhas de acesso, são armazenados no banco de dados com
    a aplicação de criptografia. Essa medida mitiga o risco de exposição
    em caso de comprometimento da base de dados.

-   **2.5.2.3 Auditoria (Logs de Atividade):** O sistema mantém um
    registro detalhado (*log*) de todas as atividades relevantes,
    incluindo criação, modificação e exclusão de cadastros de clientes e
    ordens de serviço. Tais registros são cruciais para auditorias
    internas, investigações de segurança e para assegurar a
    rastreabilidade das ações.

### 2.5.3 Auditoria e Melhoria Contínua

A implementação de *compliance* não se encerra com o desenvolvimento
inicial. O **Delta Coding Service Manager** foi concebido com uma
arquitetura que permite a melhoria contínua dos processos. A tela de
**Relatórios** é uma ferramenta de auditoria interna, que possibilita a
geração de relatórios sobre o volume de dados tratados, a frequência de
acesso e potenciais incidentes de segurança.

**Observações.**

As metas poderão ser revisadas após o primeiro ciclo de medição.

1.  Caso a integração com WhatsApp seja **manual** no MVP, mantém-se o
    critério (v) com envio assistido; a automação completa pode ficar
    para fase posterior.
