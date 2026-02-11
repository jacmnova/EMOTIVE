# Análisis Emotive 2 – Especificación vs estado actual y plan de reforma

Documento que cruza la especificación técnica (Flujo Normal, Flujo Corporativo, BI/Dashboard) con el código actual y la conversación de la llamada (Jose Cordero / Nicolas Banfi). **Solo Emotive:** alcance, brechas y plan de reforma para que los flujos sean amigables y funcionen correctamente.

---

## 1. Resumen de la especificación Emotive 2

### 1.1 Flujo Normal (Experiencia del colaborador)
- **Recepción:** El colaborador recibe una **invitación (link)** para realizar la encuesta.
- **Respuesta:** Responde el cuestionario (estado emocional y profesional).
- **Feedback automático:** Al finalizar, el sistema genera y entrega un **reporte individual** (privado): indicadores de descarregamento, dimensiones y ejes.
- **Recurrencia:** Si la empresa abre una nueva "onda", el colaborador vuelve a recibir la encuesta; su experiencia siempre termina en su reporte personal.

### 1.2 Flujo Corporativo (Gestión de ondas y grupos)
- **Carga de población:** El administrador sube la lista de funcionarios.
- **Configuración de la onda:** Se define un periodo activo (ej. "Onda 1", "Primer Trimestre"). Todas las encuestas enviadas/respondidas bajo esa etiqueta pertenecen a ese corte.
- **Liberación gradual:** El administrador selecciona grupos y les libera el cuestionario (100 hoy, 100 la próxima semana, dentro de la fecha de la onda).
- **Monitoreo:** Ver quiénes han respondido y estado de envío.
- **Cierre y acumulación:** Fecha de corte; al cerrar la onda se genera la "foto" final. Se puede abrir "Onda 2" y "reciclar" usuarios (volver a enviarles encuesta) sin recargar la base.

### 1.3 BI (Dashboard) – detalle según Figma y llamada

- Vista **agregada y navegable**; no datos individuales (privacidad). Es una "versión navegable del relatório individual" con las **mismas dimensiones**: Descarregamento, Dimensão, Eixos.
- **Pestañas superiores:** al clicar en cada una (Descarregamento | Dimensão | Eixo) **toda la data se actualiza** para ese indicador. Comportamiento igual en las tres; la única excepción es el **"verdecito" de participación** (monitor de cuántos respondieron vs enviados), que es **único para todas** y no se repite por pestaña.
- **Filtro de selección (drill-down):** permitir elegir un **grupo dentro de un universo mayor** (ej. "solo Logística") → todos los gráficos y números se recalculan para ese equipo. Las métricas son **agrupamiento y suma** (misma escala 0–6 del individual) y comparaciones de referencia.
- **Por pestaña:**
  - **Descarregamento:** monitor (personas que respondieron / enviadas), índice de descarregamento sumado, número de personas por encima de cierto umbral, áreas donde la **suma media** cae en determinada zona de riesgo → indicadores tipo BI; abertura por área de riesgo; descarga PDF.
  - **Dimensão:** mismo modelo; colaboradores em risco, setores, **ranking de setores** (mayor a menor riesgo); filtros al lado.
  - **Eixo:** mismo modelo con indicadores por eje; detalle por eje. **Chips inferiores** ("Todas", "Exaustão emocional", "Realização profissional", etc.): al clicar una dimensión, el contenido se filtra por esa dimensión.
- **Lista de relatórios (última sección):** en vez de bajar uno a uno por dimensión/descarregamento, el usuario va a "Relatórios", elige "archivo PDF", se descarga **con los filtros que tenía aplicados**.
- **Gráfico único (análisis temporal) – único gráfico de la pantalla:**  
  Está **solo abajo**; al bajar la pantalla no hay más bloques. Contenido:
  - **Eje X:** las **ondas** en que fue enviando los reportes (Trimestre 1, Trimestre 2, …).
  - **Puntos (nube):** las **personas individuales** – las notas/scores individuales de las personas para cada filtro; en la práctica como las personas no responden a lo largo del tiempo sino en un periodo corto, en cada onda habrá **una nube de puntos**, no una línea por persona.
  - **Línea global:** la **nota/promedio global de la compañía** que promedia todas esas personas; conecta el resultado de la empresa de una onda a otra para ver si **mejoró o empeoró**.
- **Exportación inteligente:** el PDF debe respetar **exactamente los filtros aplicados** en pantalla (ej. si se ve solo "Ventas", el PDF es solo Ventas).

### 1.4 Corte de medición y ondas (de la llamada)

- Ejemplo: empresa con 500 funcionarios; no es necesario liberar los 500 el mismo día. Se puede liberar 100 hoy, 100 mañana, 100 la semana que viene, y tener **hasta fin de febrero para consolidar**. Todo ese periodo se llama **Onda 1**.
- El día que se hace el **corte** se genera el reporte (la "foto" de ese periodo). A partir de ahí, el **nuevo envío** de cuestionarios se considera **Onda 2** y suma para el indicador de la Onda 2.
- **Regla:** todas las respuestas que llegan **hasta la fecha de corte** suman para el mismo indicador (el del periodo). Cuando se abre el relatório corporativo, el número de la empresa que se ve pertenece a ese periodo. Al cerrar periodo y abrir uno nuevo, las respuestas siguientes corresponden al periodo 2, y así se **acumulan los periodos** en la serie histórica.
- Para la **persona**: si recibió cuestionario en el trimestre 1, respondió, y luego responde de nuevo en el trimestre 2, tendrá **dos cuestionarios diferentes** (dos puntos en su propia serie); ambos cuentan para la composición de cada onda en el relatório central.

---

## 2. Mapeo: especificación ↔ estado actual

### 2.1 Flujo Normal (Colaborador)

| Requisito | Estado actual | Brecha / acción |
|-----------|----------------|-----------------|
| **Invitación (link)** para realizar la encuesta | No existe. El colaborador entra por login y ve "Meus questionários". No hay email/link de invitación al asignar cuestionario. | **Falta:** Envío de invitación (email con link) al asignar cuestionario; opcionalmente link directo al cuestionario (token) sin obligar a login previo. |
| **Responder** cuestionario | Sí: Meus questionários → Responder → guardar respuestas por etapas. | OK. Mejorar UX: mensaje claro "Has completado la encuesta" y redirección automática al reporte. |
| **Feedback automático** (reporte individual privado) | Sí: al terminar redirige a `/dashboard/reporte?formulario_id=…`. API devuelve dimensiones, ejes, IID, nivel de riesgo, plan. | OK. Reforzar: asegurar que solo el propio usuario vea su reporte; texto "Tu reporte personal". |
| **Recurrencia** (nueva onda = nueva encuesta) | Sí: nueva atribución con nuevo periodo_id; colaborador ve nuevo ítem en Meus questionários. | OK. Falta que la "invitación" de la nueva onda sea explícita (email/link) si implementamos invitación. |

### 2.2 Flujo Corporativo (Gestión)

| Requisito | Estado actual | Brecha / acción |
|-----------|----------------|-----------------|
| **Carga de población** | Sí: Importar usuarios (CSV) en Usuários → Importar; atributos Unidade, Área, etc. | OK. Opcional: mensaje de éxito "Población cargada para [cliente]". |
| **Configuración de la onda** | Sí: Períodos (nome, data_inicio, data_fim, cliente_id). Atribuciones con periodo_id. | OK. Reforzar en UI: "Onda 1", "Trimestre 1" como lenguaje claro. |
| **Liberación gradual** | Sí: Atribuição em massa por filtros o por grupo; se puede hacer varias veces (100 hoy, 100 después) con el mismo periodo_id. | OK. Mejorar: en "Atribuição em massa" dejar claro "Liberar cuestionario para este periodo"; opcional: "Enviar invitación por email" al atribuir. |
| **Monitoreo** (quién respondió, estado) | Parcial: Relatório corporativo muestra KPIs (respondentes, riesgo); Usuários con filtro por periodo_id ("Ver usuários con atribución en este periodo"). No hay una pantalla única "Monitor de respondentes" con lista nombre/estado. | **Falta:** Vista "Monitor de respondentes" por periodo: lista (o tabla) de personas asignadas con estado (pendiente / en curso / completo) y, si aplica, "Enviar recordatorio". |
| **Cierre y acumulación** | Parcial: data_fim en Periodo existe pero no hay "cerrar onda" que bloquee respuestas. La "foto" es por selección de periodo en el BI. | **Definir:** ¿Cerrar onda = solo informativo (data_fim) o bloquear nuevas respuestas para ese periodo? Si es bloqueo: backend no aceptar respuestas si hoy > periodo.data_fim. |
| **Reciclar usuarios** (nueva onda sin recargar BD) | Sí: crear nuevo Período y nueva Atribuição em massa con mismo cliente/filtros; no hace falta volver a importar. | OK. |
| **Emotive como "solo gerenciar períodos"** (relatório/cuestionário padrão) | Cuestionarios y formularios existen y se asignan; no hay "configuración de relatório" por cliente (es el mismo). | OK en esencia; falta la vista unificada de monitor (lista personas, estado, reenviar recordatorio). |

### 2.3 BI (Dashboard)

| Requisito | Estado actual | Brecha / acción |
|-----------|----------------|-----------------|
| **Pestañas de métricas** (Descarregamento, Dimensiones, Ejes) | Parcial: hay pestañas "Dimensão", "Eixo analítico", "Descarrilamento", "Relatórios". No hay una pestaña superior que cambie "toda la data" a una métrica (el contenido actual mezcla dimensiones y KPIs IID). | **Ajustar:** Alinear pestañas superiores del BI con: (1) Descarregamento / IID, (2) Dimensiones, (3) Ejes; y que al cambiar de pestaña los gráficos/tablas muestren esa métrica. |
| **Filtros dinámicos (drill-down)** | Sí: sidebar con Unidade, Área, Nível, Tempo de empresa, Modelo; al cambiar, se recalculan KPIs y "Onde se concentram". | OK. Revisar que todos los bloques (gráficos, tablas) reaccionen a los mismos filtros. |
| **Indicadores de riesgo** | Sí: contadores (colaboradores em risco, %), setores críticos (ranking), "Onde se concentram" (unidade → área con faixas). | OK. Falta **mapa de calor** explícito si lo piden (heatmap por área/dimensão). |
| **Análisis temporal** | Parcial: gráfico "Estamos Melhorando?" con evolución IID por periodo (línea/puntos). No hay "nube de puntos" de respuestas anonimizadas ni eje X explícito "Onda 1, Onda 2…". | **Mejorar:** Un solo gráfico al final: eje X = ondas; nube de puntos por onda (personas anonimizadas); línea del promedio global de la compañía entre ondas ("¿mejoró o empeoró?"). |
| **PDF que respeta filtros** | Parcial: el PDF del relatório corporativo recibe cliente_id, periodo_id, formulario_id. No recibe filtros de Unidade/Área actuales. | **Falta:** Pasar filtros (unidade, area, etc.) al endpoint del PDF para que el contenido del PDF sea solo el subconjunto filtrado. |

---

## 3. Plan de reforma (priorizado)

Objetivo: que los dos flujos (Normal y Corporativo) y el BI sean claros, amigables y alineados con la especificación.

### Fase 1 – Flujo Normal más claro (colaborador)
1. **Invitation flow (opción A – mínima):** Al hacer "Atribuição em massa" (o asignar a un usuario), opción "Enviar email de invitación" que envíe un email con link a `FRONTEND_URL/dashboard` (o `/auth/login`) y texto "Tienes un nuevo cuestionario en Meus questionários".  
   **Opción B (ideal):** Link directo al cuestionario con token (ej. `/responder?token=xxx`) que identifique usuario_formulario_id; si no hay sesión, redirigir a login y luego abrir el cuestionario.
2. **Post-cuestionario:** Tras enviar la última etapa, mensaje claro "Encuesta completada" y botón "Ver mi reporte" que lleve a `/dashboard/reporte?formulario_id=…`.
3. **Reporte individual:** Título visible "Tu reporte personal" y asegurar que solo el propio usuario (o gestor/admin de su cliente) pueda verlo.

### Fase 2 – Flujo Corporativo más claro (gestor/admin)
1. **Monitor de respondentes:** Nueva vista (pestaña o página) "Monitor" o "Respondentes" por Cliente + Período (+ Formulário): tabla o lista con usuario (nombre/email), estado (pendiente / en curso / completo), fecha límite. Opcional: botón "Enviar recordatorio" (email).
2. **Cierre de onda:** Definir regla: si `periodo.data_fim` está en el pasado, (opcional) no permitir nuevas respuestas para ese periodo (validación en `POST /respostas/salvar`). En la UI de Períodos, mostrar "Onda cerrada" cuando data_fim < hoy.
3. **Liberación gradual:** En "Atribuição em massa", texto tipo "Estás liberando el cuestionario para el periodo [nombre]. Puedes hacerlo varias veces para distintos grupos."

### Fase 3 – BI alineado con la especificación
1. **Pestañas de métricas:** Renombrar/organizar pestañas superiores a: **Descarregamento (IID)** | **Dimensiones** | **Ejes** | **Relatórios**. Que el contenido principal (KPIs, gráficos, tablas) cambie según la pestaña (hoy parte ya lo hace; unificar).
2. **Gráfico temporal (único gráfico abajo):** Eje X = ondas (nombres de periodo); nube de puntos por onda (scores individuales anonimizados); línea que conecta el promedio global de la compañía onda a onda ("¿mejoró o empeoró?"). Referencia: Figma y llamada (en cada onda una nube, no línea por persona).
3. **Mapa de calor:** Si se confirma necesidad, añadir bloque "Mapa de calor" por área (o unidade) vs faixa de riesgo (o dimensión).
4. **PDF con filtros:** Endpoint `GET /api/v1/pdf/relatorio-corporativo` aceptar query params `unidade`, `area`, etc. y que el contenido del PDF (tablas, KPIs) use solo ese subconjunto; en el front, al generar PDF, enviar los filtros actuales del sidebar.

### Fase 4 – UX y copy
1. Sustituir en pantallas clave "Período" por "Onda" o "Ciclo" donde ayude (ej. "Onda 1 – Primer Trimestre").
2. Mensajes de éxito/error claros (invitación enviada, onda cerrada, etc.).
3. En el menú, orden y nombres que reflejen: **Experiencia colaborador** (Meus questionários, Reporte) vs **Gestión** (Usuários, Períodos, Atribuição em massa, Relatório corporativo, Grupos).

---

## 4. Resumen de brechas y siguientes pasos

| Brecha | Prioridad | Esfuerzo estimado |
|--------|-----------|-------------------|
| Invitación (link/email) al asignar cuestionario | Alta | Medio (backend email + opcional token link) |
| Monitor de respondentes (lista estado por periodo) | Alta | Medio (nueva vista + API) |
| PDF corporativo que respeta filtros | Alta | Bajo (params + filtrar datos en PDF) |
| Cierre de onda (bloquear respuestas si data_fim pasado) | Media | Bajo (validación en salvar respostas) |
| Pestañas BI = Descarregamento / Dimensiones / Ejes | Media | Bajo (reorganizar UI existente) |
| Gráfico temporal con eje X ondas + línea promedio | Media | Bajo (ya hay evolucao-iid; ajustar eje y leyenda) |
| Link directo al cuestionario (token) | Media | Medio (token, ruta /responder?token=) |
| Mapa de calor | Baja | Medio (nuevo componente) |

Recomendación: implementar en este orden **Fase 1 (invitación mínima + post-cuestionario)**, **Fase 3 (PDF con filtros)** y **Fase 2 (Monitor de respondentes + cierre de onda)**; luego refinar BI (pestañas y gráfico temporal) y, si aplica, link con token y mapa de calor.

Cuando indiques por cuál fase o ítem quieres empezar, se puede bajar a tareas concretas de código (backend + frontend) paso a paso.

---

## 5. Nota técnica (Emotive)

- En Emotive el **relatório** y el **cuestionario** son padrão (no se configuran por cliente). **Lo que se gerencia son los períodos (ondas)**: configurar el periodo y enviar cuestionarios dentro de ese periodo; al cerrar/cambiar de periodo, se abre la siguiente onda y se puede reciclar a los mismos usuarios sin recargar la base.
- La base actual (migrations) permite iterar sin depender de llamadas asíncronas; cada flujo es independiente.

---

## 6. Glosario (solo Emotive)

| Término | Significado |
|--------|--------------|
| **Onda / Período** | Corte temporal de medición; todas las respuestas hasta la fecha de corte suman para el mismo indicador; al cerrar se abre la siguiente onda. |
| **Reciclar** | Volver a enviar cuestionario a los mismos usuarios en una nueva onda sin recargar la base (crear nuevo período y nueva atribución). |
