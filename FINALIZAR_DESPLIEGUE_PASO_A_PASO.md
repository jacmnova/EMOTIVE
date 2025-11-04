# 🚀 Finalizar Despliegue - Paso a Paso Completo

## 📍 Ubicación Actual
Tu aplicación está en: `/var/www/laravel/EMOTIVE`

## ✅ Estado Actual (Lo que ya hiciste)
- ✅ Repositorio clonado en `/var/www/laravel/EMOTIVE`
- ✅ `npm install` ejecutado
- ✅ `npm ci` ejecutado  
- ✅ `npm run build` ejecutado

## 📋 Pasos para Finalizar el Despliegue

### PASO 1: Verificar que Estás en el Directorio Correcto

```bash
cd /var/www/laravel/EMOTIVE
pwd
# Debe mostrar: /var/www/laravel/EMOTIVE
```

### PASO 2: Configurar Permisos

```bash
# Dar permisos al directorio completo
sudo chown -R ec2-user:ec2-user /var/www/laravel

# Dar permisos específicos a storage y cache
sudo chmod -R 775 storage bootstrap/cache

# Verificar permisos
ls -la storage bootstrap/cache
```

### PASO 3: Instalar Dependencias de Composer

```bash
cd /var/www/laravel/EMOTIVE

# Instalar dependencias PHP (si no lo hiciste)
composer install --no-dev --optimize-autoloader
```

### PASO 4: Verificar/Crear Archivo .env

```bash
cd /var/www/laravel/EMOTIVE

# Verificar si .env existe
ls -la .env

# Si no existe, copiarlo desde .env.example
sudo cp .env.example .env

# Dar permisos
sudo chown ec2-user:ec2-user .env
sudo chmod 664 .env

# Editar .env con tus configuraciones
sudo nano .env
```

**Configuración mínima del .env:**
```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://TU_IP_EC2

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=spd,j*qON7es

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### PASO 5: Generar APP_KEY

```bash
cd /var/www/laravel/EMOTIVE

# Generar la clave de aplicación
php artisan key:generate

# Verificar que se generó
cat .env | grep APP_KEY
```

### PASO 6: Verificar Conexión a Base de Datos

```bash
# Probar conexión
php artisan db:show

# Si hay error, verificar credenciales en .env
cat .env | grep DB_
```

### PASO 7: Ejecutar Migraciones

```bash
cd /var/www/laravel/EMOTIVE

# Ver estado actual de migraciones
php artisan migrate:status

# Ejecutar todas las migraciones pendientes
php artisan migrate --force

# Verificar que todas se ejecutaron
php artisan migrate:status
```

### PASO 8: Ejecutar Seeders (En el Orden Correcto)

Según tu `DatabaseSeeder.php`, el orden es:

```bash
cd /var/www/laravel/EMOTIVE

# Opción 1: Ejecutar todos los seeders del DatabaseSeeder
php artisan db:seed --force

# Opción 2: Ejecutar seeders individualmente en orden:
php artisan db:seed --class=UsuariosSeeder --force
php artisan db:seed --class=ClientesSeeder --force
php artisan db:seed --class=CalculoSeeder --force
php artisan db:seed --class=FormBurnOutSeeder --force
php artisan db:seed --class=VarBurnOutSeeder --force
php artisan db:seed --class=PerguntasBurnOutSeeder --force
php artisan db:seed --class=VarPerguntaBurnOutSeeder --force
php artisan db:seed --class=EtapaSeeder --force
```

**⚠️ IMPORTANTE**: `ClientesSeeder` necesita `usuario_id => 3`, por lo que **UsuariosSeeder debe ejecutarse primero**.

### PASO 9: Ejecutar Factories (Si Necesitas Datos de Prueba)

**Nota**: Revisando tu código, solo tienes `UserFactory`. Los seeders NO usan factories, pero puedes generar usuarios de prueba si lo necesitas.

#### Opción A: Usar Tinker para Generar Usuarios de Prueba

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

Dentro de Tinker:
```php
// Crear usuarios de prueba usando la factory
use App\Models\User;

// Crear 10 usuarios de prueba
User::factory()->count(10)->create();

// Verificar
User::count();

// Ver algunos usuarios
User::take(5)->get(['id', 'name', 'email']);

exit
```

#### Opción B: Crear un Seeder para Factories (Recomendado)

```bash
# Crear un seeder para datos de prueba
php artisan make:seeder TestDataSeeder
```

Edita `database/seeders/TestDataSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de prueba
        User::factory()->count(20)->create();
        
        // Agregar más factories según tus necesidades
        // Ejemplo: Producto::factory()->count(50)->create();
    }
}
```

Luego ejecuta:
```bash
php artisan db:seed --class=TestDataSeeder --force
```

### PASO 10: Crear Enlace Simbólico de Storage

```bash
cd /var/www/laravel/EMOTIVE

# Crear enlace de storage
php artisan storage:link

# Verificar que se creó
ls -la public/storage
```

### PASO 11: Optimizar Aplicación para Producción

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar cachés anteriores
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar y cachear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### PASO 12: Configurar Permisos Finales

```bash
cd /var/www/laravel/EMOTIVE

# Para producción, puedes cambiar el propietario a nginx (opcional)
# O mantener como ec2-user
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache

# El .env debe tener permisos más restrictivos
sudo chmod 640 .env
```

### PASO 13: Actualizar Configuración de Nginx

Verificar que Nginx apunta al directorio correcto:

```bash
sudo nano /etc/nginx/conf.d/laravel.conf
```

Verificar que la línea `root` apunta a:
```nginx
root /var/www/laravel/EMOTIVE/public;
```

Si necesitas cambiar:
```bash
# Editar configuración
sudo nano /etc/nginx/conf.d/laravel.conf

# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

### PASO 14: Reiniciar Servicios

```bash
# Reiniciar PHP-FPM
sudo systemctl restart php-fpm

# Reiniciar Nginx (opcional, solo si cambiaste configuración)
sudo systemctl reload nginx

# Verificar que están corriendo
sudo systemctl status php-fpm
sudo systemctl status nginx
sudo systemctl status mysqld
```

### PASO 15: Verificar que Todo Funciona

```bash
cd /var/www/laravel/EMOTIVE

# Verificar rutas
php artisan route:list | head -20

# Probar aplicación localmente
curl http://localhost

# Ver logs si hay problemas
tail -f storage/logs/laravel.log
```

## 🚀 Script Completo (Todo en uno)

**Copia y pega esto para ejecutar todo de una vez:**

```bash
cd /var/www/laravel/EMOTIVE && \
echo "🔧 Configurando permisos..." && \
sudo chown -R ec2-user:ec2-user /var/www/laravel && \
sudo chmod -R 775 storage bootstrap/cache && \
echo "📦 Instalando dependencias Composer..." && \
composer install --no-dev --optimize-autoloader && \
echo "🔑 Generando APP_KEY..." && \
php artisan key:generate && \
echo "🗄️ Ejecutando migraciones..." && \
php artisan migrate --force && \
echo "🌱 Ejecutando seeders..." && \
php artisan db:seed --force && \
echo "🔗 Creando enlace de storage..." && \
php artisan storage:link && \
echo "⚡ Optimizando aplicación..." && \
php artisan optimize && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
echo "🔄 Reiniciando servicios..." && \
sudo systemctl restart php-fpm && \
echo "✅ Despliegue completado!" && \
echo "🌐 Tu aplicación debería estar disponible en: http://$(curl -s ifconfig.me)"
```

## 📋 Ejecutar Factories (Después del despliegue base)

Si necesitas generar datos de prueba con factories:

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

Dentro de Tinker:
```php
use App\Models\User;

// Crear usuarios de prueba
User::factory()->count(10)->create();

// Verificar
User::count();
User::take(5)->get(['id', 'name', 'email']);

exit
```

## ✅ Checklist Final

- [ ] Permisos configurados
- [ ] Dependencias de Composer instaladas
- [ ] Archivo .env creado y configurado
- [ ] APP_KEY generado
- [ ] Migraciones ejecutadas (todas marcadas como "Ran")
- [ ] Seeders ejecutados (UsuariosSeeder, ClientesSeeder, etc.)
- [ ] Factories ejecutadas (si necesitas datos de prueba)
- [ ] Storage link creado
- [ ] Aplicación optimizada (config, routes, views en cache)
- [ ] Servicios reiniciados (PHP-FPM, Nginx)
- [ ] Aplicación accesible vía navegador

## 🔍 Verificar Base de Datos

```bash
# Conectar a MySQL
mysql -u laravel_user -p laravel_db

# Ver tablas
SHOW TABLES;

# Ver usuarios creados
SELECT COUNT(*) as total_usuarios FROM users;
SELECT id, name, email FROM users LIMIT 5;

# Ver clientes
SELECT COUNT(*) as total_clientes FROM clientes;
SELECT * FROM clientes;

EXIT;
```

## 🐛 Si Hay Errores

### Error en Migraciones
```bash
php artisan migrate --force -v
tail -f storage/logs/laravel.log
```

### Error en Seeders
```bash
php artisan db:seed --class=NombreSeeder --force -v
```

### Error de Permisos
```bash
sudo chown -R ec2-user:ec2-user /var/www/laravel
sudo chmod -R 775 storage bootstrap/cache
```

### Ver Logs
```bash
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

## 🎉 ¡Listo!

Después de completar todos los pasos, tu aplicación debería estar funcionando en:
- `http://TU_IP_EC2`

¡Sigue los pasos en orden y tu despliegue estará completo! 🚀

