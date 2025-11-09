# Lógica de Cálculo según el CSV

## Análisis del CSV "EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv"

### Estructura del CSV

El CSV tiene las siguientes columnas importantes:
- **User_Choice** (índice 11): Valor de respuesta del usuario (0-6)
- **Score** (índice 12): Score calculado para esa pregunta
- **EXEM, REPR, DECI, FAPS, EXTR, ASMO** (índices 14-19): Valores que aporta la pregunta a cada dimensión
- **EE, PR, SO** (índices 20-22): Valores que aporta la pregunta a cada eje

### Dimensiones y sus Preguntas

Según el análisis del CSV cuando User_Choice=5:

1. **EXEM (Exaustão Emocional)**: Preguntas [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61]
   - Suma cuando User_Choice=5: **98**

2. **REPR (Realização Profissional)**: Preguntas [28, 29, 30, 31, 32, 33, 34, 35, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99]
   - Suma cuando User_Choice=5: **98**

3. **DECI (Despersonalização/Cinismo)**: Preguntas [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56]
   - Suma cuando User_Choice=5: **113**

4. **FAPS (Fatores Psicossociais)**: Preguntas [78, 79, 80, 81, 82, 83, 84, 85, 86, 87]
   - Suma cuando User_Choice=5: **30**

5. **EXTR (Excesso de Trabalho)**: Preguntas [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77]
   - Suma cuando User_Choice=5: **80**

6. **ASMO (Assédio Moral)**: Preguntas [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
   - Suma cuando User_Choice=5: **75**

### Ejes y sus Preguntas

1. **EE (Energia Emocional)**: Preguntas [28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99]
   - Suma cuando User_Choice=5: **166**

2. **PR (Propósito e Relações)**: Preguntas [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87]
   - Suma cuando User_Choice=5: **143**

3. **SO (Sustentabilidade Ocupacional)**: Preguntas [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77]
   - Suma cuando User_Choice=5: **155**

### Preguntas Invertidas

Las preguntas que requieren inversión (escala comienza con 6): [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97]

**Lógica de inversión**: 
- Para preguntas invertidas: Score = 6 - User_Choice
- Para preguntas normales: Score = User_Choice

### Cálculo de Dimensiones

Para calcular cada dimensión:
1. Obtener todas las preguntas que pertenecen a esa dimensión
2. Para cada pregunta:
   - Si es invertida: usar valor = 6 - valor_resposta
   - Si es normal: usar valor = valor_resposta
3. Sumar todos los valores

### Cálculo de Ejes

Los ejes se calculan sumando directamente las columnas EE, PR, SO del CSV (no son la suma de dimensiones).

**IMPORTANTE**: Los ejes NO son simplemente la suma de dimensiones:
- EE ≠ EXEM + REPR
- PR ≠ DECI + FAPS  
- SO ≠ EXTR + ASMO

Los ejes tienen sus propias listas de preguntas que pueden solaparse con las dimensiones.

### Valores Esperados (Línea 9 del CSV)

La línea 9 muestra valores cuando User_Choice=5 para todas las preguntas:
- EXEM: 26 (pero la suma directa da 98)
- REPR: 26 (pero la suma directa da 98)
- DECI: 29 (pero la suma directa da 113)
- FAPS: 10 (pero la suma directa da 30)
- EXTR: 16 (pero la suma directa da 80)
- ASMO: 15 (pero la suma directa da 75)
- EE: 46 (pero la suma directa da 166)
- PR: 39 (pero la suma directa da 143)
- SO: 31 (pero la suma directa da 155)

**NOTA**: Los valores de la línea 9 NO coinciden con las sumas directas. Esto sugiere que:
1. Los valores pueden estar normalizados o escalados
2. Puede haber una fórmula de cálculo diferente
3. Los valores pueden ser cuando User_Choice=0, no 5

### Próximos Pasos

1. Verificar si los valores de la línea 9 corresponden a User_Choice=0
2. Revisar si hay fórmulas de normalización o escalado
3. Actualizar el código de cálculo para usar las listas correctas de preguntas por dimensión y eje
4. Implementar el cálculo correcto de inversión para preguntas invertidas

