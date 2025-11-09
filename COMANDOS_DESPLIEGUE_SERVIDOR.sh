#!/bin/bash

# 🚀 Script de Despliegue - Actualización de Relaciones CSV ALE
# Ejecutar en el servidor después de hacer git pull

echo "🚀 Iniciando despliegue..."

# 1. Limpiar caché
echo "📦 Limpiando caché..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Actualizar relaciones según CSV ALE
echo "🔄 Actualizando relaciones pregunta-variable según CSV ALE..."
php artisan actualizar:relaciones-ale

# 3. Verificar
echo "✅ Verificando relaciones..."
php artisan tinker --execute="
\$variaveis = \App\Models\Variavel::with('perguntas')->where('formulario_id', 1)->get();
echo '📊 RELACIONES ACTUALIZADAS:' . PHP_EOL;
foreach (\$variaveis as \$v) {
    \$tag = strtoupper(\$v->tag ?? '');
    if (in_array(\$tag, ['ASMO', 'REPR', 'DECI', 'EXEM', 'FAPS', 'EXTR'])) {
        echo \$tag . ': ' . \$v->perguntas->count() . ' preguntas' . PHP_EOL;
    }
}
"

echo ""
echo "✅ Despliegue completado!"
echo ""
echo "📋 Valores esperados:"
echo "  EXEM: 26 preguntas"
echo "  REPR: 26 preguntas"
echo "  DECI: 29 preguntas"
echo "  FAPS: 10 preguntas"
echo "  EXTR: 16 preguntas"
echo "  ASMO: 15 preguntas"

