# ⚠️ Solución: Conflicto de Nombres de Rutas (home)

## 🔍 El Problema

El error indica que hay dos rutas que intentan usar el mismo nombre `home`:
- Una ruta llamada `dashboard` está tratando de usar el nombre `home`
- Ya existe otra ruta con el nombre `home`

Esto impide que Laravel cachee las rutas.

## ✅ Solución Rápida

### Opción 1: Limpiar Cache de Rutas (Solución Temporal)

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar cache de rutas
php artisan route:clear

# NO cachear rutas por ahora
php artisan optimize --no-routes
php artisan config:cache
php artisan view:cache

# Probar
curl http://localhost | head -30
```

### Opción 2: Corregir el Conflicto (Solución Permanente)

```bash
cd /var/www/laravel/EMOTIVE

# Ver todas las rutas y sus nombres
php artisan route:list | grep -E "(home|dashboard)"

# Esto te mostrará cuáles rutas tienen conflicto
```

Luego edita el archivo de rutas:
```bash
sudo nano routes/web.php
```

**Busca líneas como:**
```php
Route::get('/dashboard', ...)->name('home');
// O
Route::get('/', ...)->name('home');
Route::get('/dashboard', ...)->name('home'); // ← Duplicado
```

**Corrige dándoles nombres únicos:**
```php
Route::get('/', ...)->name('home');
Route::get('/dashboard', ...)->name('dashboard'); // ← Nombre único
```

## 🚀 Solución Completa

```bash
cd /var/www/laravel/EMOTIVE

# 1. Limpiar cache de rutas
php artisan route:clear

# 2. Ver rutas con nombres duplicados
echo "--- Rutas llamadas 'home' ---"
php artisan route:list | grep "home"

# 3. Optimizar SIN cachear rutas (solución rápida)
php artisan optimize --no-routes
php artisan config:cache
php artisan view:cache

# 4. Probar
curl http://localhost | head -30
```

## 🔧 Corregir en el Código

Si quieres corregir permanentemente, edita `routes/web.php`:

```bash
sudo nano /var/www/laravel/EMOTIVE/routes/web.php
```

**Busca y corrige:**
- Si hay `->name('home')` duplicado, cambia uno a otro nombre
- Asegúrate de que cada ruta tenga un nombre único

**Ejemplo de corrección:**
```php
// ❌ INCORRECTO
Route::get('/', ...)->name('home');
Route::get('/dashboard', ...)->name('home'); // Conflicto!

// ✅ CORRECTO
Route::get('/', ...)->name('home');
Route::get('/dashboard', ...)->name('dashboard'); // Nombre único
```

## ⚡ Solución Inmediata (Para que Funcione Ahora)

```bash
cd /var/www/laravel/EMOTIVE && \
php artisan route:clear && \
php artisan optimize --no-routes && \
php artisan config:cache && \
php artisan view:cache && \
echo "✅ Rutas optimizadas sin cache (solución temporal)" && \
curl http://localhost | head -30
```

Después puedes corregir el código para usar nombres únicos en las rutas.

¡Ejecuta la solución inmediata para que funcione ahora! 🚀

