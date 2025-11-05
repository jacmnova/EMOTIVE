# 🔄 Cómo Visualizar Cambios Después de Push

## 📋 Opciones para Actualizar el Código en el Servidor

### Opción 1: GitHub Actions (Despliegue Automático) ⚡

Si tienes GitHub Actions configurado, los cambios se despliegan automáticamente después del push.

**Pasos:**
1. **Hacer push del cambio:**
```bash
git add .
git commit -m "Descripción del cambio"
git push origin main
```

2. **Verificar el despliegue en GitHub:**
   - Ve a tu repositorio en GitHub
   - Pestaña "Actions"
   - Verifica que el workflow se ejecute correctamente

3. **Esperar 2-5 minutos** para que se complete el despliegue

4. **Verificar en el servidor:**
```bash
# SSH al servidor
ssh ec2-user@tu-ip-ec2

# Verificar que el código se actualizó
cd /var/www/laravel/EMOTIVE
git log -1  # Ver el último commit
```

### Opción 2: Actualización Manual (Más Rápido) 🚀

Si quieres actualizar inmediatamente sin esperar GitHub Actions:

```bash
# SSH al servidor
ssh ec2-user@tu-ip-ec2

# Ir al directorio de la aplicación
cd /var/www/laravel/EMOTIVE

# Actualizar código desde Git
git pull origin main

# Limpiar caches de Laravel
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Regenerar caches (opcional, pero recomendado)
php artisan optimize
php artisan route:cache
php artisan config:cache

# Si cambiaste archivos JavaScript/CSS, recompilar
npm run build

# Reiniciar PHP-FPM para aplicar cambios
sudo systemctl restart php-fpm

# Recargar Nginx
sudo systemctl reload nginx
```

## ✅ Verificación Rápida (Todo en Uno)

```bash
cd /var/www/laravel/EMOTIVE && \
git pull origin main && \
php artisan route:clear && \
php artisan config:clear && \
php artisan view:clear && \
php artisan cache:clear && \
php artisan optimize && \
php artisan route:cache && \
php artisan config:cache && \
sudo systemctl restart php-fpm && \
sudo systemctl reload nginx && \
echo "✅ Código actualizado y caches limpiados"
```

## 🔍 Verificar que los Cambios se Aplicaron

### 1. Verificar el Código
```bash
cd /var/www/laravel/EMOTIVE

# Ver el último commit
git log -1

# Ver si el archivo tiene tus cambios
grep -n "tu_funcion_nueva" app/Http/Controllers/DadosController.php
```

### 2. Verificar en la Aplicación
- Abre la aplicación en el navegador
- Recarga la página (Ctrl+F5 o Cmd+Shift+R para forzar recarga)
- Prueba la funcionalidad que cambiaste

### 3. Verificar Logs
```bash
# Ver si hay errores
tail -50 storage/logs/laravel.log

# Ver en tiempo real mientras pruebas
tail -f storage/logs/laravel.log
```

## 🎯 Pasos Completos para Ver un Cambio

### Paso 1: Push del Cambio
```bash
# Desde tu máquina local
git add app/Http/Controllers/DadosController.php
git commit -m "Fix: Agregar método finalizar y mejoras"
git push origin main
```

### Paso 2: Actualizar en el Servidor
```bash
# SSH al servidor
ssh ec2-user@tu-ip-ec2

# Actualizar (usa el script de arriba)
cd /var/www/laravel/EMOTIVE
git pull origin main
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize
sudo systemctl restart php-fpm
```

### Paso 3: Probar en el Navegador
1. Abre `http://tu-ip-ec2`
2. Recarga la página (Ctrl+F5)
3. Prueba la funcionalidad

## ⚠️ Si No Ves los Cambios

### 1. Verificar que el Código se Actualizó
```bash
cd /var/www/laravel/EMOTIVE
git status
git log -1
```

### 2. Limpiar Caches Más Agresivamente
```bash
cd /var/www/laravel/EMOTIVE

# Limpiar todos los caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar cache de OPcache (si está habilitado)
php artisan opcache:clear  # Si tienes el paquete instalado

# O reiniciar PHP-FPM completamente
sudo systemctl restart php-fpm
```

### 3. Verificar Permisos
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel/EMOTIVE
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Verificar Errores
```bash
# Ver errores de Laravel
tail -100 storage/logs/laravel.log | grep -i error

# Ver errores de Nginx
sudo tail -50 /var/log/nginx/error.log

# Ver errores de PHP-FPM
sudo tail -50 /var/log/php-fpm/error.log
```

## 🔄 Si Cambiaste JavaScript/CSS

Si cambiaste archivos `.js`, `.css`, `.vue`, etc.:

```bash
cd /var/www/laravel/EMOTIVE

# Actualizar código
git pull origin main

# Recompilar assets
npm install  # Si agregaste dependencias
npm run build

# Limpiar caches
php artisan view:clear
php artisan cache:clear
```

## 📱 Script Automático de Actualización

Puedes crear un script en el servidor:

```bash
# Crear script
cat > ~/actualizar-app.sh <<'EOF'
#!/bin/bash
cd /var/www/laravel/EMOTIVE
echo "🔄 Actualizando código..."
git pull origin main
echo "🧹 Limpiando caches..."
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
echo "⚡ Optimizando..."
php artisan optimize
php artisan route:cache
php artisan config:cache
echo "🔄 Reiniciando servicios..."
sudo systemctl restart php-fpm
sudo systemctl reload nginx
echo "✅ Actualización completada!"
EOF

chmod +x ~/actualizar-app.sh

# Usar el script
~/actualizar-app.sh
```

## 🎯 Resumen Rápido

**Después de hacer push:**
1. SSH al servidor: `ssh ec2-user@tu-ip`
2. Ir al directorio: `cd /var/www/laravel/EMOTIVE`
3. Actualizar: `git pull origin main`
4. Limpiar caches: `php artisan route:clear && php artisan config:clear && php artisan cache:clear`
5. Optimizar: `php artisan optimize`
6. Reiniciar: `sudo systemctl restart php-fpm && sudo systemctl reload nginx`
7. Recargar página en navegador (Ctrl+F5)

¡Listo! Los cambios deberían estar visibles. 🚀

