#!/bin/bash

# Script para limpar caché de traducción en el servidor

echo "Limpando caché de configuración..."
php artisan config:clear

echo "Limpando caché de rutas..."
php artisan route:clear

echo "Limpando caché de vistas..."
php artisan view:clear

echo "Limpando todo el caché..."
php artisan cache:clear

echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Caché limpiado e otimizado!"

