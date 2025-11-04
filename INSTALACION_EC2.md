# 🚀 Guía Completa de Despliegue en AWS EC2

Esta guía te ayudará a desplegar tu aplicación Laravel en un servidor EC2 de AWS desde cero.

## 📋 Tabla de Contenidos

1. [Configuración Inicial de EC2](#1-configuración-inicial-de-ec2)
2. [Instalación Automática Rápida](#2-instalación-automática-rápida-opcional)
3. [Instalación Manual Paso a Paso](#3-instalación-manual-paso-a-paso)
4. [Configuración de GitHub Actions](#4-configuración-de-github-actions)
5. [Primer Despliegue](#5-primer-despliegue)
6. [Solución de Problemas](#6-solución-de-problemas)

---

## 1. Configuración Inicial de EC2

### 1.1. Crear Instancia EC2 en AWS

1. **Ve a la consola de AWS EC2**
2. **Lanza una nueva instancia** con las siguientes características:
   - **AMI**: **Amazon Linux 2023** (recomendado) o Ubuntu Server 22.04 LTS
   - **Tipo de instancia**: t2.micro (gratis) o t3.small para producción
   - **Par de llaves**: Crea o selecciona un par de llaves SSH (.pem)
   - **Grupo de seguridad**: Crea uno nuevo con estas reglas:
     - **SSH** (22) desde tu IP
     - **HTTP** (80) desde cualquier lugar (0.0.0.0/0)
     - **HTTPS** (443) desde cualquier lugar (0.0.0.0/0)

### 1.2. Obtener Información de Conexión

Después de crear la instancia, necesitarás:
- **IP Pública** o **DNS Público** de la instancia
- **Archivo .pem** (clave privada) descargado
- **Usuario**: 
  - `ec2-user` para **Amazon Linux**
  - `ubuntu` para **Ubuntu**

### 1.3. Conectarse al Servidor

**Para Amazon Linux:**
```bash
# Ajusta los permisos del archivo .pem
chmod 400 ruta/a/tu-llave.pem

# Conectarse (usa ec2-user para Amazon Linux)
ssh -i ruta/a/tu-llave.pem ec2-user@tu-ip-ec2
```

**Para Ubuntu:**
```bash
# Ajusta los permisos del archivo .pem
chmod 400 ruta/a/tu-llave.pem

# Conectarse (usa ubuntu para Ubuntu)
ssh -i ruta/a/tu-llave.pem ubuntu@tu-ip-ec2
```

**⚠️ IMPORTANTE**: 
- El usuario por defecto es `ec2-user` para **Amazon Linux** o `ubuntu` para **Ubuntu** (NO `root`)
- EC2 **NO tiene contraseña de root** configurada (esto es normal y seguro)
- Para comandos administrativos, usa `sudo` en lugar de cambiar a root
- **Amazon Linux usa `yum` o `dnf`** en lugar de `apt`

---

## 2. Instalación Automática Rápida (Opcional)

### Para Amazon Linux:

```bash
# Sube el archivo install-ec2-amazon-linux.sh al servidor
chmod +x install-ec2-amazon-linux.sh
./install-ec2-amazon-linux.sh
```

### Para Ubuntu:

```bash
# Sube el archivo install-ec2.sh al servidor
chmod +x install-ec2.sh
./install-ec2.sh
```

**Nota**: Los scripts instalarán las dependencias básicas, pero aún necesitarás configurar MySQL/MariaDB, Nginx y GitHub Secrets manualmente.

---

## 3. Instalación Manual Paso a Paso

> **🔍 ¿Qué sistema operativo tienes?**
> 
> Ejecuta esto para verificarlo: `cat /etc/os-release`
> 
> - Si ves `ID=amzn` o `ID=amazon` → Usa los comandos de **Amazon Linux**
> - Si ves `ID=ubuntu` → Usa los comandos de **Ubuntu**

### 3.1. Actualizar el Sistema

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

### 3.2. Instalar PHP y Extensiones

**Amazon Linux:**
```bash
# Amazon Linux 2023
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json

# Amazon Linux 2 (habilitar PHP desde extras)
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

### 3.3. Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 3.4. Instalar Node.js y NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Verificar versiones:
```bash
node -v
npm -v
```

### 3.5. Instalar Nginx

**Amazon Linux:**
```bash
# Amazon Linux 2023
sudo dnf install -y nginx

# Amazon Linux 2
sudo yum install -y nginx

sudo systemctl start nginx
sudo systemctl enable nginx
```

**Ubuntu:**
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 3.6. Instalar y Configurar MySQL/MariaDB

**Amazon Linux (usa MariaDB):**
```bash
# Amazon Linux 2023
sudo dnf install -y mariadb-server

# Amazon Linux 2
sudo yum install -y mariadb-server

sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo mysql_secure_installation
```

**Ubuntu (usa MySQL):**
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Crear base de datos y usuario:
```bash
sudo mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Opción B: SQLite (Solo para desarrollo/pruebas)

```bash
# SQLite ya está instalado con php8.2-sqlite3
```

### 3.7. Configurar Directorio de la Aplicación

```bash
# Crear usuario si no existe (opcional)
sudo useradd -m -s /bin/bash www-data || true

# Crear directorio para la aplicación
sudo mkdir -p /var/www/laravel
sudo chown -R $USER:$USER /var/www/laravel
```

**Nota**: Ajusta `/var/www/laravel` según tu preferencia. Este será el `REMOTE_PATH` en GitHub Actions.

### 3.8. Configurar Nginx

**Amazon Linux** (usa `/etc/nginx/conf.d/`):
```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Ubuntu** (usa `/etc/nginx/sites-available/`):
```bash
sudo nano /etc/nginx/sites-available/laravel
```

Pegar la siguiente configuración. **⚠️ IMPORTANTE**: Ajusta el socket de PHP-FPM según tu sistema:

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

**Verificar el socket correcto:**
```bash
# Amazon Linux
sudo systemctl status php-fpm | grep -i socket
# O buscar: ls -la /var/run/php-fpm/

# Ubuntu
sudo systemctl status php8.2-fpm | grep -i socket
# O buscar: ls -la /var/run/php/
```

Habilitar el sitio:

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

### 3.9. Configurar PHP-FPM

**Amazon Linux:**
```bash
# La ubicación puede variar, busca el archivo php.ini principal
sudo nano /etc/php.ini
# O en algunos casos:
sudo nano /etc/php.d/php.ini

# Ajustar valores:
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M

# Reiniciar
sudo systemctl restart php-fpm
```

**Ubuntu:**
```bash
sudo nano /etc/php/8.2/fpm/php.ini

# Ajustar valores:
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M

# Reiniciar
sudo systemctl restart php8.2-fpm
```

### 3.10. Configurar Permisos Iniciales

```bash
# En el directorio de la aplicación (después de hacer el primer deploy)
cd /var/www/laravel
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### 3.11. Configurar SSH para GitHub Actions

Generar par de llaves SSH para el deploy:

```bash
ssh-keygen -t rsa -b 4096 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""
```

Agregar la clave pública al `authorized_keys`:

```bash
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

**Importante**: Copiar la clave **privada** (`~/.ssh/github_deploy`) - la necesitarás para GitHub Secrets:

```bash
cat ~/.ssh/github_deploy
```

---

## 4. Configuración de GitHub Actions

### 4.1. Configurar GitHub Secrets

En GitHub, ve a tu repositorio → Settings → Secrets and variables → Actions

Agrega los siguientes secrets:

- `SSH_KEY`: Contenido de `~/.ssh/github_deploy` (la clave privada completa)
- `SSH_HOST`: IP pública o dominio de tu EC2 (ej: `ec2-xx-xx-xx-xx.compute-1.amazonaws.com` o `tu-dominio.com`)
- `SSH_USER`: Usuario SSH:
  - `ec2-user` para **Amazon Linux**
  - `ubuntu` para **Ubuntu**
- `REMOTE_PATH`: Ruta donde está la app (ej: `/var/www/laravel`)

---

## 5. Primer Despliegue

### 5.1. Crear Archivo .env en el Servidor

**IMPORTANTE**: Debes crear el archivo `.env` ANTES del primer deploy o después del primer deploy automático.

Después del primer deploy, necesitarás crear el archivo `.env`:

```bash
cd /var/www/laravel
sudo nano .env
```

Configuración mínima de `.env`:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://tu-dominio.com

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Base de datos MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=tu_password_seguro

# O Base de datos SQLite
# DB_CONNECTION=sqlite
# DB_DATABASE=/var/www/laravel/database/database.sqlite

# Cache
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail (ajusta según tu proveedor)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# OpenAI (si usas)
OPENAI_API_KEY=tu_api_key
```

Generar la clave de aplicación:

```bash
php artisan key:generate
```

### 5.2. Primer Deploy Automático

Una vez configurados los GitHub Secrets, haz push a la rama `main`:

```bash
git add .
git commit -m "Configurar despliegue en EC2"
git push origin main
```

El workflow de GitHub Actions se ejecutará automáticamente y desplegará tu aplicación.

### 5.3. Configurar Permisos Después del Deploy

Después del primer deploy, configura los permisos correctamente:

```bash
cd /var/www/laravel
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### 5.4. Verificar que Todo Funciona

```bash
cd /var/www/laravel
php artisan migrate:status
php artisan route:list

# Verificar que la aplicación responde
curl http://localhost
```

### 5.5. Configurar Queue Worker (Opcional)

Si usas queues, crea un servicio systemd:

```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

Contenido:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=laravel-queue

[Install]
WantedBy=multi-user.target
```

Habilitar y iniciar:

```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
```

### 5.6. Configurar Certificado SSL (Recomendado para Producción)

Instalar Certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
```

Obtener certificado:

```bash
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

---

## 6. Solución de Problemas

### Problema: "Me pide contraseña de root y no la tengo"

**Solución**: Ubuntu en EC2 no tiene contraseña de root por defecto (esto es normal). 

- ✅ **Usa `sudo`** para comandos administrativos: `sudo apt update`
- ✅ **Conéctate como `ubuntu`**: `ssh -i llave.pem ubuntu@ip`
- ❌ **NO uses `su root`**: No funcionará sin contraseña
- 📚 Ver `SOLUCION_PROBLEMA_ROOT.md` para más detalles

### Problema: "No match for argument: epel-release" (Amazon Linux 2023)

**Solución**: Amazon Linux 2023 NO tiene `epel-release` (no es necesario).

- ✅ **Omite `epel-release`** - los paquetes están disponibles directamente
- ✅ **Usa `dnf`** en lugar de `yum`
- ✅ **Continúa la instalación sin epel-release**
- 📚 Ver `SOLUCION_EPEL_RELEASE.md` para más detalles

### Problema: "Conflicts with curl-minimal" (Amazon Linux 2023)

**Solución**: Amazon Linux 2023 viene con `curl-minimal` preinstalado (es suficiente).

- ✅ **NO instales curl** - `curl-minimal` funciona perfectamente
- ✅ **Verifica con `curl --version`** - debería funcionar
- ✅ **Omite curl de la instalación** - solo instala: `wget git unzip`
- 📚 Ver `SOLUCION_CURL_CONFLICTO.md` para más detalles

### Ver logs de la aplicación:
```bash
tail -f /var/www/laravel/storage/logs/laravel.log
```

### Ver logs de Nginx:
```bash
sudo tail -f /var/log/nginx/error.log
```

### Ver logs de PHP-FPM:
```bash
sudo tail -f /var/log/php8.2-fpm.log
```

### Verificar permisos:
```bash
ls -la /var/www/laravel/storage
ls -la /var/www/laravel/bootstrap/cache
```

### Reiniciar servicios:
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart laravel-queue  # Si lo configuraste
```

## 7. Comandos Útiles

### 7.1. Gestión de Servicios

```bash
# Ver estado de servicios
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status laravel-queue

# Reiniciar servicios
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart laravel-queue

# Habilitar servicios al inicio
sudo systemctl enable nginx
sudo systemctl enable php8.2-fpm
sudo systemctl enable laravel-queue
```

### 7.2. Comandos de Laravel

```bash
cd /var/www/laravel

# Ejecutar migraciones manualmente
php artisan migrate --force

# Ejecutar seeders manualmente
php artisan db:seed --force

# Ver estado de migraciones
php artisan migrate:status

# Listar rutas
php artisan route:list

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar aplicación
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace simbólico de storage
php artisan storage:link
```

### 7.3. Despliegue Manual (Si no usas GitHub Actions)

```bash
cd /var/www/laravel

# Actualizar código desde Git
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 8. Checklist de Despliegue

Usa este checklist para asegurarte de que todo está configurado correctamente:

- [ ] Instancia EC2 creada y accesible
- [ ] Grupo de seguridad configurado (puertos 22, 80, 443)
- [ ] PHP 8.2 y extensiones instaladas
- [ ] Composer instalado
- [ ] Node.js y NPM instalados
- [ ] Nginx instalado y configurado
- [ ] MySQL instalado y base de datos creada
- [ ] Directorio `/var/www/laravel` creado con permisos correctos
- [ ] Configuración de Nginx creada y habilitada
- [ ] PHP-FPM configurado
- [ ] Llaves SSH generadas para GitHub Actions
- [ ] GitHub Secrets configurados (SSH_KEY, SSH_HOST, SSH_USER, REMOTE_PATH)
- [ ] Archivo `.env` creado en el servidor
- [ ] APP_KEY generado
- [ ] Primer deploy completado exitosamente
- [ ] Permisos de storage y bootstrap/cache configurados
- [ ] Aplicación accesible vía navegador
- [ ] SSL configurado (opcional pero recomendado)

---

## 9. Notas Importantes

### Seguridad

- **Nunca** subas tu archivo `.env` al repositorio
- Usa contraseñas fuertes para MySQL
- Limita el acceso SSH a tu IP en el grupo de seguridad
- Considera usar un firewall adicional como UFW
- Mantén el sistema y las dependencias actualizadas

### Rendimiento

- Usa `php artisan optimize` en producción
- Configura el cache de configuración, rutas y vistas
- Considera usar Redis para cache y sesiones en producción
- Configura opcache para PHP

### Monitoreo

- Revisa los logs regularmente: `tail -f /var/www/laravel/storage/logs/laravel.log`
- Monitorea el uso de recursos: `htop` o `top`
- Configura alertas de espacio en disco: `df -h`

---

## 📞 Soporte

Si tienes problemas durante el despliegue:

1. Revisa los logs de la aplicación
2. Revisa los logs de Nginx
3. Verifica los permisos de archivos
4. Verifica que los servicios estén corriendo
5. Asegúrate de que el archivo `.env` esté configurado correctamente

