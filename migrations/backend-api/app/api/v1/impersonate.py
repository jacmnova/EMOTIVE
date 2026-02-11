"""
Impersonación: admin/gestor puede actuar como otro usuario.
"""
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import User
from app.schemas.user import UserResponse, Token
from app.core.security import create_access_token, decode_access_token
from app.core.config import settings
from datetime import timedelta
from app.api.v1.auth import get_current_user_token, oauth2_scheme

router = APIRouter()


def _require_can_impersonate(current_user: User) -> None:
    """Solo admin, SA o gestor pueden iniciar impersonación."""
    if not (current_user.sa or current_user.admin or current_user.gestor):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Sem permissão para personificar",
        )


@router.post("/start/{user_id}", response_model=Token)
async def impersonate_start(
    user_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
    token: str = Depends(oauth2_scheme),
):
    """
    Inicia impersonación: el usuario actual (admin/gestor) pasa a actuar como user_id.
    El token devuelto tiene sub=user_id e impersonated_by=current_user.id.
    Solo se permite si el token actual NO es de impersonación (evitar cadena).
    """
    payload = decode_access_token(token)
    if payload and payload.get("impersonated_by"):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Saia da personificação atual antes de iniciar outra.",
        )
    _require_can_impersonate(current_user)

    target = db.query(User).filter(User.id == user_id, User.ativo == True).first()
    if not target:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Usuário não encontrado")

    if current_user.gestor and not current_user.admin and not current_user.sa:
        if target.cliente_id != current_user.cliente_id:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Gestor só pode personificar usuários do próprio cliente",
            )

    expires = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": str(target.id), "email": target.email, "impersonated_by": current_user.id},
        expires_delta=expires,
    )
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": UserResponse.model_validate(target),
        "impersonated_by": current_user.id,
    }


@router.post("/stop", response_model=Token)
async def impersonate_stop(
    db: Session = Depends(get_db),
    token: str = Depends(oauth2_scheme),
):
    """
    Termina la impersonación: el token debe tener impersonated_by; se devuelve un token
    para ese usuario (el admin/gestor que había iniciado la impersonación).
    """
    payload = decode_access_token(token)
    if not payload or not payload.get("impersonated_by"):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Não está em modo personificação",
        )
    original_user_id = payload["impersonated_by"]
    original = db.query(User).filter(User.id == original_user_id, User.ativo == True).first()
    if not original:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Usuário original não encontrado")

    expires = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": str(original.id), "email": original.email},
        expires_delta=expires,
    )
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "user": UserResponse.model_validate(original),
    }
