# Plan: Modificar todo y dejarlo como se necesita

Documento para alinear el sistema con las necesidades (ondas de evaluación, población/grupos, relatório corporativo, gestión de usuarios y períodos) y dejar listo para uso y pruebas.

---

## 1. Lo que ya está hecho (solo verificar)

| Área | Qué hay | Dónde |
|------|--------|--------|
| **Usuarios y atributos de grupo** | Unidade, Área, Nível, Tempo de empresa, Modelo de trabalho en user | Backend: `User`; Frontend: edición de usuario |
| **Períodos (ondas)** | CRUD de períodos por cliente; periodo_id en atribuciones | `/dashboard/periodos`, API `/periodos` |
| **Projetos** | Agrupar períodos (opcional) | `/dashboard/projetos`, API `/projetos` |
| **Atribuição em massa** | Asignar formulário + período a una población (filtros) | Menu: "Gerenciamento massivo (usuários e grupos)" → `/dashboard/atribuicao-em-massa` |
| **Relatório corporativo** | Tabs (Dimensão, Eixo, Descarrilamento, Relatórios), filtros, KPIs por IID, "Onde se concentram" (Unidade→Área), gráfico "Estamos Melhorando?" | `/dashboard/relatorio-corporativo` |
| **Evolução** | Comparar dos períodos (participação %) | `/dashboard/evolucao` |
| **APIs IID** | KPIs y concentración por IID; evolução IID por período | `GET /reportes/kpis-iid`, `GET /reportes/evolucao-iid` |

**Acción:** Revisar en el entorno real que estos flujos funcionan (login gestor/admin, crear período, atribuição em massa, relatório con formulário seleccionado).

---

## 2. Pasos para “dejarlo como se necesita”

### A. Configuración y despliegue

| # | Tarea | Comando / acción |
|---|--------|-------------------|
| 1 | Migraciones de base de datos | `cd migrations/backend-api && alembic upgrade head` |
| 2 | Variables de entorno | Copiar `.env.example` → `.env`; configurar `DATABASE_URL`, `SECRET_KEY`, `FRONTEND_URL`, etc. |
| 3 | Backend en marcha | `uvicorn app.main:app --reload --host 0.0.0.0 --port 8000` (o el puerto que uses) |
| 4 | Frontend en marcha | `cd migrations/frontend && npm run dev` (o build + servidor en producción) |
| 5 | Dependencia bcrypt | En backend: `bcrypt==4.0.1` en `requirements.txt` (compatibilidad con passlib) |

### B. Datos iniciales (primera vez)

| # | Tarea | Dónde |
|---|--------|--------|
| 1 | Crear al menos un **Cliente** | Admin: Dashboard → Clientes |
| 2 | Crear **Formulário(s)** y asociarlos al cliente si aplica | Admin: Formulários; Cliente-Formulário |
| 3 | Crear **Usuários** del cliente con Unidade, Área, etc. | Usuários → nuevo/editar o importar |
| 4 | Crear **Períodos** (ondas) para el cliente | Períodos → novo |
| 5 | (Opcional) Crear **Projetos** y asignar períodos | Projetos → novo; editar período → elegir projeto |

### C. Flujo de prueba recomendado

1. **Login** como Admin o Gestor del cliente.
2. **Atribuição em massa:**  
   Gerenciamento massivo (usuários e grupos) → elegir Cliente, Formulário, Período, (opcional) filtros → “Atribuir em massa”.
3. **Relatório corporativo:**  
   Elegir Cliente, Período, Formulário → Aplicar → revisar KPIs, “Onde se concentram” y gráfico “Estamos Melhorando?” (si hay datos IID).
4. **Evolução:**  
   Comparar períodos (dos períodos con datos).

### D. Ajustes opcionales (según feedback tipo Nicolas)

| # | Si se pide | Estado |
|---|------------|--------|
| 1 | **Grupos nombrados** (Feito) (“Grupo RH”, “Grupo Vendas”) | Añadir modelo `Grupo` (nome, cliente_id, filtros o lista de user_ids); en atribuição em massa permitir “por grupo” además de “por filtros”. |
| 2 | **PDF del relatório corporativo** | Implementar endpoint que genere PDF con KPIs + gráficos (o tabla) y enlazar el botón “BAIXAR” en la pestaña Relatórios. |
| 3 | **Ver “quién está en esta onda”** en la página del período | En `/dashboard/periodos` (o en detalle del período) añadir link “Ver usuários atribuídos” que liste/exporte usuários con atribución en ese período. |
| 4 | **Ajustes de permisos** | Feito: só Gestor/Admin/SA veem Períodos, Atribuição em massa, Relatório no menu. |
| 5 | **Pequeños ajustes de UX** | Ajustes conforme feedback após testes. |

---

## 3. Orden sugerido para “modificar todo y dejarlo como se necesita”

1. **Configurar y desplegar** (sección 2.A).  
2. **Cargar datos mínimos** (sección 2.B).  
3. **Probar el flujo** (sección 2.C) y anotar lo que no cuadra con lo que piden (Nicolas/Jose/equipo).  
4. **Priorizar** qué de la sección 2.D es imprescindible para la “primera versión de teste” y qué puede ser “pequeños ajustes después”.  
5. **Implementar** solo los puntos prioritarios (p. ej. solo 2.D.3 y 2.D.5 si lo demás se deja para después).  
6. **Documentar** en este mismo doc o en el CHECKLIST cualquier cambio hecho (por ejemplo: “Añadido link Ver usuários no período”).

---

## 4. Resumen

- **Para dejarlo “como se necesita” a nivel funcional:** el sistema ya cubre ondas (períodos), población por filtros, atribuição em massa y relatório corporativo con IID.  
- **Para “modificar todo” de forma ordenada:** seguir 2.A → 2.B → 2.C, luego decidir qué ítems de 2.D se implementan ahora y cuáles tras la primera versión de teste.

Si indicas qué punto quieres hacer primero (p. ej. “grupos nombrados” o “PDF relatório”), se puede bajar a tareas concretas de código (backend + frontend) paso a paso.
