# Guía para Actualizar Rangos en Producción

## 📋 Resumen de Cambios

Se ha implementado un sistema automático para calcular los rangos B, M, A de forma general para todas las encuestas, basado en:
- Número de preguntas por variable
- Score máximo del formulario (score_fim)
- Fórmula: B = 33.3%, M = 66.7%, A = M + 1

## 🚀 Pasos para Aplicar en Producción

### 1. Conectarse al Servidor

```bash
ssh usuario@tu-servidor
cd /ruta/al/proyecto
```

### 2. Hacer Backup de la Base de Datos (IMPORTANTE)

```bash
# Backup de la tabla variaveis
php artisan tinker
```

En tinker:
```php
DB::table('variaveis')->get()->toJson();
// Copiar el resultado y guardarlo como backup
exit
```

O usando mysqldump:
```bash
mysqldump -u usuario -p nombre_base_datos variaveis > backup_variaveis_$(date +%Y%m%d_%H%M%S).sql
```

### 3. Actualizar el Código

```bash
# Asegurarse de estar en la rama correcta
git pull origin main

# O si necesitas hacer merge de cambios locales
git status
git add .
git commit -m "Actualizar sistema de cálculo de rangos B, M, A"
git push origin main
```

### 4. Instalar/Actualizar Dependencias (si hay cambios)

```bash
composer install --no-dev --optimize-autoloader
```

### 5. Ejecutar Migraciones (si hay nuevas)

```bash
php artisan migrate --force
```

### 6. Actualizar los Rangos

```bash
# Opción 1: Actualizar todos los rangos automáticamente (RECOMENDADO)
php artisan emotive:actualizar-todos-rangos

# Opción 2: Actualizar solo el formulario 1
php artisan emotive:calcular-rangos-generales 1

# Opción 3: Si necesitas valores específicos del CSV
php artisan emotive:actualizar-rangos
```

### 7. Verificar los Cambios

```bash
# Verificar que los valores coinciden
php artisan emotive:comparar-csv

# Verificar cálculo de FAPS
php artisan emotive:verificar-faps-csv
```

### 8. Limpiar Cache (si es necesario)

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## ⚠️ Consideraciones Importantes

1. **Backup**: Siempre hacer backup antes de aplicar cambios en producción
2. **Horario**: Aplicar en horario de bajo tráfico si es posible
3. **Monitoreo**: Verificar que los relatorios se generen correctamente después del cambio
4. **Rollback**: Si algo sale mal, restaurar el backup

## 🔄 Rollback (si es necesario)

Si necesitas revertir los cambios:

```bash
# Restaurar desde backup
mysql -u usuario -p nombre_base_datos < backup_variaveis_YYYYMMDD_HHMMSS.sql

# O ejecutar el seeder original
php artisan db:seed --class=VarBurnOutSeeder
```

## 📝 Verificación Post-Implementación

1. Generar un relatorio de prueba
2. Verificar que FAPS con score 36 esté en Faixa Moderada
3. Verificar que todos los rangos se calculen correctamente
4. Revisar logs por errores

## 🆘 Solución de Problemas

Si hay errores:

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Verificar conexión a BD
php artisan tinker
DB::connection()->getPdo();
```

