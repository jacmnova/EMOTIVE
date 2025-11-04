# 🔑 Credenciales de Usuarios Admin

## 📋 Usuarios Creados por el Seeder

Según `UsuariosSeeder.php`, estos son los usuarios que se crean:

### Usuario 1: Super Admin (SA)
- **Email**: `wheelkorner@gmail.com`
- **Nombre**: Arley Humberto Rueda Rincon
- **Rol**: Super Admin (sa=1, admin=1, gestor=1, usuario=1)
- **Contraseña**: Hash: `$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO`
- **Estado**: Verificado y activo

### Usuario 2: Administrador
- **Email**: `desenvolvedor@fellipelli.com.br`
- **Nombre**: Administrador
- **Rol**: Admin (admin=1, gestor=1, usuario=1)
- **Contraseña**: Hash: `$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO`
- **Estado**: Verificado y activo

### Usuario 3: Gestor
- **Email**: `arley.rincon@fellipelli.com.br`
- **Nombre**: Gestor
- **Rol**: Gestor (gestor=1, usuario=1, admin=0)
- **Contraseña**: Hash: `$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO`
- **Estado**: Verificado y activo

## ⚠️ Problema: Contraseña Desconocida

El hash `$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO` está encriptado. **No sabemos la contraseña original**.

## ✅ Solución: Crear Usuario Admin con Contraseña Conocida

### Opción 1: Cambiar Contraseña de Usuario Existente

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Cambiar contraseña del administrador
$admin = User::where('email', 'desenvolvedor@fellipelli.com.br')->first();
$admin->password = Hash::make('admin123'); // O la contraseña que quieras
$admin->email_verified_at = now();
$admin->ativo = 1;
$admin->save();

echo "✅ Contraseña cambiada a: admin123\n";
echo "Email: desenvolvedor@fellipelli.com.br\n";
exit
```

### Opción 2: Crear Nuevo Usuario Admin

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Crear nuevo admin
$admin = User::create([
    'name' => 'Admin',
    'email' => 'admin@admin.com',
    'password' => Hash::make('admin123'),
    'email_verified_at' => now(),
    'admin' => 1,
    'gestor' => 1,
    'usuario' => 1,
    'ativo' => 1,
    'sa' => 0
]);

echo "✅ Usuario creado:\n";
echo "Email: admin@admin.com\n";
echo "Contraseña: admin123\n";
exit
```

### Opción 3: Cambiar Todos los Usuarios a Contraseña Conocida

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$password = Hash::make('admin123');

// Cambiar contraseña de todos los usuarios
User::query()->update([
    'password' => $password,
    'email_verified_at' => now(),
    'ativo' => 1
]);

echo "✅ Todos los usuarios ahora tienen contraseña: admin123\n";
exit
```

## 🔍 Verificar Usuarios Existentes

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\User;

// Ver todos los usuarios
$users = User::all(['id', 'name', 'email', 'admin', 'gestor', 'sa', 'ativo', 'email_verified_at']);
foreach($users as $user) {
    echo "ID: {$user->id} | {$user->name} | {$user->email} | Admin: {$user->admin} | Activo: {$user->ativo}\n";
}
exit
```

## 🚀 Script Completo (Crear Admin con Contraseña Conocida)

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker <<'PHP'
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Buscar o crear admin
$admin = User::firstOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Administrador',
        'password' => Hash::make('admin123'),
        'email_verified_at' => now(),
        'admin' => 1,
        'gestor' => 1,
        'usuario' => 1,
        'ativo' => 1,
        'sa' => 1
    ]
);

// Si ya existe, actualizar contraseña
if ($admin->wasRecentlyCreated === false) {
    $admin->password = Hash::make('admin123');
    $admin->email_verified_at = now();
    $admin->ativo = 1;
    $admin->save();
}

echo "✅ Usuario Admin creado/actualizado:\n";
echo "Email: admin@admin.com\n";
echo "Contraseña: admin123\n";
exit
PHP
```

## 📋 Credenciales Recomendadas

Después de ejecutar el script, puedes usar:

**Opción A: Usuario Nuevo (Recomendado)**
- **Email**: `admin@admin.com`
- **Contraseña**: `admin123`

**Opción B: Cambiar Usuario Existente**
- **Email**: `desenvolvedor@fellipelli.com.br`
- **Contraseña**: `admin123` (después de cambiar)

## ⚠️ Importante: Cambiar Contraseña Después

Una vez que entres, **cambia la contraseña inmediatamente** por seguridad.

## 🔐 Verificar Hash de Contraseña

Si quieres verificar qué contraseña corresponde a un hash:

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use Illuminate\Support\Facades\Hash;

// Verificar si una contraseña coincide con el hash
$hash = '$2y$12$2VZ2YQsmwtZjQKBvsAfgVuNZUyAyalJCv04NnVNMsgyn4SpYcczZO';

// Probar diferentes contraseñas comunes
$passwords = ['password', '123456', 'admin', 'admin123', 'mudar@123'];

foreach($passwords as $pwd) {
    if (Hash::check($pwd, $hash)) {
        echo "✅ Contraseña encontrada: $pwd\n";
        break;
    }
}
exit
```

¡Ejecuta el script completo para crear un admin con contraseña conocida! 🚀

