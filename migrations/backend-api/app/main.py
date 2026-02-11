"""
Aplicación FastAPI - E.MO.TI.VE
Migración desde Laravel
"""
from contextlib import asynccontextmanager
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from fastapi.responses import JSONResponse
from app.core.config import settings
from app.database import SessionLocal
from app.models import User
from app.core.security import get_password_hash
import os
from app.api.v1 import auth, users, clientes, formularios, perguntas, variaveis, respostas, reportes, chat, questionarios, upload, analise, pdf, usuario_formulario, cliente_formulario, contato, notificacoes, impersonate, calculos, midias, periodos, projetos, grupos


def _ensure_first_user():
    """Si no hay ningún usuario, crea uno por defecto (admin). Contraseña: admin123"""
    db = SessionLocal()
    try:
        if db.query(User).count() > 0:
            return
        from datetime import datetime, timezone
        now = datetime.now(timezone.utc)
        db.add(
            User(
                name="Administrador",
                email="admin@example.com",
                password=get_password_hash("admin123"),
                email_verified_at=now,
                admin=True,
                usuario=True,
                gestor=True,
                ativo=True,
            )
        )
        db.commit()
    except Exception:
        db.rollback()
    finally:
        db.close()


def _warn_secret_key():
    """Avisa si SECRET_KEY es la por defecto; si la cambias después, todos los tokens ya emitidos dejan de valer."""
    if settings.SECRET_KEY == "your-secret-key-change-in-production":
        import sys
        print("AVISO: SECRET_KEY es la por defecto. Define SECRET_KEY en .env (mín. 32 caracteres) y no la cambies tras emitir tokens.", file=sys.stderr)


def _test_secret_key():
    """Comprueba que crear y decodificar un token funciona con la SECRET_KEY actual."""
    from app.core.security import create_access_token, decode_access_token
    from datetime import timedelta
    try:
        t = create_access_token(data={"sub": "1", "email": "test@test.com"}, expires_delta=timedelta(minutes=5))
        payload = decode_access_token(t)
        if not payload or payload.get("sub") != "1":
            import sys
            print("ERRO: SECRET_KEY inválida ou inconsistente (token de teste não decodificou). Corrija .env e reinicie.", file=sys.stderr)
    except Exception as e:
        import sys
        print("ERRO: Teste de token falhou:", e, file=sys.stderr)


@asynccontextmanager
async def lifespan(app: FastAPI):
    _warn_secret_key()
    _test_secret_key()
    _ensure_first_user()
    yield


app = FastAPI(
    title="E.MO.TI.VE API",
    description="API para evaluación de salud emocional y bienestar en el trabajo",
    version="1.0.0",
    docs_url="/api/docs",
    redoc_url="/api/redoc",
    lifespan=lifespan,
)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Routers
app.include_router(auth.router, prefix="/api/v1/auth", tags=["Autenticación"])
app.include_router(users.router, prefix="/api/v1/users", tags=["Usuarios"])
app.include_router(clientes.router, prefix="/api/v1/clientes", tags=["Clientes"])
app.include_router(formularios.router, prefix="/api/v1/formularios", tags=["Formularios"])
app.include_router(perguntas.router, prefix="/api/v1/perguntas", tags=["Preguntas"])
app.include_router(variaveis.router, prefix="/api/v1/variaveis", tags=["Variables"])
app.include_router(respostas.router, prefix="/api/v1/respostas", tags=["Respuestas"])
app.include_router(reportes.router, prefix="/api/v1/reportes", tags=["Reportes"])
app.include_router(chat.router, prefix="/api/v1/chat", tags=["Chat"])
app.include_router(questionarios.router, prefix="/api/v1", tags=["Questionarios"])
app.include_router(upload.router, prefix="/api/v1/upload", tags=["Upload"])
app.include_router(analise.router, prefix="/api/v1/analise", tags=["Análise"])
app.include_router(pdf.router, prefix="/api/v1/pdf", tags=["PDF"])
app.include_router(usuario_formulario.router, prefix="/api/v1/usuario-formulario", tags=["Usuario Formulario"])
app.include_router(cliente_formulario.router, prefix="/api/v1/cliente-formulario", tags=["Cliente Formulario"])
app.include_router(contato.router, prefix="/api/v1/contato", tags=["Contato"])
app.include_router(notificacoes.router, prefix="/api/v1/notificacoes", tags=["Notificações"])
app.include_router(impersonate.router, prefix="/api/v1/impersonate", tags=["Impersonação"])
app.include_router(calculos.router, prefix="/api/v1/calculos", tags=["Tipos de Cálculo"])
app.include_router(midias.router, prefix="/api/v1/midias", tags=["Mídias"])
app.include_router(periodos.router, prefix="/api/v1/periodos", tags=["Períodos"])
app.include_router(projetos.router, prefix="/api/v1/projetos", tags=["Projetos"])
app.include_router(grupos.router, prefix="/api/v1/grupos", tags=["Grupos"])

# Servir archivos estáticos (uploads)
if os.path.exists(settings.UPLOAD_DIR):
    app.mount("/uploads", StaticFiles(directory=settings.UPLOAD_DIR), name="uploads")

@app.get("/")
async def root():
    return {"message": "E.MO.TI.VE API", "version": "1.0.0"}

@app.get("/health")
async def health():
    return {"status": "ok"}
