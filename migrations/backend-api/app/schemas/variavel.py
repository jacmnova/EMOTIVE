"""
Schemas Pydantic para Variavel
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class VariavelBase(BaseModel):
    formulario_id: int
    nome: str
    descricao: Optional[str] = None
    tag: str
    B: int
    M: int
    A: Optional[int] = None
    baixa: Optional[str] = None
    moderada: Optional[str] = None
    alta: Optional[str] = None
    r_baixa: Optional[str] = None
    r_moderada: Optional[str] = None
    r_alta: Optional[str] = None
    d_baixa: Optional[str] = None
    d_moderada: Optional[str] = None
    d_alta: Optional[str] = None

class VariavelCreate(VariavelBase):
    pass

class VariavelUpdate(BaseModel):
    nome: Optional[str] = None
    descricao: Optional[str] = None
    tag: Optional[str] = None
    B: Optional[int] = None
    M: Optional[int] = None
    A: Optional[int] = None
    baixa: Optional[str] = None
    moderada: Optional[str] = None
    alta: Optional[str] = None
    r_baixa: Optional[str] = None
    r_moderada: Optional[str] = None
    r_alta: Optional[str] = None
    d_baixa: Optional[str] = None
    d_moderada: Optional[str] = None
    d_alta: Optional[str] = None

class VariavelResponse(VariavelBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
