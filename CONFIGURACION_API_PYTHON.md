# 🔧 Configuración de la API de Python para Generación de Reportes

## 📋 Descripción

Cuando un usuario finaliza un formulario, el sistema Laravel genera un JSON simplificado con los datos del reporte y lo envía automáticamente a una API de Python que corre internamente.

## 🚀 Configuración

### 1. Variable de Entorno

Agrega la siguiente variable en tu archivo `.env`:

```env
PYTHON_RELATORIO_API_URL=http://localhost:5000/generate
```

**Nota:** Si no se configura esta variable, por defecto usará `http://localhost:5000/generate`

### 2. Ejemplo de Configuración

```env
# Para desarrollo local (puerto por defecto de la API Python)
PYTHON_RELATORIO_API_URL=http://localhost:5000/generate

# Para producción (si la API está en otro servidor/puerto)
PYTHON_RELATORIO_API_URL=http://127.0.0.1:5000/generate

# O si está en otro servidor
PYTHON_RELATORIO_API_URL=http://192.168.1.100:5000/generate
```

## 📨 Estructura del JSON Enviado

El sistema envía un POST request con el siguiente formato compatible con la API de generación de documentos de Python:

```json
{
  "template_id": "001",
  "data": {
    "header": {
      "title": "Questionário de Riscos Psicossociais - QRP-36"
    },
    "welcome_screen": {
      "title": "Bienvenido, João Silva",
      "body": "<p>Este es tu reporte personalizado...</p>",
      "show_btn": false,
      "text_btn": "",
      "link_btn": ""
    },
    "explanation_screen": {
      "title": "Sobre este Reporte",
      "body": "<p>Descripción del formulario...</p>",
      "show_img": false,
      "img_link": ""
    },
    "respuestas": {
      "sections": [
        {
          "title": "Satisfação no Trabalho (ST)",
          "body": "<h4>Satisfação no Trabalho (ST)</h4><p><strong>Puntuación:</strong> 45 puntos</p>..."
        }
      ]
    }
  },
  "output_format": "both"
}
```

## 🔌 Endpoint Esperado

La API de Python debe tener un endpoint que:

- **Método:** POST
- **Ruta:** `/generate` (por defecto, configurable)
- **Content-Type:** `application/json`
- **Respuesta esperada:** HTTP 200-299 para éxito

### Ejemplo de Endpoint en Python (Flask)

```python
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/generate', methods=['POST'])
def generar_relatorio():
    datos = request.json
    
    template_id = datos['template_id']
    output_format = datos['output_format']  # 'both', 'html', 'pdf'
    data = datos['data']
    
    header = data['header']
    welcome_screen = data['welcome_screen']
    explanation_screen = data['explanation_screen']
    sections = data['respuestas']['sections']
    
    # Procesar y generar el reporte (HTML y/o PDF)
    # ...
    
    return jsonify({
        'status': 'success',
        'unique_id': 'generated_unique_id',
        'files': {
            'html': 'path/to/file.html',
            'pdf': 'path/to/file.pdf'
        }
    }), 200
```

## ⚠️ Manejo de Errores

El sistema Laravel maneja los errores de la siguiente manera:

1. **Si la API no responde:** Se registra el error en los logs pero **no interrumpe** el flujo del usuario
2. **Si hay timeout:** Se registra el error (timeout configurado a 30 segundos)
3. **Si hay excepción:** Se registra en los logs con detalles completos

### Ver Logs

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log | grep "Python"
```

## 🔍 Verificación

Para verificar que los datos se están enviando correctamente:

1. Revisa los logs de Laravel:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Busca mensajes como:
   - `Datos enviados exitosamente a la API de Python`
   - `Error al enviar datos a la API de Python`

3. Verifica que tu API de Python esté recibiendo los requests

## 📝 Notas Importantes

- El envío se hace **asíncrono** - no bloquea la respuesta al usuario
- Si la API falla, el usuario puede seguir usando el sistema normalmente
- Los datos se envían **después** de marcar el formulario como completo
- El timeout es de **30 segundos** por defecto

## 🔄 Flujo Completo

```
Usuario finaliza formulario
    ↓
Formulario marcado como "completo"
    ↓
Generar JSON simplificado
    ↓
Enviar POST a API de Python
    ↓
(Continuar con flujo normal - generar análisis con OpenAI, etc.)
    ↓
Redirigir al reporte
```

