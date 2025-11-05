# 🔧 Solución: Conflicto con package-lock.json

## ⚠️ Problema

Tienes cambios locales en `package-lock.json` que entran en conflicto con los cambios del repositorio.

## ✅ Soluciones

### Opción 1: Descartar Cambios Locales (Recomendado)

Como `package-lock.json` se regenera automáticamente, puedes descartar los cambios locales:

```bash
cd /var/www/laravel/EMOTIVE

# Descartar cambios en package-lock.json
git checkout -- package-lock.json

# Ahora hacer pull
git pull origin main

# Regenerar package-lock.json si es necesario
npm install
```

### Opción 2: Guardar Cambios Temporalmente (Stash)

Si quieres guardar los cambios por si acaso:

```bash
cd /var/www/laravel/EMOTIVE

# Guardar cambios temporalmente
git stash

# Hacer pull
git pull origin main

# Ver cambios guardados (opcional)
git stash list

# Aplicar cambios guardados después (si los necesitas)
# git stash pop
```

### Opción 3: Forzar Pull (Descartar TODO lo Local)

Si estás seguro de que quieres descartar todos los cambios locales:

```bash
cd /var/www/laravel/EMOTIVE

# Descartar todos los cambios locales
git reset --hard origin/main

# O hacer fetch y reset
git fetch origin
git reset --hard origin/main
```

## 🚀 Solución Rápida (Todo en Uno)

```bash
cd /var/www/laravel/EMOTIVE && \
git checkout -- package-lock.json && \
git pull origin main && \
npm install && \
php artisan route:clear && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan optimize && \
sudo systemctl restart php-fpm && \
echo "✅ Actualizado!"
```

## ⚠️ Si Tienes Otros Archivos con Cambios

Si hay otros archivos con cambios:

```bash
cd /var/www/laravel/EMOTIVE

# Ver qué archivos tienen cambios
git status

# Descartar todos los cambios locales
git reset --hard HEAD

# Hacer pull
git pull origin main
```

## 🔍 Verificar Estado

```bash
cd /var/www/laravel/EMOTIVE

# Ver estado de Git
git status

# Ver diferencias
git diff package-lock.json
```

## ✅ Después de Resolver

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Optimizar
php artisan optimize
php artisan route:cache
php artisan config:cache

# Reiniciar servicios
sudo systemctl restart php-fpm
sudo systemctl reload nginx
```

¡Ejecuta la Opción 1 o la solución rápida para resolver el conflicto! 🚀

