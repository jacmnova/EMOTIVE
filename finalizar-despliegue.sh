#!/usr/bin/env bash
set -euo pipefail

# Script para finalizar y verificar el despliegue completo
# Ejecutar: chmod +x finalizar-despliegue.sh && sudo ./finalizar-despliegue.sh

echo "🚀 Iniciando verificación y finalización del despliegue..."
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para verificar comandos
check_command() {
    if command -v "$1" &> /dev/null; then
        echo -e "${GREEN}✅ $1 está instalado${NC}"
        return 0
    else
        echo -e "${RED}❌ $1 NO está instalado${NC}"
        return 1
    fi
}

# Función para verificar servicios
check_service() {
    if systemctl is-active --quiet "$1"; then
        echo -e "${GREEN}✅ Servicio $1 está corriendo${NC}"
        return 0
    else
        echo -e "${RED}❌ Servicio $1 NO está corriendo${NC}"
        return 1
    fi
}

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 1: VERIFICAR SOFTWARE INSTALADO"
echo "════════════════════════════════════════════════════════════════"
check_command php
check_command composer
check_command node
check_command npm
check_command nginx
check_command mysql
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 2: VERIFICAR SERVICIOS"
echo "════════════════════════════════════════════════════════════════"

# Verificar PHP-FPM
if systemctl list-units --type=service | grep -q php8.2-fpm; then
    check_service php8.2-fpm
    PHP_FPM_SERVICE="php8.2-fpm"
elif systemctl list-units --type=service | grep -q php-fpm; then
    check_service php-fpm
    PHP_FPM_SERVICE="php-fpm"
else
    echo -e "${RED}❌ PHP-FPM no encontrado${NC}"
    echo "Instalando PHP-FPM..."
    sudo dnf install -y php-fpm || sudo apt install -y php8.2-fpm
    PHP_FPM_SERVICE="php-fpm"
fi

check_service nginx
check_service mysqld || check_service mariadb
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 3: VERIFICAR PHP-FPM SOCKET"
echo "════════════════════════════════════════════════════════════════"

# Buscar socket de PHP-FPM en configuración primero
PHP_SOCKET_CONFIG=$(sudo grep "^listen\s*=" /etc/php-fpm.d/www.conf 2>/dev/null | head -1 | sed 's/.*=\s*//' | tr -d ';' | xargs)
if [ -z "$PHP_SOCKET_CONFIG" ]; then
    PHP_SOCKET_CONFIG=$(sudo grep "^listen\s*=" /etc/php/8.2/fpm/pool.d/www.conf 2>/dev/null | head -1 | sed 's/.*=\s*//' | tr -d ';' | xargs)
fi

# Buscar socket de PHP-FPM en el sistema
PHP_SOCKET=$(sudo find /var/run /run -name "*php*.sock" 2>/dev/null | head -1)

# Si no se encuentra, usar el de la configuración
if [ -z "$PHP_SOCKET" ] && [ -n "$PHP_SOCKET_CONFIG" ]; then
    PHP_SOCKET="$PHP_SOCKET_CONFIG"
    echo -e "${YELLOW}⚠️ Socket no encontrado en sistema, usando configuración: $PHP_SOCKET${NC}"
fi

if [ -z "$PHP_SOCKET" ]; then
    echo -e "${YELLOW}⚠️ Socket de PHP-FPM no encontrado${NC}"
    echo "Iniciando PHP-FPM..."
    sudo systemctl start $PHP_FPM_SERVICE
    sudo systemctl enable $PHP_FPM_SERVICE
    sleep 3
    
    # Buscar nuevamente
    PHP_SOCKET=$(sudo find /var/run /run -name "*php*.sock" 2>/dev/null | head -1)
    
    # Si aún no se encuentra, leer de configuración
    if [ -z "$PHP_SOCKET" ]; then
        PHP_SOCKET_CONFIG=$(sudo grep "^listen\s*=" /etc/php-fpm.d/www.conf 2>/dev/null | head -1 | sed 's/.*=\s*//' | tr -d ';' | xargs)
        if [ -n "$PHP_SOCKET_CONFIG" ]; then
            PHP_SOCKET="$PHP_SOCKET_CONFIG"
            echo -e "${YELLOW}⚠️ Usando socket de configuración: $PHP_SOCKET${NC}"
        fi
    fi
fi

if [ -n "$PHP_SOCKET" ]; then
    # Verificar que el socket existe o crear el directorio
    SOCKET_DIR=$(dirname "$PHP_SOCKET")
    if [ ! -d "$SOCKET_DIR" ]; then
        echo "Creando directorio para socket: $SOCKET_DIR"
        sudo mkdir -p "$SOCKET_DIR"
        sudo chown apache:apache "$SOCKET_DIR" 2>/dev/null || sudo chown nginx:nginx "$SOCKET_DIR" 2>/dev/null || true
    fi
    
    # Verificar permisos del socket si existe
    if [ -S "$PHP_SOCKET" ]; then
        echo -e "${GREEN}✅ Socket encontrado y accesible: $PHP_SOCKET${NC}"
    else
        echo -e "${YELLOW}⚠️ Socket configurado pero no existe aún: $PHP_SOCKET${NC}"
        echo "Se creará cuando PHP-FPM se reinicie correctamente"
    fi
else
    echo -e "${RED}❌ Socket no encontrado después de iniciar PHP-FPM${NC}"
    echo "Verificando configuración de PHP-FPM..."
    sudo grep "listen" /etc/php-fpm.d/www.conf 2>/dev/null || sudo grep "listen" /etc/php/8.2/fpm/pool.d/www.conf 2>/dev/null
    # Usar socket por defecto común
    PHP_SOCKET="/run/php-fpm/www.sock"
    echo -e "${YELLOW}⚠️ Usando socket por defecto: $PHP_SOCKET${NC}"
fi
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 4: VERIFICAR Y CORREGIR NGINX"
echo "════════════════════════════════════════════════════════════════"

NGINX_CONF="/etc/nginx/conf.d/laravel.conf"
if [ ! -f "$NGINX_CONF" ]; then
    echo -e "${YELLOW}⚠️ Configuración de Nginx no encontrada, creándola...${NC}"
    sudo tee "$NGINX_CONF" > /dev/null <<EOF
server {
    listen 80;
    server_name emotive.g3nia.com;
    root /var/www/laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_SOCKET:-/var/run/php-fpm/php-fpm.sock};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    echo -e "${GREEN}✅ Configuración de Nginx creada${NC}"
else
    # Actualizar socket si es necesario
    if [ -n "$PHP_SOCKET" ]; then
        CURRENT_SOCKET=$(grep "fastcgi_pass unix:" "$NGINX_CONF" | sed 's/.*unix:\([^;]*\).*/\1/' | xargs)
        if [ "$CURRENT_SOCKET" != "$PHP_SOCKET" ]; then
            echo -e "${YELLOW}⚠️ Actualizando socket en configuración de Nginx...${NC}"
            echo "  De: $CURRENT_SOCKET"
            echo "  A: $PHP_SOCKET"
            sudo sed -i "s|fastcgi_pass unix:[^;]*|fastcgi_pass unix:$PHP_SOCKET|" "$NGINX_CONF"
        fi
    fi
    echo -e "${GREEN}✅ Configuración de Nginx verificada${NC}"
fi

# Verificar configuración
if sudo nginx -t; then
    echo -e "${GREEN}✅ Configuración de Nginx es válida${NC}"
    sudo systemctl reload nginx
else
    echo -e "${RED}❌ Error en configuración de Nginx${NC}"
    exit 1
fi
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 5: VERIFICAR DIRECTORIO DE LA APLICACIÓN"
echo "════════════════════════════════════════════════════════════════"

APP_DIR="/var/www/laravel"
if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}❌ Directorio $APP_DIR no existe${NC}"
    echo "Creando directorio..."
    sudo mkdir -p "$APP_DIR"
    sudo chown -R ec2-user:ec2-user "$APP_DIR"
fi

cd "$APP_DIR" || exit 1

# Verificar que es un repositorio git
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}⚠️ No es un repositorio git. ¿Clonar desde GitHub? (y/n)${NC}"
    read -r response
    if [[ "$response" =~ ^[Yy]$ ]]; then
        echo "Necesitas clonar el repositorio manualmente:"
        echo "  git clone https://github.com/TU_USUARIO/TU_REPO.git ."
        exit 1
    fi
fi

echo -e "${GREEN}✅ Directorio de aplicación verificado${NC}"
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 6: INSTALAR DEPENDENCIAS"
echo "════════════════════════════════════════════════════════════════"

# Instalar dependencias PHP
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias PHP..."
    composer install --no-dev --optimize-autoloader
else
    echo -e "${GREEN}✅ Dependencias PHP ya instaladas${NC}"
fi

# Instalar dependencias Node.js
if [ -f "package.json" ] && [ ! -d "node_modules" ]; then
    echo "Instalando dependencias Node.js..."
    npm ci
    npm run build
elif [ -f "package.json" ]; then
    echo -e "${GREEN}✅ Dependencias Node.js ya instaladas${NC}"
fi
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 7: CONFIGURAR .ENV"
echo "════════════════════════════════════════════════════════════════"

if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️ Archivo .env no existe${NC}"
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✅ Archivo .env creado desde .env.example${NC}"
        echo -e "${YELLOW}⚠️ IMPORTANTE: Edita el archivo .env con tus credenciales${NC}"
        echo "  nano .env"
    else
        echo -e "${RED}❌ No existe .env.example${NC}"
        echo "Crea el archivo .env manualmente"
    fi
else
    echo -e "${GREEN}✅ Archivo .env existe${NC}"
fi

# Verificar APP_KEY
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generando APP_KEY..."
    php artisan key:generate
else
    echo -e "${GREEN}✅ APP_KEY configurado${NC}"
fi
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 8: CONFIGURAR PERMISOS"
echo "════════════════════════════════════════════════════════════════"

sudo chown -R ec2-user:ec2-user "$APP_DIR"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Crear enlace de storage
if [ ! -L "public/storage" ]; then
    php artisan storage:link || true
fi

echo -e "${GREEN}✅ Permisos configurados${NC}"
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 9: EJECUTAR MIGRACIONES Y SEEDERS"
echo "════════════════════════════════════════════════════════════════"

# Verificar conexión a base de datos
if php artisan migrate:status &>/dev/null; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
    echo "Ejecutando seeders..."
    php artisan db:seed --force
else
    echo -e "${YELLOW}⚠️ No se puede conectar a la base de datos${NC}"
    echo "Verifica la configuración en .env:"
    echo "  DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
fi
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 10: OPTIMIZAR APLICACIÓN"
echo "════════════════════════════════════════════════════════════════"

php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}✅ Aplicación optimizada${NC}"
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 11: REINICIAR SERVICIOS"
echo "════════════════════════════════════════════════════════════════"

sudo systemctl restart $PHP_FPM_SERVICE
sudo systemctl restart nginx

echo -e "${GREEN}✅ Servicios reiniciados${NC}"
echo ""

echo "════════════════════════════════════════════════════════════════"
echo "  PASO 12: VERIFICACIÓN FINAL"
echo "════════════════════════════════════════════════════════════════"

# Verificar servicios
check_service $PHP_FPM_SERVICE
check_service nginx

# Probar aplicación
echo "Probando aplicación..."
if curl -s http://localhost > /dev/null; then
    echo -e "${GREEN}✅ Aplicación responde en http://localhost${NC}"
else
    echo -e "${RED}❌ Aplicación no responde${NC}"
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  ✅ DESPLIEGUE COMPLETADO"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "📋 Verificaciones finales:"
echo "  1. Abre https://emotive.g3nia.com en tu navegador"
echo "  2. Verifica que el sitio carga correctamente"
echo "  3. Prueba el login y funcionalidades principales"
echo ""
echo "📝 Si hay problemas, revisa los logs:"
echo "  tail -f /var/www/laravel/storage/logs/laravel.log"
echo "  tail -f /var/log/nginx/error.log"
echo ""

