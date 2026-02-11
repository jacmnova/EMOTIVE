"""
Schemas Pydantic para User
"""
from pydantic import BaseModel, EmailStr
from typing import Optional
from datetime import datetime

class UserBase(BaseModel):
    name: str
    email: EmailStr


class UserCreate(UserBase):
    password: str
    cliente_id: Optional[int] = None
    sa: bool = False
    admin: bool = False
    gestor: bool = False
    usuario: bool = False
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None


class UserUpdate(BaseModel):
    name: Optional[str] = None
    email: Optional[EmailStr] = None
    cliente_id: Optional[int] = None
    sa: Optional[bool] = None
    admin: Optional[bool] = None
    gestor: Optional[bool] = None
    usuario: Optional[bool] = None
    ativo: Optional[bool] = None
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None


class UserResponse(UserBase):
    id: int
    avatar: Optional[str] = None
    email_verified_at: Optional[datetime] = None
    sa: bool
    admin: bool
    gestor: bool
    usuario: bool
    cliente_id: Optional[int] = None
    ativo: bool
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class Token(BaseModel):
    access_token: str
    token_type: str = "bearer"
    user: UserResponse


class ForgotPasswordRequest(BaseModel):
    email: EmailStr


class ResetPasswordRequest(BaseModel):
    token: str
    new_password: str
