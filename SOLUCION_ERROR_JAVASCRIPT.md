# 🔧 Solución: Error JavaScript "Cannot read properties of null"

## ⚠️ Problema

```
meus-questionarios:803 Uncaught TypeError: Cannot read properties of null (reading 'addEventListener')
```

El JavaScript intenta agregar un event listener a un elemento que no existe en el DOM.

## ✅ Solución Aplicada

Se agregó una verificación para comprobar que el elemento existe antes de agregar el event listener:

```javascript
// Antes (causa error):
document.getElementById('btnGerarRelatorio').addEventListener('click', ...);

// Después (seguro):
const btnGerarRelatorio = document.getElementById('btnGerarRelatorio');
if (btnGerarRelatorio) {
    btnGerarRelatorio.addEventListener('click', ...);
}
```

## 🔄 Aplicar el Cambio

### Opción 1: Si ya hiciste push del cambio

```bash
# En el servidor
cd /var/www/laravel/EMOTIVE
git pull origin main

# Limpiar caches
php artisan view:clear
php artisan cache:clear
```

### Opción 2: Si necesitas aplicarlo manualmente

```bash
# En el servidor
cd /var/www/laravel/EMOTIVE
sudo nano resources/views/participante/index.blade.php
```

Busca la línea 240 aproximadamente y cambia el script.

## ✅ Verificación

Después de aplicar el cambio:

1. **Recargar la página** (Ctrl+F5 o Cmd+Shift+R)
2. **Abrir la consola del navegador** (F12)
3. **Verificar que no hay errores**

El error debería desaparecer. 🚀

