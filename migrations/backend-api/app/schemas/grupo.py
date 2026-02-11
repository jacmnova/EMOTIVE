# -*- coding: utf-8 -*-
from pydantic import BaseModel
from typing import Optional
from datetime import datetime


class GrupoCreate(BaseModel):
    cliente_id: int
    nome: str
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None


class GrupoUpdate(BaseModel):
    nome: Optional[str] = None
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None


class GrupoResponse(BaseModel):
    id: int
    cliente_id: int
    nome: str
    unidade: Optional[str] = None
    area: Optional[str] = None
    nivel_jerarquico: Optional[str] = None
    tempo_empresa: Optional[str] = None
    modelo_trabalho: Optional[str] = None
    created_at: datetime
    updated_at: datetime
    num_usuarios: Optional[int] = None  # preenchido na listagem (contagem de usuario_grupo)

    class Config:
        from_attributes = True
