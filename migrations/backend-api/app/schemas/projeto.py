"""
Schemas Pydantic para Projeto
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime


class ProjetoBase(BaseModel):
    nome: str
    descricao: Optional[str] = None


class ProjetoCreate(ProjetoBase):
    cliente_id: int


class ProjetoUpdate(BaseModel):
    nome: Optional[str] = None
    descricao: Optional[str] = None


class ProjetoResponse(ProjetoBase):
    id: int
    cliente_id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
