# ⚠️ Solución: "No match for argument: mariadb-server"

## 🔍 El Problema

En Amazon Linux 2023, el paquete puede tener un nombre diferente o puede requerir configuración adicional. El nombre del paquete puede variar.

## ✅ Solución Inmediata

Ejecuta estos comandos para instalar MySQL/MariaDB:

### Opción 1: Instalar MySQL directamente (Recomendado)

```bash
sudo dnf install -y mysql mysql-server
sudo systemctl enable mysqld
sudo systemctl start mysqld
```

### Opción 2: Intentar instalar MariaDB

```bash
# Intentar mariadb (sin -server)
sudo dnf install -y mariadb mariadb-server

# O si eso no funciona, buscar paquetes disponibles
dnf search mariadb
dnf search mysql
```

### Opción 3: Usar repositorio adicional (si es necesario)

```bash
# Instalar MySQL desde repositorio de comunidad
sudo dnf install -y @mysql
```

## 🔧 Verificar Paquetes Disponibles

Para ver qué paquetes de base de datos están disponibles:

```bash
# Buscar paquetes relacionados con MySQL/MariaDB
dnf search mysql
dnf search mariadb

# Listar paquetes disponibles
dnf list available | grep -i mysql
dnf list available | grep -i mariadb
```

## 📋 Instalación Manual Completa

Si el script falló, continúa manualmente:

```bash
# 1. Instalar MySQL (Amazon Linux 2023)
sudo dnf install -y mysql mysql-server

# 2. Habilitar e iniciar MySQL
sudo systemctl enable mysqld
sudo systemctl start mysqld

# 3. Verificar que está corriendo
sudo systemctl status mysqld

# 4. Obtener contraseña temporal (si aplica)
sudo grep 'temporary password' /var/log/mysqld.log

# 5. Configurar MySQL
sudo mysql_secure_installation

# 6. Conectarse a MySQL
sudo mysql -u root -p
```

## 🚀 Continuar Después de Instalar MySQL

Una vez instalado MySQL, continúa con:

```bash
# Crear base de datos
sudo mysql -u root -p
```

Dentro de MySQL:
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## ⚙️ Configurar el Servicio Correctamente

Verifica el nombre del servicio:

```bash
# Listar servicios relacionados con MySQL/MariaDB
systemctl list-unit-files | grep -i mysql
systemctl list-unit-files | grep -i mariadb

# Habilitar el servicio correcto
sudo systemctl enable mysqld  # O el nombre que encuentres
sudo systemctl start mysqld
```

## 📝 Nota para el Script

El script `install-ec2-amazon-linux.sh` ya está actualizado para:
- ✅ Intentar múltiples nombres de paquete
- ✅ Detectar automáticamente el servicio instalado
- ✅ Continuar sin error si no se puede instalar automáticamente

Si ejecutas el script de nuevo, intentará instalar MySQL con diferentes nombres de paquete.

