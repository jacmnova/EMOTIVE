#!/bin/bash

# Script para actualizar rangos B, M, A en producción
# Uso: ./actualizar-rangos-produccion.sh

set -e  # Salir si hay algún error

echo "🚀 Iniciando actualización de rangos en producción..."
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio del proyecto.${NC}"
    exit 1
fi

# 1. Backup
echo -e "${YELLOW}📦 Creando backup de la tabla variaveis...${NC}"
BACKUP_FILE="backup_variaveis_$(date +%Y%m%d_%H%M%S).sql"

# Intentar hacer backup con mysqldump si está disponible
if command -v mysqldump &> /dev/null; then
    DB_NAME=$(php artisan tinker --execute="echo config('database.connections.mysql.database');" 2>/dev/null | tail -1)
    DB_USER=$(php artisan tinker --execute="echo config('database.connections.mysql.username');" 2>/dev/null | tail -1)
    
    if [ ! -z "$DB_NAME" ] && [ ! -z "$DB_USER" ]; then
        echo "   Creando backup de $DB_NAME.variaveis..."
        mysqldump -u "$DB_USER" -p "$DB_NAME" variaveis > "$BACKUP_FILE" 2>/dev/null || {
            echo -e "${YELLOW}⚠️  No se pudo crear backup con mysqldump. Continuando sin backup...${NC}"
        }
    fi
else
    echo -e "${YELLOW}⚠️  mysqldump no está disponible. Se recomienda hacer backup manualmente.${NC}"
fi

# 2. Verificar que los comandos existen
echo ""
echo -e "${YELLOW}🔍 Verificando comandos disponibles...${NC}"
php artisan list | grep -q "emotive:actualizar-todos-rangos" || {
    echo -e "${RED}❌ Error: El comando emotive:actualizar-todos-rangos no está disponible.${NC}"
    echo "   Asegúrate de que el código esté actualizado."
    exit 1
}

# 3. Mostrar estado actual
echo ""
echo -e "${YELLOW}📊 Estado actual de los rangos:${NC}"
php artisan emotive:calcular-rangos-generales 1 --no-interaction 2>/dev/null || true

# 4. Confirmar antes de continuar
echo ""
read -p "¿Deseas continuar con la actualización? (s/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${YELLOW}Operación cancelada.${NC}"
    exit 0
fi

# 5. Actualizar rangos
echo ""
echo -e "${GREEN}🔄 Actualizando rangos...${NC}"
php artisan emotive:actualizar-todos-rangos

# 6. Verificar resultado
echo ""
echo -e "${YELLOW}✅ Verificando actualización...${NC}"
php artisan emotive:comparar-csv 2>/dev/null || true

# 7. Limpiar cache
echo ""
echo -e "${YELLOW}🧹 Limpiando cache...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo -e "${GREEN}✨ Proceso completado!${NC}"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Verificar que los relatorios se generen correctamente"
echo "   2. Probar con un usuario de prueba"
echo "   3. Monitorear logs por errores"
echo ""
if [ -f "$BACKUP_FILE" ]; then
    echo -e "${GREEN}💾 Backup guardado en: $BACKUP_FILE${NC}"
fi

