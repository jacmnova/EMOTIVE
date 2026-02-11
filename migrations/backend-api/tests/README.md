# Tests Backend API

## Ejecutar tests

```bash
cd migrations/backend-api
pytest
```

## Ejecutar tests con cobertura

```bash
pytest --cov=app --cov-report=html
```

## Estructura

- `test_auth.py` - Tests de autenticación (login, register, me)
- `test_calculos.py` - Tests unitarios de lógica de cálculos
- `test_api_integration.py` - Tests de integración de endpoints API
- `test_pdf.py` - Tests de generación de PDFs
- `conftest.py` - Fixtures compartidas (client, db, usuarios de prueba)

## Notas

- Los tests usan SQLite en memoria para velocidad
- Cada test tiene su propia base de datos limpia
- Los fixtures proporcionan usuarios y tokens de prueba
