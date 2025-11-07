# 🚀 Guía Completa de Despliegue en EC2 con Dominio y SSL

Guía paso a paso para desplegar tu aplicación Laravel en AWS EC2 con el dominio `emotive.g3nia.com`, certificado SSL y despliegue automático con GitHub Actions.

---

## 📋 Índice

1. [Configuración Inicial de EC2](#1-configuración-inicial-de-ec2)
2. [Configuración del Dominio en GoDaddy](#2-configuración-del-dominio-en-godaddy)
3. [Instalación de Software en el Servidor](#3-instalación-de-software-en-el-servidor)
4. [Configuración de Base de Datos](#4-configuración-de-base-de-datos)
5. [Configuración de Nginx y SSL](#5-configuración-de-nginx-y-ssl)
6. [Configuración de GitHub Actions](#6-configuración-de-github-actions)
7. [Primer Despliegue](#7-primer-despliegue)
8. [Verificación Final](#8-verificación-final)

---

## 1. Configuración Inicial de EC2

### Paso 1.1: Crear Instancia EC2

1. **Accede a AWS Console**: https://console.aws.amazon.com/ec2/
2. **Clic en "Launch Instance"**
3. **Configuración básica**:
   - **Nombre**: `emotive-laravel-server`
   - **AMI**: Amazon Linux 2023 (o Ubuntu 22.04 LTS)
   - **Tipo de instancia**: `t3.small` o superior (mínimo recomendado)
   - **Key Pair**: Crea o selecciona una key pair (guarda el archivo `.pem`)

4. **Configuración de red**:
   - **VPC**: Selecciona una VPC existente o crea una nueva
   - **Subnet**: Selecciona una subnet pública
     - ⚠️ **IMPORTANTE**: Si no tienes una subred pública configurada, consulta `CONFIGURAR_SUBRED_PUBLICA_AWS.md` para crear una
   - **Auto-assign Public IP**: **Habilitar** (debe estar habilitado)
   - **Security Group**: Crea uno nuevo con estas reglas:
     - **SSH (22)**: Tu IP (o 0.0.0.0/0 temporalmente)
     - **HTTP (80)**: 0.0.0.0/0
     - **HTTPS (443)**: 0.0.0.0/0

5. **Storage**: Mínimo 20 GB

6. **Launch Instance**

### Paso 1.2: Obtener IP Pública

1. En la consola de EC2, selecciona tu instancia
2. Copia la **IPv4 Public IP** (ejemplo: `54.123.45.67`)
3. **Guarda esta IP** - la necesitarás para configurar el dominio

### Paso 1.3: Conectarse al Servidor

```bash
# En tu máquina local
chmod 400 tu-key.pem
ssh -i tu-key.pem ec2-user@TU_IP_PUBLICA

# Si es Ubuntu, usa:
# ssh -i tu-key.pem ubuntu@TU_IP_PUBLICA
```

---

## 2. Configuración del Dominio en GoDaddy

### Paso 2.1: Acceder a GoDaddy

1. **Inicia sesión** en https://www.godaddy.com/
2. Ve a **"Mis Productos"** → **"Dominios"**
3. Busca y selecciona **`g3nia.com`**

### Paso 2.2: Configurar DNS

1. **Clic en "DNS"** o **"Administrar DNS"**
2. **Busca la sección "Registros"** o **"Records"**

### Paso 2.3: Agregar Registro A para Subdominio

1. **Clic en "Agregar"** o **"Add"**
2. **Tipo**: `A`
3. **Nombre/Host**: `emotive` (esto creará `emotive.g3nia.com`)
4. **Valor/Points to**: **Pega la IP pública de tu EC2** (la que copiaste en el paso 1.2)
5. **TTL**: `600` (10 minutos) o `3600` (1 hora)
6. **Guardar**

### Paso 2.4: Verificar Propagación DNS

Espera 5-30 minutos y verifica:

```bash
# En tu máquina local
dig emotive.g3nia.com
# O
nslookup emotive.g3nia.com
```

Deberías ver tu IP pública de EC2.

---

## 3. Instalación de Software en el Servidor

### Paso 3.1: Conectarse al Servidor

```bash
ssh -i tu-key.pem ec2-user@TU_IP_PUBLICA
```

### Paso 3.2: Ejecutar Script de Instalación

```bash
# Clonar tu repositorio (si aún no lo tienes)
cd ~
git clone https://github.com/TU_USUARIO/TU_REPO.git temp-repo
cd temp-repo

# O si ya tienes el código, sube los scripts:
# scp -i tu-key.pem install-ec2-amazon-linux.sh ec2-user@TU_IP:/home/ec2-user/

# Ejecutar script de instalación
chmod +x install-ec2-amazon-linux.sh
./install-ec2-amazon-linux.sh
```

**Nota**: El script instalará:
- PHP 8.2 y extensiones necesarias
- Composer
- Node.js y npm
- Nginx
- MySQL/MariaDB

### Paso 3.3: Configurar MySQL

```bash
# Configurar MySQL (establecer contraseña root)
sudo mysql_secure_installation

# Conectarse a MySQL
sudo mysql -u root -p
```

En MySQL, ejecuta:

```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'Y%ejpE!)t9PX';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Guarda estas credenciales** para el archivo `.env`.

---

## 4. Configuración de Base de Datos

### Paso 4.1: Preparar Directorio de la Aplicación

```bash
# Crear directorio
sudo mkdir -p /var/www/laravel
sudo chown -R ec2-user:ec2-user /var/www/laravel

# Clonar repositorio
cd /var/www/laravel
git clone https://github.com/TU_USUARIO/TU_REPO.git .
```

---

## 5. Configuración de Nginx y SSL

### Paso 5.1: Instalar Certbot (Let's Encrypt)

```bash
# Para Amazon Linux 2023
sudo dnf install -y certbot python3-certbot-nginx

# Para Amazon Linux 2
sudo yum install -y certbot python3-certbot-nginx

# Para Ubuntu
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
```

### Paso 5.2: Configurar Nginx (Antes de SSL)

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Pega esta configuración**:

```nginx
server {
    listen 80;
    server_name emotive.g3nia.com;
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
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

### Paso 5.3: Obtener Certificado SSL

**IMPORTANTE**: Asegúrate de que el dominio `emotive.g3nia.com` ya apunte a tu IP antes de continuar.

```bash
# Obtener certificado SSL automáticamente
sudo certbot --nginx -d emotive.g3nia.com

# Seguir las instrucciones:
# - Email: tu email
# - Aceptar términos: Y
# - Compartir email: N (o Y si quieres)
```

Certbot configurará automáticamente Nginx con SSL.

### Paso 5.4: Verificar Renovación Automática

```bash
# Probar renovación
sudo certbot renew --dry-run

# Verificar que el timer esté activo
sudo systemctl status certbot.timer
```

---

## 6. Configuración de GitHub Actions

### Paso 6.1: Generar Clave SSH en el Servidor

```bash
# En el servidor EC2
ssh-keygen -t rsa -b 4096 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 600 ~/.ssh/github_deploy

# Mostrar la clave PRIVADA (cópiala completa)
cat ~/.ssh/github_deploy
```

**Copia toda la clave privada** (incluyendo `-----BEGIN OPENSSH PRIVATE KEY-----` y `-----END OPENSSH PRIVATE KEY-----`)

### Paso 6.2: Configurar Secrets en GitHub

1. **Ve a tu repositorio en GitHub**
2. **Settings** → **Secrets and variables** → **Actions**
3. **Clic en "New repository secret"**
4. **Agregar estos secrets**:

   - **Nombre**: `SSH_HOST`
     **Valor**: `TU_IP_PUBLICA` (o `emotive.g3nia.com` si ya está configurado)

   - **Nombre**: `SSH_USER`
     **Valor**: `ec2-user` (o `ubuntu` si es Ubuntu)

   - **Nombre**: `SSH_KEY`
     **Valor**: La clave privada completa que copiaste en el paso 6.1

   - **Nombre**: `SSH_PORT`
     **Valor**: `22`

### Paso 6.3: Crear Workflow de GitHub Actions

El archivo `.github/workflows/deploy.yml` ya está creado en el repositorio. Verifica que esté correcto.

---

## 7. Primer Despliegue

### Paso 7.1: Configurar Archivo .env

```bash
# En el servidor
cd /var/www/laravel
cp .env.example .env
nano .env
```

**Configuración mínima**:

```env
APP_NAME=Emotive
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://emotive.g3nia.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=TU_PASSWORD_SEGURO_AQUI

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
# Configura tus credenciales de email aquí
```

```bash
# Generar APP_KEY
php artisan key:generate
```

### Paso 7.2: Configurar Permisos

```bash
cd /var/www/laravel
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### Paso 7.3: Ejecutar Primer Despliegue Manual

```bash
cd /var/www/laravel

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Ejecutar migrations, seeders y factories
php artisan migrate --force
php artisan db:seed --force

# Ejecutar factories (solo en primer despliegue)
# Si tienes un seeder que use factories, ya se ejecutó arriba
# Si no, puedes ejecutar:
php artisan tinker --execute="
    // Ejecuta tus factories aquí
    // Ejemplo: User::factory()->count(10)->create();
"

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### Paso 7.4: Verificar que el Script de Despliegue Funcione

El script `deploy.sh` ya está configurado. Puedes probarlo:

```bash
cd /var/www/laravel
chmod +x deploy.sh
./deploy.sh
```

---

## 8. Verificación Final

### Paso 8.1: Verificar que el Sitio Funciona

1. **Abre tu navegador**: https://emotive.g3nia.com
2. **Verifica el certificado SSL**: Debe mostrar un candado verde
3. **Prueba la aplicación**: Login, navegación, etc.

### Paso 8.2: Probar Despliegue Automático

1. **Haz un cambio pequeño** en tu código local
2. **Commit y push**:
   ```bash
   git add .
   git commit -m "Test deploy automático"
   git push origin main
   ```
3. **Ve a GitHub** → **Actions** → Verifica que el workflow se ejecute
4. **Espera 2-3 minutos** y verifica que el cambio esté en producción

### Paso 8.3: Verificar Logs

```bash
# Logs de Laravel
tail -f /var/www/laravel/storage/logs/laravel.log

# Logs de Nginx
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Logs de PHP-FPM
sudo tail -f /var/log/php-fpm/error.log
```

---

## 🔧 Solución de Problemas Comunes

### Error 502 Bad Gateway

```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
sudo systemctl status php-fpm
```

### Error de Permisos

```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
```

### SSL No Funciona

```bash
# Verificar certificado
sudo certbot certificates

# Renovar manualmente
sudo certbot renew

# Verificar configuración de Nginx
sudo nginx -t
```

### DNS No Propaga

- Espera hasta 48 horas (normalmente 5-30 minutos)
- Verifica en https://www.whatsmydns.net/
- Limpia caché DNS: `sudo dscacheutil -flushcache` (macOS)

### GitHub Actions Falla

1. Verifica los secrets en GitHub
2. Verifica que la clave SSH esté correcta
3. Verifica logs en GitHub Actions
4. Prueba conexión SSH manualmente desde tu máquina

---

## 📝 Comandos Útiles

### Actualizar Manualmente

```bash
cd /var/www/laravel
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
sudo systemctl restart php-fpm
```

### Ver Estado de Servicios

```bash
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mysqld
```

### Reiniciar Todo

```bash
sudo systemctl restart nginx php-fpm mysqld
```

---

## ✅ Checklist Final

- [ ] Instancia EC2 creada y funcionando
- [ ] Dominio `emotive.g3nia.com` apunta a la IP de EC2
- [ ] Certificado SSL instalado y funcionando
- [ ] Base de datos creada y configurada
- [ ] Archivo `.env` configurado
- [ ] Migrations, seeders y factories ejecutados
- [ ] GitHub Actions configurado y funcionando
- [ ] Sitio accesible en https://emotive.g3nia.com
- [ ] Despliegue automático probado y funcionando

---

## 🎉 ¡Listo!

Tu aplicación está desplegada y cada vez que hagas `git push origin main`, se desplegará automáticamente.

**¿Necesitas ayuda?** Revisa los logs o consulta la sección de solución de problemas.

