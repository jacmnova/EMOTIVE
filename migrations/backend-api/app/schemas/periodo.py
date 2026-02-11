"""
Schemas Pydantic para Periodo
"""
from pydantic import BaseModel
from typing import Optional
from datetime import date, datetime


class PeriodoBase(BaseModel):
    nome: str
    descricao: Optional[str] = None
    data_inicio: Optional[date] = None
    data_fim: Optional[date] = None


class PeriodoCreate(PeriodoBase):
    cliente_id: int
    projeto_id: Optional[int] = None


class PeriodoUpdate(BaseModel):
    nome: Optional[str] = None
    descricao: Optional[str] = None
    data_inicio: Optional[date] = None
    data_fim: Optional[date] = None
    projeto_id: Optional[int] = None


class PeriodoResponse(PeriodoBase):
    id: int
    cliente_id: int
    projeto_id: Optional[int] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
