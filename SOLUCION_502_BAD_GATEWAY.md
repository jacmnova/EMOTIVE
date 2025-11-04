# ⚠️ Solución: Error 502 Bad Gateway

## 🔍 El Problema

El error 502 Bad Gateway significa que Nginx está intentando comunicarse con PHP-FPM pero no puede. Esto generalmente se debe a:
- PHP-FPM no está corriendo
- El socket de PHP-FPM no existe o está en otra ubicación
- Permisos incorrectos en el socket o archivos

## ✅ Solución Paso a Paso

### Paso 1: Verificar que PHP-FPM está Corriendo

```bash
# Verificar estado de PHP-FPM
sudo systemctl status php-fpm

# Si no está corriendo, iniciarlo
sudo systemctl start php-fpm

# Habilitar para que inicie automáticamente
sudo systemctl enable php-fpm

# Verificar de nuevo
sudo systemctl status php-fpm
```

### Paso 2: Encontrar el Socket Correcto de PHP-FPM

```bash
# Buscar sockets de PHP-FPM
sudo find /var/run -name "*php*" -type s 2>/dev/null

# O buscar específicamente
ls -la /var/run/php-fpm/

# Ver configuración de PHP-FPM para encontrar el socket
sudo grep -r "listen" /etc/php-fpm.d/
sudo cat /etc/php-fpm.d/www.conf | grep listen
```

### Paso 3: Verificar el Socket en la Configuración de Nginx

El socket puede estar en diferentes ubicaciones según la versión:
- `/var/run/php-fpm/php-fpm.sock` (Amazon Linux común)
- `/var/run/php/php-fpm.sock`
- `/run/php-fpm/www.sock`
- `/tmp/php-fpm.sock`

**Buscar el socket real:**
```bash
# Ver todos los sockets disponibles
sudo find /var/run /run /tmp -name "*php*.sock" 2>/dev/null

# Ver qué proceso está usando
sudo lsof | grep php-fpm
```

### Paso 4: Actualizar Configuración de Nginx con el Socket Correcto

Una vez que encuentres el socket correcto:

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Actualiza la línea `fastcgi_pass` con el socket correcto:**

Si el socket está en `/var/run/php-fpm/php-fpm.sock`:
```nginx
fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
```

Si está en otra ubicación, por ejemplo `/run/php-fpm/www.sock`:
```nginx
fastcgi_pass unix:/run/php-fpm/www.sock;
```

### Paso 5: Verificar y Recargar

```bash
# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx

# Reiniciar PHP-FPM también
sudo systemctl restart php-fpm

# Probar de nuevo
curl http://localhost
```

## 🚀 Solución Automática (Busca el Socket y Actualiza)

```bash
# 1. Iniciar PHP-FPM
sudo systemctl start php-fpm
sudo systemctl enable php-fpm

# 2. Buscar el socket
PHP_SOCKET=$(sudo find /var/run /run /tmp -name "*php*.sock" -type s 2>/dev/null | head -1)

if [ -z "$PHP_SOCKET" ]; then
    echo "⚠️ No se encontró socket de PHP-FPM"
    echo "Verificando configuración..."
    sudo grep -r "listen" /etc/php-fpm.d/
else
    echo "✅ Socket encontrado: $PHP_SOCKET"
    
    # 3. Actualizar configuración de Nginx
    sudo sed -i "s|fastcgi_pass unix:.*|fastcgi_pass unix:$PHP_SOCKET;|" /etc/nginx/conf.d/laravel.conf
    
    # 4. Verificar y recargar
    sudo nginx -t && sudo systemctl reload nginx && sudo systemctl restart php-fpm
    
    echo "✅ Configuración actualizada"
fi

# 5. Verificar
curl http://localhost | head -20
```

## 🔧 Si PHP-FPM No Inicia

### Ver logs de PHP-FPM

```bash
sudo tail -f /var/log/php-fpm/error.log
# O
sudo journalctl -u php-fpm -n 50
```

### Verificar configuración de PHP-FPM

```bash
# Ver archivo de configuración principal
sudo cat /etc/php-fpm.d/www.conf | grep -E "(listen|user|group)"

# Verificar permisos del directorio de sockets
sudo ls -la /var/run/php-fpm/
```

### Crear Directorio de Socket si No Existe

```bash
# Crear directorio para socket
sudo mkdir -p /var/run/php-fpm
sudo chown root:root /var/run/php-fpm
sudo chmod 755 /var/run/php-fpm
```

## 📋 Verificación Completa

```bash
# 1. Verificar PHP-FPM está corriendo
sudo systemctl status php-fpm

# 2. Verificar socket existe
ls -la /var/run/php-fpm/php-fpm.sock
# O la ubicación que encontraste

# 3. Verificar Nginx puede acceder al socket
sudo -u nginx test -r /var/run/php-fpm/php-fpm.sock && echo "✅ Nginx puede leer socket" || echo "❌ Problema de permisos"

# 4. Verificar configuración de Nginx
sudo nginx -t

# 5. Ver logs de error
sudo tail -20 /var/log/nginx/error.log
```

## ⚠️ Problema de Permisos Común

Si el socket existe pero Nginx no puede acceder:

```bash
# Ver permisos del socket
ls -la /var/run/php-fpm/php-fpm.sock

# Ajustar permisos (el socket debe ser accesible por el grupo de nginx)
sudo chmod 660 /var/run/php-fpm/php-fpm.sock
sudo chgrp nginx /var/run/php-fpm/php-fpm.sock
```

## 🎯 Solución Rápida Completa

```bash
# Todo en uno
sudo systemctl start php-fpm && \
sudo systemctl enable php-fpm && \
PHP_SOCKET=$(sudo find /var/run /run -name "*php*.sock" 2>/dev/null | head -1) && \
[ ! -z "$PHP_SOCKET" ] && sudo sed -i "s|fastcgi_pass unix:.*|fastcgi_pass unix:$PHP_SOCKET;|" /etc/nginx/conf.d/laravel.conf && \
sudo nginx -t && \
sudo systemctl restart php-fpm && \
sudo systemctl reload nginx && \
echo "✅ Configurado con socket: $PHP_SOCKET" && \
curl http://localhost | head -30
```

## 🔍 Ver Logs de Error Detallados

```bash
# Logs de Nginx
sudo tail -f /var/log/nginx/error.log

# Logs de PHP-FPM
sudo tail -f /var/log/php-fpm/error.log

# Logs de Laravel
tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log
```

¡Ejecuta los comandos de arriba para solucionar el 502! 🚀

