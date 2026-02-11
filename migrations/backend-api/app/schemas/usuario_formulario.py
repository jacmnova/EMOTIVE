"""
Schemas para UsuarioFormulario
"""
from pydantic import BaseModel
from typing import Optional
from datetime import date, datetime

class UsuarioFormularioCreate(BaseModel):
    usuario_id: int
    formulario_id: int
    periodo_id: Optional[int] = None
    data_limite: Optional[date] = None
    midia_id: Optional[int] = None
    enviar_invitacao: Optional[bool] = False


class UsuarioFormularioUpdate(BaseModel):
    status: Optional[str] = None  # novo, pendente, completo
    data_limite: Optional[date] = None
    video_assistido: Optional[bool] = None
    periodo_id: Optional[int] = None


class UsuarioFormularioEmMassaCreate(BaseModel):
    """Atribuição em massa: formulário + período (opcional) + filtros de população ou grupo_id."""
    cliente_id: int
    formulario_id: int
    periodo_id: Optional[int] = None
    data_limite: Optional[date] = None
    grupo_id: Optional[int] = None  # Se definido, usa os filtros do grupo; senão usa os campos abaixo
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None
    enviar_invitacao: Optional[bool] = False


class UsuarioFormularioResponse(BaseModel):
    id: int
    usuario_id: int
    formulario_id: int
    periodo_id: Optional[int] = None
    status: str
    data_limite: Optional[date] = None
    video_assistido: bool
    midia_id: Optional[int] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
