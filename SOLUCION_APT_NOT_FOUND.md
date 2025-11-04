# ⚠️ Solución: "apt: command not found"

## 🔍 El Problema

Si recibes el error `sudo: apt: command not found`, significa que tu instancia EC2 **NO es Ubuntu**. Probablemente estás usando **Amazon Linux**, que usa `yum` o `dnf` en lugar de `apt`.

## ✅ Solución Rápida

### Paso 1: Verificar qué sistema operativo tienes

Ejecuta en tu EC2:

```bash
cat /etc/os-release
```

O ejecuta este comando completo:

```bash
if [ -f /etc/os-release ]; then . /etc/os-release; echo "Sistema: $PRETTY_NAME"; echo "ID: $ID"; fi
```

### Paso 2: Usar el gestor de paquetes correcto

#### Si es **Ubuntu** (usa `apt`):
```bash
sudo apt update
sudo apt install -y nginx
```

#### Si es **Amazon Linux** (usa `yum` o `dnf`):

Para Amazon Linux 2023:
```bash
sudo dnf update -y
sudo dnf install -y nginx
```

Para Amazon Linux 2:
```bash
sudo yum update -y
sudo yum install -y nginx
```

## 🚀 Instalación Rápida para Amazon Linux

### Opción 1: Usar el script automatizado

1. Sube el archivo `install-ec2-amazon-linux.sh` a tu servidor
2. Hazlo ejecutable:
   ```bash
   chmod +x install-ec2-amazon-linux.sh
   ```
3. Ejecútalo:
   ```bash
   ./install-ec2-amazon-linux.sh
   ```

### Opción 2: Instalación manual paso a paso

#### 1. Actualizar sistema
```bash
# Amazon Linux 2023
sudo dnf update -y

# Amazon Linux 2
sudo yum update -y
```

#### 2. Instalar PHP 8.2

**Para Amazon Linux 2023:**
```bash
sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache php-json
```

**Para Amazon Linux 2:**
```bash
# Habilitar PHP desde Amazon Linux Extras
sudo amazon-linux-extras enable php8.2
sudo yum update -y
sudo yum install -y php php-fpm php-cli php-common php-mysqlnd \
    php-zip php-gd php-mbstring php-curl php-xml php-intl \
    php-bcmath php-opcache
```

#### 3. Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 4. Instalar Node.js
```bash
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo dnf install -y nodejs  # o sudo yum install -y nodejs
```

#### 5. Instalar Nginx
```bash
# Amazon Linux 2023
sudo dnf install -y nginx

# Amazon Linux 2
sudo yum install -y nginx
```

#### 6. Instalar MySQL/MariaDB
```bash
# Amazon Linux usa MariaDB (compatible con MySQL)
sudo dnf install -y mariadb-server  # o sudo yum install -y mariadb-server
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

#### 7. Habilitar servicios
```bash
sudo systemctl enable php-fpm
sudo systemctl enable nginx
sudo systemctl start php-fpm
sudo systemctl start nginx
```

## 📋 Diferencias Clave: Ubuntu vs Amazon Linux

| Tarea | Ubuntu | Amazon Linux |
|-------|--------|--------------|
| Gestor de paquetes | `apt` | `yum` (AL2) o `dnf` (AL2023) |
| Actualizar | `sudo apt update` | `sudo yum update -y` |
| Instalar | `sudo apt install nginx` | `sudo yum install -y nginx` |
| Usuario web | `www-data` | `nginx` o `apache` |
| PHP-FPM socket | `/var/run/php/php8.2-fpm.sock` | `/var/run/php-fpm/php-fpm.sock` |
| Config Nginx | `/etc/nginx/sites-available/` | `/etc/nginx/conf.d/` |

## 🔧 Configurar Nginx en Amazon Linux

La estructura de configuración es diferente. Crea el archivo de configuración:

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

Contenido (ajusta según tu dominio):

```nginx
server {
    listen 80;
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
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**⚠️ Importante**: En Amazon Linux, el socket de PHP-FPM puede ser diferente. Verifica con:
```bash
sudo systemctl status php-fpm
```

## 🎯 Verificar Instalación

```bash
# Verificar PHP
php -v

# Verificar Composer
composer --version

# Verificar Node.js
node -v
npm -v

# Verificar servicios
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mariadb
```

## 📚 Recursos Adicionales

- Para Ubuntu: Usa `install-ec2.sh`
- Para Amazon Linux: Usa `install-ec2-amazon-linux.sh`
- Script de verificación: `verificar-sistema.sh`

## ❓ ¿Qué AMI debes usar?

**Recomendado para esta guía**: Ubuntu 22.04 LTS

Si creaste una instancia nueva, asegúrate de seleccionar:
- **AMI**: Ubuntu Server 22.04 LTS (HVM), SSD Volume Type

Si ya creaste Amazon Linux y quieres continuar, usa los scripts y comandos para Amazon Linux que están arriba.

