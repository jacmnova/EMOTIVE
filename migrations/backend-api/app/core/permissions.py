"""
Permisos y autorización
"""
from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import User
from app.core.security import decode_access_token

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")


def get_current_user(
    token: str = Depends(oauth2_scheme),
    db: Session = Depends(get_db)
) -> User:
    """
    Obtiene el usuario actual desde el token JWT.
    Nota: En endpoints protegidos se usa get_current_user_token de auth (con OAuth2PasswordBearer).
    """
    if not token:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token no proporcionado",
            headers={"WWW-Authenticate": "Bearer"},
        )
    payload = decode_access_token(token)
    if not payload:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido o expirado",
            headers={"WWW-Authenticate": "Bearer"},
        )
    user_id = payload.get("sub")
    if user_id is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token inválido",
        )
    user = db.query(User).filter(User.id == user_id, User.ativo == True).first()
    if user is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Usuario no encontrado o inactivo",
        )
    return user


def require_sa(current_user: User = Depends(get_current_user)) -> User:
    """Requiere rol Super Admin"""
    # get_current_user no se usa aquí; los routers usan get_current_user_token
    if not current_user.sa:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Se requiere rol Super Admin"
        )
    return current_user


def require_admin(current_user: User = Depends(get_current_user)) -> User:
    """Requiere rol Admin o SA"""
    if not (current_user.admin or current_user.sa):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Se requiere rol Admin"
        )
    return current_user


def require_gestor(current_user: User = Depends(get_current_user)) -> User:
    """Requiere rol Gestor, Admin o SA"""
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Se requiere rol Gestor"
        )
    return current_user


def require_usuario(current_user: User = Depends(get_current_user)) -> User:
    """Requiere rol Usuario, Gestor, Admin o SA"""
    if not (current_user.usuario or current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Se requiere rol Usuario"
        )
    return current_user
