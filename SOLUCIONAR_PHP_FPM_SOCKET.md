# 🔧 Solucionar Error de PHP-FPM Socket

Error: `connect() to unix:/var/run/php-fpm/php-fpm.sock failed (2: No such file or directory)`

---

## 🔍 Diagnóstico

### Paso 1: Verificar si PHP-FPM está corriendo

```bash
sudo systemctl status php-fpm
```

O si es PHP 8.2 específico:

```bash
sudo systemctl status php8.2-fpm
```

### Paso 2: Encontrar la ubicación real del socket

```bash
# Buscar el socket de PHP-FPM
sudo find /var/run -name "*.sock" | grep php
sudo find /run -name "*.sock" | grep php

# O verificar en la configuración de PHP-FPM
sudo grep "listen" /etc/php-fpm.d/www.conf
# O
sudo grep "listen" /etc/php/8.2/fpm/pool.d/www.conf
```

---

## ✅ Solución

### Opción 1: Iniciar PHP-FPM (si no está corriendo)

```bash
# Para Amazon Linux
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
sudo systemctl status php-fpm

# O si es PHP 8.2 específico
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

### Opción 2: Verificar y corregir la ubicación del socket

Una vez que encuentres la ubicación real del socket, actualiza la configuración de Nginx:

```bash
# Editar configuración de Nginx
sudo nano /etc/nginx/conf.d/laravel.conf
```

Busca la línea:
```nginx
fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
```

Y cámbiala por la ubicación correcta. Posibles ubicaciones:

```nginx
# Amazon Linux 2023
fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;

# O puede ser:
fastcgi_pass unix:/run/php-fpm/php-fpm.sock;
fastcgi_pass unix:/run/php/php8.2-fpm.sock;
fastcgi_pass unix:/var/run/php8.2-fpm.sock;
```

### Opción 3: Verificar configuración de PHP-FPM

```bash
# Ver configuración de PHP-FPM
sudo cat /etc/php-fpm.d/www.conf | grep listen

# O para PHP 8.2
sudo cat /etc/php/8.2/fpm/pool.d/www.conf | grep listen
```

Si el socket está configurado en una ubicación diferente, actualiza Nginx para que coincida.

---

## 🔄 Pasos Completos de Solución

```bash
# 1. Verificar estado de PHP-FPM
sudo systemctl status php-fpm

# 2. Si no está corriendo, iniciarlo
sudo systemctl start php-fpm
sudo systemctl enable php-fpm

# 3. Encontrar el socket
sudo find /var/run /run -name "*php*.sock" 2>/dev/null

# 4. Verificar configuración de PHP-FPM
sudo grep "listen" /etc/php-fpm.d/www.conf

# 5. Actualizar Nginx si es necesario
sudo nano /etc/nginx/conf.d/laravel.conf
# Cambiar la línea fastcgi_pass a la ubicación correcta

# 6. Verificar configuración de Nginx
sudo nginx -t

# 7. Recargar Nginx
sudo systemctl reload nginx

# 8. Verificar que funciona
curl http://localhost
```

---

## 🧪 Verificar que Funciona

```bash
# Verificar que PHP-FPM está corriendo
sudo systemctl status php-fpm

# Verificar que el socket existe
ls -la /var/run/php-fpm/php-fpm.sock
# O la ubicación que encontraste

# Probar la aplicación
curl http://localhost
curl https://emotive.g3nia.com
```

---

## ⚠️ Si PHP-FPM No Existe

Si PHP-FPM no está instalado:

```bash
# Para Amazon Linux 2023
sudo dnf install -y php-fpm

# Para Ubuntu
sudo apt install -y php8.2-fpm

# Iniciar y habilitar
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
```

---

¡Después de corregir esto, el error debería desaparecer! 🚀

