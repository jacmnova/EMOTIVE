"""
Modelo FormularioEtapa
"""
from sqlalchemy import Column, Integer, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class FormularioEtapa(Base):
    __tablename__ = "formulario_etapas"
    
    id = Column(Integer, primary_key=True, index=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    etapa = Column(Integer, nullable=False)  # Número de etapa
    de = Column(Integer, nullable=False)     # ID pregunta inicio
    ate = Column(Integer, nullable=False)    # ID pregunta fin
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    formulario = relationship("Formulario", back_populates="etapas")
