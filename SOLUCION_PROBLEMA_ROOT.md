# 🔐 Solución: Problema de Contraseña de Root en EC2

## ❌ El Problema

En Ubuntu en EC2, **NO se crea una contraseña para root por defecto**. Esto es una medida de seguridad. Si intentas usar `su root` o `su`, te pedirá una contraseña que no existe.

## ✅ La Solución: Usar `sudo` en lugar de `su`

En lugar de cambiar a root con `su`, usa `sudo` antes de los comandos que necesitan privilegios de administrador.

### 1. Conectarse Correctamente al EC2

```bash
# Ajusta los permisos del archivo .pem
chmod 400 ruta/a/tu-llave.pem

# Conectarse con el usuario ubuntu (NO root)
ssh -i ruta/a/tu-llave.pem ubuntu@tu-ip-ec2
```

**Importante**: Usa `ubuntu@` no `root@` ni `admin@`

### 2. Usar `sudo` para Comandos Administrativos

Una vez conectado, usa `sudo` antes de cualquier comando que requiera permisos de administrador:

```bash
# ✅ CORRECTO - Usar sudo
sudo apt update
sudo apt install nginx
sudo systemctl restart nginx

# ❌ INCORRECTO - No uses su
su root  # Esto pedirá contraseña que no existe
```

### 3. Ejecutar Múltiples Comandos con sudo

Si necesitas ejecutar varios comandos con sudo:

```bash
# Opción 1: Prefijar cada comando con sudo
sudo apt update && sudo apt upgrade -y

# Opción 2: Cambiar a shell de root temporalmente (si realmente lo necesitas)
sudo -i

# Opción 3: Usar sudo su (sin contraseña)
sudo su
```

## 🔧 Si REALMENTE Necesitas Habilitar Root (NO Recomendado)

Si por alguna razón específica necesitas habilitar el acceso con contraseña a root:

```bash
# 1. Conectarte como ubuntu
ssh -i tu-llave.pem ubuntu@tu-ip-ec2

# 2. Establecer contraseña para root
sudo passwd root

# 3. Habilitar login de root por SSH (cambiar PermitRootLogin)
sudo nano /etc/ssh/sshd_config

# Busca la línea:
# PermitRootLogin prohibit-password
# Cámbiala a:
# PermitRootLogin yes

# 4. Reiniciar SSH
sudo systemctl restart sshd
```

**⚠️ ADVERTENCIA**: Habilitar root es un riesgo de seguridad. Es mejor usar `sudo` siempre.

## 📋 Comandos Comunes con sudo

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar paquetes
sudo apt install -y nginx

# Editar archivos del sistema
sudo nano /etc/nginx/sites-available/laravel

# Cambiar permisos
sudo chown -R www-data:www-data /var/www/laravel
sudo chmod -R 775 storage

# Reiniciar servicios
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Ver logs del sistema
sudo tail -f /var/log/nginx/error.log
```

## 🎯 Resumen

- **Usuario por defecto en Ubuntu EC2**: `ubuntu`
- **NO hay contraseña de root**: Esto es normal y seguro
- **Usa `sudo`**: Para comandos que necesitan permisos de administrador
- **NO uses `su`**: A menos que hayas configurado una contraseña para root (no recomendado)

## 🔍 Verificar Usuario Actual

```bash
# Ver quién eres
whoami

# Ver si tienes permisos sudo
sudo whoami

# Ver información del usuario
id
```

## ❓ Preguntas Frecuentes

**P: ¿Por qué no tengo contraseña de root?**
R: Es una medida de seguridad. Ubuntu deshabilita el login directo de root por defecto.

**P: ¿Cómo ejecuto comandos como administrador?**
R: Usa `sudo` antes del comando: `sudo nombre-del-comando`

**P: ¿Puedo crear una contraseña para root?**
R: Sí, con `sudo passwd root`, pero no es recomendado por seguridad.

**P: ¿Cómo cambio a usuario root?**
R: Usa `sudo -i` o `sudo su` (no requiere contraseña si eres ubuntu).

