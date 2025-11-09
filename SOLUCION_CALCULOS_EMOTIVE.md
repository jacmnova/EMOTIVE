# Solución para Corregir Cálculos E.MO.TI.VE

## Problema

Los cálculos no coinciden con el Excel cuando todas las respuestas son 0. Los scores esperados son:
- EXEM: 13
- REPR: 16
- DECI: 8
- FAPS: 9
- EXTR: 18
- ASMO: 0

## Análisis del CSV

El CSV "EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv" muestra:
- User_Choice=5 para todas las preguntas (no 0)
- Las columnas EXEM, REPR, DECI, FAPS, EXTR, ASMO muestran valores cuando User_Choice=5
- Preguntas invertidas: 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97

## Interpretación Necesaria

Para calcular correctamente cuando User_Choice=0, necesito entender:
1. ¿Las columnas EXEM, REPR, etc. indican pertenencia (1=sí, 0=no) o valor que aporta?
2. ¿Cómo se calcula el Score cuando User_Choice=0 para preguntas invertidas?
3. ¿El Score se suma directamente o se distribuye entre dimensiones?

## Próximos Pasos

1. Verificar con el usuario cómo interpretar el CSV
2. O buscar en el Excel original la lógica de cálculo
3. Actualizar las relaciones pregunta-dimensión según la interpretación correcta
4. Actualizar la lista de preguntas invertidas
5. Verificar que los cálculos coincidan

