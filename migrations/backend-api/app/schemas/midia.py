from pydantic import BaseModel
from typing import Optional
from datetime import datetime


class MidiaBase(BaseModel):
    titulo: str
    tipo: str  # "video" | "url"
    formulario_id: int
    url: Optional[str] = None
    arquivo: Optional[str] = None


class MidiaCreate(BaseModel):
    titulo: str
    tipo: str
    formulario_id: int
    url: Optional[str] = None


class MidiaUpdate(BaseModel):
    titulo: Optional[str] = None
    tipo: Optional[str] = None
    formulario_id: Optional[int] = None
    url: Optional[str] = None


class MidiaResponse(BaseModel):
    id: int
    titulo: str
    tipo: str
    formulario_id: int
    url: Optional[str] = None
    arquivo: Optional[str] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True
