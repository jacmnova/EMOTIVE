# 📋 Comandos Rápidos para Despliegue en EC2

Este documento contiene todos los comandos necesarios para desplegar la aplicación en EC2. Cópialos y pégalos según vayas necesitando.

## 🔐 1. Conectarse al Servidor EC2

**Para Amazon Linux:**
```bash
# Ajustar permisos del archivo .pem
chmod 400 ruta/a/tu-llave.pem

# Conectarse (usa ec2-user para Amazon Linux)
ssh -i ruta/a/tu-llave.pem ec2-user@tu-ip-ec2
```

**Para Ubuntu:**
```bash
# Ajustar permisos del archivo .pem
chmod 400 ruta/a/tu-llave.pem

# Conectarse (usa ubuntu para Ubuntu)
ssh -i ruta/a/tu-llave.pem ubuntu@tu-ip-ec2
```

**Nota**: EC2 no tiene contraseña de root. Usa `sudo` para comandos administrativos.

---

## 🚀 2. Instalación Rápida (Script Automatizado)

**Para Amazon Linux:**
```bash
# Subir el script install-ec2-amazon-linux.sh al servidor
chmod +x install-ec2-amazon-linux.sh
./install-ec2-amazon-linux.sh
```

**Para Ubuntu:**
```bash
# Subir el script install-ec2.sh al servidor
chmod +x install-ec2.sh
./install-ec2.sh
```

---

## 📦 3. Instalación Manual Completa

### Actualizar Sistema

**Amazon Linux:**
```bash
# Amazon Linux 2023
sudo dnf update -y

# Amazon Linux 2
sudo yum update -y
```

**Ubuntu:**
```bash
sudo apt update && sudo apt upgrade -y
```

### Instalar PHP y Extensiones

**Amazon Linux:**
```bash
# Amazon Linux 2023
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# Amazon Linux 2
sudo amazon-linux-extras enable php8.2
sudo yum update -y
sudo yum install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache
```

**Ubuntu:**
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

sudo apt install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-intl \
    php8.2-sqlite3 \
    php8.2-bcmath
```

### Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Instalar Node.js y NPM

**Amazon Linux:**
```bash
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo dnf install -y nodejs  # o sudo yum install -y nodejs
node -v && npm -v
```

**Ubuntu:**
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

### Instalar Nginx

**Amazon Linux:**
```bash
sudo dnf install -y nginx  # o sudo yum install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

**Ubuntu:**
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Instalar MySQL/MariaDB

**Amazon Linux (usa MariaDB):**
```bash
sudo dnf install -y mariadb-server  # o sudo yum install -y mariadb-server
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo mysql_secure_installation
```

**Ubuntu (usa MySQL):**
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### Crear Base de Datos MySQL
```bash
sudo mysql -u root -p
```

Luego ejecuta en MySQL:
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📁 4. Configurar Directorio y Permisos

```bash
# Crear directorio de aplicación
sudo mkdir -p /var/www/laravel
sudo chown -R $USER:$USER /var/www/laravel
```

---

## ⚙️ 5. Configurar Nginx

**Amazon Linux:**
```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Ubuntu:**
```bash
sudo nano /etc/nginx/sites-available/laravel
```

Pega esta configuración. **⚠️ IMPORTANTE**: Ajusta el socket de PHP-FPM (ver comentarios en el código):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tu-dominio.com www.tu-dominio.com;
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
        # Amazon Linux usa este socket:
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        # Ubuntu usa este socket:
        # fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Habilitar sitio:

**Amazon Linux:**
```bash
# Ya está en conf.d, solo verificar
sudo nginx -t
sudo systemctl reload nginx
```

**Ubuntu:**
```bash
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🐘 6. Configurar PHP-FPM

**Amazon Linux:**
```bash
sudo nano /etc/php.ini
# O en algunos casos: sudo nano /etc/php.d/php.ini

# Ajusta estos valores:
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M

# Reiniciar
sudo systemctl restart php-fpm
```

**Ubuntu:**
```bash
sudo nano /etc/php/8.2/fpm/php.ini

# Ajusta estos valores:
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M

# Reiniciar
sudo systemctl restart php8.2-fpm
```

---

## 🔑 7. Configurar SSH para GitHub Actions

```bash
# Generar llaves SSH
ssh-keygen -t rsa -b 4096 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""

# Agregar clave pública
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# Mostrar clave privada (copia esto para GitHub Secrets)
cat ~/.ssh/github_deploy
```

---

## 🔐 8. Configurar GitHub Secrets

En GitHub → Settings → Secrets and variables → Actions, agrega:

- **SSH_KEY**: Contenido completo de `~/.ssh/github_deploy` (la clave privada)
- **SSH_HOST**: IP pública o DNS de tu EC2 (ej: `54.123.45.67` o `ec2-xx-xx-xx-xx.compute-1.amazonaws.com`)
- **SSH_USER**: 
  - `ec2-user` para **Amazon Linux**
  - `ubuntu` para **Ubuntu**
- **REMOTE_PATH**: `/var/www/laravel`

---

## 📝 9. Crear Archivo .env

Después del primer deploy, crea el archivo `.env`:

```bash
cd /var/www/laravel
sudo nano .env
```

Contenido mínimo (ajusta según tu configuración):

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=tu_password_seguro

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

Generar APP_KEY:
```bash
cd /var/www/laravel
php artisan key:generate
```

---

## ✅ 10. Configurar Permisos Después del Deploy

```bash
cd /var/www/laravel
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

---

## 🚀 11. Hacer Push para Desplegar

Desde tu máquina local:

```bash
git add .
git commit -m "Preparar para despliegue"
git push origin main
```

El workflow de GitHub Actions se ejecutará automáticamente.

---

## 🔍 12. Verificar Despliegue

```bash
cd /var/www/laravel

# Ver estado de migraciones
php artisan migrate:status

# Listar rutas
php artisan route:list

# Verificar que responde
curl http://localhost

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 🔄 13. Comandos de Mantenimiento

### Reiniciar Servicios

**Amazon Linux:**
```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
sudo systemctl restart laravel-queue
```

**Ubuntu:**
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart laravel-queue
```

### Ver Estado de Servicios

**Amazon Linux:**
```bash
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status laravel-queue
```

**Ubuntu:**
```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status laravel-queue
```

### Limpiar Cache
```bash
cd /var/www/laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimizar Aplicación
```bash
cd /var/www/laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔒 14. Configurar SSL (Opcional pero Recomendado)

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificado (reemplaza con tu dominio)
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Renovar automáticamente (ya está configurado por defecto)
sudo certbot renew --dry-run
```

---

## 🛠️ 15. Solución de Problemas

### Ver Logs de Aplicación
```bash
tail -f /var/www/laravel/storage/logs/laravel.log
```

### Ver Logs de Nginx
```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### Ver Logs de PHP-FPM
```bash
sudo tail -f /var/log/php8.2-fpm.log
```

### Verificar Permisos
```bash
ls -la /var/www/laravel/storage
ls -la /var/www/laravel/bootstrap/cache
```

### Verificar Configuración de Nginx
```bash
sudo nginx -t
```

### Ver Espacio en Disco
```bash
df -h
```

---

## 📊 16. Despliegue Manual (Sin GitHub Actions)

Si necesitas desplegar manualmente:

```bash
cd /var/www/laravel

# Actualizar código
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Ejecutar migraciones
php artisan migrate --force

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
# Amazon Linux:
sudo systemctl restart php-fpm
# Ubuntu:
# sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## ⚠️ Notas Importantes

1. **Nunca** subas el archivo `.env` al repositorio
2. Guarda tus contraseñas de manera segura
3. Asegúrate de que los servicios estén corriendo después de cada reinicio del servidor
4. Revisa los logs regularmente para detectar problemas
5. Mantén el sistema actualizado: `sudo apt update && sudo apt upgrade -y`

---

## 📚 Documentación Completa

Para una guía detallada paso a paso, consulta: `INSTALACION_EC2.md`

