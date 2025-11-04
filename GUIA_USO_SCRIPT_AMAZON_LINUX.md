# 🚀 Guía de Uso: install-ec2-amazon-linux.sh

Esta guía te ayudará a ejecutar el script de instalación en tu instancia Amazon Linux EC2.

## 📋 Requisitos Previos

1. ✅ Instancia EC2 con Amazon Linux 2023 o Amazon Linux 2
2. ✅ Conectado como `ec2-user`
3. ✅ Archivo del script disponible en el servidor

## 🔧 Paso 1: Subir el Script al Servidor

### Opción A: Desde tu máquina local (recomendado)

```bash
# Desde tu máquina local, copia el script al servidor
scp -i tu-llave.pem install-ec2-amazon-linux.sh ec2-user@tu-ip-ec2:~/
```

### Opción B: Crear el archivo directamente en el servidor

```bash
# Conéctate al servidor
ssh -i tu-llave.pem ec2-user@tu-ip-ec2

# Crea el archivo (puedes usar nano, vi, o pegar el contenido)
nano install-ec2-amazon-linux.sh
# Pega el contenido del script y guarda (Ctrl+X, Y, Enter)
```

## ⚡ Paso 2: Dar Permisos de Ejecución

```bash
chmod +x install-ec2-amazon-linux.sh
```

## 🚀 Paso 3: Ejecutar el Script

```bash
./install-ec2-amazon-linux.sh
```

El script realizará automáticamente:
- ✅ Actualizar el sistema
- ✅ Instalar herramientas básicas (wget, git, unzip)
- ✅ Instalar PHP y extensiones necesarias
- ✅ Instalar Composer
- ✅ Instalar Node.js y NPM
- ✅ Instalar Nginx
- ✅ Instalar MariaDB
- ✅ Configurar PHP-FPM
- ✅ Crear directorio de aplicación
- ✅ Generar llaves SSH para GitHub Actions

## ⏱️ Tiempo Estimado

El script tarda aproximadamente **10-15 minutos** en completarse, dependiendo de la velocidad de conexión.

## ✅ Verificar que Todo Funcionó

Después de ejecutar el script, verifica:

```bash
# Verificar PHP
php -v

# Verificar Composer
composer --version

# Verificar Node.js
node -v
npm -v

# Verificar servicios
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mariadb
```

## 📝 Próximos Pasos Después del Script

### 1. Configurar MySQL/MariaDB

```bash
sudo mysql_secure_installation
```

### 2. Crear Base de Datos

```bash
sudo mysql -u root -p
```

En MySQL:
```sql
CREATE DATABASE laravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configurar Nginx

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

Pega la configuración (ver `INSTALACION_EC2.md` sección 3.8)

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Copiar la Clave SSH para GitHub

Al final del script, se mostrará una clave SSH privada. **Cópiala completa** y guárdala en GitHub Secrets como `SSH_KEY`.

```bash
# Si necesitas verla de nuevo:
cat ~/.ssh/github_deploy
```

### 5. Configurar GitHub Secrets

En GitHub → Settings → Secrets and variables → Actions, agrega:
- `SSH_KEY`: La clave privada mostrada por el script
- `SSH_HOST`: Tu IP pública de EC2
- `SSH_USER`: `ec2-user`
- `REMOTE_PATH`: `/var/www/laravel`

## ⚠️ Solución de Problemas

### Si el script falla en algún punto:

1. **Lee el mensaje de error** - el script te dirá dónde falló
2. **Continúa manualmente** desde donde falló usando los comandos en `COMANDOS_DEPLOY_EC2.md`
3. **Verifica los logs** si es necesario

### Errores Comunes:

- **"epel-release not found"**: ✅ Normal en AL2023, el script lo maneja
- **"curl conflicts"**: ✅ El script detecta curl-minimal y continúa
- **"Permission denied"**: Ejecuta `chmod +x install-ec2-amazon-linux.sh`

## 📚 Documentación Adicional

- `INSTALACION_EC2.md` - Guía completa paso a paso
- `COMANDOS_DEPLOY_EC2.md` - Comandos rápidos
- `SOLUCION_APT_NOT_FOUND.md` - Si usas Ubuntu por error
- `SOLUCION_CURL_CONFLICTO.md` - Problemas con curl

## 🎯 Checklist Post-Instalación

- [ ] Script ejecutado exitosamente
- [ ] PHP instalado y funcionando (`php -v`)
- [ ] Composer instalado (`composer --version`)
- [ ] Node.js instalado (`node -v`)
- [ ] Nginx corriendo (`sudo systemctl status nginx`)
- [ ] PHP-FPM corriendo (`sudo systemctl status php-fpm`)
- [ ] MariaDB corriendo (`sudo systemctl status mariadb`)
- [ ] Clave SSH generada para GitHub
- [ ] Base de datos MySQL creada
- [ ] Nginx configurado para Laravel
- [ ] GitHub Secrets configurados

¡Listo para desplegar! 🚀

