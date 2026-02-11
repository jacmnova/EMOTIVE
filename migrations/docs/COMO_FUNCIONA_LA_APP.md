# Cómo funciona la aplicación Emotive 2

Resumen del flujo de la aplicación (frontend + backend API) para colaboradores, gestores y administradores.

---

## 1. Roles y menú

- **Colaborador** (solo responde cuestionarios): ve **Início**, **Meus questionários**, **Perfil**, **Notificações**.
- **Gestor** (gestión de un cliente): además ve **Usuários**, **Períodos (Ondas)**, **Atribuição em massa**, **Grupos**, **Relatório corporativo (BI)**, **Comparar períodos**, **Monitor de respondentes**, **Projetos**.
- **Admin / SA**: además de lo anterior, ve **Clientes** y **Formulários** (no se muestran en el menú: Chat, Mídias, Cálculos).

El gestor solo ve datos de su cliente; admin/SA pueden elegir cliente en relatórios y atribuição.

---

## 2. Flujo del colaborador

1. **Acceso**  
   - Recibe un **link de invitación** (email) con token o entra por login.  
   - En el dashboard ve **Meus questionários**: lista de cuestionarios asignados (pendientes o completados).

2. **Responder**  
   - Clic en “Responder” → pantalla del cuestionario por etapas.  
   - Al guardar la última etapa, la encuesta queda **completa**.

3. **Después de completar**  
   - Mensaje tipo “Encuesta completada” y botón **Ver mi reporte**.  
   - Entra al **reporte personal** (solo él o su gestor): dimensiones, ejes, IID, nivel de riesgo, plan.

4. **Recurrencia**  
   - Si se abre una nueva “onda” (período), el gestor asigna de nuevo el cuestionario.  
   - El colaborador verá un nuevo ítem en Meus questionários y repite el flujo.

---

## 3. Flujo del gestor / admin (corporativo)

1. **Preparar datos**  
   - **Usuários**: crear/editar usuarios y rellenar Unidade, Área, Nível, Tempo de empresa, Modelo de trabalho (para filtros y relatórios).  
   - **Períodos**: crear ondas (nombre, fechas, cliente).  
   - **Projetos** (opcional): agrupar períodos.

2. **Asignar cuestionarios**  
   - **Uno a uno:** en Usuários → editar usuario → “Questionários asignados” → elegir formulário, período y data limite → Asignar.  
   - **En masa:** **Atribuição em massa** → elegir cliente, formulário, período, data limite y filtros (unidade, área, etc.) → Atribuir. Se puede marcar “Enviar invitación por email”.  
   - Las asignaciones pueden asociarse a un **período** (onda) para series temporales.

3. **Monitor de respondentes**  
   - Lista por Cliente + Período + Formulário: quién está pendiente / completo.  
   - Botón **Enviar recordatorio** por respondente pendiente (email de recordatorio).

4. **Relatório corporativo**  
   - Elegir Cliente (y opcionalmente Período, Formulário).  
   - Pestañas: **Descarregamento (IID)** | **Dimensão** | **Eixos** | **Relatórios**.  
   - Filtros laterales: Unidade, Área, Nível, Tempo de empresa, Modelo de trabalho → al aplicar se recalculan KPIs, setores críticos, “Onde se concentram” y gráficos.  
   - **KPIs:** colaboradores em risco, setores críticos, mapa de calor por área/faixa.  
   - **Gráfico temporal:** evolución IID por ondas (nube de puntos + línea media).  
   - **Relatórios:** descarga de PDF con los **mismos filtros** aplicados en pantalla.

5. **Cierre de onda**  
   - Si la fecha actual es posterior a `data_fim` del período, la onda se considera cerrada: no se aceptan nuevas respuestas para ese período y en la UI se muestra “Onda cerrada”.

---

## 4. Secciones ocultas en el menú

Por decisión de producto no se muestran en la barra lateral:

- **Chat**  
- **Mídias**  
- **Cálculos**  

Las rutas siguen existiendo; solo se han quitado del array de navegación del layout del dashboard.

---

## 5. APIs principales (resumen)

| Uso | Endpoint |
|-----|----------|
| Listar clientes | `GET /api/v1/clientes/` |
| Listar períodos | `GET /api/v1/periodos/` |
| Listar formularios | `GET /api/v1/formularios/` |
| Agregado por grupo | `GET /api/v1/reportes/agregado-grupo?cliente_id=&periodo_id=&formulario_id=` |
| KPIs (risco, setores) | `GET /api/v1/reportes/kpis?…` |
| KPIs IID | `GET /api/v1/reportes/kpis-iid?…` |
| Evolução IID (temporal) | `GET /api/v1/reportes/evolucao-iid?…` |
| Atribuição em massa | `POST /api/v1/usuario-formulario/em-massa` |
| PDF relatório corporativo | `GET /api/v1/pdf/relatorio-corporativo?cliente_id=&periodo_id=&formulario_id=` (+ filtros) |

---

## 6. Nota sobre los chips de dimensión (Relatório corporativo)

En la pestaña **Dimensão**, los chips (Todas, Exaustão Emocional, Despersonalização / Cinismo, etc.) **solo cambian el chip activo**; el contenido (números, setores críticos, gráficos) **no se filtra aún por dimensión**. Ver limitaciones en `CHECKLIST_RELATORIO_CORPORATIVO.md`.
