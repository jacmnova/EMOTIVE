# Checklist – Relatório Corporativo e Séries Temporais

Controlo da implementação das funcionalidades: **Relatório de Grupo**, **Séries/Ondas**, **Reportes de Evolução** e **Gestão de Población**.

---

## Como funciona

### Conceitos

1. **Atributos do usuário** (Unidade, Área, Nível hierárquico, Tempo de empresa, Modelo de trabalho)  
   Preenchem-se ao criar/editar cada usuário. Servem para **agrupar** e filtrar nos relatórios e na atribuição em massa.

2. **Período / Onda**  
   Uma “onda” de aplicação (ex.: “Onda 1 - Jan 2025”). Tem nome, datas opcionais e pertence a um cliente. Cada **atribuição** de questionário a um usuário pode estar associada a um período (opcional).

3. **Projeto** (opcional)  
   Agrupa vários períodos (ex.: “Bem-estar 2025”). Ao criar/editar um período pode escolher o projeto.

4. **População**  
   Conjunto de usuários definido por **filtros**: cliente + opcionalmente unidade, área, nível, tempo de empresa, modelo de trabalho. Usa-se para **atribuição em massa** e para os relatórios por grupo.

### Quem pode fazer o quê

- **Gestor:** vê e age só sobre o **seu** cliente (períodos, projetos, usuários, relatórios e atribuição em massa desse cliente).
- **Admin / SA:** vê todos os clientes; em relatórios e atribuição em massa deve escolher o cliente.

### Fluxo típico

1. **Preparar dados**  
   - Criar/editar **usuários** e preencher Unidade, Área, etc.  
   - (Opcional) Criar **Projetos** e **Períodos** em Períodos / Projetos no menu.

2. **Atribuir questionários**  
   - **Um a um:** em Usuários → editar usuário → “Questionários asignados” → escolher formulário, período (opcional) e data limite → Asignar.  
   - **Em massa:** menu **Atribuição em massa** → escolher cliente, formulário, período (opcional), data limite e filtros de população (unidade, área, etc.) → Atribuir em massa. Cria uma atribuição para cada usuário que cumpre os filtros (sem duplicar).

3. **Ver relatórios**  
   - **Relatório corporativo:** escolher cliente (e opcionalmente período/formulário) → ver **resumo**, **KPIs** (colaboradores em risco, setores críticos), **gráficos** por unidade/área e **tabelas** por unidade, área, nível, tempo de empresa, modelo de trabalho.  
   - **Evolução:** escolher dois períodos e tipo de grupo → ver **comparação** (tabela e gráfico P1 vs P2) e variação em pontos percentuais.

### Onde está no menu (Gestor / Admin)

- **Períodos** – listar, criar, editar períodos; ver número de atribuições por período; links para Projetos, Relatório e Atribuição em massa.  
- **Projetos** – listar, criar, editar projetos (agrupar períodos).  
- **Relatório corporativo** – agregado por grupo, KPIs e gráficos.  
- **Atribuição em massa** e **Comparar períodos (Evolução)** – acessíveis também por links dentro do Relatório corporativo.

### APIs principais

| Uso | Endpoint |
|-----|----------|
| Listar usuários com filtros (população) | `GET /users?cliente_id=&unidade=&area=...` |
| Atribuir em massa | `POST /usuario-formulario/em-massa` (body: cliente_id, formulario_id, periodo_id?, filtros) |
| Agregado por grupo | `GET /reportes/agregado-grupo?cliente_id=&periodo_id=&formulario_id=` |
| KPIs (risco, setores críticos) | `GET /reportes/kpis?cliente_id=&periodo_id=` |
| Comparar dois períodos | `GET /reportes/evolucao?periodo_id_1=&periodo_id_2=&cliente_id=&grupo=unidade\|area\|nivel_jerarquico` |

**Risco** = utilizador com pelo menos uma atribuição não completa e com data limite já vencida.

---

## Fase 0 – Pré-requisitos de dados (atributos do usuário)

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 0.1 | Adicionar colunas ao `users`: unidade, área, nível hierárquico, tempo de empresa, modelo de trabalho | ✅ Concluído | Migração + modelo + schemas |
| 0.2 | Atualizar API de usuários (create/update/get) com novos campos | ✅ Concluído | Schemas já expõem os campos |
| 0.3 | Atualizar frontend (edição de usuário) para preencher atributos de grupo | ✅ Concluído | Editar + novo usuário |

---

## Fase 1 – Períodos / Ondas (séries de aplicação)

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 1.1 | Criar modelo `Periodo` (nome, descrição, data_inicio, data_fim, cliente_id) | ✅ Concluído | |
| 1.2 | Adicionar `periodo_id` em `usuario_formulario` (nullable FK) | ✅ Concluído | Migração |
| 1.3 | API CRUD de períodos (list, create, update, delete) | ✅ Concluído | Filtro por cliente; gestor só do seu cliente |
| 1.4 | Ao atribuir questionário ao usuário, permitir selecionar período (opcional) | ✅ Concluído | Selector na edição do usuário |
| 1.5 | Listar atribuições por período no dashboard / relatório | ✅ Concluído | Filtro periodo_id na API; página Períodos com contagem |

---

## Fase 2 – Relatório de Grupo (visão de saúde da empresa)

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 2.1 | API agregada por grupo (Unidade, Área, Nível, etc.): participação, % completo | ✅ Concluído | GET /reportes/agregado-grupo |
| 2.2 | KPIs: colaboradores em risco, setores críticos (definir regras) | ✅ Concluído | GET /reportes/kpis; risco = incompleto + data_limite vencida |
| 2.3 | Gráficos de evolução (por período, se disponível) | ✅ Concluído | Barras CSS em relatório e comparação P1 vs P2 |
| 2.4 | Página frontend “Relatório Corporativo” (dashboard conforme Figma) | ✅ Concluído | /dashboard/relatorio-corporativo |

---

## Fase 3 – Aplicar questionários em série (projeto / ondas)

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 3.1 | Conceito de “Projeto” (opcional: agrupar períodos) | ✅ Concluído | Modelo Projeto, periodo.projeto_id, CRUD /projetos, UI |
| 3.2 | Atribuição em massa por população (grupo + período) | ✅ Concluído | POST /usuario-formulario/em-massa |
| 3.3 | UI: criar onda, selecionar população, atribuir formulário | ✅ Concluído | /dashboard/atribuicao-em-massa |

---

## Fase 4 – Reportes de evolução (comparação temporal)

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 4.1 | API que compara resultados do mesmo grupo em 2+ períodos | ✅ Concluído | GET /reportes/evolucao |
| 4.2 | Frontend: seleção de períodos e grupo, visualização comparativa | ✅ Concluído | /dashboard/evolucao |

---

## Fase 5 – Gerenciar população de usuários

| # | Tarefa | Estado | Notas |
|---|--------|--------|--------|
| 5.1 | Definir “população” (filtros: cliente, unidade, área, nível, etc.) | ✅ Concluído | GET /users com filtros + em-massa |
| 5.2 | Atribuição em massa de questionário a uma população | ✅ Concluído | POST /usuario-formulario/em-massa |
| 5.3 | UI: listar populações, criar/editar, atribuir questionário em massa | ✅ Concluído | /dashboard/atribuicao-em-massa |

---

## Legenda

- ⬜ Pendente  
- 🔄 Em progresso  
- ✅ Concluído  

**Última atualização:** 2026-02-10

### Completados além do checklist (2026-02-10)

- **GET /users?periodo_id=** – Lista apenas usuários com atribuição no período (para "Ver usuários" na página Períodos).
- **Página Usuários** – Usa `periodo_id` da URL e mostra "Com atribuição no período: [nome]"; link "Ver todos" para remover filtro.
- **PDF Relatório corporativo** – `GET /api/v1/pdf/relatorio-corporativo?cliente_id=&periodo_id=&formulario_id=` gera PDF com KPIs e "Onde se concentram"; botão BAIXAR na aba Relatórios ativo (requer os três filtros).

---

## Conclusão

Todas as tarefas das Fases 0 a 5 foram implementadas. Para colocar em produção:

1. **Backend:** executar migrações: `cd migrations/backend-api && alembic upgrade head`
2. **Frontend:** build e deploy conforme ambiente (ex.: `npm run build`)
3. **Gestores/Admin:** aceder a Períodos, Projetos, Relatório corporativo, Atribuição em massa e Evolução pelo menu do dashboard

---

## Limitaciones conocidas (Relatório corporativo)

- **Chips de dimensión** (Todas, Exaustão Emocional, Despersonalização / Cinismo, etc.): en la pestaña **Dimensão** los chips solo cambian el estilo del botón activo; **el contenido (KPIs, setores críticos, gráficos) no se filtra por dimensión**. Las APIs actuales (`agregado-grupo`, `kpis`, `kpis-iid`) no reciben parámetro de dimensión. Para que el contenido cambie al elegir un chip haría falta: (1) que el backend exponga filtro por dimensión en esos endpoints, o (2) filtrar en front los datos ya devueltos si el API incluyera desglose por dimensión.
