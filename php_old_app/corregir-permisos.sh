#!/usr/bin/env bash
set -euo pipefail

# Script para corregir permisos de Laravel
# Ejecutar: chmod +x corregir-permisos.sh && sudo ./corregir-permisos.sh

echo "🔐 Corrigiendo permisos de Laravel..."
echo ""

APP_DIR="/var/www/laravel"

if [ ! -d "$APP_DIR" ]; then
    echo "❌ Directorio $APP_DIR no existe"
    exit 1
fi

cd "$APP_DIR" || exit 1

# Detectar usuario del servidor web
if id "apache" &>/dev/null; then
    WEB_USER="apache"
elif id "nginx" &>/dev/null; then
    WEB_USER="nginx"
elif id "www-data" &>/dev/null; then
    WEB_USER="www-data"
else
    WEB_USER="ec2-user"
fi

echo "Usuario del servidor web detectado: $WEB_USER"
echo ""

# 1. Cambiar propietario de todo el directorio
echo "📁 Cambiando propietario de archivos..."
sudo chown -R ec2-user:ec2-user "$APP_DIR"
echo "✅ Propietario cambiado a ec2-user"
echo ""

# 2. Crear directorios necesarios si no existen
echo "📁 Creando directorios necesarios..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
echo "✅ Directorios creados"
echo ""

# 3. Dar permisos a storage y bootstrap/cache
echo "🔐 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Permisos específicos para directorios
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

# Permisos especiales para algunos directorios críticos
chmod -R 775 storage/framework
chmod -R 775 storage/logs
echo "✅ Permisos configurados"
echo ""

# 4. Si el servidor web necesita acceso, agregar al grupo
if [ "$WEB_USER" != "ec2-user" ]; then
    echo "👥 Agregando $WEB_USER al grupo ec2-user..."
    sudo usermod -a -G ec2-user "$WEB_USER" 2>/dev/null || true
    echo "✅ Usuario agregado al grupo"
    echo ""
fi

# 5. Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cachés limpiados"
echo ""

# 6. Verificar permisos
echo "✅ Verificación de permisos:"
ls -ld storage
ls -ld storage/framework
ls -ld storage/framework/views
ls -ld bootstrap/cache
echo ""

# 7. Probar escritura
echo "🧪 Probando escritura..."
touch storage/framework/views/test.txt 2>/dev/null && rm storage/framework/views/test.txt && echo "✅ Escritura funciona correctamente" || echo "❌ Error de escritura"
echo ""

echo "✅ Permisos corregidos"
echo ""
echo "📝 Si aún hay problemas, ejecuta:"
echo "  sudo chown -R ec2-user:$WEB_USER storage bootstrap/cache"
echo "  sudo chmod -R 775 storage bootstrap/cache"
echo ""

