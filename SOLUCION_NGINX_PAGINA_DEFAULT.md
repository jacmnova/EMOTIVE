# ⚠️ Solución: Nginx muestra página por defecto en lugar de Laravel

## 🔍 El Problema

Nginx está mostrando la página de bienvenida por defecto en lugar de tu aplicación Laravel. Esto significa que la configuración de Nginx no está apuntando correctamente a tu aplicación.

## ✅ Solución: Configurar Nginx Correctamente

### Paso 1: Verificar Configuración Actual

```bash
# Ver configuración actual
sudo cat /etc/nginx/conf.d/laravel.conf

# Ver si existe el archivo
ls -la /etc/nginx/conf.d/
```

### Paso 2: Crear/Editar Configuración de Laravel

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Reemplaza TODO el contenido con esto** (ajusta la IP con tu IP pública):

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    
    root /var/www/laravel/EMOTIVE/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**⚠️ IMPORTANTE**: 
- `default_server` hace que esta sea la configuración por defecto
- `root /var/www/laravel/EMOTIVE/public;` apunta a tu aplicación Laravel
- El socket PHP-FPM es para Amazon Linux: `unix:/var/run/php-fpm/php-fpm.sock`

### Paso 3: Verificar el Socket de PHP-FPM

```bash
# Verificar que el socket existe
ls -la /var/run/php-fpm/php-fpm.sock

# Si no existe, buscar dónde está
sudo systemctl status php-fpm | grep -i socket
# O buscar en:
ls -la /var/run/php-fpm/
ls -la /var/run/ | grep php
```

Si el socket está en otra ubicación, ajusta la línea `fastcgi_pass` en la configuración.

### Paso 4: Remover Configuración por Defecto (Si Existe)

```bash
# Ver si hay configuración por defecto
ls -la /etc/nginx/conf.d/default.conf

# Si existe, removerla o renombrarla
sudo mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf.backup

# O verificar si hay en sites-enabled (Ubuntu)
sudo rm /etc/nginx/sites-enabled/default 2>/dev/null || true
```

### Paso 5: Verificar Configuración de Nginx

```bash
# Verificar que la configuración es válida
sudo nginx -t
```

**Debería mostrar:**
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### Paso 6: Recargar Nginx

```bash
# Recargar configuración
sudo systemctl reload nginx

# O reiniciar si reload no funciona
sudo systemctl restart nginx

# Verificar estado
sudo systemctl status nginx
```

### Paso 7: Verificar que Funciona

```bash
# Probar localmente
curl http://localhost

# Debería mostrar HTML de Laravel, no la página de Nginx
# Si ves "Laravel" o contenido de tu app, ¡funcionó!
```

### Paso 8: Verificar Permisos del Directorio

```bash
# Asegurar permisos correctos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 755 /var/www/laravel/EMOTIVE/public
```

## 🚀 Solución Rápida (Todo en uno)

```bash
# 1. Crear configuración de Laravel
sudo tee /etc/nginx/conf.d/laravel.conf > /dev/null <<'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    root /var/www/laravel/EMOTIVE/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# 2. Remover configuración por defecto
sudo mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf.backup 2>/dev/null || true

# 3. Verificar configuración
sudo nginx -t

# 4. Recargar Nginx
sudo systemctl reload nginx

# 5. Verificar
curl http://localhost | head -20
```

## 🔍 Si Aún No Funciona

### Verificar que PHP-FPM está corriendo

```bash
sudo systemctl status php-fpm
sudo systemctl start php-fpm  # Si no está corriendo
```

### Verificar el socket de PHP-FPM

```bash
# Buscar socket
sudo find /var/run -name "*.sock" | grep php

# O ver en la configuración de PHP-FPM
sudo grep -r "listen" /etc/php-fpm.d/
```

Si el socket está en otra ubicación, actualiza la configuración de Nginx con la ruta correcta.

### Ver logs de error

```bash
# Logs de Nginx
sudo tail -f /var/log/nginx/error.log

# Logs de Laravel
tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log
```

### Verificar que index.php existe

```bash
ls -la /var/www/laravel/EMOTIVE/public/index.php
```

Si no existe, puede ser que el repositorio no se clonó correctamente o falta algo.

## ✅ Verificación Final

Después de aplicar los cambios:

```bash
# Deberías ver contenido de Laravel, no "Welcome to nginx"
curl http://localhost

# O abrir en navegador
# http://TU_IP_EC2
```

¡Listo! 🚀

