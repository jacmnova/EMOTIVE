# 🚀 Despliegue Rápido - Sin Dominio (Solo IP)

Guía rápida para desplegar tu app Laravel en EC2 usando solo la IP pública.

## ✅ Requisitos Previos

- ✅ PHP, Composer, Node.js, NPM, Nginx, MySQL instalados
- ✅ Conectado al servidor como `ec2-user`
- ✅ IP pública de tu EC2

## 📋 Pasos Rápidos (15 minutos)

### Paso 1: Configurar Nginx (2 min)

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

**Pega esto** (reemplaza `TU_IP_AQUI` con tu IP pública de EC2):

```nginx
server {
    listen 80;
    server_name TU_IP_AQUI;
    root /var/www/laravel/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

```bash
# Verificar y recargar
sudo nginx -t
sudo systemctl reload nginx
```

### Paso 2: Clonar tu Repositorio (3 min)

```bash
cd /var/www/laravel
sudo git clone https://github.com/TU_USUARIO/TU_REPO.git .
# O si es privado:
# sudo git clone git@github.com:TU_USUARIO/TU_REPO.git .
```

### Paso 3: Instalar Dependencias (5 min)

```bash
cd /var/www/laravel

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Instalar dependencias Node.js
npm ci

# Compilar assets
npm run build
```

### Paso 4: Configurar Base de Datos (2 min)

```bash
# Crear base de datos (si no la tienes)
sudo mysql -u root -p
```

```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 5: Crear Archivo .env (2 min)

```bash
cd /var/www/laravel
sudo cp .env.example .env
sudo nano .env
```

**Configuración mínima** (ajusta la IP y password):

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://TU_IP_AQUI

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=password_seguro

CACHE_STORE=file
SESSION_DRIVER=file
```

```bash
# Generar APP_KEY
php artisan key:generate
```

### Paso 6: Configurar Permisos (1 min)

```bash
cd /var/www/laravel

# Dar permisos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache

# Crear enlace de storage
php artisan storage:link
```

### Paso 7: Ejecutar Migraciones (1 min)

```bash
cd /var/www/laravel

# Migraciones
php artisan migrate --force

# Seeders (si tienes)
php artisan db:seed --force

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 8: Verificar (1 min)

```bash
# Verificar servicios
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mysqld

# Probar la app
curl http://localhost
```

## 🎉 Listo!

Abre tu navegador y ve a: `http://TU_IP_AQUI`

## 🔧 Si Algo Falla

### Error 502 Bad Gateway
```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### Error de Permisos
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache
```

### Ver Logs
```bash
tail -f /var/www/laravel/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

## 📝 Comandos Útiles para Futuros Updates

```bash
cd /var/www/laravel
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
sudo systemctl restart php-fpm
```

---

**¿Listo en 15 minutos?** ¡Sigue estos pasos uno por uno! 🚀

