#!/bin/bash

# Script para diagnosticar el error 500 del relatorio en EC2
# Uso: ./diagnosticar-error-relatorio.sh

echo "=========================================="
echo "🔍 DIAGNÓSTICO DE ERROR 500 - RELATORIO"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Ir al directorio del proyecto
cd /var/www/laravel/EMOTIVE || {
    echo -e "${RED}❌ Error: No se pudo acceder al directorio del proyecto${NC}"
    exit 1
}

echo -e "${GREEN}✓ Directorio del proyecto: $(pwd)${NC}"
echo ""

# 1. Verificar permisos de logs
echo "=========================================="
echo "1️⃣ Verificando permisos de logs..."
echo "=========================================="
if [ -f storage/logs/laravel.log ]; then
    echo -e "${GREEN}✓ Archivo de log existe${NC}"
    ls -la storage/logs/laravel.log
else
    echo -e "${YELLOW}⚠ Archivo de log no existe, creándolo...${NC}"
    touch storage/logs/laravel.log
    chmod 664 storage/logs/laravel.log
    chown www-data:www-data storage/logs/laravel.log 2>/dev/null || chown ec2-user:ec2-user storage/logs/laravel.log
fi
echo ""

# 2. Buscar errores recientes del relatorio
echo "=========================================="
echo "2️⃣ Buscando errores recientes de relatorioShow..."
echo "=========================================="
if [ -f storage/logs/laravel.log ]; then
    echo -e "${YELLOW}Últimos errores relacionados con relatorioShow:${NC}"
    tail -500 storage/logs/laravel.log | grep -A 30 -i "relatorioShow\|relatorio\.show" | tail -50
    echo ""
    
    echo -e "${YELLOW}Últimas excepciones/errores:${NC}"
    tail -200 storage/logs/laravel.log | grep -A 20 -i "exception\|error" | tail -60
else
    echo -e "${RED}❌ No se encontró el archivo de log${NC}"
fi
echo ""

# 3. Buscar errores específicos de formulario_id=1 y usuario_id=5
echo "=========================================="
echo "3️⃣ Buscando errores con formulario_id=1 y usuario_id=5..."
echo "=========================================="
if [ -f storage/logs/laravel.log ]; then
    tail -1000 storage/logs/laravel.log | grep -E "formulario_id.*1|usuario_id.*5" | tail -20
else
    echo -e "${YELLOW}⚠ No se encontró el archivo de log${NC}"
fi
echo ""

# 4. Verificar errores de validación
echo "=========================================="
echo "4️⃣ Buscando errores de validación..."
echo "=========================================="
if [ -f storage/logs/laravel.log ]; then
    tail -500 storage/logs/laravel.log | grep -A 15 -i "validation\|validación" | tail -30
else
    echo -e "${YELLOW}⚠ No se encontró el archivo de log${NC}"
fi
echo ""

# 5. Verificar errores de Nginx
echo "=========================================="
echo "5️⃣ Verificando errores de Nginx..."
echo "=========================================="
if [ -f /var/log/nginx/error.log ]; then
    echo -e "${YELLOW}Últimos errores de Nginx relacionados con meurelatorio:${NC}"
    sudo tail -50 /var/log/nginx/error.log | grep -i "meurelatorio" | tail -10
    if [ $? -ne 0 ]; then
        echo -e "${GREEN}✓ No se encontraron errores de Nginx relacionados${NC}"
    fi
else
    echo -e "${YELLOW}⚠ No se encontró el log de Nginx${NC}"
fi
echo ""

# 6. Verificar que los datos existen en la base de datos
echo "=========================================="
echo "6️⃣ Verificando datos en la base de datos..."
echo "=========================================="
echo -e "${YELLOW}Verificando si existen formulario_id=1 y usuario_id=5...${NC}"
php artisan tinker --execute="
    \$formulario = \App\Models\Formulario::find(1);
    \$usuario = \App\Models\User::find(5);
    echo 'Formulario ID 1: ' . (\$formulario ? 'EXISTE (' . \$formulario->nome . ')' : 'NO EXISTE') . PHP_EOL;
    echo 'Usuario ID 5: ' . (\$usuario ? 'EXISTE (' . \$usuario->name . ')' : 'NO EXISTE') . PHP_EOL;
    if (\$formulario && \$usuario) {
        \$respostas = \App\Models\Resposta::where('user_id', 5)
            ->whereIn('pergunta_id', \$formulario->perguntas->pluck('id'))
            ->count();
        echo 'Respuestas del usuario 5 para formulario 1: ' . \$respostas . PHP_EOL;
        echo 'Total de preguntas del formulario: ' . \$formulario->perguntas->count() . PHP_EOL;
    }
" 2>&1 | tail -10
echo ""

# 7. Verificar configuración de rutas
echo "=========================================="
echo "7️⃣ Verificando configuración de rutas..."
echo "=========================================="
php artisan route:list | grep -E "relatorio|meurelatorio" | head -5
echo ""

# 8. Probar la ruta directamente
echo "=========================================="
echo "8️⃣ Probando la ruta directamente..."
echo "=========================================="
echo -e "${YELLOW}Ejecutando prueba de la ruta...${NC}"
php artisan tinker --execute="
    try {
        \$request = new \Illuminate\Http\Request();
        \$request->merge(['formulario_id' => 1, 'usuario_id' => 5]);
        \$controller = new \App\Http\Controllers\DadosController();
        \$result = \$controller->relatorioShow(\$request);
        echo '✓ La ruta se ejecutó correctamente' . PHP_EOL;
    } catch (\Exception \$e) {
        echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
        echo 'Archivo: ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
    }
" 2>&1 | tail -20
echo ""

# 9. Resumen
echo "=========================================="
echo "📋 RESUMEN"
echo "=========================================="
echo -e "${GREEN}✓ Diagnóstico completado${NC}"
echo ""
echo "Para ver los logs en tiempo real, ejecuta:"
echo "  tail -f /var/www/laravel/EMOTIVE/storage/logs/laravel.log"
echo ""
echo "Para probar la URL directamente:"
echo "  curl 'http://localhost/meurelatorio/show?formulario_id=1&usuario_id=5'"
echo ""

