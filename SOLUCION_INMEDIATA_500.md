# ✅ Solución Inmediata para Error 500

## 🔍 Problema Encontrado

En `routes/web.php` hay dos rutas usando el mismo nombre `home`:
- Línea 54: `/home` → `name('home')`
- Línea 55: `/dashboard` → `name('home')` ❌ (duplicado)

## ✅ Solución Aplicada

He corregido el archivo `routes/web.php` cambiando:
- `/dashboard` ahora usa `name('dashboard')` en lugar de `name('home')`

## 🚀 Pasos para Aplicar la Corrección

### Opción 1: Si tienes acceso al archivo en el servidor

```bash
cd /var/www/laravel/EMOTIVE

# Editar routes/web.php
sudo nano routes/web.php
```

**Busca las líneas 54-55 y cambia:**
```php
// Cambiar esta línea:
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Por esta:
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
```

### Opción 2: Hacer push del cambio desde local

Si tienes acceso al código local:

```bash
# En tu máquina local
git add routes/web.php
git commit -m "Fix: Corregir conflicto de nombres de rutas (home/dashboard)"
git push origin main
```

Luego en el servidor (si usas GitHub Actions, se desplegará automáticamente):
```bash
cd /var/www/laravel/EMOTIVE
git pull origin main
```

### Opción 3: Cambio directo en servidor (Rápido)

```bash
cd /var/www/laravel/EMOTIVE

# Hacer backup
cp routes/web.php routes/web.php.backup

# Reemplazar la línea problemática
sudo sed -i "s|Route::get('/dashboard', \[App\\\\Http\\\\Controllers\\\\HomeController::class, 'index'\])->name('home');|Route::get('/dashboard', [App\\\\Http\\\\Controllers\\\\HomeController::class, 'index'])->name('dashboard');|" routes/web.php

# Verificar el cambio
grep -A 1 "/dashboard" routes/web.php
```

## 🔄 Después de Corregir

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar cache de rutas
php artisan route:clear

# Optimizar (ahora debería funcionar)
php artisan optimize
php artisan route:cache

# Probar
curl http://localhost | head -30
```

## ✅ Todo en Uno

```bash
cd /var/www/laravel/EMOTIVE && \
sudo sed -i "s|/dashboard.*name('home')|/dashboard', [App\\\\Http\\\\Controllers\\\\HomeController::class, 'index'])->name('dashboard');|" routes/web.php && \
php artisan route:clear && \
php artisan optimize && \
php artisan route:cache && \
echo "✅ Corregido! Probando..." && \
curl http://localhost | head -30
```

¡Ya corregí el archivo! Haz push del cambio o aplica la corrección en el servidor. 🚀

