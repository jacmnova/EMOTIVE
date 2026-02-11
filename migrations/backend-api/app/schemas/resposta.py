"""
Schemas Pydantic para Resposta
"""
from pydantic import BaseModel
from typing import Dict, Optional
from datetime import datetime

class RespostaItem(BaseModel):
    pergunta_id: int
    valor_resposta: int  # 0-6

class RespostasSalvar(BaseModel):
    formulario_id: int
    respostas: Dict[str, int]  # pergunta_id (str en JSON) -> valor_resposta 0-6
    etapa_de: Optional[int] = None
    etapa_ate: Optional[int] = None
    etapa_atual: Optional[int] = None

class RespostaResponse(BaseModel):
    id: int
    user_id: int
    pergunta_id: int
    valor_resposta: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
