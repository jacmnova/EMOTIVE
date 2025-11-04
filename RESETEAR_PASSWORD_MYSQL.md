# 🔐 Resetear Contraseña de MySQL/MariaDB

Guía para recuperar o resetear la contraseña de root de MySQL.

## 🔍 Método 1: Si MySQL está recién instalado (Sin contraseña configurada)

Si MySQL se instaló recientemente y no configuraste contraseña aún:

```bash
# Intentar entrar sin contraseña
sudo mysql -u root
```

Si funciona, cambia la contraseña:
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_password_aqui';
FLUSH PRIVILEGES;
EXIT;
```

## 🔧 Método 2: Resetear Contraseña de Root (Si tienes acceso sudo)

### Paso 1: Detener MySQL

```bash
sudo systemctl stop mysqld
# O si es MariaDB:
sudo systemctl stop mariadb
```

### Paso 2: Iniciar MySQL en modo seguro (skip-grant-tables)

```bash
# Crear un script temporal
sudo tee /tmp/mysql-reset.sh > /dev/null <<'EOF'
#!/bin/bash
mysqld_safe --skip-grant-tables --skip-networking &
EOF

sudo chmod +x /tmp/mysql-reset.sh
sudo /tmp/mysql-reset.sh

# Esperar unos segundos para que MySQL inicie
sleep 5
```

### Paso 3: Conectarse sin contraseña

```bash
sudo mysql -u root
```

### Paso 4: Resetear la contraseña

Dentro de MySQL, ejecuta:

```sql
-- Limpiar privilegios primero
FLUSH PRIVILEGES;

-- Resetear contraseña de root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'tu_nueva_password_segura';
-- O si la versión es antigua:
-- SET PASSWORD FOR 'root'@'localhost' = PASSWORD('tu_nueva_password_segura');

FLUSH PRIVILEGES;
EXIT;
```

### Paso 5: Reiniciar MySQL normalmente

```bash
# Matar el proceso de MySQL en modo seguro
sudo pkill mysqld

# Reiniciar MySQL normalmente
sudo systemctl start mysqld

# Verificar
sudo systemctl status mysqld
```

### Paso 6: Probar la nueva contraseña

```bash
mysql -u root -p
# Ingresa la nueva contraseña cuando te la pida
```

## 🚀 Método 3: Reset Rápido (Recomendado)

**Todo en un solo bloque de comandos:**

```bash
# 1. Detener MySQL
sudo systemctl stop mysqld

# 2. Iniciar MySQL sin verificación de contraseñas
sudo mysqld_safe --skip-grant-tables --skip-networking &

# 3. Esperar a que inicie
sleep 5

# 4. Resetear contraseña directamente
sudo mysql -u root <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_password_123';
FLUSH PRIVILEGES;
EXIT;
EOF

# 5. Detener MySQL en modo seguro
sudo pkill mysqld
sleep 2

# 6. Reiniciar MySQL normalmente
sudo systemctl start mysqld

# 7. Probar nueva contraseña
mysql -u root -p
# Ingresa: nueva_password_123
```

**⚠️ IMPORTANTE**: Cambia `nueva_password_123` por una contraseña segura después.

## 🔄 Método 4: Si usas MariaDB

Para MariaDB, el proceso es similar pero el servicio se llama `mariadb`:

```bash
sudo systemctl stop mariadb
sudo mysqld_safe --skip-grant-tables --skip-networking &
sleep 5

sudo mysql -u root <<EOF
FLUSH PRIVILEGES;
SET PASSWORD FOR 'root'@'localhost' = PASSWORD('nueva_password_123');
FLUSH PRIVILEGES;
EXIT;
EOF

sudo pkill mysqld
sudo systemctl start mariadb
```

## ✅ Verificar que Funciona

```bash
# Probar conexión
mysql -u root -p

# Dentro de MySQL, verificar:
SHOW DATABASES;
EXIT;
```

## 📝 Crear Usuario para Laravel (Si prefieres no usar root)

Después de resetear la contraseña de root, puedes crear un usuario específico para Laravel:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'password_laravel';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 🔒 Mejorar Seguridad Después

Una vez que resetees la contraseña:

```bash
sudo mysql_secure_installation
```

Este script te permitirá:
- Configurar la contraseña de root
- Remover usuarios anónimos
- Deshabilitar login remoto de root
- Remover base de datos de test
- Recargar tabla de privilegios

## ⚠️ Si Nada Funciona: Reinstalar MySQL

Como último recurso (esto borrará todas las bases de datos):

```bash
# ⚠️ ADVERTENCIA: Esto borra TODOS los datos
sudo systemctl stop mysqld
sudo dnf remove -y mysql-community-server mysql-community-client
sudo rm -rf /var/lib/mysql
sudo dnf install -y mysql-community-server
sudo systemctl start mysqld
sudo mysql_secure_installation
```

## 🎯 Resumen Rápido (Copia y Pega)

```bash
# Reset rápido de contraseña
sudo systemctl stop mysqld
sudo mysqld_safe --skip-grant-tables --skip-networking &
sleep 5
sudo mysql -u root -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_pass_123'; FLUSH PRIVILEGES;"
sudo pkill mysqld
sudo systemctl start mysqld
mysql -u root -p  # Usa: nueva_pass_123
```

**¡Recuerda cambiar `nueva_pass_123` por una contraseña segura!**

