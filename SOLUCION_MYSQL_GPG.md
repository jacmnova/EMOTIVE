# ⚠️ Solución: Error GPG check FAILED en MySQL

## 🔍 El Problema

Las claves GPG de MySQL no están correctamente instaladas o no coinciden con los paquetes que se están instalando.

## ✅ Solución Inmediata

Ejecuta estos comandos en orden:

### Paso 1: Importar las claves GPG correctas

```bash
# Importar clave GPG desde el repositorio oficial
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022

# También importar la clave alternativa
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql

# Verificar que se importaron
rpm -qa gpg-pubkey* | grep mysql
```

### Paso 2: Limpiar caché y reintentar

```bash
# Limpiar caché de paquetes
sudo dnf clean packages

# Reintentar instalación
sudo dnf install -y mysql-community-server
```

## 🔧 Si Aún Falla: Instalar Sin Verificación GPG (Solo si es necesario)

Si las claves GPG siguen fallando, puedes instalar temporalmente sin verificación GPG:

```bash
# Instalar sin verificación GPG (NO recomendado para producción)
sudo dnf install -y --nogpgcheck mysql-community-server
```

**⚠️ ADVERTENCIA**: Esto omite la verificación de seguridad. Solo úsalo si es absolutamente necesario y luego verifica la integridad de los paquetes.

## 🎯 Solución Completa (Recomendada)

```bash
# 1. Remover claves GPG antiguas
sudo rpm -e gpg-pubkey-3a79bd29 2>/dev/null || true

# 2. Importar claves GPG nuevas
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql

# 3. Limpiar caché
sudo dnf clean all

# 4. Actualizar repositorios
sudo dnf update -y

# 5. Instalar MySQL
sudo dnf install -y mysql-community-server
```

## 📋 Alternativa: Instalar desde Repositorio RPM

Si el problema persiste, puedes instalar el repositorio manualmente:

```bash
# Descargar e instalar el repositorio
wget https://dev.mysql.com/get/mysql80-community-release-el9-1.noarch.rpm
sudo rpm -ivh mysql80-community-release-el9-1.noarch.rpm

# Importar clave GPG manualmente
sudo rpm --import https://repo.mysql.com/RPM-GPG-KEY-mysql-2022

# Instalar MySQL
sudo dnf install -y mysql-community-server
```

## ✅ Verificar Instalación

Después de instalar exitosamente:

```bash
# Habilitar e iniciar MySQL
sudo systemctl enable mysqld
sudo systemctl start mysqld

# Verificar estado
sudo systemctl status mysqld

# Obtener contraseña temporal
sudo grep 'temporary password' /var/log/mysqld.log
```

## 🚀 Configurar MySQL

```bash
# Configurar MySQL
sudo mysql_secure_installation

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

