# ✅ Completar Despliegue - Migraciones, Seeders y Factories

## 📋 Pasos para Completar el Despliegue

### Paso 1: Verificar que Estás en el Directorio Correcto

```bash
cd /var/www/laravel/EMOTIVE
pwd  # Debe mostrar: /var/www/laravel/EMOTIVE
```

### Paso 2: Verificar Permisos

```bash
# Asegurar permisos correctos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache
```

### Paso 3: Verificar Conexión a Base de Datos

```bash
# Probar conexión (ajusta credenciales si es necesario)
php artisan db:show
# O verificar .env
cat .env | grep DB_
```

### Paso 4: Ejecutar Migraciones

```bash
cd /var/www/laravel/EMOTIVE

# Ver estado de migraciones
php artisan migrate:status

# Ejecutar migraciones (--force es necesario en producción)
php artisan migrate --force
```

### Paso 5: Ejecutar Seeders

```bash
# Ver seeders disponibles
php artisan db:seed --class=NombreSeeder 2>/dev/null || php artisan db:seed --help

# Ejecutar todos los seeders
php artisan db:seed --force

# O ejecutar un seeder específico
# php artisan db:seed --class=UsuariosSeeder --force
```

### Paso 6: Factories (Opcional - Solo para Datos de Prueba)

**⚠️ IMPORTANTE**: Las factories normalmente se usan para generar datos de prueba. En producción, generalmente NO se ejecutan. Pero si necesitas datos de prueba:

```bash
# Opción 1: Usar Tinker para ejecutar factories
php artisan tinker
```

Dentro de Tinker:
```php
// Ejemplo: crear usuarios de prueba
User::factory()->count(10)->create();

// O cualquier otra factory que tengas
// Modelo::factory()->count(5)->create();

exit
```

**Opción 2: Crear un Seeder que Use Factories**

```bash
# Crear un seeder para producción
php artisan make:seeder ProductionSeeder
```

Edita el seeder (`database/seeders/ProductionSeeder.php`):
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Usar factories en seeders
        User::factory()->count(10)->create();
        
        // Otros modelos con factories...
    }
}
```

Luego ejecuta:
```bash
php artisan db:seed --class=ProductionSeeder --force
```

### Paso 7: Optimizar Aplicación

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar cachés anteriores
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 8: Crear Enlace de Storage

```bash
php artisan storage:link
```

### Paso 9: Verificar que Todo Funciona

```bash
# Ver rutas disponibles
php artisan route:list

# Verificar conexión a BD
php artisan db:show

# Ver logs si hay problemas
tail -f storage/logs/laravel.log
```

## 🚀 Comandos Completos (Todo en uno)

```bash
cd /var/www/laravel/EMOTIVE

# Permisos
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache

# Migraciones
php artisan migrate --force

# Seeders
php artisan db:seed --force

# Optimizar
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage
php artisan storage:link

# Reiniciar PHP-FPM
sudo systemctl restart php-fpm
```

## 📋 Ejecutar Factories con Tinker (Si es necesario)

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

Dentro de Tinker, ejecuta tus factories:
```php
// Ejemplo para User
User::factory()->count(10)->create();

// Verificar
User::count();
User::first();

exit
```

## ✅ Checklist Final

- [ ] Migraciones ejecutadas: `php artisan migrate:status` muestra todo "Ran"
- [ ] Seeders ejecutados: Datos iniciales creados
- [ ] Factories ejecutadas (si necesario): Datos de prueba creados
- [ ] Aplicación optimizada: `php artisan optimize`
- [ ] Storage link creado: `php artisan storage:link`
- [ ] Servicios reiniciados: PHP-FPM corriendo
- [ ] Aplicación accesible: `curl http://localhost` funciona

## 🔍 Verificar Base de Datos

```bash
# Conectar a MySQL
mysql -u laravel_user -p laravel_db

# Ver tablas creadas
SHOW TABLES;

# Ver datos en una tabla (ejemplo)
SELECT COUNT(*) FROM users;
SELECT * FROM users LIMIT 5;

EXIT;
```

## ⚠️ Si Hay Errores

### Error en Migraciones
```bash
# Ver errores específicos
php artisan migrate --force -v

# Si hay conflicto, puedes hacer rollback
php artisan migrate:rollback --force

# O resetear (¡CUIDADO! Borra datos)
# php artisan migrate:fresh --force --seed
```

### Error en Seeders
```bash
# Ejecutar seeder específico con más detalle
php artisan db:seed --class=NombreSeeder --force -v
```

### Ver Logs
```bash
tail -f storage/logs/laravel.log
```

¡Listo para completar el despliegue! 🚀

