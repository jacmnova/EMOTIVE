from pydantic import BaseModel
from typing import Optional
from datetime import datetime


class TipoCalculoBase(BaseModel):
    nome: str
    descricao: Optional[str] = None
    operador: Optional[str] = None
    formula: Optional[str] = None


class TipoCalculoCreate(TipoCalculoBase):
    pass


class TipoCalculoUpdate(BaseModel):
    nome: Optional[str] = None
    descricao: Optional[str] = None
    operador: Optional[str] = None
    formula: Optional[str] = None


class TipoCalculoResponse(TipoCalculoBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
