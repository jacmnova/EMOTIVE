"""
Schemas para ClienteFormulario
"""
from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class ClienteFormularioCreate(BaseModel):
    cliente_id: int
    formulario_id: int
    quantidade: Optional[int] = None

class ClienteFormularioUpdate(BaseModel):
    quantidade: Optional[int] = None
    ativo: Optional[bool] = None

class ClienteFormularioResponse(BaseModel):
    id: int
    cliente_id: int
    formulario_id: int
    quantidade: Optional[int] = None
    ativo: bool
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
