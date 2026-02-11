# Manual de lanzamiento – E.MO.TI.VE (Next.js + FastAPI)

Guía para poner en marcha la aplicación migrada: backend en Python (FastAPI) y frontend en Next.js.

---

## Inicio rápido (desarrollo local)

```bash
# 1. Backend
cd migrations/backend-api
python3 -m venv .venv && source .venv/bin/activate   # Windows: .venv\Scripts\activate
pip install -r requirements.txt
cp .env.example .env   # Editar SECRET_KEY y opcionalmente DATABASE_URL
mkdir -p uploads
uvicorn app.main:app --reload --port 8000

# 2. Frontend (otra terminal)
cd migrations/frontend
npm install
cp .env.local.example .env.local   # Dejar NEXT_PUBLIC_API_URL=http://localhost:8000
npm run dev
```

Abrir **http://localhost:3000** (frontend) y **http://localhost:8000/api/docs** (API).

---

## 1. Requisitos previos

| Componente      | Versión recomendada |
|-----------------|---------------------|
| Python          | 3.11 o 3.12 (evitar 3.13 si hay problemas con dependencias) |
| Node.js         | 18.x o 20.x LTS     |
| npm             | 9.x o 10.x          |
| Base de datos   | SQLite (desarrollo) o MySQL 8 (producción) |
| Git             | Cualquier versión reciente |

Opcional para producción: servidor web (Nginx), proceso PM2 o similar, certificados SSL.

---

## 2. Estructura del proyecto

```
NR1/
├── migrations/
│   ├── docs/                  # Toda la documentación (.md)
│   │   ├── README.md          # Índice
│   │   ├── MANUAL_LANZAMIENTO.md  # Este manual
│   │   ├── PLAN_MIGRACION.md
│   │   ├── TESTING.md
│   │   └── ...
│   ├── backend-api/           # API FastAPI
│   │   ├── app/
│   │   ├── tests/
│   │   ├── requirements.txt
│   │   ├── .env.example
│   │   └── run-tests.sh
│   ├── frontend/               # Next.js
│   │   ├── app/
│   │   ├── lib/
│   │   ├── package.json
│   │   ├── .env.local.example
│   │   └── run-tests.sh
│   └── README.md
└── php_old_app/                # Aplicación Laravel original (referencia)
```

---

## 3. Backend (API FastAPI)

### 3.1 Entorno virtual e instalación

```bash
cd migrations/backend-api

# Crear entorno virtual
python3 -m venv .venv

# Activar (Linux/macOS)
source .venv/bin/activate

# Activar (Windows CMD)
.venv\Scripts\activate.bat

# Instalar dependencias
pip install --upgrade pip
pip install -r requirements.txt
```

Si usas **Python 3.13** y falla la instalación de `pydantic-core`, usa Python 3.11 o 3.12, o revisa que `requirements.txt` tenga `pydantic>=2.9.0` (versiones con wheels para 3.13).

### 3.2 Variables de entorno

```bash
cp .env.example .env
# Editar .env con tu configuración
```

Variables mínimas para desarrollo:

| Variable | Descripción | Ejemplo desarrollo |
|----------|-------------|--------------------|
| `DATABASE_URL` | Conexión BD | `sqlite:///./database.sqlite` |
| `SECRET_KEY` | Clave JWT (mín. 32 caracteres) | Una cadena larga y aleatoria |
| `CORS_ORIGINS` | Orígenes permitidos (frontend) | `["http://localhost:3000"]` |
| `FRONTEND_URL` | URL del frontend (emails) | `http://localhost:3000` |

Opcionales para funcionalidad completa:

| Variable | Descripción |
|----------|-------------|
| `OPENAI_API_KEY` | Para Chat y análisis con IA |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM` | Envío de emails (verificación, recuperación de contraseña) |
| `PDF_SERVICE_URL` | Servicio externo de PDF (opcional; también se usa WeasyPrint) |

Para **MySQL** en producción:

```env
DATABASE_URL=mysql+pymysql://usuario:contraseña@localhost:3306/nombre_bd
```

### 3.3 Base de datos

**SQLite (desarrollo):**  
Al arrancar la API, si el archivo no existe, puedes crearlo y las tablas con un script o con Alembic si está configurado.

**Crear tablas desde modelos (desarrollo rápido):**

Si no usas migraciones Alembic, puedes crear las tablas con un script que importe los modelos y ejecute `Base.metadata.create_all(engine)` (ver `app/database.py`). Si usas Alembic:

```bash
alembic upgrade head
```

**Columnas extra para notificaciones (si la BD ya existía):**

Si la tabla `users` ya existe y no tiene columnas de reset de contraseña:

```sql
-- SQLite
ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(64);
ALTER TABLE users ADD COLUMN password_reset_expires DATETIME;

-- MySQL
ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN password_reset_expires DATETIME NULL;
```

### 3.4 Ejecutar el backend

**Desarrollo:**

```bash
cd migrations/backend-api
source .venv/bin/activate
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

- API: `http://localhost:8000`
- Documentación: `http://localhost:8000/api/docs`
- ReDoc: `http://localhost:8000/api/redoc`

**Producción (ejemplo sin proxy):**

```bash
uvicorn app.main:app --host 0.0.0.0 --port 8000 --workers 2
```

En producción es recomendable poner detrás de Nginx (u otro proxy) con SSL y configurar `CORS_ORIGINS` y `FRONTEND_URL` según el dominio real.

### 3.5 Directorios que debe crear la aplicación

- `uploads/` (o el path definido en `UPLOAD_DIR`): avatares, logos, etc.

```bash
mkdir -p migrations/backend-api/uploads
```

---

## 4. Frontend (Next.js)

### 4.1 Instalación

```bash
cd migrations/frontend
npm install
```

### 4.2 Variables de entorno

```bash
cp .env.local.example .env.local
# Editar .env.local
```

| Variable | Descripción | Ejemplo desarrollo |
|----------|-------------|--------------------|
| `NEXT_PUBLIC_API_URL` | URL base de la API | `http://localhost:8000` |

En producción debe apuntar a la URL pública del backend (ej. `https://api.midominio.com`).

### 4.3 Ejecutar el frontend

**Desarrollo:**

```bash
cd migrations/frontend
npm run dev
```

- Aplicación: `http://localhost:3000`

**Producción (build + servidor):**

```bash
npm run build
npm start
```

Por defecto el servidor de Next.js escucha en el puerto 3000. En producción es habitual poner Nginx (u otro) delante y servir el frontend por HTTPS.

---

## 5. Lanzamiento en desarrollo (ambos a la vez)

1. **Terminal 1 – Backend**

   ```bash
   cd migrations/backend-api
   source .venv/bin/activate
   uvicorn app.main:app --reload --port 8000
   ```

2. **Terminal 2 – Frontend**

   ```bash
   cd migrations/frontend
   npm run dev
   ```

3. Comprobar:
   - Frontend: `http://localhost:3000`
   - API: `http://localhost:8000`
   - Docs: `http://localhost:8000/api/docs`

4. Flujo típico: registro o login en el frontend; el frontend llama a `NEXT_PUBLIC_API_URL` (p. ej. `http://localhost:8000`) para auth y resto de endpoints.

---

## 6. Producción – Resumen

### 6.1 Backend

- Usar **MySQL** (o Postgres) con `DATABASE_URL` correcta.
- `SECRET_KEY` fuerte y único, nunca la de desarrollo.
- `DEBUG=false`, `APP_ENV=production` si están en tu config.
- `CORS_ORIGINS` y `FRONTEND_URL` con el dominio real (https).
- Servir con **Gunicorn + Uvicorn** o solo Uvicorn con varios workers, detrás de Nginx (proxy + SSL).
- Configurar logs y, si aplica, monitoreo.

### 6.2 Frontend

- `NEXT_PUBLIC_API_URL` = URL pública del backend (https).
- `npm run build` y servir con `npm start` o export estático según tu elección.
- Poner detrás de Nginx (u otro) con SSL.

### 6.3 Nginx (ejemplo mínimo)

- Un `server` para el frontend (puerto 3000 o estático).
- Otro `server` o `location` para el backend (proxy a `http://127.0.0.1:8000`).
- Certificados SSL (Let's Encrypt, etc.) y redirección HTTP → HTTPS.

---

## 7. Verificación y pruebas

### 7.1 Backend

```bash
cd migrations/backend-api
source .venv/bin/activate
python -m pytest tests/ -v --tb=short
```

O usando el script:

```bash
./run-tests.sh
```

### 7.2 Frontend

```bash
cd migrations/frontend
npm test -- --watchAll=false
npm run test:e2e   # Opcional; requiere npx playwright install
```

### 7.3 Comprobación rápida de la API

```bash
# Health
curl http://localhost:8000/health

# Login (sustituir email/password por un usuario existente)
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin@example.com&password=tu_password"
```

---

## 8. Solución de problemas

| Problema | Qué revisar |
|----------|-------------|
| `pydantic-core` no compila (Python 3.13) | Usar Python 3.11 o 3.12, o `pydantic>=2.9.0` en `requirements.txt`. |
| CORS al llamar desde el frontend | `CORS_ORIGINS` en `.env` del backend debe incluir la URL del frontend (ej. `http://localhost:3000`). |
| 401 en `/api/v1/auth/me` | Token JWT expirado o inválido; volver a hacer login en el frontend. |
| Emails no se envían | Revisar `MAIL_*` en el backend; en Gmail usar “Contraseña de aplicación”. |
| PDF no se genera | Dependencias de WeasyPrint (p. ej. en Linux: paquetes de sistema para GTK/Cairo); o configurar `PDF_SERVICE_URL` si usas servicio externo. |
| Frontend no conecta con la API | Comprobar `NEXT_PUBLIC_API_URL` en `.env.local` y que el backend esté levantado en esa URL. |

---

## 9. Referencias rápidas

- **Documentación API:** `http://localhost:8000/api/docs` (con el backend en marcha).
- **Plan de migración:** `migrations/docs/PLAN_MIGRACION.md`.
- **Tests:** `migrations/docs/TESTING.md`.
- **Índice de documentación:** `migrations/docs/README.md`.
- **README backend:** `migrations/backend-api/README.md`.
- **README frontend:** `migrations/frontend/README.md`.

---

*Última actualización: según estado del plan de migración (Fases 1–4). Para Fase 5 de despliegue detallado (Nginx, SSL, CI/CD), ver `docs/PLAN_MIGRACION.md` y ampliar este manual con los pasos concretos de tu entorno.*
