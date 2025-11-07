# 🚀 Instrucciones para Finalizar el Despliegue

Guía paso a paso para completar el despliegue en el servidor EC2.

---

## 📋 Opción 1: Script Automático (Recomendado)

### Paso 1: Subir el script al servidor

Desde tu máquina local:

```bash
# Copiar el script al servidor
scp -i tu-key.pem finalizar-despliegue.sh ec2-user@emotive.g3nia.com:/home/ec2-user/
```

O crea el script directamente en el servidor:

```bash
# En el servidor EC2
nano finalizar-despliegue.sh
# Pega el contenido del archivo finalizar-despliegue.sh
```

### Paso 2: Ejecutar el script

```bash
# En el servidor EC2
chmod +x finalizar-despliegue.sh
sudo ./finalizar-despliegue.sh
```

El script verificará y corregirá automáticamente:
- ✅ Software instalado
- ✅ Servicios corriendo
- ✅ Socket de PHP-FPM
- ✅ Configuración de Nginx
- ✅ Dependencias instaladas
- ✅ Archivo .env
- ✅ Permisos
- ✅ Migraciones y seeders
- ✅ Optimizaciones

---

## 📋 Opción 2: Pasos Manuales

Si prefieres hacerlo manualmente, sigue estos pasos:

### 1. Verificar e iniciar PHP-FPM

```bash
# Verificar estado
sudo systemctl status php-fpm

# Si no está corriendo
sudo systemctl start php-fpm
sudo systemctl enable php-fpm

# Encontrar el socket
sudo find /var/run /run -name "*php*.sock" 2>/dev/null
```

### 2. Verificar y corregir Nginx

```bash
# Editar configuración
sudo nano /etc/nginx/conf.d/laravel.conf
```

Asegúrate de que la línea `fastcgi_pass` apunte al socket correcto:
```nginx
fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
```

```bash
# Verificar y recargar
sudo nginx -t
sudo systemctl reload nginx
```

### 3. Instalar dependencias

```bash
cd /var/www/laravel

# Dependencias PHP
composer install --no-dev --optimize-autoloader

# Dependencias Node.js (si aplica)
npm ci
npm run build
```

### 4. Configurar .env

```bash
cd /var/www/laravel

# Si no existe .env, crearlo
if [ ! -f .env ]; then
    cp .env.example .env
    nano .env
fi

# Generar APP_KEY si no existe
php artisan key:generate
```

**Pega el contenido de `ENV_PRODUCCION.txt` en el archivo .env**

### 5. Configurar permisos

```bash
cd /var/www/laravel
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### 6. Ejecutar migraciones y seeders

```bash
cd /var/www/laravel
php artisan migrate --force
php artisan db:seed --force
```

### 7. Optimizar

```bash
cd /var/www/laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Reiniciar servicios

```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### 9. Verificar

```bash
# Verificar servicios
sudo systemctl status php-fpm
sudo systemctl status nginx

# Probar aplicación
curl http://localhost
curl https://emotive.g3nia.com
```

---

## 🔍 Verificación Final

### Verificar que todo funciona:

1. **Servicios corriendo:**
   ```bash
   sudo systemctl status php-fpm
   sudo systemctl status nginx
   sudo systemctl status mysqld
   ```

2. **Aplicación responde:**
   ```bash
   curl http://localhost
   curl https://emotive.g3nia.com
   ```

3. **En el navegador:**
   - Abre: https://emotive.g3nia.com
   - Verifica que carga correctamente
   - Prueba el login

4. **Ver logs si hay problemas:**
   ```bash
   tail -f /var/www/laravel/storage/logs/laravel.log
   tail -f /var/log/nginx/error.log
   ```

---

## ⚠️ Problemas Comunes

### Error 502 Bad Gateway
```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### Error de permisos
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
```

### Error de base de datos
```bash
# Verificar conexión
php artisan migrate:status

# Verificar credenciales en .env
cat .env | grep DB_
```

---

## ✅ Checklist Final

- [ ] PHP-FPM está corriendo
- [ ] Nginx está corriendo
- [ ] MySQL/MariaDB está corriendo
- [ ] Socket de PHP-FPM existe y es accesible
- [ ] Configuración de Nginx es correcta
- [ ] Dependencias instaladas (vendor, node_modules)
- [ ] Archivo .env configurado con credenciales
- [ ] APP_KEY generado
- [ ] Permisos correctos en storage y bootstrap/cache
- [ ] Migraciones ejecutadas
- [ ] Seeders ejecutados
- [ ] Aplicación optimizada
- [ ] Sitio accesible en https://emotive.g3nia.com

---

## 🎉 ¡Listo!

Una vez completados todos los pasos, tu aplicación debería estar funcionando correctamente.

Si necesitas ayuda con algún paso específico, comparte el error o el output que ves.

