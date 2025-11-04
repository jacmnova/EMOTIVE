#!/usr/bin/env bash
set -euo pipefail

# Script de instalación para Amazon Linux (usa yum/dnf)
# Uso: ./install-ec2-amazon-linux.sh

echo "🚀 Iniciando instalación de Laravel en Amazon Linux EC2..."

# Detectar gestor de paquetes
if command -v dnf &> /dev/null; then
    PKG_MGR="dnf"
    PKG_INSTALL="sudo dnf install -y"
    PKG_UPDATE="sudo dnf update -y"
elif command -v yum &> /dev/null; then
    PKG_MGR="yum"
    PKG_INSTALL="sudo yum install -y"
    PKG_UPDATE="sudo yum update -y"
else
    echo "❌ No se encontró yum ni dnf. Este script es para Amazon Linux."
    exit 1
fi

# Actualizar sistema
echo "📦 Actualizando sistema..."
$PKG_UPDATE

# Instalar herramientas básicas
echo "🔧 Instalando herramientas básicas..."
# epel-release solo disponible en Amazon Linux 2, no en AL2023
if [[ "$PKG_MGR" == "yum" ]]; then
    $PKG_INSTALL epel-release || echo "⚠️ epel-release no disponible, continuando sin él..."
fi

# curl viene preinstalado como curl-minimal en AL2023, verificar antes de instalar
if ! command -v curl &> /dev/null; then
    $PKG_INSTALL curl || echo "⚠️ curl ya disponible (curl-minimal), continuando..."
else
    echo "✅ curl ya está instalado"
fi

# Instalar las demás herramientas
$PKG_INSTALL wget git unzip

# Instalar PHP 8.2
echo "🐘 Instalando PHP 8.2..."
# Amazon Linux 2023 usa PHP desde Amazon Linux Extras
if [ -f /etc/os-release ]; then
    . /etc/os-release
    if [[ "$VERSION_ID" == "2023" ]]; then
        echo "Detectado Amazon Linux 2023"
        sudo dnf install -y php php-fpm php-cli php-common php-mysqlnd \
            php-zip php-gd php-mbstring php-curl php-xml php-intl \
            php-sqlite3 php-bcmath php-opcache php-json
    else
        # Amazon Linux 2
        sudo amazon-linux-extras enable php8.2 -y || echo "PHP 8.2 no disponible en extras, usando repositorio alternativo"
        $PKG_UPDATE
        $PKG_INSTALL php php-fpm php-cli php-common php-mysqlnd \
            php-zip php-gd php-mbstring php-curl php-xml php-intl \
            php-bcmath php-opcache php-json || \
        $PKG_INSTALL php82 php82-fpm php82-cli php82-common php82-mysqlnd \
            php82-zip php82-gd php82-mbstring php82-curl php82-xml \
            php82-intl php82-bcmath php82-opcache
    fi
fi

# Verificar versión de PHP
php -v

# Instalar Composer
echo "📦 Instalando Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    composer --version
fi

# Instalar Node.js
echo "📦 Instalando Node.js..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
    $PKG_INSTALL nodejs
    node -v
    npm -v
fi

# Instalar Nginx
echo "🌐 Instalando Nginx..."
$PKG_INSTALL nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Instalar MySQL/MariaDB
echo "🗄️ Instalando MariaDB/MySQL (compatible con MySQL)..."
# Intentar diferentes nombres de paquete según la versión de Amazon Linux
if [[ "$PKG_MGR" == "dnf" ]]; then
    # Amazon Linux 2023 - intentar diferentes nombres
    $PKG_INSTALL mariadb-server || \
    $PKG_INSTALL mariadb || \
    $PKG_INSTALL mysql-server || \
    echo "⚠️ No se pudo instalar MariaDB/MySQL automáticamente. Instálalo manualmente después."
else
    # Amazon Linux 2
    $PKG_INSTALL mariadb-server || $PKG_INSTALL mariadb
fi

# Intentar habilitar e iniciar el servicio (nombre puede variar)
if systemctl list-unit-files | grep -q mariadb; then
    sudo systemctl enable mariadb
    sudo systemctl start mariadb || sudo systemctl start mariadb-server || true
elif systemctl list-unit-files | grep -q mysql; then
    sudo systemctl enable mysql
    sudo systemctl start mysql || sudo systemctl start mysqld || true
else
    echo "⚠️ Verifica manualmente el servicio de base de datos instalado"
fi

# Configurar PHP-FPM
echo "⚙️ Configurando PHP-FPM..."
PHP_INI_PATH="/etc/php.ini"
PHP_FPM_INI_PATH="/etc/php-fpm.ini"

if [ -f "$PHP_INI_PATH" ]; then
    sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' "$PHP_INI_PATH"
    sudo sed -i 's/post_max_size = .*/post_max_size = 64M/' "$PHP_INI_PATH"
    sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI_PATH"
elif [ -f "/etc/php.d/php.ini" ]; then
    sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php.d/php.ini
    sudo sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php.d/php.ini
    sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php.d/php.ini
fi

# Habilitar servicios
echo "🔄 Habilitando servicios..."
sudo systemctl enable php-fpm
sudo systemctl start php-fpm

# Crear directorio de aplicación
APP_DIR="${APP_DIR:-/var/www/laravel}"
echo "📁 Creando directorio de aplicación: $APP_DIR"
sudo mkdir -p "$APP_DIR"
sudo chown -R $USER:$USER "$APP_DIR" || sudo chown -R nginx:nginx "$APP_DIR" || sudo chown -R apache:apache "$APP_DIR"

# Generar llaves SSH para GitHub Actions
echo "🔐 Configurando SSH para GitHub Actions..."
if [ ! -f ~/.ssh/github_deploy ]; then
    ssh-keygen -t rsa -b 4096 -C "github-actions-deploy" -f ~/.ssh/github_deploy -N ""
    cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
    chmod 600 ~/.ssh/authorized_keys
    echo ""
    echo "✅ Llave SSH generada. Copia la siguiente clave PRIVADA para GitHub Secrets (SSH_KEY):"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    cat ~/.ssh/github_deploy
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
fi

echo ""
echo "✅ Instalación completada!"
echo ""
echo "📝 Próximos pasos:"
echo "1. Configura MySQL: sudo mysql_secure_installation"
echo "2. Crea la base de datos: sudo mysql -u root"
echo "3. Configura Nginx: sudo nano /etc/nginx/conf.d/laravel.conf"
echo "4. Configura GitHub Secrets con la clave SSH mostrada arriba"
echo "5. Haz push a main para iniciar el deploy automático"
echo ""
echo "⚠️ NOTA: La ruta de PHP-FPM puede ser diferente en Amazon Linux:"
echo "   Busca el socket en: /var/run/php-fpm/php-fpm.sock"
echo "   O verifica con: sudo systemctl status php-fpm"
echo ""
if ! command -v mysql &> /dev/null && ! command -v mariadb &> /dev/null; then
    echo "⚠️ MySQL/MariaDB NO se instaló automáticamente."
    echo "   Ejecuta el script: ./instalar-mysql-amazon-linux-2023.sh"
    echo "   O instala manualmente usando los comandos en SOLUCION_MARIADB_SERVER.md"
fi

