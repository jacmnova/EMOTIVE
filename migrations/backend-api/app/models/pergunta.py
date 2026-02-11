"""
Modelo Pergunta
"""
from sqlalchemy import Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Pergunta(Base):
    __tablename__ = "perguntas"
    
    id = Column(Integer, primary_key=True, index=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    numero_da_pergunta = Column(Integer, nullable=False)
    pergunta = Column(String(500), nullable=False)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    formulario = relationship("Formulario", back_populates="perguntas")
    variaveis = relationship("Variavel", secondary="pergunta_variavel", back_populates="perguntas")
    respostas = relationship("Resposta", back_populates="pergunta", cascade="all, delete-orphan")
    pergunta_variaveis = relationship("PerguntaVariavel", back_populates="pergunta")
