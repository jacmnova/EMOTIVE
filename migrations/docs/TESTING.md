# Guía de Testing - E.MO.TI.VE

## Backend (Python/FastAPI)

### Instalación

```bash
cd migrations/backend-api
pip install -r requirements.txt
```

### Ejecutar tests

```bash
# Todos los tests
pytest

# Tests específicos
pytest tests/test_auth.py
pytest tests/test_calculos.py

# Con cobertura
pytest --cov=app --cov-report=html
```

### Estructura de tests

- `tests/conftest.py` - Fixtures compartidas (client, db, usuarios)
- `tests/test_auth.py` - Autenticación (login, register, me)
- `tests/test_calculos.py` - Lógica de cálculos (puntuaciones, índices, IID)
- `tests/test_api_integration.py` - Integración API (CRUD endpoints)
- `tests/test_pdf.py` - Generación de PDFs
- `tests/test_load.py` - Pruebas de carga básicas

### Notas

- Usa SQLite en memoria para velocidad
- Cada test tiene su propia base de datos limpia
- Los fixtures proporcionan usuarios y tokens de prueba

## Frontend (Next.js)

### Instalación

```bash
cd migrations/frontend
npm install
```

### Tests Unitarios (Jest)

```bash
npm test
npm test -- --watch
```

### Tests E2E (Playwright)

```bash
# Instalar Playwright browsers (primera vez)
npx playwright install

# Ejecutar tests E2E
npm run test:e2e

# Interfaz visual
npm run test:e2e:ui
```

### Estructura de tests

- `__tests__/lib/` - Tests unitarios de utilidades (auth, api)
- `e2e/auth.spec.ts` - Tests E2E de autenticación
- `e2e/dashboard.spec.ts` - Tests E2E de dashboard

### Notas

- Los tests E2E requieren que el servidor de desarrollo esté corriendo (`npm run dev`)
- Los tests unitarios mockean las llamadas a la API
- Configurar `NEXT_PUBLIC_API_URL` en `.env.local` para tests E2E

## Próximos pasos

1. **Aumentar cobertura**: Agregar más tests unitarios para edge cases
2. **Tests E2E completos**: Flujos completos (crear formulario, responder, ver reporte)
3. **Tests de carga avanzados**: Usar pytest-benchmark y k6 para benchmarks detallados
4. **CI/CD**: Integrar tests en GitHub Actions o similar
