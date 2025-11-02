3 Desenvolvimento de soluções de SI
====================================

3.1 Conexão com o Plano de IC e Planejamento da Solução
-------------------------------------------------------

O projeto terá como funcionalidade inicial, essencial e simplificada
(MVP), o Módulo de Gestão de Ordem de Serviço.

Este módulo será composto por três funcionalidades primárias, visando
unificar as informações e melhorar o gerenciamento das demandas:

**Cadastro de Clientes Completo:** Permitirá adicionar, editar e
consultar todas as informações necessárias dos clientes, formando a base
de dados essencial para o sistema.

**Criação e Rastreamento da OS:** Será a ferramenta central para
registrar novas Ordens de Serviço de forma digital e padronizada,
garantindo que todas as informações de atendimento sejam unificadas em
um só sistema.

**Visualização Preliminar de Custos na OS:** Integrará campos na Ordem
de Serviço para permitir a visualização de custos associados ao serviço,
como insumos e tempo, auxiliando a empresa a gerenciar melhor suas
demandas e despesas.

Em resumo, a aplicação resolverá a dispersão de dados, iniciando pela
**digitalização e unificação do processo de Ordens de Serviço**,
conforme a necessidade urgente identificada

**  
**

**Quadro-resumo**

| **Problema mapeado**                                                    | **Solução Proposta**                                           | **Como Será Resolvido no Sistema**                                                                                         |
|-------------------------------------------------------------------------|----------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| Cadastro de clientes em planilhas                                       | Cadastros dos clientes padronizados no sistema                 | Será criado a funcionalidade de registro de clientes no sistema                                                            |
| Ordem de serviço criada em documento Word                               | criação de um sistema para criação de ordem de serviço         | Clientes e empregados da empresa deverão criar as ordens de serviço no sistema para facilitar o gerenciamento das demandas |
| Falta ferramenta para visualização das ordens de serviços e manutenções | Dashboard para visualizar as ordens de serviços de manutenções | Criação de um dashboard com as informações do atualizadas no sistema                                                       |

3.2 Levantamento de Requisitos e Modelagem Inicial 
--------------------------------------------------

### 3.2.1 Histórias de Usuário

**US01 – Registrar Ordem de Serviço (Cliente/Atendente)**

Como cliente cadastrado quero registrar uma OS informando meu CNPJ e o
problema para solicitar atendimento rapidamente.

-   Dado um CNPJ válido e já cadastrado, quando submeto o
    problem\_description e contato, então a OS é criada com status =
    Aberta, número/protocolo e opened\_at.

-   Dado um CNPJ não cadastrado, quando tento abrir uma OS, então o
    sistema bloqueia e exibe uma mensagem orientando entrar em contato.

-   Ao criar a OS, o sistema grava snapshot de nome/contato do cliente.

**US02 – Editar Ordem de Serviço (Atendente)**

Como atendente quero editar dados de uma OS para corrigir informações e
acompanhar o andamento.

-   Só permite edição enquanto status for diferente de concluído.

-   Campos editáveis: problem\_description, technician\_name,
    services\_done, parts\_list, status.

-   Toda alteração gera log (quem, quando, o quê).

**US03 – Deletar Ordem de Serviço (Atendente)**

Como atendente quero excluir uma OS criada por engano para manter a base
consistente.

-   Exclusão apenas quando status = Aberta e sem custos/peças lançados.

-   Exige confirmação dupla e registra log de auditoria.

**US04 – Registrar Clientes (Atendente)**

Como atendente quero cadastrar clientes com CNPJ, dados de contato e
endereço para unificar o cadastro.

-   Impede duplicidade por CNPJ.

-   Campos obrigatórios: name, document (CNPJ), email, phone, address.

-   Exibe feedbak de sucesso/erro e registra log.

**US05 – Editar Clientes (Atendente)**

Como atendente quero atualizar dados de clientes para manter o cadastro
íntegro.

-   Editar auditada (antes/depois, usuário, data).

-   Alterar CNPJ só é permitido por perfil autorizado e sem quebrar
    vínculos de OS.

**US06 – Deletar Clientes (Atendente)**

Como atendente quero excluir um cliente sem vínculo para evitar
registros obsoletos.

-   Só permite exclusão se não houver OS vinculada; caso exista, sistema
    bloqueia e sugere inativação (flag).

-   Confirmação dupla e log de auditoria.

**US07 – Visualizar Dashboard (Atendente/Gestor)**

Como atendente quero visualizar um dashboard operacional para acompanhar
OS e prioridades do dia.

-   Exibe quantidade de OS abertas em um período (semana, mês),
    quantidade de novos clientes por um período.

### 3.2.2 Requisitos Funcionais

-   **RF01:** Cadastrar e manter clientes.

-   **RF02:** Criar e manter ordem de serviço

-   **RF03:** Criar ordem de serviço pelo cliente diretamente no site.

-   **RF04:** Dashboards com KPIs

**Não Funcionais**

-   **RNF01:** O sistema deve permitir o cliente acessar a criação de
    ordem de serviço pela web utilizando navegador informando apenas o
    CNPJ, somente clientes previamente cadastrados

-   **RNF02:** Deve possui uma boa usabilidade, para nao precisar dar
    treinamento para usuário

-   **RNF03:** Se o sistema cair, voltar a funcionar em até 8 horas;
    perder no máximo os dados do último dia; backups diários testados;
    monitoramento com alertas.

-   **RNF04:** Atender à LGPD; revisar riscos de privacidade antes de
    entrar no ar; contratos com fornecedores que tratam dados.

### 3.2.3 Ferramentas/plataformas

 Com o objetivo de criar um sistema web simples para cadastro de
 clientes, abertura e gestão de Ordens de Serviço (OS), foram
 escolhidas as seguintes ferramentas:

**  
**

**Stack**

-   Laravel 11.x (MVC padrão)

-   PHP 8.2+ MySQL

-   Composer 2.x

<span id="_Toc212962539" class="anchor"></span>**3.2.4. Diagrama de caso
de uso**  
  
O Diagrama de Caso de Uso apresenta o escopo funcional do MVP do *Delta
Coding Service Manager* e os atores envolvidos:  
  
**1. Cliente** (apenas abertura de OS via CNPJ)  
  
**2. Atendente** (opera o backoffice). Estão mapeados os casos de uso
principais do ciclo de atendimento: Registrar/Editar/Deletar Ordem de
Serviço, Registrar/Editar/Deletar Clientes e Visualizar Dashboard.

**Regras e premissas do diagrama (MVP):**

-   O Cliente só interage com Registrar OS (sem acesso a
    edição/remoção).

-   O Atendente possui permissões para CRUD de Clientes e OS, além de
    Dashboard.

-   A exclusão de registros segue políticas de auditoria e restrições
    (ex.: OS apenas quando Aberta e sem lançamentos).

<img width="886" height="916" alt="image" src="https://github.com/user-attachments/assets/1f531d15-bd2b-47da-80b8-b55b005f8bf1" />


**  
**

### 3.2.5. Modelo de dados ER

O modelo de entidade-relacionamento do MVP é composto por duas entidades
principais: Clients e Service\_Orders com relação 1:N, onde 1 cliente
pode ter várias ordens de serviço.

<img width="833" height="652" alt="image" src="https://github.com/user-attachments/assets/05b22956-51fa-4de0-a1dc-25e3cabe469c" />


3.3 Protótipo e Planejamento da Arquitetura 
-------------------------------------------

-   **Protótipo navegável:**

> Criação de uma Ordem de Serviço pelo cliente:
>
> Link: <https://rodrigo.ia.br/dc/>

1.  **Clicar em ordem de serviço no canto superior direto conforme
    mostra a figura abaixo:**

<img width="530" height="886" alt="image" src="https://github.com/user-attachments/assets/41cb9a05-5242-40db-ac40-fe51a71b5dc9" />


**  
**

> **2. Informar o CNPJ do cliente previamente cadastrado, validar CNPJ e
> depois** **clicar em prosseguir. Exemplo de CNPJ:** 67012345000166.
>
<img width="721" height="580" alt="image" src="https://github.com/user-attachments/assets/b6a7ff4c-a383-44a4-8830-6f52377fb036" />


**  
**

> **3. Selecionar o equipamento, descrever e clicar em enviar OS (Ordem
> de Serviço)**
>
<img width="686" height="622" alt="image" src="https://github.com/user-attachments/assets/4c123de1-4042-4b74-be3f-bd0c896f54f8" />


Sistema utilizado pelos colaboradores da Delta Coding: Link:
<https://rodrigo.ia.br/dc/system/public/>

> A tela inicial possui o **Dashboard** e o menu Lateral onde o usuário
> pode navegar entre:
>
> **Clintes:** funções de cadastra um novo cliente, listar (visualizar,
> editar e excluir).
>
> **Ordem de serviços:** com as funções de cadastra uma nova Ordem de
> Serviço, listar (visualizar, editar e excluir).
>
<img width="664" height="606" alt="image" src="https://github.com/user-attachments/assets/6220f2f7-4a08-4efd-b928-c0ba50758c07" />


**  
**

  **Cadastrar Cliente**

<img width="684" height="678" alt="image" src="https://github.com/user-attachments/assets/250a3fc6-b8e3-4e6f-9941-b96fbaa41e4b" />


  **Visualizar, editar e excluir Clientes**

<img width="830" height="509" alt="image" src="https://github.com/user-attachments/assets/20693675-e156-4459-8a2e-82b49c480f42" />


**  
**

  **Cadastrar Ordem de Serviço**

<img width="886" height="409" alt="image" src="https://github.com/user-attachments/assets/717a6e3a-c50f-4ecf-891e-f5931a12254b" />


  **Visualizar, editar, fechar e excluir Ordem de Serviço**

<img width="886" height="409" alt="image" src="https://github.com/user-attachments/assets/df7e0c0e-24ac-4c9f-9733-5817870d2b0f" />


3.4 Preparação do Desenvolvimento
=================================

Para executar o desenvolvimento do MVP (Minimum Viable Product) do
"Delta Coding Service Manager", a equipe definiu um plano de execução
incremental focado em entregar valor rapidamente, começando pela
fundação do sistema (Clientes), passando pelo processo-chave (Ordens de
Serviço) e finalizando com a camada de análise (Dashboard).

**1. Plano de Execução (Priorização do MVP)**

O desenvolvimento será dividido em três fases (ou Sprints) principais,
seguindo a ordem de dependência funcional:

-   Fase 1: Estrutura Base e CRUD de Clientes.

    -   Objetivo: Estabelecer a arquitetura do projeto (Laravel), o
        banco de dados e a primeira entidade principal. Ao final desta
        fase, o Atendente já deve conseguir cadastrar, listar, editar e
        remover clientes.

    -   Entregáveis: Projeto Laravel funcional, tabelas do banco de
        dados criadas (migrations) e módulo de Clientes 100%
        operacional.

-   Fase 2: Módulo de Ordens de Serviço (CRUD de OS).

    -   Objetivo: Implementar o *core* do sistema, conectando as Ordens
        de Serviço aos Clientes cadastrados na Fase 1.

    -   Entregáveis: Módulo de OS funcional (criar, editar, listar,
        fechar OS), incluindo o relacionamento 1:N com Clientes.

-   Fase 3: Dashboard Gerencial.

    -   Objetivo: Criar a camada de visualização de dados, consumindo as
        informações geradas nas Fases 1 e 2.

    -   Entregáveis: Painel visual (conforme Seção 3.5) com os KPIs de
        "Novos Clientes" e "Novas Ordens".

**2. Divisão de Tarefas e Início (Partes Estruturais)**

Abaixo está o detalhamento das tarefas para a Fase 1, que marca o início
do desenvolvimento estrutural do sistema.

**Planejamento e Validação (Leandro):** Tendo conduzido a análise
inicial da empresa (SWOT) e o planejamento, Leandro atuará como o
principal ponto de contato com a Delta Coding, garantindo que o
desenvolvimento esteja alinhado às necessidades do cliente e validando
as entregas.

**Banco de Dados (Douglas):** responsável por toda a estrutura de dados.
Douglas implementará o Modelo de Dados ER (Seção 3.2.5) no banco de
dados, criando as tabelas, migrations e relacionamentos necessários para
suportar os módulos de Clientes e Ordens de Serviço.

**Design e Layout (Vinícius e Laryssa):** responsáveis pela criação das
telas e wireframes (conforme Seção 3.3), definindo a experiência do
usuário. Laryssa ficará encarregada do desenvolvimento do layout
(frontend), traduzindo os wireframes para o código (Blade/CSS/JS), além
de monitorar a aplicação.

**Desenvolvimento (Rodrigo):** responsável pela lógica *backend*
(utilizando Laravel). Ele implementará o CRUD de Clientes e,
subsequentemente, o CRUD de Ordens de Serviço, além de desenvolver o
Dashboard, seguindo a ordem de prioridade estabelecida.

**Documentação (Fernando):** responsável por documentar o sistema à
medida que ele é desenvolvido, registrando a arquitetura, as regras de
negócio implementadas e o esquema do banco de dados.

**3. Início do Desenvolvimento**

Conforme definido pela equipe, o desenvolvimento das partes estruturais
(Fase 1) foi iniciado formalmente ao final da Etapa 02 do projeto (Plano
de IC) e início da Etapa 03 (Desenvolvimento), coincidindo com a
aprovação dos protótipos e do modelo de dados.

3.5 Geração de Relatórios ou Dashboards Internos 
================================================

**Dashboard**

<img width="886" height="755" alt="image" src="https://github.com/user-attachments/assets/719bf01d-7d44-499c-8ac4-eafd23db4ac9" />


O painel consolida, os principais indicadores operacionais do
atendimento (cliente e ordens de serviço) para apoiar a decisão
estratégica sobre planos e manutenção preventiva. Ele transforma os
dados do serviço em insights ligados às KIQs definidas.

**Componentes do painel:**

-   **Cards**

    -   **Novos clientes (período selecionado)** – mede
        aquisição/ativação de clientes.

    -   **Novas ordens (OS)** – mede demanda de serviço (entrada de
        chamados).

    -   **Crescimento (%)** – variação relativa do volume de OS/clientes
        vs período anterior.

-   **Série temporal de Receita/Ticket**

    -   Revenue (linha) ao longo do tempo; alterna **Today / Week /
        Month / Year**.

-   **Controles de período e exportação**

    -   Data selector define a janela de análise.

    -   Print e Download Report geram relatório PDF/CSV para reuniões e
        auditorias.

