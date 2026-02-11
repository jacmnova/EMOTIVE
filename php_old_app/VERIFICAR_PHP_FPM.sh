#!/bin/bash

# Script para verificar y reiniciar PHP-FPM

echo "🔍 Verificando versión de PHP..."
php -v

echo ""
echo "🔍 Buscando servicios PHP-FPM disponibles..."

# Verificar diferentes versiones de PHP-FPM
for version in 8.1 8.2 8.3 8.4 7.4; do
    if systemctl list-units --type=service | grep -q "php${version}-fpm"; then
        echo "✅ Encontrado: php${version}-fpm"
        echo "   Para reiniciar: sudo systemctl restart php${version}-fpm"
    fi
done

echo ""
echo "🔍 Verificando procesos PHP-FPM en ejecución..."
ps aux | grep php-fpm | grep -v grep

echo ""
echo "🔍 Verificando si PHP-FPM está corriendo como servicio de otro nombre..."
systemctl list-units --type=service | grep -i php

echo ""
echo "💡 Si no encuentras el servicio, prueba:"
echo "   sudo service php-fpm restart"
echo "   sudo service php8.1-fpm restart"
echo "   sudo /etc/init.d/php-fpm restart"

