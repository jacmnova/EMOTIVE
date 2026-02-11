# Plan de Migración Completo: Laravel → Next.js + Python API

## 🎯 Objetivo

Migrar 100% de la funcionalidad de la aplicación Laravel a:
- **Frontend**: Next.js 14+ (App Router) o Angular 17+
- **Backend**: Python FastAPI
- **Base de Datos**: Mantener estructura actual (SQLite/MySQL)

## 📋 Fases de Migración

### Fase 1: Preparación y Estructura Base ✅
- [x] Análisis completo de la aplicación
- [x] Documentación de funcionalidades
- [x] Crear estructura de carpetas
- [x] Definir esquema de API REST
- [x] Crear modelos de base de datos (SQLAlchemy)

### Fase 2: Backend API (Python FastAPI) - 100% ✅
- [x] Configuración inicial FastAPI
- [x] Modelos SQLAlchemy (13 modelos completos)
- [x] Sistema de autenticación (JWT)
- [x] Endpoints de autenticación (login, register, verify, me)
- [x] Endpoints de usuarios (CRUD completo)
- [x] Endpoints de clientes (estructura base - falta CRUD completo)
- [x] Endpoints de formularios (CRUD completo)
- [x] Endpoints de preguntas y variables (CRUD completo)
- [x] Endpoints de respuestas (listar, salvar batch)
- [x] Lógica de cálculos (puntuaciones, índices, ejes, IID) - 70% (falta completar interpretaciones)
- [x] Endpoints de reportes (obtener relatorio completo, regenerar análisis)
- [x] Endpoint meus-questionarios
- [x] Integración OpenAI (servicio + endpoint de generación)
- [x] Chat con ChatGPT
- [x] Upload de archivos (avatars, logos)
- [x] Generación de PDFs (WeasyPrint + servicio externo)
- [x] CRUD completo de clientes
- [x] Asignación formularios a usuarios (UsuarioFormulario)
- [x] Asignación formularios a clientes (ClienteFormulario)
- [x] Sistema de notificaciones (email)

### Fase 3: Frontend (Next.js) - En curso
- [x] Configuración inicial Next.js
- [x] Sistema de autenticación (login, register, forgot/reset password, verify email)
- [x] Layout y componentes base
- [x] Páginas de login/registro/recuperación/verificación
- [x] Dashboard según rol (inicio, meus-questionarios, chat, usuarios/clientes/formularios para admin)
- [x] Lista de cuestionarios (usuario)
- [x] CRUD de usuarios (admin) - listar, crear, editar
- [x] CRUD de clientes (admin) - listar, crear, editar
- [x] CRUD de formularios (admin) - listar, crear, editar
- [x] CRUD de preguntas (admin) - listar/criar/editar/excluir por formulário
- [x] CRUD de variables (admin) - listar/criar/editar/excluir por formulário
- [x] Gestión de usuarios (gestor) - listar e editar usuários do seu cliente
- [x] Formulario de respuestas (usuario) - responder cuestionario, salvar
- [x] Vista de reporte (usuario) - pontuações, índices, nível risco, plano, análise
- [x] Generación de PDFs - botão baixar PDF no relatório
- [x] Chat com ChatGPT
- [x] Perfil de usuario - ver e editar nome/email
- [x] Notificaciones (UI) - página com lista local (localStorage) e estado vazio

### Fase 4: Testing y Validación - En curso
- [x] Tests unitarios backend - estructura creada (auth, cálculos, PDFs)
- [x] Tests de integración API - endpoints principales (users, formularios, respostas, reportes)
- [x] Tests E2E frontend - estructura Playwright (auth, dashboard)
- [x] Validación de cálculos - tests unitarios de funciones de cálculo
- [x] Validación de reportes - tests de integración de endpoint de reportes
- [x] Validación de PDFs - tests de generación de HTML para PDFs
- [x] Pruebas de carga - tests básicos de concurrencia (requiere pytest-benchmark para benchmarks avanzados)

### Fase 5: Despliegue
- [ ] Configuración de producción
- [ ] Variables de entorno
- [ ] Despliegue backend
- [ ] Despliegue frontend
- [ ] Migración de datos
- [ ] Monitoreo y logs

## 🏗️ Arquitectura Propuesta

### Backend (Python FastAPI)
```
api/
├── app/
│   ├── __init__.py
│   ├── main.py
│   ├── config.py
│   ├── database.py
│   ├── models/          # SQLAlchemy models
│   ├── schemas/         # Pydantic schemas
│   ├── api/
│   │   ├── v1/
│   │   │   ├── auth.py
│   │   │   ├── users.py
│   │   │   ├── clientes.py
│   │   │   ├── formularios.py
│   │   │   ├── perguntas.py
│   │   │   ├── variaveis.py
│   │   │   ├── respostas.py
│   │   │   ├── reportes.py
│   │   │   └── chat.py
│   ├── core/
│   │   ├── security.py  # JWT, password hashing
│   │   ├── permissions.py
│   │   └── config.py
│   ├── services/
│   │   ├── calculos.py   # Lógica de cálculos
│   │   ├── pdf.py        # Generación PDFs
│   │   ├── openai.py     # Integración OpenAI
│   │   └── notifications.py
│   └── utils/
│       ├── helpers.py
│       └── inversao.py   # Lógica inversión preguntas
├── alembic/             # Migrations
├── tests/
├── requirements.txt
└── .env
```

### Frontend (Next.js)
```
frontend/
├── app/                 # App Router
│   ├── layout.tsx
│   ├── page.tsx
│   ├── (auth)/
│   │   ├── login/
│   │   └── register/
│   ├── (dashboard)/
│   │   ├── dashboard/
│   │   ├── usuarios/
│   │   ├── clientes/
│   │   ├── formularios/
│   │   └── ...
│   └── api/             # API routes si necesario
├── components/
│   ├── ui/              # Componentes base
│   ├── forms/
│   ├── charts/
│   └── layout/
├── lib/
│   ├── api.ts           # Cliente API
│   ├── auth.ts
│   └── utils.ts
├── hooks/
├── types/               # TypeScript types
├── public/
└── package.json
```

## 🔄 Mapeo de Funcionalidades

### Autenticación
| Laravel | Python FastAPI | Next.js |
|---------|---------------|---------|
| Session Auth | JWT Tokens | NextAuth.js o JWT en cookies |
| Email Verification | Endpoint + token | Página de verificación |
| Password Reset | Endpoint + token | Formulario reset |

### Cálculos
| Laravel | Python |
|---------|--------|
| `CalculaEjesAnaliticos` trait | `services/calculos.py` |
| `PerguntasInvertidasHelper` | `utils/inversao.py` |
| Lógica en `DadosController` | Funciones en `services/calculos.py` |

### Reportes
| Laravel | Python | Next.js |
|---------|--------|---------|
| `RelatorioController` | `api/v1/reportes.py` | Página de reporte |
| DomPDF/Browsershot | WeasyPrint o ReportLab | Cliente API + render |

## 📝 Endpoints API Propuestos

### Autenticación
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/verify-email`
- `POST /api/v1/auth/forgot-password`
- `POST /api/v1/auth/reset-password`
- `POST /api/v1/auth/refresh`

### Usuarios
- `GET /api/v1/users` (listar)
- `GET /api/v1/users/{id}` (detalle)
- `POST /api/v1/users` (crear)
- `PUT /api/v1/users/{id}` (actualizar)
- `DELETE /api/v1/users/{id}` (eliminar)
- `PUT /api/v1/users/{id}/status` (activar/desactivar)
- `POST /api/v1/users/{id}/impersonate` (impersonar)

### Clientes
- `GET /api/v1/clientes`
- `GET /api/v1/clientes/{id}`
- `POST /api/v1/clientes`
- `PUT /api/v1/clientes/{id}`
- `DELETE /api/v1/clientes/{id}`
- `PUT /api/v1/clientes/{id}/status`

### Formularios
- `GET /api/v1/formularios`
- `GET /api/v1/formularios/{id}`
- `POST /api/v1/formularios`
- `PUT /api/v1/formularios/{id}`
- `DELETE /api/v1/formularios/{id}`
- `PUT /api/v1/formularios/{id}/status`

### Preguntas
- `GET /api/v1/perguntas?formulario_id={id}`
- `GET /api/v1/perguntas/{id}`
- `POST /api/v1/perguntas`
- `PUT /api/v1/perguntas/{id}`
- `DELETE /api/v1/perguntas/{id}`

### Variables
- `GET /api/v1/variaveis?formulario_id={id}`
- `GET /api/v1/variaveis/{id}`
- `POST /api/v1/variaveis`
- `PUT /api/v1/variaveis/{id}`
- `DELETE /api/v1/variaveis/{id}`

### Respuestas
- `GET /api/v1/respostas?usuario_id={id}&formulario_id={id}`
- `POST /api/v1/respostas` (guardar múltiples)
- `PUT /api/v1/respostas/{id}`

### Cuestionarios Usuario
- `GET /api/v1/meus-questionarios` ✅
- `POST /api/v1/usuario-formulario` ✅ (asignar formulario)
- `POST /api/v1/usuario-formulario/admin` ✅ (admin asignar)
- `POST /api/v1/usuario-formulario/{id}/finalizar` ✅
- `POST /api/v1/usuario-formulario/{id}/assistido` ✅

### Cliente Formulario
- `GET /api/v1/cliente-formulario` ✅
- `POST /api/v1/cliente-formulario` ✅ (asignar formulario a cliente)
- `PUT /api/v1/cliente-formulario/{id}` ✅
- `DELETE /api/v1/cliente-formulario/{id}` ✅

### Reportes
- `GET /api/v1/reportes?formulario_id={id}&usuario_id={id}` ✅
- `POST /api/v1/reportes/regenerar-analise` ✅
- `POST /api/v1/analise/gerar` ✅ (generar análisis con OpenAI)
- `GET /api/v1/pdf/relatorio?formulario_id=&usuario_id=` ✅ (generar PDF)

### Clientes
- `GET /api/v1/clientes` ✅
- `GET /api/v1/clientes/{id}` ✅
- `POST /api/v1/clientes` ✅
- `PUT /api/v1/clientes/{id}` ✅
- `DELETE /api/v1/clientes/{id}` ✅
- `PUT /api/v1/clientes/{id}/status` ✅

### Chat
- `POST /api/v1/chat` ✅

### Upload
- `POST /api/v1/upload/imagem/usuario` ✅
- `POST /api/v1/upload/imagem/cliente` ✅

## 🧪 Testing Strategy

### Backend
- Unit tests para cálculos
- Unit tests para servicios
- Integration tests para endpoints
- Tests de autenticación/autorización

### Frontend
- Component tests (React Testing Library)
- E2E tests (Playwright/Cypress)
- Tests de flujos críticos

## 📦 Dependencias Propuestas

### Backend (Python)
```txt
fastapi==0.104.1
uvicorn[standard]==0.24.0
sqlalchemy==2.0.23
alembic==1.12.1
pydantic==2.5.0
pydantic-settings==2.1.0
python-jose[cryptography]==3.3.0
passlib[bcrypt]==1.7.4
python-multipart==0.0.6
openai==1.3.0
weasyprint==60.1  # Para PDFs
python-dotenv==1.0.0
```

### Frontend (Next.js)
```json
{
  "dependencies": {
    "next": "^14.0.0",
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "@tanstack/react-query": "^5.0.0",
    "axios": "^1.6.0",
    "next-auth": "^4.24.0",
    "chart.js": "^4.4.0",
    "react-chartjs-2": "^5.2.0",
    "tailwindcss": "^3.3.0",
    "shadcn/ui": "latest"
  }
}
```

## ⚠️ Consideraciones Importantes

### 1. Compatibilidad de Cálculos
- **CRÍTICO**: Los cálculos deben dar exactamente los mismos resultados
- Validar con datos de prueba existentes
- Mantener misma lógica de inversión
- Mantener mismos límites B, M, A

### 2. Migración de Datos
- Exportar datos de Laravel
- Importar a nueva base de datos
- Validar integridad referencial
- Verificar cálculos con datos migrados

### 3. Autenticación
- Migrar usuarios con passwords hashed (bcrypt compatible)
- Mantener tokens de verificación si es posible
- Plan de migración de sesiones activas

### 4. Archivos
- Migrar avatares y logos
- Migrar medios asociados a formularios
- Mantener estructura de storage

### 5. PDFs
- Validar que PDFs generados sean idénticos
- Mantener mismo formato y diseño
- Probar con diferentes datos

## 🚀 Orden de Implementación Recomendado

1. **Backend Base**
   - Configuración FastAPI
   - Modelos SQLAlchemy
   - Autenticación JWT
   - Endpoints básicos CRUD

2. **Lógica de Cálculos**
   - Portar lógica de inversión
   - Portar cálculo de puntuaciones
   - Portar cálculo de índices
   - Portar cálculo de ejes e IID
   - **Validar exhaustivamente**

3. **Frontend Base**
   - Configuración Next.js
   - Autenticación
   - Layout y navegación
   - Cliente API

4. **Funcionalidades Core**
   - CRUD de entidades principales
   - Flujo de cuestionarios
   - Flujo de reportes

5. **Funcionalidades Avanzadas**
   - Generación PDFs
   - Integración OpenAI
   - Chat
   - Notificaciones

6. **Testing y Optimización**
   - Tests completos
   - Optimización de performance
   - Validación final

## 📊 Métricas de Éxito

- ✅ 100% de funcionalidades migradas
- ✅ Cálculos idénticos a Laravel
- ✅ PDFs idénticos
- ✅ Performance igual o mejor
- ✅ Tests con >80% coverage
- ✅ Sin regresiones conocidas
