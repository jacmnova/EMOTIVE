# 📊 Explicación del Cálculo de Índices

## 1. AGRUPACIÓN DE PREGUNTAS

### Cómo se agrupan las preguntas por dimensión:

1. **Desde la Base de Datos**: Se obtienen las relaciones `pergunta_variavel`
   - Cada pregunta puede pertenecer a UNA o MÚLTIPLES dimensiones
   - La relación se guarda en la tabla `pergunta_variavel`

2. **Estado Actual en BD**:
   - EXEM: 98 preguntas
   - REPR: 26 preguntas
   - DECI: 26 preguntas
   - FAPS: 29 preguntas
   - EXTR: 10 preguntas
   - ASMO: 15 preguntas

3. **Según el CSV**:
   - EXEM: 99 preguntas ⚠️ (falta 1)
   - REPR: 26 preguntas ✅
   - DECI: 26 preguntas ✅
   - FAPS: 29 preguntas ✅
   - EXTR: 10 preguntas ✅
   - ASMO: 16 preguntas ⚠️ (falta 1)

## 2. CÁLCULO DE VALORES

### Paso a paso:

1. **Para cada dimensión (EXEM, REPR, DECI, etc.)**:
   ```php
   $pontuacao = 0;
   foreach ($variavel->perguntas as $pergunta) {
       $resposta = obtener_respuesta_del_usuario($pergunta->id);
       $valorOriginal = $resposta->valor_resposta; // 0, 1, 2, 3, 4, 5, o 6
       
       // Verificar si la pregunta es invertida
       $esInvertida = PerguntasInvertidasHelper::precisaInversao($pergunta);
       
       if ($esInvertida) {
           $valorUsado = 6 - $valorOriginal; // Inversión: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
       } else {
           $valorUsado = $valorOriginal; // Sin cambios
       }
       
       $pontuacao += $valorUsado;
   }
   ```

2. **Ejemplo con EXEM (98 preguntas)**:
   - Si el usuario responde **5** en todas las preguntas:
     - Preguntas normales (77): 77 × 5 = 385
     - Preguntas invertidas (21): 21 × (6-5) = 21 × 1 = 21
     - **Total = 406 puntos**

3. **Cálculo del porcentaje para el gráfico**:
   ```php
   $maximoPosible = $totalPreguntas × 6; // 98 × 6 = 588
   $porcentaje = ($pontuacao / $maximoPosible) × 100;
   // Ejemplo: (406 / 588) × 100 = 69.05%
   ```

## 3. PROBLEMAS IDENTIFICADOS

### ❌ Problema 1: Número de preguntas incorrecto
- EXEM tiene 98 preguntas en BD, pero debería tener 99 según CSV
- ASMO tiene 15 preguntas en BD, pero debería tener 16 según CSV

### ❌ Problema 2: Las relaciones pregunta-variable pueden estar incorrectas
- El comando `actualizar:relaciones-por-texto` busca por texto, pero puede no encontrar todas las preguntas
- Algunas preguntas pueden tener texto ligeramente diferente

### ❌ Problema 3: El CSV muestra valores diferentes
- La línea 9 del CSV muestra:
  - REPR: 156
  - DECI: 156
  - FAPS: 174
  - EXTR: 60
  - ASMO: 96
  - EXEM: "SCORE" (no tiene valor numérico)

## 4. PREGUNTAS PARA CLARIFICAR

1. **¿Cómo se calcula EXEM según el CSV?**
   - ¿Es la suma de todas las otras dimensiones?
   - ¿Tiene un cálculo diferente?

2. **¿Las relaciones pregunta-variable están correctas?**
   - ¿Debería ejecutar `actualizar:relaciones-por-texto` nuevamente?
   - ¿Hay alguna pregunta que falta o sobra?

3. **¿El cálculo del porcentaje es correcto?**
   - ¿Debería usar un máximo diferente?
   - ¿El gráfico debería mostrar valores absolutos en lugar de porcentajes?

