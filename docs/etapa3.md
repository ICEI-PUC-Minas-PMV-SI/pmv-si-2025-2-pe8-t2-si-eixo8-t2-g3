#Conexão com o Plano de IC e Planejamento da Solução
#Orientações:

Relembrar o KIT, as KIQs e os dados críticos do Plano de IC.
Identificar o(s) processo(s) que serão resolvidos com a aplicação.
Definir funcionalidades iniciais a serem desenvolvidas.
~~Criar um quadro-resumo com:~~
~~→ Problema mapeado | Solução proposta | Como será resolvida no sistema~~
|Problema mapeado| Solução Proposta | Como Será Resolvido no Sistema|

|Cadastro de clientes em planilhas| Cadastros dos clientes padronizados no sistema | Será criado a funcionalidade de registro de clientes no sistema|

|Ordem de serviço criada em documento Word | criação de um sistema para criação de ordem de serviço | Clientes e empregados da empresa deverao criar as ordens de serviço no sistema para facilitar o gerenciamento das demandas|

|Falta ferramenta para visualização das ordens de serviços e manutenções | Dashboard para visualizar as ordens de serviços de manutenções | Crição de um dashboard com as informações do atualizadas no sistema|

#Levantamento de Requisitos e Modelagem Inicial
#Orientações:

Elaborar histórias de usuário

~~Definir requisitos funcionais e não funcionais~~
**Requisitos Funcionais**

**RF01:** Cadastrar e manter clientes clientes

**RF02:** Criar e manter ordem de serviço

**RF03:** Criar ordem de serviço pelo cliente diretamente no site.

**RF04:** Dashboards com KPIs 

**Requisitos Não Funcionais**

**RNF01:** O sistema deve permitir o cliente acessar a criação de ordem de serviço pela web utilizando navegador informando apenas o CNPJ, somente clientes previamente cadastrados

**RNF02:** Deve possui uma boa usabilidade, para nao precisar dar treinamento para usuario

**RNF03:** Se o sistema cair, voltar a funcionar em até 8 horas; perder no máximo os dados do último dia; backups diários testados; monitoramento com alertas.

**RNF04:** Atender à LGPD; revisar riscos de privacidade antes de entrar no ar; contratos com fornecedores que tratam dados.  ****
Escolher ferramentas/plataformas.
Construir Diagrama de caso de uso
~~Desenvolver esboço do banco de dados (modelo ER)~~
<img width="533" height="417" alt="image" src="https://github.com/user-attachments/assets/e7e41495-d210-439a-884d-3c35d5bd7ef6" />



#Protótipo e Planejamento da Arquitetura
#Orientações:

~~Desenvolver wireframes ou protótipo navegável.~~
**Criação da Ordem de Serviço pelo Cliente**
Clicar em ordem de Serviços
<img width="1069" height="1395" alt="image" src="https://github.com/user-attachments/assets/ade0b78a-4cba-4888-b2b9-e7bb8c1cb56c" />
Digitar o CNPJ previamente cadastrado na Delta Coding Validar CNPJ e depois em prosseguir
<img width="1053" height="1178" alt="image" src="https://github.com/user-attachments/assets/9a057f4a-cb7b-46b5-8123-0e3ccc4e2f1d" />
Marcar a o equipamento, descrever o problema e depois e enviar OS.
<img width="1091" height="839" alt="image" src="https://github.com/user-attachments/assets/2808a3a9-d518-4a5f-a248-f35d2ef4de1a" />


Planejar a estrutura de navegação do sistema; o fluxo de telas, o armazenamento e acesso aos dados.
#Preparação do Desenvolvimento
#Orientações:

Montar plano de execução: o que será implementado primeiro?
Divisão das tarefas entre os integrantes.
Início do desenvolvimento de partes estruturais do sistema.

#Geração de Relatórios ou Dashboards Internos
#Objetivo: Demonstrar como o sistema auxilia na tomada de decisão estratégica, conforme as necessidades de Inteligência Competitiva levantadas no plano.
#Orientações:

~~Configurar dentro do sistema relatórios gerenciais ou dashboards simples com visualizações relevantes.~~
<img width="1049" height="893" alt="image" src="https://github.com/user-attachments/assets/aed0ded4-80f8-400f-9dd1-be9e53c1ec60" />


#Os relatórios devem:

Ser baseados em dados coletados e armazenados no sistema.
Responder diretamente às KIQs do plano de IC.
Apresentar informações em resposta às perguntas levantadas no plano.
