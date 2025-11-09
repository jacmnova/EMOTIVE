# 🚀 Despliegue: Corrección de Cálculo EE, PR, SO e IID

## ✅ Cambios Realizados

### 1. Agrupaciones Actualizadas según CSV ALE

Las agrupaciones de preguntas para EE, PR, SO han sido actualizadas según el archivo `EMULADOR - EMOTIVE ALE - perguntas_completas_99 (1).csv`:

**EE (Energia Emocional)**: 19 preguntas
- [28, 29, 30, 33, 34, 37, 38, 39, 40, 41, 43, 44, 45, 47, 55, 56, 61, 95, 99]
- Máximo posible: 114 puntos (19 × 6)

**PR (Propósito e Relações)**: 12 preguntas
- [28, 29, 30, 33, 34, 55, 56, 80, 82, 83, 84, 85]
- Máximo posible: 72 puntos (12 × 6)

**SO (Sustentabilidade Ocupacional)**: 14 preguntas
- [62, 63, 64, 65, 66, 68, 69, 70, 72, 73, 74, 75, 76, 77]
- Máximo posible: 84 puntos (14 × 6)

### 2. Cálculo de IID Actualizado

El cálculo del Índice Integrado de Descarrilamento (IID) ahora usa los nuevos máximos:

- **Promedio de máximos**: (114 + 72 + 84) / 3 = **90**
- **Fórmula**: (Promedio de EE, PR, SO) / 90 × 100

### 3. Archivos Modificados

- `app/Traits/CalculaEjesAnaliticos.php`
  - Actualizado método `calcularIndicesDesdeRespostas()` con nuevas agrupaciones
  - Actualizado método `calcularIID()` con nuevos máximos

- `app/Console/Commands/ProbarCalculoEEPRSO.php` (nuevo)
  - Comando para probar los cálculos de EE, PR, SO e IID

## 🧪 Pruebas

Para probar los cálculos:

```bash
php artisan emotive:probar-ee-pr-so {usuario_id}
```

Ejemplo:
```bash
php artisan emotive:probar-ee-pr-so 11
```

## 🚀 Pasos para Desplegar

```bash
# 1. En el servidor, después de git pull
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 2. Probar con un usuario
php artisan emotive:probar-ee-pr-so 11

# 3. Verificar que los valores de EE, PR, SO e IID sean correctos
```

## ⚠️ Nota Importante

**NO se modificaron los cálculos del gráfico radar** (EXEM, REPR, DECI, FAPS, EXTR, ASMO). Estos siguen funcionando correctamente según las relaciones actualizadas del CSV ALE.

