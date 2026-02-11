# Manual paso a paso – Ejecutar E.MO.TI.VE (Next.js + FastAPI)

Guía lineal para poner en marcha la aplicación migrada desde cero. Sigue los pasos en orden.

---

## Antes de empezar

Abre una terminal en la raíz del repositorio (donde está la carpeta `migrations/`).

Comprueba que tienes instalado:

| Herramienta   | Comando de verificación     | Versión recomendada   |
|---------------|----------------------------|------------------------|
| Python        | `python3 --version`        | 3.11 o 3.12            |
| Node.js       | `node --version`           | 18.x o 20.x LTS        |
| npm           | `npm --version`            | 9.x o 10.x             |

Si falta algo, instálalo antes de continuar.

---

## Paso 1 – Entrar en el backend

```bash
cd migrations/backend-api
```

---

## Paso 2 – Crear y activar el entorno virtual (Python)

```bash
python3 -m venv .venv
```

- **macOS / Linux:**
  ```bash
  source .venv/bin/activate
  ```
- **Windows (CMD):**
  ```cmd
  .venv\Scripts\activate.bat
  ```
- **Windows (PowerShell):**
  ```powershell
  .venv\Scripts\Activate.ps1
  ```

Deberías ver `(.venv)` al inicio del prompt.

---

## Paso 3 – Instalar dependencias del backend

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

Si aparece error con `pydantic-core` (por ejemplo en Python 3.13), usa Python 3.11 o 3.12.

---

## Paso 4 – Configurar variables de entorno del backend

```bash
cp .env.example .env
```

Abre el archivo `.env` y ajusta al menos:

| Variable        | Para desarrollo (puedes dejar así)        | Qué hace                    |
|-----------------|--------------------------------------------|-----------------------------|
| `DATABASE_URL`  | `sqlite:///./database.sqlite`              | Base de datos SQLite local  |
| `SECRET_KEY`    | Cambiar por una cadena larga y aleatoria   | Firma de los JWT            |
| `CORS_ORIGINS`  | `["http://localhost:3000","http://127.0.0.1:3000"]` | Orígenes permitidos (frontend) |
| `FRONTEND_URL`  | `http://localhost:3000`                    | Enlaces en emails           |

Opcional para emails (verificación, recuperar contraseña): rellena `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`.  
Opcional para análisis y chat: `OPENAI_API_KEY`.

---

## Paso 5 – Crear la base de datos con migraciones (Alembic)

Sigue en `migrations/backend-api` con el entorno virtual activado.

```bash
alembic upgrade head
```

Esto crea el archivo `database.sqlite` (si usas SQLite) y todas las tablas. Si usas MySQL, configura antes `DATABASE_URL` en `.env` y luego ejecuta el mismo comando.

**Si la migración falló a mitad** (por ejemplo "table tipo_calculo already exists"): borra la base y vuelve a ejecutar. Con SQLite: `rm database.sqlite` dentro de `migrations/backend-api`, luego `alembic upgrade head`.

---

## Paso 5b – Seeders (primer usuario y datos iniciales)

**Sin este paso no hay ningún usuario en la base:** no podrás iniciar sesión. Es necesario ejecutar el seed para crear al menos un usuario.

```bash
python -m app.seed
```

Se crean:
- **12 tipos de cálculo** (SOMATORIO, MEDIA, MÁXIMO, etc.).
- **3 usuarios** (todos con contraseña `admin123`):
  - **Primer usuario (SA/Admin):** `wheelkorner@gmail.com`
  - Administrador: `desenvolvedor@fellipelli.com.br`
  - Gestor (cliente FELLIPELLI): `arley.rincon@fellipelli.com.br`
- **1 cliente** FELLIPELLI.
- Opcionalmente el **formulario "Burnout 99"** con sus etapas.

Para no crear el formulario Burnout: `python -m app.seed --no-formulario`.

**Respaldo:** Si no ejecutas el seed y arrancas el backend con la base vacía, la API crea automáticamente un único usuario la primera vez: `admin@example.com` / `admin123`. Aun así es recomendable ejecutar el seed para tener los tres usuarios y el cliente FELLIPELLI.

---

## Paso 6 – Crear la carpeta de subidas (backend)

```bash
mkdir -p uploads
```

(O el path que tengas en `UPLOAD_DIR` en `.env`.)

---

## Paso 7 – Arrancar el backend

En la misma terminal:

```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

No cierres esta terminal. Comprueba:

- API: **http://localhost:8000**
- Documentación: **http://localhost:8000/api/docs**

---

## Paso 8 – Abrir una segunda terminal para el frontend

Abre **otra terminal**, ve a la raíz del repo y entra en el frontend:

```bash
cd migrations/frontend
```

(Usa la ruta correcta si estás en otro directorio; por ejemplo desde la raíz del proyecto: `cd /ruta/al/NR1/migrations/frontend`.)

---

## Paso 9 – Instalar dependencias del frontend

```bash
npm install
```

---

## Paso 10 – Configurar variables de entorno del frontend

```bash
cp .env.local.example .env.local
```

Abre `.env.local` y verifica:

- `NEXT_PUBLIC_API_URL=http://localhost:8000`

Para desarrollo local suele bastar con eso. En producción pondrás aquí la URL pública del backend.

---

## Paso 11 – Arrancar el frontend

```bash
npm run dev
```

No cierres esta terminal. La aplicación estará en:

- **Frontend:** **http://localhost:3000**

---

## Paso 12 – Comprobar que todo funciona

1. Abre el navegador en **http://localhost:3000**.
2. Prueba **Registro** o **Iniciar sesión** (si ya tienes usuario).
3. Opcional: en **http://localhost:8000/api/docs** prueba el endpoint `GET /health` o `POST /api/v1/auth/login`.

Si el frontend carga y puedes hacer login o registro, el stack está funcionando.

---

## Resumen rápido (cuando ya lo hayas hecho una vez)

**Primera vez** (tras `alembic upgrade head`): ejecutar seeders para tener usuarios y datos iniciales:

```bash
cd migrations/backend-api && source .venv/bin/activate && python -m app.seed
```

**Terminal 1 – Backend**

```bash
cd migrations/backend-api
source .venv/bin/activate   # Windows: .venv\Scripts\activate
uvicorn app.main:app --reload --port 8000
```

**Terminal 2 – Frontend**

```bash
cd migrations/frontend
npm run dev
```

Luego abre **http://localhost:3000**.

---

## Comandos útiles después de instalar

| Acción                    | Dónde              | Comando |
|---------------------------|--------------------|---------|
| Ver migraciones aplicadas | `backend-api`      | `alembic current` |
| Nueva migración (tras cambiar modelos) | `backend-api` | `alembic revision --autogenerate -m "descripcion"` luego `alembic upgrade head` |
| Tests backend             | `backend-api`      | `pytest` o `./run-tests.sh` |
| Tests frontend (unit)     | `frontend`         | `npm test -- --watchAll=false` |
| Tests E2E frontend        | `frontend`         | `npx playwright install` luego `npm run test:e2e` |
| Seeders (datos iniciales) | `backend-api`      | `python -m app.seed` (usuarios, clientes, tipo_calculo, formulario demo) |

---

## Si algo falla

- **"Token inválido o expirado"** (siempre al usar el dashboard): (1) En el backend, en `.env`, define **SECRET_KEY** con una cadena larga y aleatoria (mín. 32 caracteres); no la cambies después de haber iniciado sesión. (2) Cierra sesión en el frontend (o borra en el navegador `access_token` y `user` en Local Storage) y vuelve a iniciar sesión. (3) Comprueba que `NEXT_PUBLIC_API_URL` apunte al mismo backend donde hiciste login.
- **CORS / 401:** Revisa que el backend tenga en `.env` `CORS_ORIGINS` con `http://localhost:3000` y que `SECRET_KEY` esté definida.
- **Frontend no conecta:** Comprueba que el backend esté en marcha en el puerto 8000 y que `NEXT_PUBLIC_API_URL` en `.env.local` sea `http://localhost:8000`.
- **Error de base de datos:** Si cambiaste de SQLite a MySQL, asegura que `DATABASE_URL` en `.env` sea correcta y ejecuta de nuevo `alembic upgrade head`.
- **Emails no se envían:** Configura las variables `MAIL_*` en `.env` del backend; en Gmail usa “Contraseña de aplicación”.

Más detalles y opciones de producción: **MANUAL_LANZAMIENTO.md**.

---

*Manual paso a paso – E.MO.TI.VE (Next.js + FastAPI). Actualizado para incluir Alembic y flujo completo.*



##  1. Manual (primer usuario)
    En Paso 5b del manual queda indicado que:
    Sin ejecutar el seed no hay ningún usuario y no se puede iniciar sesión.
    El primer usuario creado por el seed es:
    **Email: wheelkorner@gmail.com
    **Contraseña: admin123
    **Rol: SA + Admin.
    También se listan los otros dos usuarios (Administrador y Gestor) y se aclara que todos usan la contraseña admin123.

## 2. Primer usuario automático si la base está vacía
    En el backend se añadió un respaldo en el arranque de la API:
    Si en la base no hay ningún usuario, se crea uno solo la primera vez que arranca la app:
    Email: admin@example.com
    Contraseña: admin123
    Rol: Admin + Gestor + Usuario.
    Así, aunque no ejecutes el seed, siempre habrá al menos un usuario para entrar. El seed sigue siendo lo recomendable para tener los 3 usuarios y el cliente FELLIPELLI.
    Resumen para iniciar sesión:
    Origen	Email	Contraseña
    **Seed (recomendado)	wheelkorner@gmail.com (primer usuario)	admin123
    **Respaldo (sin seed)	admin@example.com	admin123
