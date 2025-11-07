#!/usr/bin/env bash
set -euo pipefail

# Script para verificar error 500
# Ejecutar: chmod +x verificar-error-500.sh && ./verificar-error-500.sh

echo "🔍 Verificando error 500..."
echo ""

APP_DIR="/var/www/laravel"
cd "$APP_DIR" || exit 1

# 1. Crear directorio de logs si no existe
echo "📁 Creando directorio de logs..."
mkdir -p storage/logs
chmod -R 775 storage
sudo chown -R ec2-user:ec2-user storage
echo "✅ Directorio de logs creado"
echo ""

# 2. Verificar .env
echo "📝 Verificando .env..."
if [ ! -f ".env" ]; then
    echo "❌ Archivo .env no existe"
    exit 1
fi

# Verificar APP_KEY
if ! grep -q "APP_KEY=base64:" .env; then
    echo "⚠️ APP_KEY no configurado, generando..."
    php artisan key:generate
fi

# Habilitar debug temporalmente para ver el error
echo "🔍 Habilitando APP_DEBUG temporalmente..."
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
php artisan config:clear
echo "✅ Debug habilitado (recarga la página para ver el error)"
echo ""

# 3. Verificar permisos
echo "🔐 Verificando permisos..."
sudo chown -R ec2-user:ec2-user /var/www/laravel
chmod -R 775 storage bootstrap/cache
echo "✅ Permisos verificados"
echo ""

# 4. Ver logs de PHP-FPM
echo "📋 Logs de PHP-FPM:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
sudo tail -30 /var/log/php-fpm/error.log 2>/dev/null || echo "No hay logs de PHP-FPM"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 5. Ver logs de Laravel (si existen)
echo "📋 Logs de Laravel:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ -f "storage/logs/laravel.log" ]; then
    tail -50 storage/logs/laravel.log
else
    echo "⚠️ Archivo de log no existe aún"
    echo "Se creará cuando recargues la página"
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 6. Verificar conexión a base de datos
echo "🗄️ Verificando conexión a base de datos..."
if php artisan migrate:status &>/dev/null; then
    echo "✅ Conexión a base de datos OK"
else
    echo "❌ Error de conexión a base de datos"
    echo "Verifica las credenciales en .env:"
    grep "^DB_" .env
fi
echo ""

# 7. Probar artisan
echo "🧪 Probando artisan..."
php artisan --version
echo ""

# 8. Verificar vendor
echo "📦 Verificando dependencias..."
if [ -d "vendor" ]; then
    echo "✅ Dependencias instaladas"
else
    echo "❌ Dependencias no instaladas"
    echo "Ejecuta: composer install --no-dev --optimize-autoloader"
fi
echo ""

echo "✅ Verificación completada"
echo ""
echo "📝 Próximos pasos:"
echo "1. Recarga la página https://emotive.g3nia.com/login"
echo "2. Deberías ver el error detallado (porque APP_DEBUG=true)"
echo "3. Comparte el error que ves en el navegador"
echo "4. O ejecuta: tail -f storage/logs/laravel.log (en otra terminal)"
echo ""

