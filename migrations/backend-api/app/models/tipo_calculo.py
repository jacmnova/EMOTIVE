"""
Modelo TipoCalculo
"""
from sqlalchemy import Column, Integer, String, Text, DateTime
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class TipoCalculo(Base):
    __tablename__ = "tipo_calculo"
    
    id = Column(Integer, primary_key=True, index=True)
    nome = Column(String(255), nullable=False)
    descricao = Column(String(500), nullable=True)
    operador = Column(String(255), nullable=True)
    formula = Column(Text, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    formularios = relationship("Formulario", back_populates="tipo_calculo")
