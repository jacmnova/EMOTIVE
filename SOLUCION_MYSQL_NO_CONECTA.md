# ⚠️ Solución: Can't connect to MySQL server

## 🔍 El Problema

El error `Can't connect to local MySQL server through socket` significa que MySQL no está corriendo o no se inició correctamente.

## ✅ Solución Paso a Paso

### Paso 1: Verificar si MySQL está instalado

```bash
# Verificar instalación
rpm -qa | grep mysql
# O
which mysqld
```

### Paso 2: Verificar el estado del servicio

```bash
# Ver estado
sudo systemctl status mysqld

# O si es MariaDB:
sudo systemctl status mariadb
```

### Paso 3: Iniciar MySQL

```bash
# Intentar iniciar MySQL
sudo systemctl start mysqld

# Si no funciona, verificar el nombre del servicio
sudo systemctl list-units --type=service | grep -i mysql
sudo systemctl list-units --type=service | grep -i mariadb
```

### Paso 4: Si el servicio no existe o falla

#### Opción A: Reinicializar MySQL

```bash
# Verificar si existe el directorio de datos
ls -la /var/lib/mysql/

# Si está vacío o no existe, inicializar MySQL
sudo mysqld --initialize --user=mysql

# O para MariaDB
sudo mysql_install_db --user=mysql --datadir=/var/lib/mysql
```

#### Opción B: Verificar logs de error

```bash
# Ver logs de MySQL
sudo tail -f /var/log/mysqld.log
# O
sudo journalctl -u mysqld -n 50
```

### Paso 5: Habilitar e iniciar el servicio

```bash
# Habilitar para que inicie automáticamente
sudo systemctl enable mysqld

# Iniciar el servicio
sudo systemctl start mysqld

# Verificar estado
sudo systemctl status mysqld
```

### Paso 6: Obtener contraseña temporal (si MySQL fue inicializado)

```bash
# Si MySQL fue inicializado, busca la contraseña temporal
sudo grep 'temporary password' /var/log/mysqld.log
```

## 🔧 Solución Completa (Todo en uno)

```bash
# 1. Verificar servicio
sudo systemctl status mysqld || sudo systemctl status mariadb

# 2. Si no existe, verificar si MySQL está instalado
rpm -qa | grep mysql

# 3. Intentar iniciar
sudo systemctl start mysqld 2>/dev/null || sudo systemctl start mariadb

# 4. Si falla, inicializar
if [ ! -d /var/lib/mysql/mysql ]; then
    echo "Inicializando MySQL..."
    sudo mysqld --initialize --user=mysql 2>/dev/null || \
    sudo mysql_install_db --user=mysql --datadir=/var/lib/mysql
fi

# 5. Habilitar e iniciar
sudo systemctl enable mysqld 2>/dev/null || sudo systemctl enable mariadb
sudo systemctl start mysqld 2>/dev/null || sudo systemctl start mariadb

# 6. Verificar
sudo systemctl status mysqld || sudo systemctl status mariadb
```

## 🚀 Si MySQL no está instalado

Si MySQL no está instalado, instálalo:

```bash
# Instalar MySQL
sudo dnf install -y --nogpgcheck mysql-community-server

# Inicializar
sudo mysqld --initialize --user=mysql

# Iniciar
sudo systemctl enable mysqld
sudo systemctl start mysqld

# Obtener contraseña temporal
sudo grep 'temporary password' /var/log/mysqld.log
```

## 📋 Verificar Conexión

```bash
# Intentar conectar
sudo mysql -u root

# O con contraseña temporal
mysql -u root -p
# Usa la contraseña del log: /var/log/mysqld.log
```

## ⚠️ Problemas Comunes

### Error: "Unit mysqld.service not found"

```bash
# Verificar si el paquete está instalado
rpm -qa | grep mysql

# Si no está, instalar
sudo dnf install -y --nogpgcheck mysql-community-server
```

### Error: "Permission denied" en /var/lib/mysql

```bash
# Arreglar permisos
sudo chown -R mysql:mysql /var/lib/mysql
sudo systemctl start mysqld
```

### Error: "Port 3306 already in use"

```bash
# Ver qué está usando el puerto
sudo lsof -i :3306

# O detener el proceso
sudo pkill mysqld
sudo systemctl start mysqld
```

## ✅ Checklist de Verificación

- [ ] MySQL está instalado: `rpm -qa | grep mysql`
- [ ] Servicio existe: `sudo systemctl status mysqld`
- [ ] Servicio está corriendo: `sudo systemctl start mysqld`
- [ ] Socket existe: `ls -la /var/lib/mysql/mysql.sock` o `ls -la /var/run/mysqld/mysqld.sock`
- [ ] Puedes conectarte: `sudo mysql -u root`

