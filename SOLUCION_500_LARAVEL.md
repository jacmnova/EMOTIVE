# ⚠️ Solución: Error 500 Server Error en Laravel

## ✅ Buenas Noticias

El error cambió de **502 Bad Gateway** a **500 Server Error**, lo que significa:
- ✅ Nginx está funcionando correctamente
- ✅ PHP-FPM está funcionando y comunicándose con Nginx
- ✅ Laravel está recibiendo las peticiones
- ❌ Pero hay un error en la aplicación Laravel

## 🔍 Paso 1: Ver el Error Específico

```bash
cd /var/www/laravel/EMOTIVE

# Ver el último error en los logs
tail -50 storage/logs/laravel.log

# O ver en tiempo real
tail -f storage/logs/laravel.log
```

## 🔧 Errores Comunes y Soluciones

### Error 1: APP_KEY no configurado

**Síntoma**: Error sobre "No application encryption key"

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE
php artisan key:generate
```

### Error 2: Permisos de Storage

**Síntoma**: Error sobre "Permission denied" o archivos no encontrados

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE
sudo chown -R ec2-user:ec2-user storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Error 3: Base de Datos no Conectada

**Síntoma**: Error sobre conexión a base de datos

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE

# Verificar .env
cat .env | grep DB_

# Probar conexión
php artisan db:show

# Si falla, verificar credenciales en .env
sudo nano .env
```

### Error 4: Cache de Configuración Desactualizada

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE

# Limpiar todos los caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Error 5: Migraciones Pendientes

**Síntoma**: Error sobre tablas no encontradas

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE
php artisan migrate:status
php artisan migrate --force
```

### Error 6: Storage Link No Creado

**Solución:**
```bash
cd /var/www/laravel/EMOTIVE
php artisan storage:link
```

## 🚀 Solución Completa (Todo en uno)

```bash
cd /var/www/laravel/EMOTIVE && \
echo "🔧 Configurando permisos..." && \
sudo chown -R ec2-user:ec2-user storage bootstrap/cache && \
sudo chmod -R 775 storage bootstrap/cache && \
echo "🔑 Verificando APP_KEY..." && \
php artisan key:generate --force 2>/dev/null || echo "APP_KEY ya existe" && \
echo "🗄️ Verificando base de datos..." && \
php artisan db:show 2>/dev/null || echo "Verificar credenciales DB en .env" && \
echo "🧹 Limpiando caches..." && \
php artisan cache:clear && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
echo "⚡ Regenerando caches..." && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
echo "🔗 Creando storage link..." && \
php artisan storage:link && \
echo "✅ Verificando logs..." && \
tail -30 storage/logs/laravel.log | tail -10
```

## 📋 Checklist de Verificación

```bash
cd /var/www/laravel/EMOTIVE

# 1. Verificar APP_KEY
grep APP_KEY .env

# 2. Verificar permisos
ls -la storage/bootstrap/cache

# 3. Verificar conexión BD
php artisan db:show

# 4. Verificar migraciones
php artisan migrate:status

# 5. Ver logs
tail -50 storage/logs/laravel.log
```

## 🔍 Ver Error Detallado (Modo Debug)

Si quieres ver el error completo en el navegador (solo temporalmente):

```bash
cd /var/www/laravel/EMOTIVE

# Habilitar debug temporalmente
sudo sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env

# Limpiar cache de configuración
php artisan config:clear

# Recargar página en navegador
# Recuerda deshabilitar después: sudo sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
```

## ✅ Verificación Final

Después de aplicar las soluciones:

```bash
# Probar de nuevo
curl http://localhost

# Deberías ver HTML de Laravel o la página de inicio, no el error 500
```

¡Revisa los logs primero para ver el error específico! 🔍

