# 🔍 Verificar Payload Enviado a la API de Python

## ⚠️ Problema

El payload que se envía a la API de Python solo contiene `{formulario_id: "1", usuario_id: "1"}` en lugar del JSON completo con la estructura del relatorio.

## ✅ Solución Aplicada

Se corrigió el método `enviarDatosAPython()` para:

1. **Enviar como JSON** con headers correctos:
   - `Content-Type: application/json`
   - `Accept: application/json`
   - Usa `acceptJson()` y `withHeaders()`

2. **Agregar logs** para debug:
   - Log del payload antes de enviar
   - Log del payload en caso de error

## 🔍 Verificar que Funciona

### 1. Ver los Logs

```bash
cd /var/www/laravel/EMOTIVE
tail -f storage/logs/laravel.log | grep -i "python\|api"
```

Deberías ver algo como:
```
Enviando datos a la API de Python
payload: {
  "template_id": "001",
  "data": {
    "header": {...},
    "welcome_screen": {...},
    ...
  }
}
```

### 2. Probar el Endpoint Directamente

```bash
# Desde el servidor
curl -X POST http://localhost/relatorio/generar-api \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: tu_token" \
  -d '{
    "formulario_id": 1,
    "usuario_id": 1
  }' \
  -v
```

### 3. Verificar en la API de Python

Asegúrate de que tu API de Python esté escuchando en el puerto correcto y pueda recibir JSON.

## 📋 Estructura del Payload Correcto

El payload que se envía debería ser:

```json
{
  "template_id": "001",
  "data": {
    "header": {
      "title": "Nombre del Formulario - Label"
    },
    "welcome_screen": {
      "title": "Bienvenido, Nombre Usuario",
      "body": "<p>Este es tu reporte personalizado...</p>",
      "show_btn": false,
      "text_btn": "",
      "link_btn": ""
    },
    "explanation_screen": {
      "title": "Sobre este Reporte",
      "body": "<p>Descripción...</p>",
      "show_img": false,
      "img_link": ""
    },
    "respuestas": {
      "sections": [
        {
          "title": "Variable (TAG)",
          "body": "<h4>...</h4><p>Puntuación: X puntos</p>..."
        }
      ]
    }
  },
  "output_format": "both"
}
```

## 🔧 Si Aún No Funciona

1. **Verificar que `prepararDatosParaRelatorio()` devuelve los datos correctos:**
```bash
cd /var/www/laravel/EMOTIVE
php artisan tinker
```

```php
$controller = new \App\Http\Controllers\DadosController();
$datos = $controller->prepararDatosParaRelatorio(1, 1);
print_r($datos);
exit
```

2. **Verificar la URL de la API:**
```bash
grep PYTHON_RELATORIO_API_URL .env
```

3. **Probar la API de Python directamente:**
```bash
curl -X POST http://localhost:5000/generate \
  -H "Content-Type: application/json" \
  -d '{
    "template_id": "001",
    "data": {
      "header": {"title": "Test"}
    },
    "output_format": "both"
  }'
```

¡Ahora debería enviar el payload completo! 🚀

