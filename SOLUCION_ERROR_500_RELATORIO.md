# 🔧 Solución Error 500 - Relatorio

## 🔍 Problemas Identificados

### 1. Conflicto de Rutas (Error al cachear rutas)
El error muestra que hay un conflicto con el nombre de ruta `home`:
```
Unable to prepare route [dashboard] for serialization. Another route has already been assigned name [home].
```

**Causa:** `Auth::routes()` puede estar registrando una ruta 'home' automáticamente, y luego tenemos otra ruta 'home' manual.

**Solución aplicada:** Se modificó `Auth::routes()` para especificar opciones explícitas.

### 2. Error 500 al acceder al relatorio
El error 500 puede deberse a:
- Datos faltantes en la base de datos
- Validaciones fallando
- Valores null o no numéricos en los cálculos
- Problemas con las relaciones de Eloquent

## ✅ Soluciones Aplicadas

### 1. Corrección de Rutas (routes/web.php)
Se cambió:
```php
Auth::routes();
```

Por:
```php
Auth::routes(['register' => true, 'verify' => false]);
```

### 2. Validaciones Agregadas en DadosController
Se agregaron validaciones para:
- Verificar que el formulario tenga preguntas
- Verificar que haya variables asociadas
- Validar valores numéricos antes de calcular
- Manejar valores null correctamente

## 🚀 Pasos para Aplicar en el Servidor EC2

### Paso 1: Conectarse al servidor
```bash
ssh ec2-user@52.15.213.45
# O el usuario que uses
```

### Paso 2: Ir al directorio del proyecto
```bash
cd /var/www/laravel/EMOTIVE
```

### Paso 3: Actualizar el código
```bash
# Opción A: Si usas Git
git pull origin main

# Opción B: Si no usas Git, copiar el archivo routes/web.php manualmente
```

### Paso 4: Limpiar caches
```bash
# Limpiar todos los caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimizar (ahora debería funcionar sin el error de rutas)
php artisan optimize
```

### Paso 5: Ejecutar el script de diagnóstico
```bash
# Copiar el script al servidor (si no está)
# O ejecutar directamente los comandos del script

# Ver errores recientes del relatorio
tail -200 storage/logs/laravel.log | grep -A 30 -i "relatorioShow\|error\|exception" | tail -80

# Ver errores específicos
tail -500 storage/logs/laravel.log | grep -E "formulario_id.*1|usuario_id.*5" | tail -20
```

### Paso 6: Verificar datos en la base de datos
```bash
php artisan tinker
```

Luego ejecutar:
```php
$formulario = \App\Models\Formulario::find(1);
$usuario = \App\Models\User::find(5);
echo "Formulario: " . ($formulario ? $formulario->nome : "NO EXISTE") . "\n";
echo "Usuario: " . ($usuario ? $usuario->name : "NO EXISTE") . "\n";
if ($formulario) {
    echo "Preguntas: " . $formulario->perguntas->count() . "\n";
}
if ($formulario && $usuario) {
    $respostas = \App\Models\Resposta::where('user_id', 5)
        ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
        ->count();
    echo "Respuestas: " . $respostas . "\n";
}
exit
```

### Paso 7: Probar la URL
```bash
# Probar localmente
curl "http://localhost/meurelatorio/show?formulario_id=1&usuario_id=5"

# O desde el navegador
# http://52.15.213.45/meurelatorio/show?formulario_id=1&usuario_id=5
```

## 🔍 Comandos de Diagnóstico Rápido

### Ver último error completo
```bash
cd /var/www/laravel/EMOTIVE
tail -500 storage/logs/laravel.log | grep -A 50 -i "error\|exception" | tail -100
```

### Ver errores del relatorio específicamente
```bash
cd /var/www/laravel/EMOTIVE
tail -1000 storage/logs/laravel.log | grep -A 30 -i "relatorioShow" | tail -80
```

### Ver errores en tiempo real
```bash
cd /var/www/laravel/EMOTIVE
tail -f storage/logs/laravel.log
```

Luego, en otra terminal o navegador, accede a:
```
http://52.15.213.45/meurelatorio/show?formulario_id=1&usuario_id=5
```

### Verificar permisos
```bash
cd /var/www/laravel/EMOTIVE
ls -la storage/logs/
chmod 664 storage/logs/laravel.log
chown www-data:www-data storage/logs/laravel.log
# O si el usuario es diferente:
# chown ec2-user:ec2-user storage/logs/laravel.log
```

## 📋 Checklist de Verificación

- [ ] Código actualizado en el servidor
- [ ] Caches limpiados
- [ ] Rutas optimizadas sin errores
- [ ] Formulario ID 1 existe en la base de datos
- [ ] Usuario ID 5 existe en la base de datos
- [ ] El formulario tiene preguntas asociadas
- [ ] El usuario tiene respuestas para ese formulario
- [ ] Permisos de logs correctos
- [ ] Error específico identificado en los logs

## 🐛 Errores Comunes y Soluciones

### Error: "Formulario no encontrado"
**Solución:** Verificar que el formulario_id=1 existe en la tabla `formularios`

### Error: "Usuario no encontrado"
**Solución:** Verificar que el usuario_id=5 existe en la tabla `users`

### Error: "El formulario no tiene preguntas asociadas"
**Solución:** Ejecutar los seeders o verificar que hay preguntas en la tabla `perguntas` con `formulario_id=1`

### Error: "El formulario no tiene variables asociadas"
**Solución:** Verificar que hay variables en la tabla `variaveis` con `formulario_id=1`

### Error: "Division by zero" o valores null
**Solución:** Ya se agregaron validaciones en el código para manejar estos casos

## 📞 Si el Error Persiste

1. Ejecutar el script de diagnóstico completo
2. Copiar el error completo de los logs
3. Verificar que todos los datos existen en la base de datos
4. Verificar que las relaciones de Eloquent están correctamente definidas

