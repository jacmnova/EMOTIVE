# 📋 Resumen de Despliegue - Actualización CSV ALE

## 🎯 Cambios Principales

1. **Nuevo comando**: `actualizar:relaciones-ale` - Agrupa preguntas según CSV ALE
2. **Cálculo de porcentajes**: Gráfico radar ahora muestra 0-100% en lugar de valores absolutos
3. **Corrección de error**: Variable no definida en `DadosController.php`

## 🚀 Pasos Rápidos para el Servidor

### Opción 1: Script Automático

```bash
# En el servidor, después de git pull
chmod +x COMANDOS_DESPLIEGUE_SERVIDOR.sh
./COMANDOS_DESPLIEGUE_SERVIDOR.sh
```

### Opción 2: Manual

```bash
# 1. Actualizar código
git pull origin main

# 2. Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. ACTUALIZAR RELACIONES (IMPORTANTE)
php artisan actualizar:relaciones-ale

# 4. Verificar
php artisan tinker
>>> $v = \App\Models\Variavel::where('tag', 'ExEm')->first();
>>> $v->perguntas->count();
# Debería mostrar: 26
```

## 📊 Resultados Esperados

Después de ejecutar `actualizar:relaciones-ale`:
- **EXEM**: 26 preguntas ✅
- **REPR**: 26 preguntas ✅
- **DECI**: 29 preguntas ✅
- **FAPS**: 10 preguntas ✅
- **EXTR**: 16 preguntas (puede mostrar 15 si falta 1)
- **ASMO**: 15 preguntas ✅

## ⚠️ Notas Importantes

1. **CSV necesario**: El comando busca el CSV en la raíz del proyecto o en Downloads
2. **Backup recomendado**: Hacer backup de `pergunta_variavel` antes de ejecutar
3. **Rangos actualizados**: Los rangos B, M, A se actualizan automáticamente

## ✅ Verificación

Después del despliegue, verificar:
1. Generar un relatório y verificar que el radar muestre valores 0-100%
2. Verificar que las dimensiones tengan el número correcto de preguntas
3. Probar con usuario que tenga respuestas

