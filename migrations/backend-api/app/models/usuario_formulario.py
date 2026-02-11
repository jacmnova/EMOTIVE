"""
Modelo Pivot UsuarioFormulario
"""
from sqlalchemy import Column, Integer, String, Boolean, Date, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import enum
from app.database import Base

class StatusFormulario(str, enum.Enum):
    NOVO = "novo"
    PENDENTE = "pendente"
    COMPLETO = "completo"

class UsuarioFormulario(Base):
    __tablename__ = "usuario_formulario"
    
    id = Column(Integer, primary_key=True, index=True)
    usuario_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=True)
    periodo_id = Column(Integer, ForeignKey("periodos.id"), nullable=True)
    status = Column(String(20), default=StatusFormulario.NOVO.value)
    data_limite = Column(Date, nullable=True)
    video_assistido = Column(Boolean, default=False)
    midia_id = Column(Integer, ForeignKey("midias.id"), nullable=True)
    deleted_by = Column(Integer, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    deleted_at = Column(DateTime, nullable=True)  # Soft delete
    
    # Relationships
    usuario = relationship("User", back_populates="usuario_formularios")
    formulario = relationship("Formulario", back_populates="usuario_formularios")
    periodo = relationship("Periodo", back_populates="usuario_formularios", foreign_keys=[periodo_id])
    midia = relationship("Midia", back_populates="usuario_formularios")
