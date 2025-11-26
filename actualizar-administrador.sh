#!/bin/bash

# Script para actualizar el email y nombre de un administrador
# Uso: ./actualizar-administrador.sh

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Actualizar Administrador"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Ejecutar el comando Artisan
php artisan admin:actualizar \
    --email-viejo=wheelkorner@gmail.com \
    --email-nuevo=jose@gafi.com.br \
    --nombre-nuevo="Jose A Cordero"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Proceso completado"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

