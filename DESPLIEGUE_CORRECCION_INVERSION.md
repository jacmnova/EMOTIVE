# Despliegue: Corrección de Lógica de Inversión

## 📋 Resumen de Cambios

Se corrigió la lógica de inversión para que se aplique uniformemente a todas las dimensiones (EXEM, REPR, DECI, FAPS, EXTR, ASMO).

### Archivos Modificados

1. **app/Http/Controllers/DadosController.php**
   - Mejorada documentación del método `obterValorRespostaComInversao`
   - Añadidos comentarios explicando que la lógica se aplica a todas las dimensiones
   - Lógica de inversión: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0

2. **app/Http/Controllers/RelatorioController.php**
   - Mejorada documentación del método `obterValorRespostaComInversao`
   - Añadidos comentarios explicando que la lógica se aplica a todas las dimensiones
   - Misma lógica de inversión que DadosController

3. **app/Http/Controllers/AnaliseController.php**
   - Actualizada lógica de inversión para mantener consistencia
   - Misma lógica: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0

4. **app/Traits/CalculaEjesAnaliticos.php**
   - Actualizada lógica de inversión en el cálculo de ejes analíticos
   - Misma lógica aplicada uniformemente

5. **app/Console/Commands/ProbarLogicaInversion.php** (NUEVO)
   - Comando para probar la lógica de inversión
   - Verifica que todas las dimensiones usen la misma lógica

## 🔧 Comandos para Ejecutar en el Servidor

### 1. Subir los cambios al servidor

```bash
# En tu máquina local
git add .
git commit -m "Corrección: Lógica de inversión uniforme para todas las dimensiones"
git push origin main
```

### 2. En el servidor - Actualizar código

```bash
# Conectarse al servidor
ssh usuario@servidor

# Ir al directorio del proyecto
cd /ruta/al/proyecto

# Actualizar código desde git
git pull origin main

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Actualizar relaciones pregunta-variable (IMPORTANTE)

```bash
# Ejecutar comando para actualizar relaciones según el CSV
php artisan actualizar:relaciones-preguntas
```

Este comando:
- Elimina las relaciones antiguas
- Crea nuevas relaciones según el CSV
- Actualiza automáticamente los rangos B, M, A de las variables

### 4. Verificar que todo funciona

```bash
# Probar la lógica de inversión
php artisan emotive:probar-inversion 1
```

Este comando mostrará:
- Total de preguntas por dimensión
- Preguntas normales vs invertidas
- Cálculos para diferentes casos
- Verificación de que la lógica es correcta

### 5. Reiniciar servicios (si es necesario)

```bash
# Si usas PHP-FPM
sudo systemctl restart php-fpm

# Si usas supervisor para colas
sudo supervisorctl restart all
```

## ✅ Verificación Post-Despliegue

1. **Verificar que las relaciones están correctas:**
   ```bash
   php artisan emotive:probar-inversion 1
   ```
   Debe mostrar preguntas invertidas asociadas a las dimensiones correctas.

2. **Probar un cálculo real:**
   - Generar un reporte para un usuario de prueba
   - Verificar que los valores del gráfico radar sean correctos
   - Verificar que cuando todas las respuestas son 0, las preguntas invertidas se convierten en 6

3. **Verificar logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Buscar mensajes de "APLICANDO INVERSIÓN" para confirmar que la lógica funciona.

## 📝 Notas Importantes

- **Lógica de inversión:** Las preguntas invertidas convierten 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
- **Para resultado 0:** Las preguntas normales deben estar en 0, las invertidas en 6
- **Preguntas invertidas:** 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
- **Todas las dimensiones** usan la misma lógica de inversión basada en `numero_da_pergunta`

## 🐛 Solución de Problemas

### Problema: Valores diferentes de 0 cuando todas las respuestas están en 0

Si después del despliegue, dimensiones como ASMO, REPR o DECI muestran valores cuando deberían ser 0:

1. **Diagnosticar el problema:**
   ```bash
   # Reemplazar {usuario_id} con el ID del usuario que tiene el problema
   php artisan emotive:diagnosticar-radar {usuario_id} 1 --todas-respuestas
   ```

2. **Verificar que las relaciones se actualizaron:**
   ```bash
   php artisan tinker
   >>> $variavel = \App\Models\Variavel::where('tag', 'ExEm')->first();
   >>> $variavel->perguntas->count();
   ```

3. **Verificar que las preguntas invertidas están identificadas:**
   ```bash
   php artisan tinker
   >>> $pergunta = \App\Models\Pergunta::where('numero_da_pergunta', 48)->first();
   >>> $pergunta->id;
   ```

4. **Limpiar todo el caché:**
   ```bash
   php artisan optimize:clear
   ```

### Verificar respuestas del usuario

Si el diagnóstico muestra que hay respuestas con valor > 0:
- Verificar en la base de datos que todas las respuestas estén realmente en 0
- Si hay preguntas invertidas, recordar que para resultado 0: normales en 0, invertidas en 6

## 📦 Archivos a Subir

Asegúrate de que estos archivos estén en el commit:

- ✅ app/Http/Controllers/DadosController.php
- ✅ app/Http/Controllers/RelatorioController.php
- ✅ app/Http/Controllers/AnaliseController.php
- ✅ app/Traits/CalculaEjesAnaliticos.php
- ✅ app/Console/Commands/ProbarLogicaInversion.php

## 🎯 Resultado Esperado

Después del despliegue:
- ✅ Todas las dimensiones usan la misma lógica de inversión
- ✅ Las preguntas invertidas se identifican correctamente por `numero_da_pergunta`
- ✅ Cuando una pregunta invertida tiene valor 0, se convierte en 6
- ✅ Cuando una pregunta invertida tiene valor 6, se convierte en 0
- ✅ El gráfico radar muestra valores correctos

