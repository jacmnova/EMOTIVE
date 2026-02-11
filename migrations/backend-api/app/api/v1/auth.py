"""
Endpoints de autenticación
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from sqlalchemy.orm import Session
from datetime import timedelta, datetime, timezone
from app.database import get_db
from app.models import User
from app.schemas.user import (
    UserCreate,
    UserResponse,
    Token,
    ForgotPasswordRequest,
    ResetPasswordRequest,
)
from app.core.security import verify_password, get_password_hash, create_access_token, decode_questionario_access_token
from app.core.config import settings
from app.services.email import send_verification_email, send_reset_password_email
import secrets

router = APIRouter()
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")

def get_current_user_token(
    token: str = Depends(oauth2_scheme),
    db: Session = Depends(get_db)
) -> User:
    """Obtiene usuario desde token"""
    from app.core.security import decode_access_token
    
    payload = decode_access_token(token)
    if not payload:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido o expirado. Cierra sesión e inicia de nuevo. Si persiste, revisa SECRET_KEY en .env del backend.",
        )
    
    raw_sub = payload.get("sub")
    if raw_sub is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido o expirado",
        )
    try:
        user_id = int(raw_sub)
    except (TypeError, ValueError):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido o expirado",
        )
    user = db.query(User).filter(User.id == user_id, User.ativo == True).first()
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Usuario no encontrado",
        )
    return user

@router.post("/login", response_model=Token)
async def login(
    form_data: OAuth2PasswordRequestForm = Depends(),
    db: Session = Depends(get_db)
):
    """
    Login de usuario
    """
    user = db.query(User).filter(User.email == form_data.username).first()
    
    if not user or not verify_password(form_data.password, user.password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Email o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    if not user.ativo:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Usuario inactivo",
        )
    
    access_token_expires = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": str(user.id), "email": user.email},
        expires_delta=access_token_expires
    )
    
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": UserResponse.model_validate(user)
    }

@router.post("/register", response_model=UserResponse)
async def register(
    user_data: UserCreate,
    db: Session = Depends(get_db)
):
    """
    Registro de nuevo usuario
    """
    # Verificar si el email ya existe
    existing_user = db.query(User).filter(User.email == user_data.email).first()
    if existing_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="El email ya está registrado"
        )
    
    # Crear usuario
    hashed_password = get_password_hash(user_data.password)
    verification_token = secrets.token_urlsafe(32)
    
    user = User(
        name=user_data.name,
        email=user_data.email,
        password=hashed_password,
        verification_token=verification_token,
        cliente_id=user_data.cliente_id,
        sa=user_data.sa,
        admin=user_data.admin,
        gestor=user_data.gestor,
        usuario=user_data.usuario,
        ativo=True
    )
    
    db.add(user)
    db.commit()
    db.refresh(user)

    send_verification_email(user.email, user.name, verification_token)

    return UserResponse.model_validate(user)

@router.get("/me")
async def get_current_user(
    current_user: User = Depends(get_current_user_token),
    token: str = Depends(oauth2_scheme),
):
    """
    Obtiene información del usuario actual. Si hay impersonación, incluye impersonated_by (id del admin/gestor).
    """
    from app.core.security import decode_access_token
    payload = decode_access_token(token)
    data = UserResponse.model_validate(current_user).model_dump()
    data["impersonated_by"] = payload.get("impersonated_by") if payload else None
    return data

@router.post("/reenviar-verificacion")
async def reenviar_verificacion(
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Reenvía el email de verificación al usuario actual (si aún no está verificado).
    """
    if current_user.email_verified_at:
        return {"message": "E-mail já verificado."}

    token = secrets.token_urlsafe(32)
    current_user.verification_token = token
    db.commit()
    send_verification_email(current_user.email, current_user.name, token)
    return {"message": "E-mail de verificação reenviado com sucesso!"}


@router.post("/verify-email")
async def verify_email(
    token: str = Query(..., description="Token de verificación enviado por email"),
    db: Session = Depends(get_db)
):
    """
    Verifica email del usuario (token en query: ?token=xxx)
    """
    user = db.query(User).filter(User.verification_token == token).first()

    if not user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Token inválido o expirado"
        )

    user.email_verified_at = datetime.now(timezone.utc)
    user.verification_token = None
    db.commit()

    return {"message": "Email verificado correctamente"}

@router.post("/forgot-password")
async def forgot_password(
    body: ForgotPasswordRequest,
    db: Session = Depends(get_db)
):
    """
    Solicita reset de contraseña. Envía email con enlace si el usuario existe.
    """
    user = db.query(User).filter(User.email == body.email, User.ativo == True).first()

    if user:
        reset_token = secrets.token_urlsafe(32)
        user.password_reset_token = reset_token
        user.password_reset_expires = datetime.now(timezone.utc) + timedelta(hours=1)
        db.commit()
        send_reset_password_email(user.email, user.name, reset_token)

    return {"message": "Si el email existe, se enviará un enlace de recuperación"}


@router.post("/reset-password")
async def reset_password(
    body: ResetPasswordRequest,
    db: Session = Depends(get_db)
):
    """
    Resetea la contraseña usando el token recibido por email.
    """
    user = db.query(User).filter(User.password_reset_token == body.token).first()

    if not user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Token inválido o expirado"
        )

    if user.password_reset_expires and user.password_reset_expires < datetime.now(timezone.utc):
        user.password_reset_token = None
        user.password_reset_expires = None
        db.commit()
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="El enlace de recuperación ha expirado. Solicite uno nuevo."
        )

    user.password = get_password_hash(body.new_password)
    user.password_reset_token = None
    user.password_reset_expires = None
    db.commit()

    return {"message": "Contraseña actualizada correctamente"}


@router.get("/acesso-questionario")
async def acesso_questionario_por_token(
    token: str = Query(..., description="Token do link (responder?token=...)"),
    db: Session = Depends(get_db),
):
    """
    Público: valida o token de link direto ao questionário e retorna usuario_formulario_id.
    O frontend usa isso para redirecionar para /dashboard/questionarios/[id].
    """
    from app.models import UsuarioFormulario

    uf_id = decode_questionario_access_token(token)
    if not uf_id:
        raise HTTPException(status_code=400, detail="Link inválido ou expirado.")
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.id == uf_id,
        UsuarioFormulario.deleted_at == None,
    ).first()
    if not uf:
        raise HTTPException(status_code=404, detail="Atribuição não encontrada.")
    return {"usuario_formulario_id": uf.id, "formulario_id": uf.formulario_id}
