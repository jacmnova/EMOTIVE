# 📦 Instalar Dependencias en el Servidor

Si ves el error `Failed to open stream: No such file or directory in /var/www/laravel/vendor/autoload.php`, necesitas instalar las dependencias.

---

## 🔧 Solución Rápida

Ejecuta estos comandos en el servidor EC2:

```bash
cd /var/www/laravel

# Instalar dependencias PHP (Composer)
composer install --no-dev --optimize-autoloader

# Instalar dependencias Node.js (si es necesario)
npm ci

# Compilar assets (si es necesario)
npm run build
```

---

## 📋 Pasos Completos

### 1. Verificar que estás en el directorio correcto

```bash
cd /var/www/laravel
pwd
# Debe mostrar: /var/www/laravel
```

### 2. Instalar dependencias PHP con Composer

```bash
composer install --no-dev --optimize-autoloader
```

**Nota:** 
- `--no-dev` instala solo dependencias de producción (más rápido)
- `--optimize-autoloader` optimiza el autoloader para producción

**Si Composer no está instalado:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 3. Instalar dependencias Node.js (si tu proyecto las usa)

```bash
# Verificar que Node.js está instalado
node -v
npm -v

# Instalar dependencias
npm ci

# Compilar assets para producción
npm run build
```

### 4. Verificar que se instaló correctamente

```bash
# Verificar que existe el directorio vendor
ls -la vendor/

# Probar que artisan funciona
php artisan --version
```

---

## ⚠️ Si Composer da Error de Memoria

Si ves un error de memoria, aumenta el límite:

```bash
php -d memory_limit=512M /usr/local/bin/composer install --no-dev --optimize-autoloader
```

O temporalmente:

```bash
php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader
```

---

## ✅ Después de Instalar

Una vez instaladas las dependencias, puedes continuar con:

```bash
# Generar APP_KEY (si no lo has hecho)
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders
php artisan db:seed --force

# Optimizar
php artisan optimize
```

---

## 🔄 Si el Despliegue Automático Falla

El script `deploy.sh` ya incluye la instalación de dependencias, pero si falla manualmente, ejecuta:

```bash
cd /var/www/laravel
./deploy.sh
```

Este script ejecutará automáticamente:
- `composer install`
- `npm ci && npm run build`
- Migraciones y seeders
- Optimizaciones

---

¡Listo! Después de ejecutar `composer install`, el error debería desaparecer. 🚀

