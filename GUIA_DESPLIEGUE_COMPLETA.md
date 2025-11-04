# 🚀 Guía Completa de Despliegue - Paso a Paso

Esta guía te llevará desde tener todo instalado hasta tener tu aplicación Laravel funcionando en EC2.

## ✅ Estado Actual (Lo que ya tienes)

- ✅ PHP, Composer, Node.js, NPM instalados
- ✅ Nginx instalado y corriendo
- ✅ MySQL instalado y corriendo
- ✅ PHP-FPM configurado
- ✅ Directorio `/var/www/laravel` creado

## 📋 Pasos para Desplegar

### Paso 1: Configurar Nginx para Laravel

```bash
# Crear archivo de configuración
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Pega esta configuración** (ajusta `tu-dominio.com` con tu IP o dominio):

```nginx
server {
    listen 80;
    server_name tu-ip-ec2 tu-dominio.com www.tu-dominio.com;
    root /var/www/laravel/public;

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

**Verificar y recargar Nginx:**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Paso 2: Configurar Permisos

```bash
# Dar permisos al directorio de Laravel
sudo chown -R ec2-user:ec2-user /var/www/laravel
# O si prefieres usar nginx como propietario:
# sudo chown -R nginx:nginx /var/www/laravel

# Dar permisos a storage y cache
sudo chmod -R 775 /var/www/laravel/storage
sudo chmod -R 775 /var/www/laravel/bootstrap/cache
```

### Paso 3: Configurar GitHub Secrets

Necesitas configurar estos secrets en GitHub para el despliegue automático:

1. **Ve a tu repositorio en GitHub**
2. **Settings → Secrets and variables → Actions**
3. **New repository secret**

Agrega estos 4 secrets:

#### Secret 1: SSH_KEY
```bash
# En tu servidor EC2, ejecuta:
cat ~/.ssh/github_deploy
```
Copia TODO el contenido (desde `-----BEGIN OPENSSH PRIVATE KEY-----` hasta `-----END OPENSSH PRIVATE KEY-----`)
- **Name**: `SSH_KEY`
- **Value**: El contenido completo de la clave privada

#### Secret 2: SSH_HOST
- **Name**: `SSH_HOST`
- **Value**: Tu IP pública de EC2 (ej: `54.123.45.67`) o DNS (ej: `ec2-xx-xx-xx-xx.compute-1.amazonaws.com`)

#### Secret 3: SSH_USER
- **Name**: `SSH_USER`
- **Value**: `ec2-user` (para Amazon Linux)

#### Secret 4: REMOTE_PATH
- **Name**: `REMOTE_PATH`
- **Value**: `/var/www/laravel`

### Paso 4: Configurar Base de Datos

```bash
# Si aún no configuraste MySQL
sudo mysql_secure_installation

# Crear base de datos
sudo mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'tu_password_seguro_aqui';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 5: Primer Despliegue Automático

**Opción A: Despliegue Automático (Recomendado)**

1. **Haz push a la rama main:**
```bash
# Desde tu máquina local
git add .
git commit -m "Preparar para despliegue en EC2"
git push origin main
```

2. **El workflow de GitHub Actions se ejecutará automáticamente**
   - Ve a: `https://github.com/tu-usuario/tu-repo/actions`
   - Verás el workflow "Deploy to AWS EC2" ejecutándose
   - Espera a que termine (5-10 minutos)

**Opción B: Despliegue Manual**

Si prefieres desplegar manualmente:

```bash
# Conéctate al servidor
ssh -i tu-llave.pem ec2-user@tu-ip-ec2

# Ir al directorio
cd /var/www/laravel

# Clonar tu repositorio
sudo git clone https://github.com/tu-usuario/tu-repo.git .

# Dar permisos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Copiar archivo .env
sudo cp .env.example .env
sudo nano .env

# Generar APP_KEY
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force
php artisan db:seed --force

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Dar permisos finales
sudo chown -R nginx:nginx /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache
```

### Paso 6: Crear Archivo .env

**Si usaste despliegue automático**, crea el `.env` después del primer deploy:

```bash
cd /var/www/laravel
sudo nano .env
```

**Configuración mínima del .env:**

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://tu-ip-ec2

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=tu_password_seguro_aqui

CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

OPENAI_API_KEY=tu_api_key_si_la_necesitas
```

**Generar APP_KEY:**
```bash
php artisan key:generate
```

### Paso 7: Configurar Permisos Finales

```bash
cd /var/www/laravel

# Cambiar propietario a nginx (recomendado para producción)
sudo chown -R nginx:nginx /var/www/laravel

# Dar permisos específicos
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Crear enlace simbólico de storage (si no existe)
php artisan storage:link
```

### Paso 8: Ejecutar Migraciones

```bash
cd /var/www/laravel

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (si tienes)
php artisan db:seed --force

# Optimizar aplicación
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 9: Verificar que Todo Funciona

```bash
# Verificar servicios
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mysqld

# Verificar que Laravel responde
curl http://localhost

# Ver logs si hay problemas
tail -f /var/www/laravel/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Paso 10: Acceder a la Aplicación

Abre tu navegador y ve a:
- `http://tu-ip-ec2` (tu IP pública de EC2)
- O `http://tu-dominio.com` (si configuraste un dominio)

## 🔄 Despliegues Futuros

Una vez configurado, los despliegues futuros son automáticos:

```bash
# Desde tu máquina local
git add .
git commit -m "Cambios en la aplicación"
git push origin main
```

GitHub Actions desplegará automáticamente los cambios.

## ⚠️ Solución de Problemas

### Error 502 Bad Gateway
```bash
# Verificar que PHP-FPM está corriendo
sudo systemctl status php-fpm

# Verificar el socket
ls -la /var/run/php-fpm/php-fpm.sock

# Reiniciar servicios
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### Error de Permisos
```bash
sudo chown -R nginx:nginx /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache
```

### Ver Logs
```bash
# Logs de Laravel
tail -f /var/www/laravel/storage/logs/laravel.log

# Logs de Nginx
tail -f /var/log/nginx/error.log

# Logs de PHP-FPM
tail -f /var/log/php-fpm/error.log
```

## 📋 Checklist Final

- [ ] Nginx configurado para Laravel
- [ ] Permisos de `/var/www/laravel` configurados
- [ ] GitHub Secrets configurados (SSH_KEY, SSH_HOST, SSH_USER, REMOTE_PATH)
- [ ] Base de datos MySQL creada
- [ ] Archivo `.env` creado y configurado
- [ ] APP_KEY generado
- [ ] Migraciones ejecutadas
- [ ] Aplicación accesible vía navegador
- [ ] Servicios corriendo (nginx, php-fpm, mysqld)

¡Listo! 🎉

