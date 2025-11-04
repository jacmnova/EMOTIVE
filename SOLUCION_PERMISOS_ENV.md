# ⚠️ Solución: Permission denied al generar APP_KEY

## 🔍 El Problema

El error indica que no tienes permisos para escribir en el archivo `.env`. El archivo está en `/var/www/laravel/EMOTIVE/.env`.

## ✅ Solución Rápida

### Opción 1: Dar Permisos al Usuario Actual

```bash
# Verificar la estructura
ls -la /var/www/laravel/

# Dar permisos al directorio completo
sudo chown -R ec2-user:ec2-user /var/www/laravel

# Dar permisos de escritura
sudo chmod -R 775 /var/www/laravel
sudo chmod 664 /var/www/laravel/EMOTIVE/.env 2>/dev/null || true

# Intentar generar key de nuevo
cd /var/www/laravel/EMOTIVE
php artisan key:generate
```

### Opción 2: Usar sudo (temporal)

```bash
cd /var/www/laravel/EMOTIVE
sudo php artisan key:generate
```

**Luego ajustar permisos:**
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 /var/www/laravel
```

### Opción 3: Crear .env Manualmente y Generar Key

```bash
cd /var/www/laravel/EMOTIVE

# Si .env no existe, crearlo
sudo cp .env.example .env 2>/dev/null || sudo touch .env

# Dar permisos
sudo chown ec2-user:ec2-user .env
sudo chmod 664 .env

# Generar key
php artisan key:generate
```

## 🔧 Solución Completa (Todo en uno)

```bash
# Ir al directorio
cd /var/www/laravel/EMOTIVE

# Verificar estructura
pwd
ls -la

# Dar permisos completos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 /var/www/laravel

# Asegurar que .env existe y tiene permisos
sudo touch .env
sudo chown ec2-user:ec2-user .env
sudo chmod 664 .env

# Generar APP_KEY
php artisan key:generate

# Verificar que funcionó
cat .env | grep APP_KEY
```

## 📋 Verificar Permisos Actuales

```bash
# Ver quién es dueño del directorio
ls -la /var/www/laravel/EMOTIVE/

# Ver permisos del .env
ls -la /var/www/laravel/EMOTIVE/.env
```

## 🚀 Después de Generar la Key

Una vez que la key se genere correctamente, asegúrate de que los permisos estén bien para producción:

```bash
cd /var/www/laravel/EMOTIVE

# Dar permisos correctos para producción
sudo chown -R ec2-user:ec2-user storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# El .env debe ser solo lectura para otros
sudo chmod 640 .env
```

## ⚠️ Si el Directorio es Diferente

Si tu aplicación está en `/var/www/laravel/EMOTIVE/` en lugar de `/var/www/laravel/`, ajusta la configuración de Nginx:

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

Y cambia:
```nginx
root /var/www/laravel/EMOTIVE/public;
```

Luego:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

