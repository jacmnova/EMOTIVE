#!/usr/bin/env bash
set -euo pipefail

# Script para configurar SSL para emotive.fellipelli.com.br
# Uso: sudo ./configurar-ssl-emotive.sh

DOMAIN="emotive.fellipelli.com.br"
APP_ROOT="/var/www/laravel/public"

echo "🔒 Configurando SSL para $DOMAIN..."
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que el dominio apunta al servidor
echo "📡 Verificando DNS..."
DOMAIN_IP=$(dig +short $DOMAIN | tail -n1)
SERVER_IP=$(curl -s ifconfig.me || curl -s ipinfo.io/ip)

if [ -z "$DOMAIN_IP" ]; then
    echo -e "${RED}❌ Error: No se pudo resolver $DOMAIN${NC}"
    echo "   Asegúrate de que el registro DNS esté configurado correctamente"
    exit 1
fi

echo "   Dominio resuelve a: $DOMAIN_IP"
echo "   IP del servidor: $SERVER_IP"

if [ "$DOMAIN_IP" != "$SERVER_IP" ]; then
    echo -e "${YELLOW}⚠️  ADVERTENCIA: El dominio no apunta a este servidor${NC}"
    echo "   El dominio apunta a: $DOMAIN_IP"
    echo "   Este servidor tiene IP: $SERVER_IP"
    echo ""
    read -p "¿Continuar de todos modos? (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
else
    echo -e "${GREEN}✅ DNS configurado correctamente${NC}"
fi
echo ""

# Detectar sistema operativo
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_ID="$ID"
else
    echo -e "${RED}❌ No se pudo detectar el sistema operativo${NC}"
    exit 1
fi

# Determinar ruta de configuración de Nginx
if [[ "$OS_ID" == "ubuntu" ]] || [[ "$OS_ID" == "debian" ]]; then
    NGINX_CONF="/etc/nginx/sites-available/laravel"
    NGINX_ENABLED="/etc/nginx/sites-enabled/laravel"
elif [[ "$OS_ID" == "amzn" ]] || [[ "$OS_ID" == "amazon" ]]; then
    NGINX_CONF="/etc/nginx/conf.d/laravel.conf"
    NGINX_ENABLED="$NGINX_CONF"
else
    NGINX_CONF="/etc/nginx/conf.d/laravel.conf"
    NGINX_ENABLED="$NGINX_CONF"
fi

# Verificar que Nginx está instalado
if ! command -v nginx &> /dev/null; then
    echo -e "${RED}❌ Nginx no está instalado${NC}"
    exit 1
fi

# Verificar que Certbot está instalado
if ! command -v certbot &> /dev/null; then
    echo -e "${YELLOW}⚠️  Certbot no está instalado. Instalando...${NC}"
    
    if [[ "$OS_ID" == "ubuntu" ]] || [[ "$OS_ID" == "debian" ]]; then
        sudo apt update
        sudo apt install -y certbot python3-certbot-nginx
    elif [[ "$OS_ID" == "amzn" ]] || [[ "$OS_ID" == "amazon" ]]; then
        sudo dnf install -y certbot python3-certbot-nginx || sudo yum install -y certbot python3-certbot-nginx
    else
        echo -e "${RED}❌ No se pudo instalar Certbot automáticamente${NC}"
        echo "   Instala Certbot manualmente para tu distribución"
        exit 1
    fi
fi

# Verificar que existe configuración de Nginx
if [ ! -f "$NGINX_CONF" ]; then
    echo -e "${YELLOW}⚠️  Configuración de Nginx no encontrada. Creando configuración básica...${NC}"
    
    # Buscar socket de PHP-FPM
    PHP_SOCKET=$(sudo find /var/run /run -name "*php*.sock" 2>/dev/null | head -1)
    if [ -z "$PHP_SOCKET" ]; then
        PHP_SOCKET="/var/run/php-fpm/php-fpm.sock"
    fi
    
    sudo tee "$NGINX_CONF" > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $APP_ROOT;

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
        fastcgi_pass unix:$PHP_SOCKET;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    
    # Habilitar sitio (solo para Ubuntu/Debian)
    if [[ "$OS_ID" == "ubuntu" ]] || [[ "$OS_ID" == "debian" ]]; then
        if [ ! -L "$NGINX_ENABLED" ]; then
            sudo ln -s "$NGINX_CONF" "$NGINX_ENABLED"
        fi
    fi
    
    # Verificar y recargar Nginx
    sudo nginx -t
    sudo systemctl reload nginx
    echo -e "${GREEN}✅ Configuración de Nginx creada${NC}"
fi

# Verificar que el server_name en Nginx coincide
CURRENT_SERVER_NAME=$(sudo grep -E "^\s*server_name" "$NGINX_CONF" | head -1 | sed 's/.*server_name\s*\([^;]*\);.*/\1/' | xargs)

if [ "$CURRENT_SERVER_NAME" != "$DOMAIN" ]; then
    echo -e "${YELLOW}⚠️  Actualizando server_name en configuración de Nginx...${NC}"
    echo "   De: $CURRENT_SERVER_NAME"
    echo "   A: $DOMAIN"
    sudo sed -i "s/server_name.*/server_name $DOMAIN;/" "$NGINX_CONF"
    sudo nginx -t
    sudo systemctl reload nginx
    echo -e "${GREEN}✅ server_name actualizado${NC}"
fi

echo ""
echo "🔒 Obteniendo certificado SSL con Certbot..."
echo ""

# Obtener certificado SSL
sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email desenvolvedor@fellipelli.com.br --redirect

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Certificado SSL instalado correctamente${NC}"
    echo ""
    echo "📋 Verificaciones:"
    echo "  1. Abre https://$DOMAIN en tu navegador"
    echo "  2. Verifica que el certificado SSL esté activo (candado verde)"
    echo "  3. Verifica que HTTP redirige a HTTPS"
    echo ""
    echo "🔄 Para renovar automáticamente, Certbot ya está configurado"
    echo "   El certificado se renovará automáticamente antes de expirar"
else
    echo ""
    echo -e "${RED}❌ Error al obtener el certificado SSL${NC}"
    echo ""
    echo "📝 Posibles causas:"
    echo "  1. El dominio no apunta a este servidor"
    echo "  2. El puerto 80 no está abierto en el firewall"
    echo "  3. Nginx no está configurado correctamente"
    echo ""
    echo "🔍 Verifica los logs:"
    echo "  sudo tail -f /var/log/letsencrypt/letsencrypt.log"
    exit 1
fi

echo ""
echo "✅ Configuración SSL completada para $DOMAIN"

