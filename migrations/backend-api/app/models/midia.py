"""
Modelo Midia
"""
from sqlalchemy import Column, Integer, String, DateTime, ForeignKey, Enum
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import enum
from app.database import Base

class TipoMidia(str, enum.Enum):
    VIDEO = "video"
    URL = "url"

class Midia(Base):
    __tablename__ = "midias"
    
    id = Column(Integer, primary_key=True, index=True)
    titulo = Column(String(255), nullable=False)
    tipo = Column(Enum(TipoMidia), nullable=False)
    url = Column(String(500), nullable=True)
    arquivo = Column(String(500), nullable=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    formulario = relationship("Formulario", back_populates="midias")
    usuario_formularios = relationship("UsuarioFormulario", back_populates="midia")
