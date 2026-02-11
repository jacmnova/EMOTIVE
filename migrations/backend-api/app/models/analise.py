"""
Modelo Analise
"""
from sqlalchemy import Column, Integer, Text, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Analise(Base):
    __tablename__ = "analises"
    
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    texto = Column(Text, nullable=False)  # Análisis generado por OpenAI
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    user = relationship("User", back_populates="analises")
    formulario = relationship("Formulario")
