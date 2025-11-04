# 🔧 Corregir Conflicto de Rutas en el Servidor

## ⚠️ El Problema

El archivo en el servidor todavía tiene el conflicto. Necesitas actualizar `routes/web.php` en el servidor.

## ✅ Solución Rápida

### Opción 1: Editar Directamente en el Servidor

```bash
cd /var/www/laravel/EMOTIVE

# Editar el archivo
sudo nano routes/web.php
```

**Busca la línea 55** y cambia:
```php
// De esto:
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// A esto:
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
```

Guarda (Ctrl+X, Y, Enter) y luego:
```bash
php artisan route:clear
php artisan optimize
php artisan route:cache
curl http://localhost | head -30
```

### Opción 2: Reemplazo Automático con sed

```bash
cd /var/www/laravel/EMOTIVE

# Hacer backup
cp routes/web.php routes/web.php.backup

# Reemplazar la línea problemática
sudo sed -i "55s/.*/Route::get('\/dashboard', [App\\\\Http\\\\Controllers\\\\HomeController::class, 'index'])->name('dashboard');/" routes/web.php

# Verificar que se cambió
grep -A 1 "/dashboard" routes/web.php

# Limpiar y regenerar cache
php artisan route:clear
php artisan optimize
php artisan route:cache

# Probar
curl http://localhost | head -30
```

### Opción 3: Pull desde Git (Si ya hiciste push)

```bash
cd /var/www/laravel/EMOTIVE

# Actualizar desde Git
git pull origin main

# Limpiar cache
php artisan route:clear
php artisan optimize
php artisan route:cache

# Probar
curl http://localhost | head -30
```

## 🚀 Todo en Uno (Copia y Pega)

```bash
cd /var/www/laravel/EMOTIVE && \
cp routes/web.php routes/web.php.backup && \
sudo sed -i "55s/.*/Route::get('\/dashboard', [App\\\\Http\\\\Controllers\\\\HomeController::class, 'index'])->name('dashboard');/" routes/web.php && \
grep "/dashboard" routes/web.php && \
php artisan route:clear && \
php artisan optimize && \
php artisan route:cache && \
echo "✅ Corregido! Probando..." && \
curl http://localhost | head -30
```

## 🔍 Verificar que el Cambio se Aplicó

```bash
cd /var/www/laravel/EMOTIVE

# Ver la línea corregida
grep -A 1 "/dashboard" routes/web.php

# Debe mostrar:
# Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
# NO debe tener name('home')
```

## ⚠️ Si el sed no Funciona

Usa nano para editar manualmente:

```bash
cd /var/www/laravel/EMOTIVE
sudo nano routes/web.php
```

Ve a la línea 55 (usa Ctrl+_ y escribe 55) y asegúrate de que dice:
```php
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
```

¡Ejecuta la Opción 2 o 3 para corregir el archivo en el servidor! 🚀

