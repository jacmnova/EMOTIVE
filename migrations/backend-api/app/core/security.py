"""
Seguridad: JWT, password hashing, etc.
"""
from datetime import datetime, timedelta
from typing import Optional
import logging
from jose import JWTError, jwt
from passlib.context import CryptContext
from app.core.config import settings

logger = logging.getLogger(__name__)
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def verify_password(plain_password: str, hashed_password: str) -> bool:
    """Verifica si la contraseña coincide con el hash"""
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password: str) -> str:
    """Genera hash de contraseña"""
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None) -> str:
    """Crea token JWT"""
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, settings.SECRET_KEY, algorithm=settings.ALGORITHM)
    return encoded_jwt if isinstance(encoded_jwt, str) else encoded_jwt.decode("utf-8")

def decode_access_token(token: str) -> Optional[dict]:
    """Decodifica token JWT. Devuelve None si el token está mal formado, expirado o la firma no coincide (p. ej. SECRET_KEY distinta)."""
    if not token or not token.strip():
        return None
    raw = token.strip()
    try:
        payload = jwt.decode(raw, settings.SECRET_KEY, algorithms=[settings.ALGORITHM])
        return payload
    except JWTError as e:
        if settings.DEBUG:
            logger.warning("JWT decode failed: %s (token prefix: %s...)", type(e).__name__, raw[:20] if len(raw) > 20 else raw)
        return None


# Token para link directo al cuestionario (responder?token=xxx); expira en 30 días
QUESTIONARIO_TOKEN_EXPIRE_DAYS = 30

def create_questionario_access_token(usuario_formulario_id: int) -> str:
    """Crea JWT para acceso directo al cuestionario por link (sin login previo)."""
    to_encode = {"uf_id": usuario_formulario_id, "typ": "q"}
    expire = datetime.utcnow() + timedelta(days=QUESTIONARIO_TOKEN_EXPIRE_DAYS)
    to_encode["exp"] = expire
    encoded = jwt.encode(to_encode, settings.SECRET_KEY, algorithm=settings.ALGORITHM)
    return encoded if isinstance(encoded, str) else encoded.decode("utf-8")

def decode_questionario_access_token(token: str) -> Optional[int]:
    """Decodifica token de cuestionario; devuelve usuario_formulario_id o None."""
    if not token or not token.strip():
        return None
    payload = decode_access_token(token.strip())
    if not payload or payload.get("typ") != "q":
        return None
    uf_id = payload.get("uf_id")
    if uf_id is None:
        return None
    try:
        return int(uf_id)
    except (TypeError, ValueError):
        return None
