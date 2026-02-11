"""
Schemas Pydantic para Formulario
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class FormularioBase(BaseModel):
    nome: str
    label: str
    descricao: str
    instrucoes: str
    score_ini: int
    score_fim: int
    calculo_id: Optional[int] = None
    status: bool = False

class FormularioCreate(FormularioBase):
    pass

class FormularioUpdate(BaseModel):
    nome: Optional[str] = None
    label: Optional[str] = None
    descricao: Optional[str] = None
    instrucoes: Optional[str] = None
    score_ini: Optional[int] = None
    score_fim: Optional[int] = None
    calculo_id: Optional[int] = None
    status: Optional[bool] = None

class FormularioResponse(FormularioBase):
    id: int
    created_at: datetime
    updated_at: datetime
    num_perguntas: Optional[int] = None
    num_variaveis: Optional[int] = None

    class Config:
        from_attributes = True


class EtapaCreate(BaseModel):
    etapa: int
    de: int  # ID pergunta início
    ate: int  # ID pergunta fim


class EtapaResponse(BaseModel):
    id: int
    formulario_id: int
    etapa: int
    de: int
    ate: int
    created_at: Optional[datetime] = None

    class Config:
        from_attributes = True
