# Comandos para Aplicar Correcciones E.MO.TI.VE en Producción

## Pasos a Ejecutar en el Servidor

### 1. Conectarse al servidor
```bash
ssh usuario@tu-servidor
cd /ruta/a/tu/proyecto
```

### 2. Actualizar el código (si usas Git)
```bash
git pull origin main
# o la rama que uses
```

### 3. Instalar dependencias (si hay cambios en composer.json)
```bash
composer install --no-dev --optimize-autoloader
```

### 4. **IMPORTANTE: Actualizar las relaciones pregunta-variable**
Este es el paso más crítico. Debe ejecutarse para que las preguntas se relacionen correctamente con las variables según el CSV:
```bash
php artisan actualizar:relaciones-preguntas
```

Deberías ver una salida como:
```
Actualizando relaciones pregunta-variable según el CSV...
Relaciones antiguas eliminadas.
  ExEm: Exaustão Emocional - 26 preguntas
  RePr: Realização Profissional - 26 preguntas
  DeCi: Despersonalização / Cinismo - 29 preguntas
  FaPs: Fatores Psicossociais - 10 preguntas
  ExTr: Excesso de Trabalho - 16 preguntas
  AsMo: Assédio Moral - 15 preguntas

✅ Relaciones actualizadas correctamente. Total: 122 relaciones.
```

### 5. Limpiar caché y optimizar
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 6. Optimizar para producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 7. Verificar permisos (si es necesario)
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# Ajusta www-data según tu configuración
```

### 8. Reiniciar servicios (si es necesario)
```bash
# Si usas PHP-FPM
sudo systemctl restart php8.1-fpm
# o la versión que uses

# Si usas supervisor para queues
sudo supervisorctl restart all
```

## Verificación

### Verificar que las relaciones están correctas:
```bash
php artisan tinker
```

Luego ejecuta:
```php
$variavel = \App\Models\Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'ExEm')->first();
echo "Total preguntas: " . $variavel->perguntas->count() . "\n";
// Debería mostrar 26 preguntas
exit
```

### Verificar que la inversión funciona:
```bash
php artisan emotive:diagnostico {user_id} {formulario_id}
```

Reemplaza `{user_id}` y `{formulario_id}` con valores reales.

## Notas Importantes

1. **El comando `actualizar:relaciones-preguntas` es CRÍTICO**: Sin ejecutarlo, las relaciones pregunta-variable no estarán correctas y los cálculos serán incorrectos.

2. **Los cambios en el código ya están aplicados** si hiciste `git pull`. Los archivos modificados fueron:
   - `app/Http/Controllers/DadosController.php`
   - `app/Http/Controllers/RelatorioController.php`
   - `app/Http/Controllers/AnaliseController.php`
   - `app/Traits/CalculaEjesAnaliticos.php`
   - `app/Console/Commands/ActualizarRelacionesPreguntas.php`

3. **Después de ejecutar los comandos**, pide a un usuario que genere un nuevo reporte con todas las respuestas en 5 para verificar que los valores coincidan con el CSV:
   - EXEM: 98
   - REPR: 98
   - DECI: 113
   - FAPS: 30
   - EXTR: 80
   - ASMO: 75

## Si algo sale mal

### Revertir cambios (si es necesario):
```bash
git log --oneline -10  # Ver últimos commits
git revert HEAD  # Revertir último commit si es necesario
```

### Ver logs:
```bash
tail -f storage/logs/laravel.log
```

### Verificar errores:
```bash
php artisan config:cache
php artisan route:cache
```

