# Problema: Mapeo de Preguntas

## 🔍 Problema Identificado

1. **En la Base de Datos:**
   - Hay 99 preguntas
   - Múltiples preguntas tienen el mismo `numero_da_pergunta` (rango 1-36)
   - Ejemplo: Hay 4 preguntas con `numero_da_pergunta = 1`

2. **En el CSV:**
   - ID_Quest II va de 1 a 99 (orden en el CSV)
   - ID_Quest (numero_da_pergunta) va de 1 a 99 (pero no secuencial)
   - Ejemplo: ID_Quest II #001 → numero_da_pergunta #062

3. **El Sistema:**
   - Usa `numero_da_pergunta` para identificar preguntas invertidas
   - Usa `numero_da_pergunta` para asociar preguntas a dimensiones
   - Pero hay múltiples preguntas con el mismo `numero_da_pergunta`

## ❌ Consecuencia

Cuando el sistema busca una pregunta por `numero_da_pergunta = 62`, puede encontrar múltiples resultados o ninguno, dependiendo de cómo esté implementado.

## ✅ Solución Necesaria

Necesitamos verificar:
1. ¿Cómo se están guardando las respuestas? ¿Por ID de pregunta o por numero_da_pergunta?
2. ¿Cómo se están asociando las preguntas a las dimensiones? ¿Por ID o por numero_da_pergunta?
3. ¿El CSV realmente usa numero_da_pergunta o debería usar ID_Quest II?

## 📊 Valores Esperados según CSV

Cuando User_Choice=6 para todas las preguntas:
- EXEM: 0 (todas las preguntas son invertidas)
- REPR: 0 (todas las preguntas son invertidas)
- DECI: 0 (todas las preguntas son invertidas)
- FAPS: 0 (todas las preguntas son invertidas)
- EXTR: 0 (todas las preguntas son invertidas)
- ASMO: 0 (todas las preguntas son invertidas)

Esto significa que según el CSV, TODAS las preguntas de estas dimensiones son invertidas cuando User_Choice=6.

