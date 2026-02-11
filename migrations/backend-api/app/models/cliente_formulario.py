"""
Modelo Pivot ClienteFormulario
"""
from sqlalchemy import Column, Integer, Boolean, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class ClienteFormulario(Base):
    __tablename__ = "cliente_formulario"
    
    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=True)
    quantidade = Column(Integer, nullable=True)
    ativo = Column(Boolean, default=True)
    deleted_by = Column(Integer, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    deleted_at = Column(DateTime, nullable=True)  # Soft delete
    
    # Relationships
    cliente = relationship("Cliente", back_populates="cliente_formularios")
    formulario = relationship("Formulario", back_populates="cliente_formularios")
