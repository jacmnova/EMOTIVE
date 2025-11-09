# 🚀 Despliegue: Actualización de Relaciones según CSV ALE

## ✅ Cambios Realizados

1. **Nuevo comando creado**: `app/Console/Commands/ActualizarRelacionesPorTextoALE.php`
   - Agrupa preguntas según el CSV ALE usando columnas 27-32
   - Usa identificación por texto para evitar problemas con IDs

2. **Cálculo de porcentajes corregido**:
   - `DadosController.php`: Calcula porcentaje para gráfico radar (0-100)
   - `RelatorioController.php`: Mismo cálculo para PDF
   - Vista actualizada para mostrar porcentajes en lugar de valores absolutos

3. **Corrección de error**: Variable `$numeroPergunta` no definida en `DadosController.php`

## 📋 Pasos para Desplegar en el Servidor

### 1. Subir los cambios al repositorio

```bash
# En tu máquina local
git add .
git commit -m "Actualizar relaciones pregunta-variable según CSV ALE y corregir cálculo de porcentajes"
git push origin main
```

### 2. En el servidor

```bash
# Conectarse al servidor
ssh usuario@servidor

# Ir al directorio del proyecto
cd /ruta/al/proyecto

# Actualizar código
git pull origin main

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# IMPORTANTE: Actualizar relaciones según CSV ALE
php artisan actualizar:relaciones-ale

# Verificar que funcionó
php artisan tinker
>>> $v = \App\Models\Variavel::where('tag', 'ExEm')->first();
>>> $v->perguntas->count();
# Debería mostrar: 26
```

### 3. Verificar Relaciones

```bash
php artisan tinker
```

```php
// Verificar todas las dimensiones
$variaveis = \App\Models\Variavel::with('perguntas')->where('formulario_id', 1)->get();

foreach ($variaveis as $v) {
    $tag = strtoupper($v->tag ?? '');
    if (in_array($tag, ['ASMO', 'REPR', 'DECI', 'EXEM', 'FAPS', 'EXTR'])) {
        echo $tag . ': ' . $v->perguntas->count() . ' preguntas' . PHP_EOL;
    }
}
```

**Valores esperados:**
- EXEM: 26 preguntas
- REPR: 26 preguntas
- DECI: 29 preguntas
- FAPS: 10 preguntas
- EXTR: 16 preguntas (puede mostrar 15 si falta 1)
- ASMO: 15 preguntas

### 4. Verificar Cálculos

```bash
# Probar con un usuario que tenga respuestas
php artisan emotive:diagnosticar-radar [usuario_id] 1
```

## ⚠️ Importante

1. **CSV necesario**: El comando busca el CSV en:
   - `base_path('EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv')`
   - O en `/Users/novadesck/Downloads/EMULADOR - EMOTIVE ALE - perguntas_completas_99 (1).csv`
   
   **Asegúrate de copiar el CSV al servidor** si es necesario.

2. **Backup**: Antes de ejecutar `actualizar:relaciones-ale`, haz un backup de la tabla `pergunta_variavel`:
   ```bash
   mysqldump -u usuario -p nombre_bd pergunta_variavel > backup_pergunta_variavel.sql
   ```

3. **Rangos actualizados**: El comando actualiza automáticamente los rangos B, M, A para todas las variables.

## 📊 Archivos Modificados

- `app/Console/Commands/ActualizarRelacionesPorTextoALE.php` (nuevo)
- `app/Http/Controllers/DadosController.php` (cálculo de porcentaje)
- `app/Http/Controllers/RelatorioController.php` (cálculo de porcentaje)
- `resources/views/participante/emotive/partials/_scripts.blade.php` (usar porcentaje)

## ✅ Verificación Post-Despliegue

1. Generar un relatório y verificar que los valores del radar estén entre 0-100
2. Verificar que las dimensiones tengan el número correcto de preguntas
3. Probar con un usuario que tenga todas las respuestas en 0 y verificar que dé 0%

