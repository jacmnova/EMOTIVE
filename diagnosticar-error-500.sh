#!/usr/bin/env bash
set -euo pipefail

# Script para diagnosticar error 500
# Ejecutar: chmod +x diagnosticar-error-500.sh && ./diagnosticar-error-500.sh

echo "🔍 Diagnosticando error 500..."
echo ""

APP_DIR="/var/www/laravel"

# 1. Verificar que el directorio existe
if [ ! -d "$APP_DIR" ]; then
    echo "❌ Directorio $APP_DIR no existe"
    exit 1
fi

cd "$APP_DIR" || exit 1

# 2. Crear directorio de logs si no existe
echo "📁 Verificando directorio de logs..."
mkdir -p storage/logs
chmod -R 775 storage
echo "✅ Directorio de logs verificado"

# 3. Verificar permisos
echo "🔐 Verificando permisos..."
sudo chown -R ec2-user:ec2-user "$APP_DIR"
chmod -R 775 storage bootstrap/cache
echo "✅ Permisos verificados"

# 4. Verificar .env
echo "📝 Verificando archivo .env..."
if [ ! -f ".env" ]; then
    echo "❌ Archivo .env no existe"
    echo "Crea el archivo .env desde ENV_PRODUCCION.txt"
    exit 1
fi

# Verificar APP_KEY
if ! grep -q "APP_KEY=base64:" .env; then
    echo "⚠️ APP_KEY no configurado, generando..."
    php artisan key:generate
fi

# 5. Verificar conexión a base de datos
echo "🗄️ Verificando conexión a base de datos..."
if php artisan migrate:status &>/dev/null; then
    echo "✅ Conexión a base de datos OK"
else
    echo "❌ Error de conexión a base de datos"
    echo "Verifica las credenciales en .env:"
    grep "^DB_" .env
fi

# 6. Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cachés limpiados"

# 7. Verificar logs de Nginx
echo "📋 Últimos errores de Nginx:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
sudo tail -20 /var/log/nginx/error.log
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# 8. Verificar logs de PHP-FPM
echo ""
echo "📋 Últimos errores de PHP-FPM:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
sudo tail -20 /var/log/php-fpm/error.log 2>/dev/null || echo "No hay logs de PHP-FPM"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# 9. Verificar logs de Laravel (si existen)
echo ""
echo "📋 Logs de Laravel:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ -f "storage/logs/laravel.log" ]; then
    tail -50 storage/logs/laravel.log
else
    echo "⚠️ Archivo de log no existe aún"
    echo "Se creará cuando ocurra el próximo error"
fi
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# 10. Probar artisan
echo ""
echo "🧪 Probando artisan..."
if php artisan --version &>/dev/null; then
    echo "✅ Artisan funciona correctamente"
else
    echo "❌ Error al ejecutar artisan"
    php artisan --version
fi

# 11. Verificar APP_DEBUG
echo ""
echo "🔍 Configuración de debug:"
grep "APP_DEBUG" .env || echo "APP_DEBUG no configurado"

echo ""
echo "✅ Diagnóstico completado"
echo ""
echo "📝 Próximos pasos:"
echo "1. Revisa los logs de Nginx arriba para ver el error específico"
echo "2. Si APP_DEBUG=true, verás el error detallado en el navegador"
echo "3. Verifica las credenciales de la base de datos en .env"
echo "4. Asegúrate de que las migraciones se ejecutaron: php artisan migrate:status"

