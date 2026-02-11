# Backend API - E.MO.TI.VE

API REST construida con FastAPI para la aplicación E.MO.TI.VE.

## 🚀 Inicio Rápido

### Instalación

```bash
# Crear entorno virtual
python -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate

# Instalar dependencias
pip install -r requirements.txt

# Configurar variables de entorno
cp .env.example .env
# Editar .env con tus configuraciones
```

### Ejecutar

```bash
uvicorn app.main:app --reload
```

La API estará disponible en `http://localhost:8000`
Documentación en `http://localhost:8000/api/docs`

### Migración de base de datos (notificaciones por email)

Si ya tienes la tabla `users` creada, añade las columnas para recuperación de contraseña:

```sql
-- SQLite
ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(64);
ALTER TABLE users ADD COLUMN password_reset_expires DATETIME;

-- MySQL
ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN password_reset_expires DATETIME NULL;
```

Configura en `.env`: `FRONTEND_URL`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM` para que los emails de verificación y recuperación de contraseña se envíen correctamente.

### Seeders (datos iniciales)

Tras ejecutar las migraciones (`alembic upgrade head`), puedes poblar la base con datos de prueba:

```bash
python -m app.seed
```

Crea tipos de cálculo, 3 usuarios (contraseña: `admin123`), 1 cliente y opcionalmente el formulario Burnout 99. Ver `app/seed.py`. Para no crear el formulario: `python -m app.seed --no-formulario`.

## 📁 Estructura

```
app/
├── main.py              # Aplicación FastAPI
├── database.py          # Configuración SQLAlchemy
├── core/
│   ├── config.py       # Configuración
│   ├── security.py     # JWT, password hashing
│   └── permissions.py  # Permisos y autorización
├── models/              # Modelos SQLAlchemy
├── schemas/             # Schemas Pydantic
├── api/
│   └── v1/             # Endpoints API v1
│       ├── auth.py     # Autenticación
│       ├── users.py    # Usuarios
│       ├── clientes.py # Clientes
│       └── ...
├── services/            # Lógica de negocio
│   └── calculos.py     # Cálculos de puntuaciones
└── utils/               # Utilidades
    └── inversao.py      # Helper inversión preguntas
```

## 🔐 Autenticación

La API usa JWT (JSON Web Tokens) para autenticación.

### Login

```bash
POST /api/v1/auth/login
Content-Type: application/x-www-form-urlencoded

username=user@example.com
password=password
```

### Usar token

```bash
Authorization: Bearer <token>
```

## 📝 Endpoints Principales

### Autenticación
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/register` - Registro
- `GET /api/v1/auth/me` - Usuario actual
- `POST /api/v1/auth/verify-email?token=` - Verificar email
- `POST /api/v1/auth/forgot-password` - Solicitar recuperación de contraseña (body: `{"email": "..."}`)
- `POST /api/v1/auth/reset-password` - Restablecer contraseña con token (body: `{"token": "...", "new_password": "..."}`)

### Usuarios
- `GET /api/v1/users` - Listar usuarios
- `GET /api/v1/users/{id}` - Obtener usuario
- `POST /api/v1/users` - Crear usuario
- `PUT /api/v1/users/{id}` - Actualizar usuario
- `DELETE /api/v1/users/{id}` - Eliminar usuario
- `PUT /api/v1/users/{id}/status` - Activar/desactivar

### Formularios
- `GET /api/v1/formularios` - Listar formularios
- `GET /api/v1/formularios/{id}` - Obtener formulario
- `POST /api/v1/formularios` - Crear (admin)
- `PUT /api/v1/formularios/{id}` - Actualizar (admin)
- `DELETE /api/v1/formularios/{id}` - Eliminar (admin)
- `PUT /api/v1/formularios/{id}/status` - Activar/desactivar

### Preguntas
- `GET /api/v1/perguntas?formulario_id=` - Listar (filtro opcional)
- `GET /api/v1/perguntas/{id}` - Obtener
- `POST /api/v1/perguntas` - Crear (admin)
- `PUT /api/v1/perguntas/{id}` - Actualizar (admin)
- `DELETE /api/v1/perguntas/{id}` - Eliminar (admin)

### Variables
- `GET /api/v1/variaveis?formulario_id=` - Listar
- `GET /api/v1/variaveis/formulario/{id}` - Por formulario
- `GET /api/v1/variaveis/{id}` - Obtener
- `POST /api/v1/variaveis` - Crear (admin)
- `PUT /api/v1/variaveis/{id}` - Actualizar (admin)
- `DELETE /api/v1/variaveis/{id}` - Eliminar (admin)

### Respuestas
- `GET /api/v1/respostas?usuario_id=&formulario_id=` - Listar respuestas
- `POST /api/v1/respostas/salvar` - Guardar respuestas (body: formulario_id, respostas: {pergunta_id: valor})

### Clientes
- `GET /api/v1/clientes` - Listar clientes
- `GET /api/v1/clientes/{id}` - Obtener cliente
- `POST /api/v1/clientes` - Crear cliente (admin)
- `PUT /api/v1/clientes/{id}` - Actualizar cliente (admin)
- `DELETE /api/v1/clientes/{id}` - Eliminar cliente (admin)
- `PUT /api/v1/clientes/{id}/status` - Activar/desactivar

### Respuestas
- `GET /api/v1/respostas?usuario_id=&formulario_id=` - Listar respuestas
- `POST /api/v1/respostas/salvar` - Guardar respuestas (body: formulario_id, respostas: {pergunta_id: valor})

### Reportes
- `GET /api/v1/reportes?formulario_id=&usuario_id=` - Reporte completo (puntuaciones, ejes, IID, análisis)
- `POST /api/v1/reportes/regenerar-analise` - Regenerar análisis (admin)

### Questionarios
- `GET /api/v1/meus-questionarios` - Cuestionarios asignados al usuario actual

### Usuario Formulario
- `POST /api/v1/usuario-formulario` - Asignar formulario a usuario
- `POST /api/v1/usuario-formulario/admin` - Asignar (admin, sin validar)
- `POST /api/v1/usuario-formulario/{id}/finalizar` - Finalizar formulario
- `POST /api/v1/usuario-formulario/{id}/assistido` - Marcar video asistido
- `PUT /api/v1/usuario-formulario/{id}` - Actualizar (admin)

### Cliente Formulario
- `GET /api/v1/cliente-formulario` - Listar asignaciones
- `POST /api/v1/cliente-formulario` - Asignar formulario a cliente (admin)
- `PUT /api/v1/cliente-formulario/{id}` - Actualizar (admin)
- `DELETE /api/v1/cliente-formulario/{id}` - Eliminar (admin)

### Análise
- `POST /api/v1/analise/gerar` - Generar análisis con OpenAI

### PDF
- `GET /api/v1/pdf/relatorio?formulario_id=&usuario_id=` - Generar PDF del reporte

### Chat
- `POST /api/v1/chat` - Chat con ChatGPT

### Upload
- `POST /api/v1/upload/imagem/usuario` - Subir avatar
- `POST /api/v1/upload/imagem/cliente` - Subir logo cliente

## 🧪 Testing

```bash
pytest
```

## 📚 Documentación

- Swagger UI: `http://localhost:8000/api/docs`
- ReDoc: `http://localhost:8000/api/redoc`
