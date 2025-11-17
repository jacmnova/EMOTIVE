#!/usr/bin/env bash
set -euo pipefail

# Script para configurar Nginx con SSL para emotive.fellipelli.com.br
# Uso: sudo ./configurar-nginx-ssl.sh

DOMAIN="emotive.fellipelli.com.br"
APP_ROOT="/var/www/laravel/public"

echo "🌐 Configurando Nginx para $DOMAIN..."

# Detectar sistema operativo
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_ID="$ID"
else
    echo "❌ No se pudo detectar el sistema operativo"
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

# Crear configuración inicial de Nginx (HTTP)
echo "📝 Creando configuración de Nginx..."
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
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
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

# Verificar configuración
echo "🔍 Verificando configuración de Nginx..."
sudo nginx -t

if [ $? -ne 0 ]; then
    echo "❌ Error en la configuración de Nginx"
    exit 1
fi

# Recargar Nginx
echo "🔄 Recargando Nginx..."
sudo systemctl reload nginx

echo "✅ Configuración HTTP completada"
echo ""
echo "📋 Próximos pasos:"
echo "1. Asegúrate de que el dominio $DOMAIN apunta a la IP de este servidor"
echo "2. Verifica DNS: dig $DOMAIN o nslookup $DOMAIN"
echo "3. Ejecuta: sudo certbot --nginx -d $DOMAIN"
echo "4. Certbot configurará automáticamente SSL"

