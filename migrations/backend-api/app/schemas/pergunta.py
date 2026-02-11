"""
Schemas Pydantic para Pergunta
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class PerguntaBase(BaseModel):
    formulario_id: int
    numero_da_pergunta: int
    pergunta: str

class PerguntaCreate(PerguntaBase):
    pass

class PerguntaUpdate(BaseModel):
    numero_da_pergunta: Optional[int] = None
    pergunta: Optional[str] = None

class PerguntaResponse(PerguntaBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
