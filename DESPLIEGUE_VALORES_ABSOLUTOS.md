# 🚀 Despliegue: Valores Absolutos en Gráfico Radar

## ✅ Cambios Realizados

1. **Eliminado cálculo de porcentajes**: Ahora se usan valores absolutos directamente
2. **Máximo dinámico del gráfico**: 
   - Si el máximo posible de alguna dimensión > 100 → máximo del gráfico = 200
   - Si todos los máximos ≤ 100 → máximo del gráfico = 100
3. **Tooltip actualizado**: Muestra "X pontos" en lugar de "X%"

## 📊 Lógica del Máximo del Gráfico

```php
$maximoPosible = $totalPreguntas * 6;
$maximoGrafico = $maximoPosible > 100 ? 200 : 100;
```

**Ejemplos:**
- EXEM: 26 preguntas × 6 = 156 → máximo gráfico = 200
- DECI: 29 preguntas × 6 = 174 → máximo gráfico = 200
- FAPS: 10 preguntas × 6 = 60 → máximo gráfico = 100
- ASMO: 15 preguntas × 6 = 90 → máximo gráfico = 100

## 🚀 Pasos para Desplegar

```bash
# 1. En el servidor, después de git pull
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 2. Actualizar relaciones (si aún no se hizo)
php artisan actualizar:relaciones-ale

# 3. Verificar
# Generar un relatório y verificar que los valores sean absolutos (no porcentajes)
```

## 📝 Archivos Modificados

- `app/Http/Controllers/DadosController.php` - Eliminado cálculo de porcentaje
- `app/Http/Controllers/RelatorioController.php` - Eliminado cálculo de porcentaje
- `resources/views/participante/emotive/partials/_scripts.blade.php` - Usa valores absolutos y máximo dinámico

