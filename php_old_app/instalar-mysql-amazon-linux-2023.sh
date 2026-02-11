#!/usr/bin/env bash

# Script para instalar MySQL en Amazon Linux 2023
# Ejecutar: chmod +x instalar-mysql-amazon-linux-2023.sh && ./instalar-mysql-amazon-linux-2023.sh

echo "🗄️ Instalando MySQL en Amazon Linux 2023..."

# Opción 1: Intentar instalar desde el repositorio de comunidad
echo "📦 Intentando instalar MySQL desde repositorio de comunidad..."
sudo dnf install -y @mysql || echo "⚠️ @mysql no disponible"

# Opción 2: Si lo anterior falla, usar repositorio de Oracle MySQL Community
if ! command -v mysql &> /dev/null; then
    echo "📦 Instalando MySQL desde repositorio oficial..."
    
    # Descargar e instalar el repositorio de MySQL
    sudo dnf install -y https://dev.mysql.com/get/mysql80-community-release-el9-1.noarch.rpm || \
    sudo dnf install -y https://dev.mysql.com/get/mysql80-community-release-el8-7.noarch.rpm || \
    echo "⚠️ No se pudo instalar repositorio MySQL"
    
    # Importar GPG key
    sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022
    
    # Deshabilitar módulos que pueden causar conflicto
    sudo dnf module disable mysql -y || true
    
    # Instalar MySQL Server
    sudo dnf install -y mysql-community-server
fi

# Opción 3: Si aún falla, instalar MariaDB desde repositorio alternativo
if ! command -v mysql &> /dev/null && ! command -v mariadb &> /dev/null; then
    echo "📦 Intentando instalar MariaDB..."
    
    # Agregar repositorio de MariaDB
    sudo tee /etc/yum.repos.d/mariadb.repo <<EOF
[mariadb]
name = MariaDB
baseurl = https://downloads.mariadb.com/MariaDB/mariadb-10.11/repo/rhel/9/x86_64
gpgkey = https://downloads.mariadb.com/MariaDB/MariaDB-Server-GPG-KEY
gpgcheck = 1
EOF
    
    sudo dnf install -y MariaDB-server MariaDB-client || echo "⚠️ MariaDB no disponible"
fi

# Habilitar e iniciar servicio
if systemctl list-unit-files | grep -q mysqld; then
    echo "✅ MySQL detectado, habilitando servicio..."
    sudo systemctl enable mysqld
    sudo systemctl start mysqld
    sudo systemctl status mysqld --no-pager
elif systemctl list-unit-files | grep -q mariadb; then
    echo "✅ MariaDB detectado, habilitando servicio..."
    sudo systemctl enable mariadb
    sudo systemctl start mariadb
    sudo systemctl status mariadb --no-pager
else
    echo "⚠️ No se encontró ningún servicio de base de datos"
    echo "Busca manualmente: systemctl list-unit-files | grep -i mysql"
    echo "Busca manualmente: systemctl list-unit-files | grep -i mariadb"
fi

# Obtener contraseña temporal si existe
if [ -f /var/log/mysqld.log ]; then
    TEMP_PASS=$(sudo grep 'temporary password' /var/log/mysqld.log | tail -1 | awk '{print $NF}')
    if [ ! -z "$TEMP_PASS" ]; then
        echo ""
        echo "🔑 Contraseña temporal de MySQL (si aplica): $TEMP_PASS"
        echo "⚠️ Cambia esta contraseña inmediatamente con: sudo mysql_secure_installation"
    fi
fi

echo ""
echo "✅ Instalación de MySQL/MariaDB completada (o intentada)"
echo ""
echo "📝 Próximos pasos:"
echo "1. Verificar servicio: sudo systemctl status mysqld (o mariadb)"
echo "2. Configurar MySQL: sudo mysql_secure_installation"
echo "3. Crear base de datos: sudo mysql -u root -p"

