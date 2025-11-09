# Instrucciones para Diagnosticar el Problema del Radar

## 🔍 Problema Reportado

Cuando todas las respuestas están configuradas en 0, las dimensiones ASMO, REPR y DECI muestran valores diferentes de 0 en el gráfico radar.

## 📋 Pasos para Diagnosticar

### 1. Identificar el Usuario y Formulario

Necesitas el ID del usuario que está viendo el gráfico con valores incorrectos.

### 2. Ejecutar Comando de Diagnóstico

```bash
php artisan emotive:diagnosticar-radar {usuario_id} {formulario_id} --todas-respuestas
```

Ejemplo:
```bash
php artisan emotive:diagnosticar-radar 5 1 --todas-respuestas
```

Este comando mostrará:
- Total de respuestas del usuario
- Cuántas respuestas están en 0
- Cuántas respuestas tienen valor > 0
- Detalle de cada dimensión (ASMO, REPR, DECI, etc.)
- Qué preguntas están contribuyendo con valores > 0
- Si hay preguntas invertidas que están convirtiendo 0 en 6

### 3. Verificar las Respuestas en la Base de Datos

```bash
php artisan tinker
```

Luego ejecuta:
```php
$usuarioId = 5; // Cambiar por el ID real
$formularioId = 1;

$respostas = \App\Models\Resposta::where('user_id', $usuarioId)
    ->whereIn('pergunta_id', function($query) use ($formularioId) {
        $query->select('id')
            ->from('perguntas')
            ->where('formulario_id', $formularioId);
    })
    ->get();

// Ver respuestas que NO están en 0
$respostasConValor = $respostas->filter(function($r) {
    return (int)($r->valor_resposta ?? 0) > 0;
});

echo "Respuestas con valor > 0: " . $respostasConValor->count() . PHP_EOL;
foreach ($respostasConValor as $r) {
    $p = \App\Models\Pergunta::find($r->pergunta_id);
    echo "Pregunta #{$p->numero_da_pergunta} (ID: {$r->pergunta_id}): valor = {$r->valor_resposta}" . PHP_EOL;
}
```

### 4. Verificar las Relaciones Pregunta-Variable

```bash
php artisan tinker
```

```php
// Verificar qué preguntas están asociadas a cada dimensión
$variaveis = \App\Models\Variavel::with('perguntas')->where('formulario_id', 1)->get();

foreach ($variaveis as $v) {
    $tag = strtoupper($v->tag);
    if (in_array($tag, ['ASMO', 'REPR', 'DECI'])) {
        echo PHP_EOL . $tag . ' - ' . $v->nome . PHP_EOL;
        echo 'Total preguntas: ' . $v->perguntas->count() . PHP_EOL;
        echo 'Preguntas: ';
        $nums = $v->perguntas->pluck('numero_da_pergunta')->toArray();
        echo implode(', ', $nums) . PHP_EOL;
    }
}
```

## 🎯 Posibles Causas

1. **Respuestas no están realmente en 0**: Algunas respuestas pueden tener valores diferentes de 0
2. **Preguntas invertidas**: Si hay preguntas invertidas en estas dimensiones, cuando están en 0 se convierten en 6
3. **Relaciones incompletas**: Las relaciones pregunta-variable pueden estar incompletas
4. **Preguntas duplicadas**: Puede haber múltiples preguntas con el mismo `numero_da_pergunta`

## ✅ Solución Esperada

Si todas las respuestas están en 0:
- **Preguntas normales**: 0 → 0 (suma 0)
- **Preguntas invertidas**: 0 → 6 (suma 6 por cada pregunta invertida)

Para que el resultado sea 0 cuando todo está en 0, las preguntas invertidas deben estar en 6 (no en 0).

## 📝 Información Necesaria

Para resolver el problema, necesito:
1. ID del usuario que está viendo el problema
2. ID del formulario
3. Resultado del comando `emotive:diagnosticar-radar`
4. Confirmación de si realmente todas las respuestas están en 0

