# 🔧 Solución: Emails No Se Envían y No Puedes Entrar

## 🔍 Problemas Identificados

1. **Emails no se envían**: El mailer está configurado como `log` por defecto
2. **No puedes entrar**: Probablemente necesitas verificación de email o el usuario no está activo

## ✅ Solución Paso a Paso

### PASO 1: Verificar Estado de tu Usuario

```bash
cd /var/www/laravel/EMOTIVE

# Conectar a MySQL
mysql -u laravel_user -p laravel_db
```

Dentro de MySQL, verifica tu usuario:
```sql
SELECT id, name, email, email_verified_at, ativo, sa, admin FROM users WHERE email = 'tu_email@ejemplo.com';
EXIT;
```

### PASO 2: Activar Usuario Manualmente (Si es Necesario)

Si tu usuario no tiene `email_verified_at` configurado o `ativo = 0`:

```bash
mysql -u laravel_user -p laravel_db
```

```sql
-- Verificar tu usuario
SELECT id, name, email, email_verified_at, ativo FROM users WHERE email = 'tu_email@ejemplo.com';

-- Activar y verificar email manualmente
UPDATE users 
SET email_verified_at = NOW(), 
    ativo = 1 
WHERE email = 'tu_email@ejemplo.com';

-- Verificar cambio
SELECT id, name, email, email_verified_at, ativo FROM users WHERE email = 'tu_email@ejemplo.com';
EXIT;
```

### PASO 3: Configurar Email para Producción

El problema es que el mailer está en `log` (solo guarda en logs, no envía). Necesitas configurar SMTP.

#### Opción A: Usar SMTP (Gmail, Outlook, etc.)

```bash
cd /var/www/laravel/EMOTIVE
sudo nano .env
```

**Agrega/Actualiza estas líneas** (ejemplo con Gmail):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password_gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="tu_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Para Gmail, necesitas crear una "App Password"**:
1. Ve a tu cuenta de Google
2. Seguridad → Verificación en 2 pasos (debe estar activa)
3. Contraseñas de aplicaciones → Generar nueva

#### Opción B: Usar Mailtrap (Para Pruebas)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Opción C: Usar AWS SES (Recomendado para EC2)

```env
MAIL_MAILER=ses
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"

# AWS SES no necesita MAIL_HOST, MAIL_PORT, etc.
```

### PASO 4: Limpiar Cache de Configuración

```bash
cd /var/www/laravel/EMOTIVE

# Limpiar cache de configuración
php artisan config:clear

# Regenerar cache
php artisan config:cache

# Probar envío de email
php artisan tinker
```

Dentro de Tinker:
```php
use App\Models\User;
$user = User::first();
$user->notify(new \App\Notifications\VerificarEmail('test-token'));
exit
```

Verificar logs:
```bash
tail -f storage/logs/laravel.log
```

### PASO 5: Activar Usuarios Existentes (Solución Rápida)

Si ya tienes usuarios creados pero no pueden entrar:

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
// Activar todos los usuarios y verificar emails
use App\Models\User;

// Verificar todos los usuarios
User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
User::where('ativo', 0)->update(['ativo' => 1]);

// Verificar
User::count();
User::whereNotNull('email_verified_at')->count();

exit
```

## 🚀 Solución Rápida (Todo en Uno)

```bash
cd /var/www/laravel/EMOTIVE && \

# 1. Activar y verificar todos los usuarios existentes
php artisan tinker <<'PHP'
use App\Models\User;
User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
User::where('ativo', 0)->update(['ativo' => 1]);
echo "Usuarios activados\n";
exit
PHP

# 2. Configurar email como log (temporal para que funcione)
sudo sed -i 's/MAIL_MAILER=.*/MAIL_MAILER=log/' .env

# 3. Limpiar cache
php artisan config:clear
php artisan config:cache

echo "✅ Usuarios activados. Puedes entrar ahora."
echo "📧 Para enviar emails reales, configura SMTP en .env"
```

## 📧 Configurar SMTP Completo

### Ejemplo con Gmail:

```bash
cd /var/www/laravel/EMOTIVE
sudo nano .env
```

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="tu_email@gmail.com"
MAIL_FROM_NAME="Sistema Burnout"
```

Luego:
```bash
php artisan config:clear
php artisan config:cache
```

### Ejemplo con SendGrid:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu_api_key_sendgrid
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="Sistema Burnout"
```

## 🔑 Verificar Usuario Específico

```bash
cd /var/www/laravel/EMOTIVE

php artisan tinker
```

```php
use App\Models\User;

// Buscar tu usuario
$user = User::where('email', 'tu_email@ejemplo.com')->first();

// Ver estado
echo "Email: " . $user->email . "\n";
echo "Verificado: " . ($user->email_verified_at ? 'Sí' : 'No') . "\n";
echo "Activo: " . ($user->ativo ? 'Sí' : 'No') . "\n";

// Activar si es necesario
$user->email_verified_at = now();
$user->ativo = 1;
$user->save();

echo "✅ Usuario activado\n";
exit
```

## ✅ Checklist

- [ ] Usuario tiene `email_verified_at` configurado
- [ ] Usuario tiene `ativo = 1`
- [ ] Email configurado en `.env` (SMTP o log)
- [ ] Cache de configuración limpiado
- [ ] Puedes hacer login

## 🎯 Solución Inmediata para Entrar

```bash
cd /var/www/laravel/EMOTIVE

# Activar todos los usuarios
php artisan tinker <<'PHP'
use App\Models\User;
User::query()->update([
    'email_verified_at' => now(),
    'ativo' => 1
]);
echo "✅ Todos los usuarios activados\n";
exit
PHP

echo "Ahora puedes entrar con cualquier usuario"
```

¡Ejecuta la solución inmediata para poder entrar ahora! 🚀

