# 🏭 Configurar Factories para el Primer Despliegue

El script `deploy.sh` detecta automáticamente si es el primer despliegue y ejecuta las factories. Aquí te explico cómo configurarlo.

---

## 🔍 Cómo Funciona

El script `deploy.sh` verifica si es el primer despliegue intentando ejecutar `php artisan migrate:status`. Si falla (porque no hay tablas), asume que es el primer despliegue.

---

## ✅ Opción 1: Usar un Seeder (Recomendado)

La mejor práctica es crear un seeder específico para factories.

### Paso 1: Crear Seeder de Factories

```bash
php artisan make:seeder FactorySeeder
```

### Paso 2: Editar el Seeder

Edita `database/seeders/FactorySeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
// Importa otros modelos que necesites

class FactorySeeder extends Seeder
{
    public function run(): void
    {
        // Ejecutar factories aquí
        User::factory()->count(10)->create();
        
        // Agrega más factories según necesites:
        // Cliente::factory()->count(5)->create();
        // Formulario::factory()->count(3)->create();
    }
}
```

### Paso 3: Agregar al DatabaseSeeder

Edita `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        UsuariosSeeder::class,
        ClientesSeeder::class,
        CalculoSeeder::class,
        // ... otros seeders ...
        
        // Agregar FactorySeeder solo si es necesario
        // FactorySeeder::class,  // Descomenta si quieres ejecutarlo siempre
    ]);
}
```

### Paso 4: Modificar deploy.sh (Opcional)

Si quieres que las factories solo se ejecuten en el primer despliegue, puedes modificar `deploy.sh` para llamar al seeder condicionalmente:

```bash
# En deploy.sh, dentro del bloque if [ "$FIRST_DEPLOY" = true ]
if [ "$FIRST_DEPLOY" = true ]; then
  echo "🏭 Ejecutando factories (primer despliegue)..."
  php artisan db:seed --class=FactorySeeder --force
fi
```

---

## ✅ Opción 2: Ejecutar Factories Directamente en deploy.sh

Si prefieres ejecutar las factories directamente en el script de despliegue:

### Editar deploy.sh

Busca esta sección en `deploy.sh`:

```bash
# Ejecutar factories solo en el primer despliegue
if [ "$FIRST_DEPLOY" = true ]; then
  echo "🏭 Ejecutando factories (primer despliegue)..."
  
  # Descomenta y ajusta según tus necesidades:
  php artisan tinker --execute="
    \App\Models\User::factory()->count(10)->create();
    \App\Models\Cliente::factory()->count(5)->create();
    // Agrega más factories aquí según necesites
  "
fi
```

Descomenta y ajusta las líneas según tus modelos.

---

## ✅ Opción 3: Crear un Comando Artisan Personalizado

### Paso 1: Crear Comando

```bash
php artisan make:command SeedFactories
```

### Paso 2: Editar el Comando

Edita `app/Console/Commands/SeedFactories.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
// Importa otros modelos

class SeedFactories extends Command
{
    protected $signature = 'db:seed-factories';
    protected $description = 'Ejecutar factories para datos de prueba';

    public function handle()
    {
        $this->info('Ejecutando factories...');
        
        User::factory()->count(10)->create();
        // Agrega más factories aquí
        
        $this->info('Factories ejecutadas exitosamente!');
        return 0;
    }
}
```

### Paso 3: Modificar deploy.sh

En `deploy.sh`, dentro del bloque `if [ "$FIRST_DEPLOY" = true ]`:

```bash
if [ "$FIRST_DEPLOY" = true ]; then
  echo "🏭 Ejecutando factories (primer despliegue)..."
  php artisan db:seed-factories
fi
```

---

## 🧪 Verificar que Funciona

### Verificar Detección de Primer Despliegue

```bash
cd /var/www/laravel
php artisan migrate:status
```

Si no hay tablas, mostrará un error (esto es normal en el primer despliegue).

### Ejecutar Manualmente el Primer Despliegue

```bash
cd /var/www/laravel
./deploy.sh
```

Deberías ver:
```
✨ Detectado primer despliegue
🏭 Ejecutando factories (primer despliegue)...
```

---

## 📝 Notas Importantes

1. **Solo en Producción**: Las factories normalmente se usan para datos de prueba. En producción, considera usar seeders con datos reales.

2. **Datos Sensibles**: Si las factories crean usuarios con contraseñas conocidas, cámbialas después del despliegue.

3. **Performance**: Si vas a crear muchos registros, considera ejecutar las factories en un job en background.

4. **Verificación**: Después del primer despliegue, verifica que los datos se crearon correctamente:
   ```bash
   php artisan tinker
   >>> User::count()
   >>> Cliente::count()
   ```

---

## 🔄 Después del Primer Despliegue

Una vez que el primer despliegue se ejecute, el script detectará que ya hay tablas y **NO** ejecutará las factories en despliegues futuros. Esto es el comportamiento esperado.

Si necesitas ejecutar factories nuevamente (por ejemplo, para resetear datos de prueba), puedes:

1. Ejecutar manualmente:
   ```bash
   php artisan db:seed --class=FactorySeeder
   ```

2. O crear un comando específico para desarrollo.

---

## ✅ Recomendación Final

**Usa la Opción 1 (Seeder)** porque:
- ✅ Es más mantenible
- ✅ Puedes versionarlo en Git
- ✅ Es más fácil de probar localmente
- ✅ Puedes ejecutarlo manualmente cuando necesites

---

¿Listo? El script `deploy.sh` ya está configurado para detectar el primer despliegue. Solo necesitas elegir una de las opciones arriba y configurar tus factories. 🚀

