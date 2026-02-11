# Tests Frontend

## Tests Unitarios (Jest)

```bash
npm test
npm test -- --watch
```

Tests en `__tests__/` usando Jest y React Testing Library.

## Tests E2E (Playwright)

```bash
npm run test:e2e
npm run test:e2e:ui  # Interfaz visual
```

Tests E2E en `e2e/` usando Playwright.

## Estructura

- `__tests__/lib/` - Tests unitarios de utilidades (auth, api)
- `e2e/` - Tests end-to-end (auth, dashboard, flujos completos)

## Notas

- Los tests E2E requieren que el servidor de desarrollo esté corriendo
- Los tests unitarios mockean las llamadas a la API
- Configurar `NEXT_PUBLIC_API_URL` en `.env.local` para tests E2E
