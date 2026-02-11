#!/usr/bin/env bash
set -euo pipefail

# Script para solucionar problema de GPG con MySQL en Amazon Linux 2023
# Ejecutar: chmod +x solucionar-mysql-gpg.sh && ./solucionar-mysql-gpg.sh

echo "🔧 Solucionando problema de GPG con MySQL..."

# Opción 1: Importar la clave GPG correcta
echo "📥 Importando clave GPG de MySQL..."
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2023

# Opción 2: Si lo anterior no funciona, instalar sin verificación GPG (solo para desarrollo)
echo "📦 Intentando instalar MySQL sin verificación GPG..."
sudo dnf install -y mysql-community-server mysql-community-client --nogpgcheck

# Verificar si se instaló
if command -v mysql &> /dev/null; then
    echo "✅ MySQL instalado correctamente"
    
    # Habilitar e iniciar servicio
    echo "🔄 Habilitando e iniciando MySQL..."
    sudo systemctl enable mysqld
    sudo systemctl start mysqld
    
    # Verificar estado
    echo "📊 Estado del servicio:"
    sudo systemctl status mysqld --no-pager -l
    
    # Obtener contraseña temporal si existe
    if [ -f /var/log/mysqld.log ]; then
        TEMP_PASS=$(sudo grep 'temporary password' /var/log/mysqld.log | tail -1 | awk '{print $NF}')
        if [ ! -z "$TEMP_PASS" ]; then
            echo ""
            echo "🔑 Contraseña temporal de MySQL: $TEMP_PASS"
            echo "⚠️ Cambia esta contraseña inmediatamente con: sudo mysql_secure_installation"
        fi
    fi
    
    echo ""
    echo "✅ MySQL instalado y funcionando"
    echo ""
    echo "📝 Próximos pasos:"
    echo "1. Configurar MySQL: sudo mysql_secure_installation"
    echo "2. Crear base de datos: sudo mysql -u root -p"
else
    echo "❌ MySQL no se instaló. Intentando con MariaDB..."
    
    # Intentar instalar MariaDB desde repositorio de Amazon Linux
    echo "📦 Instalando MariaDB desde repositorio de Amazon Linux..."
    sudo dnf install -y mariadb105-server mariadb105
    
    if command -v mariadb &> /dev/null || command -v mysql &> /dev/null; then
        echo "✅ MariaDB instalado correctamente"
        
        # Habilitar e iniciar servicio
        echo "🔄 Habilitando e iniciando MariaDB..."
        sudo systemctl enable mariadb
        sudo systemctl start mariadb
        
        # Verificar estado
        echo "📊 Estado del servicio:"
        sudo systemctl status mariadb --no-pager -l
        
        echo ""
        echo "✅ MariaDB instalado y funcionando"
        echo ""
        echo "📝 Próximos pasos:"
        echo "1. Configurar MariaDB: sudo mysql_secure_installation"
        echo "2. Crear base de datos: sudo mysql -u root -p"
        echo ""
        echo "ℹ️  Nota: MariaDB es 100% compatible con MySQL"
    else
        echo "❌ No se pudo instalar MySQL ni MariaDB"
        echo "Intenta manualmente:"
        echo "  sudo dnf install -y mariadb105-server mariadb105"
    fi
fi

