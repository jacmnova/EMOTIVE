# 🔧 Solución Directa para Error 502 Bad Gateway

## 🚨 Diagnóstico Rápido

Ejecuta estos comandos y dime qué muestran:

```bash
# 1. Verificar PHP-FPM
sudo systemctl status php-fpm

# 2. Buscar socket
ls -la /var/run/php-fpm/php-fpm.sock 2>/dev/null || echo "Socket no encontrado en /var/run/php-fpm/"
sudo find /var/run /run -name "*php*.sock" 2>/dev/null

# 3. Ver configuración de PHP-FPM
sudo grep "listen" /etc/php-fpm.d/www.conf | grep -v "^;" | grep -v "^#"

# 4. Ver configuración de Nginx
sudo grep "fastcgi_pass" /etc/nginx/conf.d/laravel.conf

# 5. Ver logs de error
sudo tail -10 /var/log/nginx/error.log
```

## ✅ Solución Paso a Paso (Sigue en Orden)

### PASO 1: Iniciar PHP-FPM

```bash
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
sudo systemctl status php-fpm
```

**Debe mostrar "Active: active (running)"**

### PASO 2: Crear Socket Directory (Si No Existe)

```bash
sudo mkdir -p /var/run/php-fpm
sudo chmod 755 /var/run/php-fpm
```

### PASO 3: Verificar/Configurar Socket en PHP-FPM

```bash
# Ver configuración actual
sudo cat /etc/php-fpm.d/www.conf | grep "^listen" | head -1

# Si no existe el archivo, buscar en otros lugares
sudo find /etc -name "*php-fpm*" -type f 2>/dev/null
```

### PASO 4: Configurar Socket en PHP-FPM

```bash
sudo nano /etc/php-fpm.d/www.conf
```

**Busca la línea `listen =` y asegúrate de que sea:**
```
listen = /var/run/php-fpm/php-fpm.sock
```

**O si usa TCP:**
```
listen = 127.0.0.1:9000
```

**También verifica estas líneas (quita el `;` si están comentadas):**
```
listen.owner = nginx
listen.group = nginx
listen.mode = 0660
```

### PASO 5: Reiniciar PHP-FPM

```bash
sudo systemctl restart php-fpm
sudo systemctl status php-fpm
```

### PASO 6: Verificar que el Socket se Creó

```bash
ls -la /var/run/php-fpm/php-fpm.sock
```

**Debería mostrar algo como:**
```
srw-rw---- 1 root nginx ... /var/run/php-fpm/php-fpm.sock
```

### PASO 7: Actualizar Nginx con el Socket Correcto

Si el socket está en `/var/run/php-fpm/php-fpm.sock`:

```bash
sudo sed -i 's|fastcgi_pass.*|fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;|' /etc/nginx/conf.d/laravel.conf
```

Si el socket está en otra ubicación, edita manualmente:
```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

Y asegúrate de que la línea `fastcgi_pass` apunte al socket correcto.

### PASO 8: Verificar y Recargar

```bash
# Verificar configuración
sudo nginx -t

# Si está bien, recargar
sudo systemctl reload nginx
sudo systemctl restart php-fpm

# Probar
curl http://localhost
```

## 🔥 Solución Si PHP-FPM Usa TCP en Lugar de Socket

Si PHP-FPM está configurado para usar TCP (puerto 9000):

```bash
# Verificar si PHP-FPM escucha en puerto 9000
sudo netstat -tlnp | grep 9000
# O
sudo ss -tlnp | grep 9000

# Si escucha en 9000, cambiar Nginx a TCP
sudo sed -i 's|fastcgi_pass unix:.*|fastcgi_pass 127.0.0.1:9000;|' /etc/nginx/conf.d/laravel.conf
sudo nginx -t && sudo systemctl reload nginx
```

## 🚀 Script Completo de Solución

```bash
# 1. Iniciar PHP-FPM
sudo systemctl start php-fpm
sudo systemctl enable php-fpm

# 2. Crear directorio de socket
sudo mkdir -p /var/run/php-fpm
sudo chmod 755 /var/run/php-fpm

# 3. Configurar socket en PHP-FPM (si no está configurado)
sudo sed -i 's/^listen = .*/listen = \/var\/run\/php-fpm\/php-fpm.sock/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.owner/listen.owner/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.group/listen.group/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.mode/listen.mode/' /etc/php-fpm.d/www.conf

# 4. Reiniciar PHP-FPM
sudo systemctl restart php-fpm

# 5. Esperar un momento para que se cree el socket
sleep 2

# 6. Verificar socket
if [ -S /var/run/php-fpm/php-fpm.sock ]; then
    echo "✅ Socket creado correctamente"
    ls -la /var/run/php-fpm/php-fpm.sock
else
    echo "⚠️ Socket no encontrado, buscando alternativas..."
    sudo find /var/run /run -name "*php*.sock" 2>/dev/null
fi

# 7. Actualizar Nginx
sudo sed -i 's|fastcgi_pass.*|fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;|' /etc/nginx/conf.d/laravel.conf

# 8. Verificar y recargar
sudo nginx -t && sudo systemctl reload nginx

# 9. Probar
curl http://localhost | head -30
```

## 🔍 Si Aún No Funciona - Verificar Logs

```bash
# Logs de Nginx (muy importante)
sudo tail -30 /var/log/nginx/error.log

# Logs de PHP-FPM
sudo tail -30 /var/log/php-fpm/error.log 2>/dev/null || sudo journalctl -u php-fpm -n 30

# Estado de servicios
sudo systemctl status php-fpm
sudo systemctl status nginx
```

## ⚡ Solución Alternativa: Usar TCP en Lugar de Socket

Si el socket sigue sin funcionar, cambia a TCP:

```bash
# 1. Configurar PHP-FPM para usar TCP
sudo sed -i 's/^listen = .*/listen = 127.0.0.1:9000/' /etc/php-fpm.d/www.conf

# 2. Reiniciar PHP-FPM
sudo systemctl restart php-fpm

# 3. Verificar que escucha en 9000
sudo netstat -tlnp | grep 9000

# 4. Cambiar Nginx a TCP
sudo sed -i 's|fastcgi_pass unix:.*|fastcgi_pass 127.0.0.1:9000;|' /etc/nginx/conf.d/laravel.conf

# 5. Recargar
sudo nginx -t && sudo systemctl reload nginx

# 6. Probar
curl http://localhost
```

¡Ejecuta primero los comandos de diagnóstico y comparte los resultados! 🔍

