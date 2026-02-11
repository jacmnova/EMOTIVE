# Cómo funciona la fórmula del cálculo

## En la versión PHP (Laravel)

En el proyecto Laravel (`php_old_app`):

- **Tabla** `tipo_calculo`: tiene `nome`, `descricao`, `operador` (string 20), `formula` (text), `ativo`, `timestamps`. La migración y el modelo `Calculo` (que usa la tabla `tipo_calculo`) definen estos campos.
- **CRUD**: `CalculosController` hace create/update con validación de `nome`, `descricao`, `operador` y `formula` (todos requeridos). Solo **guarda y muestra** esos datos; no los usa en ningún cálculo.
- **Cálculo real del reporte**: el relatório usa el trait `CalculaEjesAnaliticos` en `DadosController` (y `RelatorioController`). Ahí se calculan las puntuaciones por dimensión y los índices EE, PR, SO con **lógica fija** (suma de respostas, inversión cuando aplica, mapeo EE=EXEM+REPR, etc.). **No se lee** `formulario.calculo_id` ni el `operador`/`formula` de `tipo_calculo`.
- **Seeder**: `CalculoSeeder` inserta tipos con nombre, descripción, operador y fórmula en texto (ej. "media(grupo1) - media(grupo2)" para DELTA ENTRE GRUPOS). Es contenido **descriptivo**; ningún código PHP evalúa esa fórmula.

Conclusión: en PHP la fórmula y el operador son **metadato/documentación**. El comportamiento es el mismo que en la migración FastAPI: se almacenan y muestran, pero no intervienen en la lógica de cálculo del reporte.

---

## Estado actual (FastAPI/Next.js)

En el modelo **Tipo de Cálculo** (`tipo_calculo`) tienes:

- **nome**: nombre del tipo (ej. "MÉDIA", "DELTA ENTRE GRUPOS").
- **descricao**: descripción textual.
- **operador**: identificador del operador (ej. "diferenca", "media").
- **formula**: expresión en texto (ej. `media(grupo1) - media(grupo2)`).

Estos campos se **guardan y muestran** en el listado y en el modal de edición de cálculos.  
Hoy **no se usan** en la lógica que calcula puntuaciones ni en el reporte.

### Dónde se calcula realmente

Los resultados (puntuaciones por dimensión, índices EE/PR/SO, etc.) se calculan en:

- **`app/services/calculos.py`**
  - `calcular_puntuacion_dimension`: suma las respuestas de las preguntas de cada variable, aplica inversión si toca y clasifica en Baixa/Moderada/Alta con los umbrales **B, M, A** de la **Variável**.
  - `calcular_indices_desde_respostas`: agrupa dimensiones (EXEM+REPR→EE, etc.) y suma puntuaciones con reglas fijas.

Esa lógica **no lee** `formulario.calculo_id` ni el `TipoCalculo` (ni su `formula` ni su `operador`).  
Es decir: la **fórmula del cálculo** es, por ahora, solo **metadato/documentación** del tipo de cálculo; no se interpreta ni se ejecuta.

---

## Cómo podría funcionar la fórmula (futuro)

Para que la fórmula (y el operador) **influyan** en los resultados, haría falta:

1. **En el reporte / servicio de cálculos**
   - Cargar el formulario con su `calculo_id` y el `TipoCalculo` asociado (incluyendo `operador` y `formula`).

2. **Interpretar la fórmula**
   - Opción **A – Predefinido por operador**: el campo `operador` indica un algoritmo concreto (ej. "media", "diferenca", "soma"). El backend tiene una función por operador y usa `formula` solo como descripción o para elegir parámetros (qué grupos, qué variables).
   - Opción **B – Expresión evaluable**: definir un mini-lenguaje seguro (por ejemplo solo `media(...)`, `suma(...)`, nombres de dimensiones, operadores `+`, `-`, etc.) y evaluar la expresión en el backend (parser + evaluador propio o librería segura), rellenando variables con las puntuaciones ya calculadas por dimensión/grupo.

3. **Dónde encajar**
   - Usar el resultado de esa evaluación donde ahora se usa una regla fija (por ejemplo para un índice compuesto o para un “score global” que hoy sea una suma simple).  
   - Las puntuaciones por dimensión pueden seguir calculándose como ahora (suma de respuestas, B/M/A de la variável); la fórmula podría aplicarse **sobre esos resultados** (ej. `media(EXEM, REPR) - media(DECI, FAPS)`).

---

## Resumen

| Qué | Estado |
|-----|--------|
| Campos **operador** y **formula** en `tipo_calculo` | Se guardan y muestran en la UI. |
| Uso en cálculos / reporte | **No**: la lógica actual no los lee ni los ejecuta. |
| Cálculo real | En `app/services/calculos.py`, con reglas fijas y umbrales B/M/A de cada variável. |
| Cómo hacer que la fórmula “funcione” | Cargar el tipo de cálculo en el reporte/servicio y añadir un paso que interprete `operador` y/o `formula` sobre las puntuaciones ya calculadas (por operadores predefinidos o con un evaluador seguro). |
