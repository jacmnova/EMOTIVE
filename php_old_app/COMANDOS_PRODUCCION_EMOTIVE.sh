#!/bin/bash

# Script para aplicar correcciones E.MO.TI.VE en producción
# Ejecutar en el servidor después de hacer git pull

echo "🚀 Aplicando correcciones E.MO.TI.VE..."

# 1. Ir al directorio del proyecto
cd /var/www/html  # Ajusta según tu configuración

# 2. Actualizar código (si no lo hiciste con git pull)
# git pull origin main

# 3. Instalar dependencias si es necesario
composer install --no-dev --optimize-autoloader

# 4. ⚠️ CRÍTICO: Actualizar relaciones pregunta-variable
echo "📊 Actualizando relaciones pregunta-variable..."
php artisan actualizar:relaciones-preguntas

# 5. Limpiar caché
echo "🧹 Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 6. Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 7. Verificar permisos
echo "🔐 Verificando permisos..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Ajusta según tu usuario

# 8. Reiniciar servicios (opcional, solo si es necesario)
echo "🔄 Reiniciando servicios..."
# Identificar versión de PHP-FPM
PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
if systemctl list-units --type=service | grep -q "php${PHP_VERSION}-fpm"; then
    echo "   Reiniciando php${PHP_VERSION}-fpm..."
    sudo systemctl restart php${PHP_VERSION}-fpm
elif systemctl list-units --type=service | grep -q "php-fpm"; then
    echo "   Reiniciando php-fpm..."
    sudo systemctl restart php-fpm
else
    echo "   ⚠️  No se encontró servicio PHP-FPM, reiniciando Nginx..."
    sudo systemctl restart nginx
fi

echo "✅ Correcciones aplicadas correctamente!"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Verifica que las relaciones se actualizaron correctamente"
echo "   2. Pide a un usuario que genere un nuevo reporte"
echo "   3. Verifica que los valores coincidan con el CSV"

