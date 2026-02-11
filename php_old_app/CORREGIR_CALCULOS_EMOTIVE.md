# Corrección de Cálculos E.MO.TI.VE

## Problema Identificado

Cuando todas las respuestas son 0, los scores esperados según el Excel son:
- EXEM: 13
- REPR: 16
- DECI: 8
- FAPS: 9
- EXTR: 18
- ASMO: 0

Pero el sistema está calculando valores diferentes.

## Análisis del CSV

El CSV muestra:
- Línea 9: Scores totales cuando todo es 0
- Columnas EXEM, REPR, DECI, FAPS, EXTR, ASMO: Valores que cada pregunta aporta a cada dimensión

## Acciones Requeridas

1. **Verificar mapeo pregunta-dimensión**: Las relaciones en `ActualizarRelacionesPreguntas.php` pueden no coincidir con el CSV
2. **Verificar preguntas invertidas**: La lista actual puede estar incompleta o incorrecta
3. **Ajustar cálculo**: Asegurar que cuando User_Choice=0:
   - Preguntas normales: Score = 0
   - Preguntas invertidas: Score = 6

## Próximos Pasos

1. Analizar el CSV línea por línea para extraer las relaciones correctas
2. Actualizar `ActualizarRelacionesPreguntas.php` con las relaciones correctas
3. Actualizar la lista de preguntas invertidas
4. Verificar que los cálculos coincidan con el Excel

