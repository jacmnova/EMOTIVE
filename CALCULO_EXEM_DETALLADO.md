# 📊 Cálculo Detallado de EXEM

## 1. AGRUPACIÓN DE PREGUNTAS

### Desde la Base de Datos:
```php
$exem = Variavel::with('perguntas')
    ->where('tag', 'ExEm')
    ->where('formulario_id', 1)
    ->first();

// Obtiene 98 preguntas desde la tabla pivot pergunta_variavel
```

### Estado Actual:
- **BD**: 98 preguntas asociadas a EXEM
- **CSV**: 99 preguntas deberían estar en EXEM
- **Diferencia**: Falta 1 pregunta

### Cómo se obtienen las preguntas:
1. Se busca la variable con `tag = 'ExEm'` y `formulario_id = 1`
2. Se cargan las preguntas desde la relación `pergunta_variavel`
3. Cada fila en `pergunta_variavel` relaciona una pregunta con EXEM

## 2. CÁLCULO DE VALORES

### Proceso paso a paso:

```php
$pontuacao = 0;

foreach ($exem->perguntas as $pergunta) {
    // 1. Obtener respuesta del usuario
    $resposta = $respostasUsuario->get($pergunta->id);
    
    if (!$resposta || $resposta->valor_resposta === null) {
        continue; // Saltar si no hay respuesta
    }
    
    // 2. Obtener valor original (0-6)
    $valorOriginal = (int)$resposta->valor_resposta;
    
    // 3. Verificar si es pregunta invertida
    $esInvertida = PerguntasInvertidasHelper::precisaInversao($pergunta);
    
    // 4. Aplicar lógica de inversión
    if ($esInvertida) {
        $valorUsado = 6 - $valorOriginal; // 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
    } else {
        $valorUsado = $valorOriginal; // Sin cambios
    }
    
    // 5. Sumar al total
    $pontuacao += $valorUsado;
}
```

### Ejemplo con todas las respuestas en 5:
- **Preguntas normales (77)**: 77 × 5 = 385
- **Preguntas invertidas (21)**: 21 × (6-5) = 21 × 1 = 21
- **Total = 406 puntos**

### Cálculo del porcentaje:
```php
$maximoPosible = $totalPreguntas × 6; // 98 × 6 = 588
$porcentaje = ($pontuacao / $maximoPosible) × 100;
// Ejemplo: (406 / 588) × 100 = 69.05%
```

## 3. PROBLEMA IDENTIFICADO

### Diferencia entre CSV y BD:

**CSV dice que EXEM debería tener estas preguntas:**
- #062, #001, #036, #088, #037, #078, #002, #003, #079, #063, etc. (99 preguntas)

**BD tiene:**
- numero_da_pergunta: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, etc. (98 preguntas)

### El problema:
- El CSV usa números como `#062`, `#001`, `#036`
- La BD tiene `numero_da_pergunta` como `1`, `2`, `3`, etc.
- **Hay un desajuste en el mapeo**: La pregunta #062 del CSV no corresponde a numero_da_pergunta 1 en la BD

## 4. PREGUNTA FALTANTE

Según el análisis:
- CSV tiene 99 preguntas para EXEM
- BD tiene 98 preguntas para EXEM
- **Falta 1 pregunta**

La pregunta que falta probablemente es una que:
1. Tiene EXEM > 0 en el CSV
2. No está relacionada en la tabla `pergunta_variavel` con la variable EXEM

