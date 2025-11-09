# Guía para Verificar el Problema de FAPS

## 🔍 Pasos para Diagnosticar

### 1. Identificar el Usuario con el Problema

Si ves que FAPS muestra 33 pero debería estar en Faixa Moderada:

```bash
# Ver todos los usuarios con respuestas
php artisan tinker
```

En tinker:
```php
$users = \App\Models\User::whereHas('respostas')->get();
foreach($users as $u) {
    echo "ID: {$u->id} - {$u->name} - {$u->email}\n";
}
```

### 2. Verificar el Cálculo para ese Usuario

```bash
php artisan emotive:diagnosticar-faps {user_id} 1
```

### 3. Verificar los Rangos en la Base de Datos

```bash
php artisan tinker
```

En tinker:
```php
$faps = \App\Models\Variavel::where('tag', 'FaPs')->first();
echo "B: {$faps->B}, M: {$faps->M}, A: {$faps->A}\n";
// Debería mostrar: B: 20, M: 40, A: 41
```

### 4. Verificar la Clasificación

Con score 33:
- 33 > 20 (no es Baixa) ✅
- 33 ≤ 40 (es Moderada) ✅
- 33 no es > 40 (no es Alta) ✅

**Resultado esperado: Faixa Moderada**

### 5. Si el Problema Persiste

Verificar que el código esté actualizado:

```bash
# Verificar que los rangos se calculen correctamente
php artisan emotive:calcular-rangos-generales 1

# Actualizar todos los rangos
php artisan emotive:actualizar-todos-rangos

# Limpiar cache
php artisan cache:clear
php artisan config:clear
```

## 🐛 Problemas Comunes

### Problema 1: FAPS aparece en Faixa Alta en lugar de Moderada

**Causa**: Los rangos en la BD no están actualizados (A = 60 en lugar de 41)

**Solución**:
```bash
php artisan emotive:actualizar-todos-rangos
```

### Problema 2: FAPS no aparece en el resumen

**Causa**: Problema de comparación de tags (case sensitivity)

**Solución**: Ya corregido en `_resultado_emotive.blade.php`

### Problema 3: El score mostrado no coincide

**Causa**: Problema en el cálculo de respuestas o inversión

**Solución**: Ejecutar diagnóstico
```bash
php artisan emotive:diagnosticar-faps {user_id} 1
```

## ✅ Verificación Final

Después de aplicar cambios, verificar:

1. **Rangos correctos**:
   ```bash
   php artisan emotive:comparar-csv
   ```

2. **Cálculo correcto**:
   ```bash
   php artisan emotive:verificar-faps {user_id} 1
   ```

3. **Visualización correcta**:
   - Generar un relatorio de prueba
   - Verificar que FAPS con score 33 aparezca en "Faixa Moderada"
   - Verificar que el gráfico radar muestre el valor correcto

