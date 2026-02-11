# Desplegar el backend en Railway

Guía para poner en producción la API E.MO.TI.VE en Railway con PostgreSQL.

---

## 1. Crear proyecto en Railway

1. Entra en [railway.app](https://railway.app) y crea un **nuevo proyecto**.
2. **Deploy from GitHub repo**: conecta el repositorio y selecciona este repo.
3. **Root Directory**: como el backend está en `migrations/backend-api`, en el servicio del backend configura:
   - **Settings** → **Root Directory** → `migrations/backend-api`
   Así el build y el start se ejecutan desde esa carpeta.

---

## 2. Base de datos PostgreSQL

Tienes dos opciones:

### Opción A: PostgreSQL ya creado (tu caso)

Ya tienes la base en Railway (o externa). Solo hay que inyectar la URL en el servicio del backend:

1. En el **servicio del backend** (no en el de la BD) → **Variables**.
2. Añade:
   - **Nombre:** `DATABASE_URL`
   - **Valor:** tu connection string de PostgreSQL, por ejemplo:
     ```
     postgresql://postgres:PASSWORD@HOST:PORT/railway
     ```
   Si Railway te dio `postgres://...`, puedes pegarla igual: la app la convierte a `postgresql://` internamente.

### Opción B: Crear PostgreSQL en el mismo proyecto

1. En el proyecto → **+ New** → **Database** → **PostgreSQL**.
2. En el servicio de PostgreSQL → **Variables** → copia `DATABASE_URL`.
3. En el **servicio del backend** → **Variables** → **Add variable** → **Add variable from another service** y elige `DATABASE_URL` del PostgreSQL.
   Así se inyecta automáticamente en cada deploy.

---

## 3. Variables de entorno obligatorias (backend)

En el servicio del backend, en **Variables**, configura al menos:

| Variable | Descripción | Ejemplo |
|----------|-------------|--------|
| `DATABASE_URL` | URL de PostgreSQL (ver apartado 2) | `postgresql://user:pass@host:port/railway` |
| `SECRET_KEY` | Clave para JWT (mín. 32 caracteres; no cambiarla tras tener usuarios) | Una string larga y aleatoria en base64 |
| `FRONTEND_URL` | URL del frontend en producción (emails, CORS) | `https://tu-app.vercel.app` |
| `CORS_ORIGINS` | Orígenes permitidos (lista JSON o separados por comas) | `https://tu-app.vercel.app` o `["https://..."]` |

Opcionales pero recomendadas:

| Variable | Descripción |
|----------|-------------|
| `APP_ENV` | `production` |
| `DEBUG` | `false` |
| `MAIL_*` | SMTP para emails (verificación, reset password, recordatorios). Ver `.env.example` |

---

## 4. Comandos de deploy (ya en código)

El `railway.toml` del backend define:

- **Pre-deploy:** `alembic upgrade head` (ejecuta migraciones antes de arrancar).
- **Start:** `uvicorn app.main:app --host 0.0.0.0 --port $PORT`.

No hace falta configurar nada más en la pestaña de deploy si usas ese archivo.

---

## 5. Dominio y comprobación

1. En el servicio del backend → **Settings** → **Networking** → **Generate Domain**.
2. Prueba:
   - `https://tu-dominio.up.railway.app/` → debe devolver `{"message":"E.MO.TI.VE API","version":"1.0.0"}`.
   - `https://tu-dominio.up.railway.app/health` → `{"status":"ok"}`.
   - `https://tu-dominio.up.railway.app/api/docs` → Swagger UI.

---

## 6. Frontend apuntando al backend

En el frontend (Vercel u otro), define:

```env
NEXT_PUBLIC_API_URL=https://tu-dominio.up.railway.app
```

Y en Railway, en `CORS_ORIGINS`, incluye la URL del frontend (por ejemplo `https://tu-app.vercel.app`).

---

## 7. Primer usuario

Si la base está vacía, al arrancar la API se crea un usuario por defecto:

- **Email:** `admin@example.com`
- **Contraseña:** `admin123`

Cambia la contraseña y el email en cuanto entres. Opcional: ejecutar el seed para datos de prueba:

```bash
# Desde la raíz del repo, con DATABASE_URL en el entorno
cd migrations/backend-api && python -m app.seed
```

---

## Resumen rápido

1. Proyecto Railway → Deploy from GitHub → Root Directory = `migrations/backend-api`.
2. Variables en el servicio backend: `DATABASE_URL`, `SECRET_KEY`, `FRONTEND_URL`, `CORS_ORIGINS`.
3. Generate Domain en el servicio backend.
4. Configurar `NEXT_PUBLIC_API_URL` y CORS en el frontend.

Si algo falla, revisa **Logs** del servicio en Railway; los errores de conexión a BD o de migraciones suelen aparecer ahí.
