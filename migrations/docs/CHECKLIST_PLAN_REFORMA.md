# Checklist – Plan de reforma Emotive 2

Checklist de todo lo previsto en `ANALISIS_EMOTIVE2_Y_PLAN_REFORMA.md`. Se va marcando conforme se implementa.

---

## Fase 1 – Flujo Normal (colaborador)

| # | Item | Estado | Notas |
|---|------|--------|--------|
| 1.1 | Invitación por email al asignar (opción A mínima) | ✅ Hecho | Checkbox en Atribuição em massa y en asignación individual; email con link al dashboard. |
| 1.1b | Link directo al cuestionario con token (`/responder?token=`) | ✅ Hecho | Token JWT (30 días); /responder valida e redireciona a questionário ou login. E-mails de convite/lembrete usam o link. |
| 1.2 | Post-cuestionario: mensaje "Encuesta completada" + botón "Ver mi reporte" | ✅ Hecho | Pantalla intermedia con enlaces. |
| 1.3 | Reporte individual: título "Tu reporte personal" y solo propio usuario/gestor | ✅ Hecho | Título condicional por uid. |

---

## Fase 2 – Flujo Corporativo (gestor/admin)

| # | Item | Estado | Notas |
|---|------|--------|--------|
| 2.1 | Monitor de respondentes (vista por Cliente + Período + Formulário) | ✅ Hecho | Lista estado pendiente/completo, filtros. |
| 2.1b | Botón "Enviar recordatorio" por respondente en Monitor | ✅ Hecho | Botão "Enviar recordatório" por linha (só pendentes); e-mail de lembrete. |
| 2.2 | Cierre de onda: no aceptar respuestas si hoy > periodo.data_fim | ✅ Hecho | Validación en POST /respostas/salvar. |
| 2.2b | UI: mostrar "Onda cerrada" en Períodos cuando data_fim < hoy | ✅ Hecho | Badge "Onda cerrada" en lista de períodos. |
| 2.3 | Texto liberación gradual en Atribuição em massa | ✅ Hecho | Párrafo explicativo. |

---

## Fase 3 – BI alineado con especificación

| # | Item | Estado | Notas |
|---|------|--------|--------|
| 3.1 | Pestañas superiores: Descarregamento (IID) \| Dimensão \| Eixos \| Relatórios | ✅ Hecho | Orden y etiquetas; tab por defecto = Descarregamento. |
| 3.2 | Gráfico temporal único: eje X = ondas, nube de puntos + línea promedio | ✅ Hecho | API iid_pontos; nuvem + linha da média. |
| 3.3 | Mapa de calor (área vs faixa de risco) | ✅ Hecho | Tabela calor setor × faixas no Relatório corporativo. |
| 3.4 | PDF corporativo que respeta filtros (unidade, area, etc.) | ✅ Hecho | Query params en endpoint y front al generar PDF. |

---

## Fase 4 – UX y copy

| # | Item | Estado | Notas |
|---|------|--------|--------|
| 4.1 | Sustituir "Período" por "Onda" donde ayude (ej. "Onda 1 – Trimestre") | ✅ Hecho | Períodos (Ondas) en menú y título; badge "Onda cerrada". |
| 4.2 | Mensajes de éxito/error claros (invitación enviada, onda cerrada, etc.) | ✅ Hecho | Atribuição em massa, Monitor, asignar usuário, criar período. |
| 4.3 | Menú: orden y nombres (Experiencia colaborador vs Gestión) | ✅ Hecho | Orden: Início, Meus questionários, Chat → Usuários, Períodos (Ondas), Atribuição em massa, Grupos, Relatório, Monitor, Projetos. |

---

## Resumen

- **Hecho:** 16 ítems (plan completo).
- **Pendiente:** 0 ítems.

Última actualización: 1.1b link com token, 3.2 gráfico temporal com nuvem de pontos, 3.3 mapa de calor.
