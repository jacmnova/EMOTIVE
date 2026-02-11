# Análise: Reunião Jose Cordero e Nicolas Banfi (Migração e Relatório Emotive)

Documento que cruza as notas da reunião com o estado atual do projeto **Emotive (migrations)** e identifica alinhamento e lacunas.

---

## 1. Resumo do que foi combinado na reunião

### 1.1 Contexto e prazos
- **Nexo:** Jose está fechando três pontos; sustentação mais controlada (formato diferente). Migração de “antigravity” para “cursor” (menos problemas).
- **Emotive:** migração esperada em cerca de uma semana, em paralelo ao Nexo.
- **Foco:** finalizar Nexo → homologação → 100% Emotive e Expand. Problemas com serviço de e-mail em resolução.

### 1.2 Mudança central no Emotive
- **De:** relatórios e resultados **individuais**.
- **Para:** abordagem **grupal/corporativa**:
  - **Soma de resultados** para visão integrada de grupos de pessoas ou da empresa.
  - Relatórios individuais **continuam**; o novo é a **gestão de grupos** e a **agregação** para relatório corporativo.
  - **Temporal:** aplicar o relatório ao longo do tempo (ex.: trimestres) e **acumular** resultados da empresa em **série histórica**.

### 1.3 Estrutura do relatório corporativo (detalhado por Nicolas)
- Monitor de **respondentes**.
- **Índice de descarrilamento** somado.
- Indicadores de **BI** e abertura por **área de risco**.
- Mesmas **dimensões e eixos** do individual, mas como **soma em grupos** de pessoas.

### 1.4 Ondas / períodos
- Resultado consolidado da empresa é “cortado” numa **data específica** para fechar um período.
- Novos envios = **nova onda** (ex.: Período 2), com acumulação.
- **Gestão de períodos:** gestor configura períodos de medição e envia novos ciclos; relatório central **soma as respostas de cada ciclo** para composição final da empresa.
- Essencial: **monitoramento + gestão de pessoas + reciclagem de ciclos**.

### 1.5 Emotive como orquestrador
- Não mais “fábrica de instrumentos”, e sim **orquestrador de aplicações**.
- Front-end central para configurar acesso a várias aplicações.
- Admins: gerir gestores, liberar ferramentas.
- Gestores: criar clientes, liberar questionários.
- Integração com ferramentas como Get Competências (evitar saltar de ferramenta em ferramenta).

---

## 2. Mapeamento: reunião ↔ código atual (migrations)

| Requisito da reunião | O que existe no projeto | Estado |
|----------------------|-------------------------|--------|
| **Visão grupal** – soma de resultados por grupos | Agregação por Unidade, Área, Nível, Tempo de empresa, Modelo de trabalho; KPIs por IID; “Onde se concentram” (Unidade → Área); relatório corporativo com abas Dimensão, Eixo, Descarrilamento, Relatórios | ✅ Alinhado |
| **Grupos de pessoas** (nomeados) | Modelo `Grupo` (nome, cliente_id, unidade, área, etc.); CRUD `/dashboard/grupos`; atribuição em massa **por grupo** | ✅ Implementado |
| **Relatórios individuais continuam** | Relatório por participante (respostas, índices, plano) segue existindo | ✅ Mantido |
| **Temporal / trimestres** | Períodos (ondas) por cliente; `periodo_id` em atribuições; filtro por período no relatório corporativo | ✅ Alinhado |
| **Série histórica / acumular** | Evolução IID por período (`evolucao-iid`); comparação entre períodos; “Estamos Melhorando?”; lista de relatórios gerados por período | ✅ Alinhado |
| **Fechar período numa data** | Período tem nome, datas opcionais; atribuições ligadas ao período; relatório “corta” por período selecionado | ✅ Conceito presente (datas em período; corte por seleção de período) |
| **Monitor de respondentes** | KPIs: total respondentes, completos, %; colaboradores em risco; setores críticos; “Onde se concentram” | ✅ Alinhado |
| **Índice descarrilamento somado** | Aba Descarrilamento no relatório corporativo; lógica por IID/faixas | ✅ Presente |
| **Indicadores BI e abertura por área de risco** | KPIs IID, setores críticos (unidade/área), faixas (baixa, moderada, alta, crítico) | ✅ Alinhado |
| **Gestão de períodos pelo gestor** | CRUD Períodos; gestor vê só seu cliente; envio de ciclos = atribuição em massa por período | ✅ Implementado |
| **Reciclagem de ciclos** | Novo período → nova “onda”; atribuição em massa para novo período; evolução compara P1 vs P2 | ✅ Fluxo suportado |
| **Orquestrador (admins/gestores, liberar ferramentas)** | Roles: SA, Admin, Gestor, Usuario; menu condicional; gestores criam/gerem clientes (conforme modelo); liberação de questionários por atribuição | ⚠️ Parcial – orquestração “multi-app” (ex.: Get Competências) não implementada no código atual |

---

## 3. Lacunas e pontos a validar

### 3.1 Já cobertos no código
- Visão grupal e temporal.
- Grupos nomeados e atribuição por grupo.
- Relatório corporativo com dimensões/eixos/descarrilamento, KPIs, “Onde se concentram”, evolução.
- Gestão de períodos (ondas) e ciclos.
- PDF do relatório corporativo e lista de relatórios gerados.

### 3.2 A clarificar ou reforçar
1. **“Corte numa data específica”**  
   Hoje o “corte” é pela **escolha do período** no filtro (cada atribuição tem `periodo_id`). Se a ideia for **fechar automaticamente** num dia X (ex.: 31/03) e travar respostas para esse período, isso pode exigir regras de negócio (ex.: data_fim do período, bloqueio de novas respostas após data_fim). Vale confirmar com Nicolas/Jose.

2. **Série histórica “acumulada”**  
   Hoje temos **evolução por período** (comparar P1 vs P2, etc.). Se quiserem um único gráfico “histórico” com vários trimestres em sequência (ex.: linha no tempo), pode ser uma extensão da tela/API de evolução (N períodos em vez de 2).

3. **Orquestrador multi-aplicação**  
   Get Competências e outras apps integradas a partir do mesmo front não estão no escopo atual do código. Isso seria uma fase seguinte (links, SSO, “liberar ferramentas” por app).

4. **E-mail**  
   Jose citou problemas com o serviço de e-mail; o projeto já tem notificações/emails (ex.: reset password, verificação). Garantir que envio de convites/lembretes por ciclo esteja estável.

### 3.3 Sugestão de mensagem para Jose/Nicolas
- “O relatório corporativo já cobre: visão grupal (agregação por unidade/área e grupos nomeados), períodos/ondas, monitor de respondentes, IID, descarrilamento e evolução entre períodos. PDF e lista de relatórios gerados estão disponíveis. Precisamos só alinhar: (1) se o fechamento de período deve ser automático numa data; (2) se desejam um gráfico de série histórica com N períodos; (3) prioridade da orquestração multi-app (Get Competências, etc.).”

---

## 4. Conclusão

As decisões da reunião (visão grupal, temporal, ondas, relatório corporativo com monitor, descarrilamento e BI por área de risco, gestão de períodos e reciclagem de ciclos) estão **bem alinhadas** com o que está implementado no projeto **migrations**.  

Os itens em aberto são sobretudo **detalhe de regras** (fechamento automático por data), **evolução de visualização** (série histórica com N períodos) e **escopo futuro** (orquestrador multi-aplicação).  
Com esse alinhamento e os ajustes de e-mail e Nexo, a base está pronta para migração e homologação do Emotive dentro do prazo discutido.
