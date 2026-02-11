"""
Schemas Pydantic para Cliente
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime
from app.models.cliente import TipoCliente

class ClienteBase(BaseModel):
    tipo: TipoCliente
    cpf_cnpj: str
    nome_fantasia: Optional[str] = None
    razao_social: Optional[str] = None
    email: Optional[str] = None
    contato: Optional[str] = None
    telefone: Optional[str] = None

class ClienteCreate(ClienteBase):
    usuario_id: Optional[int] = None

class ClienteUpdate(BaseModel):
    tipo: Optional[TipoCliente] = None
    cpf_cnpj: Optional[str] = None
    nome_fantasia: Optional[str] = None
    razao_social: Optional[str] = None
    email: Optional[str] = None
    contato: Optional[str] = None
    telefone: Optional[str] = None
    usuario_id: Optional[int] = None

class ClienteResponse(ClienteBase):
    id: int
    usuario_id: Optional[int] = None
    logo_url: str
    ativo: bool
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
