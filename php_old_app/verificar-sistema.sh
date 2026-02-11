#!/usr/bin/env bash

# Script para verificar el sistema operativo en EC2

echo "🔍 Verificando sistema operativo..."

# Detectar el SO
if [ -f /etc/os-release ]; then
    . /etc/os-release
    echo "Sistema operativo: $PRETTY_NAME"
    echo "ID: $ID"
    echo "Versión: $VERSION_ID"
    
    if [[ "$ID" == "ubuntu" ]]; then
        echo "✅ Sistema: Ubuntu - Usa 'apt'"
        echo ""
        echo "Para actualizar: sudo apt update"
        echo "Para instalar: sudo apt install <paquete>"
    elif [[ "$ID" == "amzn" ]] || [[ "$ID" == "amazon" ]]; then
        echo "✅ Sistema: Amazon Linux - Usa 'yum' o 'dnf'"
        echo ""
        echo "Para actualizar: sudo yum update -y"
        echo "Para instalar: sudo yum install -y <paquete>"
    elif [[ "$ID" == "rhel" ]] || [[ "$ID" == "centos" ]]; then
        echo "✅ Sistema: RedHat/CentOS - Usa 'yum' o 'dnf'"
    else
        echo "⚠️ Sistema no identificado completamente"
    fi
else
    echo "⚠️ No se pudo detectar el sistema operativo"
fi

echo ""
echo "📋 Información adicional:"
echo "Usuario actual: $(whoami)"
echo "PATH: $PATH"
echo ""
echo "Verificar si apt existe:"
which apt || echo "❌ apt NO está instalado"
echo ""
echo "Verificar si yum existe:"
which yum || echo "❌ yum NO está instalado"
echo ""
echo "Verificar si dnf existe:"
which dnf || echo "❌ dnf NO está instalado"

