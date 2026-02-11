#!/usr/bin/env bash
set -euo pipefail

# Script de instalación automática para EC2
# Uso: ./install-ec2.sh
# Detecta automáticamente si es Ubuntu o Amazon Linux

echo "🚀 Iniciando instalación de Laravel en EC2..."

# Detectar el sistema operativo
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_ID="$ID"
else
    echo "❌ No se pudo detectar el sistema operativo"
    exit 1
fi

# Si es Amazon Linux, usar el script específico
if [[ "$OS_ID" == "amzn" ]] || [[ "$OS_ID" == "amazon" ]]; then
    echo "✅ Detectado Amazon Linux - Redirigiendo a script específico..."
    echo "Ejecuta: ./install-ec2-amazon-linux.sh"
    exit 0
fi

# Si no es Ubuntu, advertir
if [[ "$OS_ID" != "ubuntu" ]]; then
    echo "⚠️ Sistema operativo detectado: $OS_ID"
    echo "Este script está optimizado para Ubuntu."
    echo "¿Deseas continuar? (y/n)"
    read -r response
    if [[ ! "$response" =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Verificar que apt existe
if ! command -v apt &> /dev/null; then
    echo "❌ Error: 'apt' no está disponible"
    echo "Verifica que estés usando Ubuntu"
    echo "Ejecuta: ./verificar-sistema.sh para verificar tu sistema"
    exit 1
fi

# Actualizar sistema
echo "📦 Actualizando sistema..."
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.2 y extensiones
echo "🐘 Instalando PHP 8.2 y extensiones..."
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

# Instalar Composer
echo "📦 Instalando Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Instalar Node.js
echo "📦 Instalando Node.js..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
fi

# Instalar Nginx
echo "🌐 Instalando Nginx..."
sudo apt install -y nginx

# Instalar MySQL
echo "🗄️ Instalando MySQL..."
sudo apt install -y mysql-server

# Configurar PHP-FPM
echo "⚙️ Configurando PHP-FPM..."
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini

# Habilitar servicios
echo "🔄 Habilitando servicios..."
sudo systemctl enable nginx
sudo systemctl enable php8.2-fpm
sudo systemctl start nginx
sudo systemctl start php8.2-fpm

# Crear directorio de aplicación (ajusta según tu preferencia)
APP_DIR="${APP_DIR:-/var/www/laravel}"
echo "📁 Creando directorio de aplicación: $APP_DIR"
sudo mkdir -p "$APP_DIR"
sudo chown -R $USER:$USER "$APP_DIR" || sudo chown -R www-data:www-data "$APP_DIR"

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
echo "2. Crea la base de datos y usuario MySQL"
echo "3. Configura Nginx: sudo nano /etc/nginx/sites-available/laravel"
echo "4. Configura GitHub Secrets con la clave SSH mostrada arriba"
echo "5. Haz push a main para iniciar el deploy automático"
echo ""
echo "📚 Ver INSTALACION_EC2.md para más detalles"

