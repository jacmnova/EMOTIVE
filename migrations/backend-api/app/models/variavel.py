"""
Modelo Variavel (Dimensiones)
"""
from sqlalchemy import Column, Integer, String, Text, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Variavel(Base):
    __tablename__ = "variaveis"
    
    id = Column(Integer, primary_key=True, index=True)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    nome = Column(String(255), nullable=False)
    descricao = Column(Text, nullable=True)
    tag = Column(String(10), nullable=False)  # EXEM, REPR, DECI, FAPS, EXTR, ASMO
    
    # Límites de faixas
    B = Column(Integer, nullable=False)  # Límite inferior faixa baja
    M = Column(Integer, nullable=False)  # Límite inferior faixa media
    A = Column(Integer, nullable=True)   # Límite inferior faixa alta
    
    # Descripciones de faixas
    baixa = Column(Text, nullable=True)
    moderada = Column(Text, nullable=True)
    alta = Column(Text, nullable=True)
    
    # Recomendaciones por faixa
    r_baixa = Column(Text, nullable=True)
    r_moderada = Column(Text, nullable=True)
    r_alta = Column(Text, nullable=True)
    
    # Dicas por faixa (opcional)
    d_baixa = Column(Text, nullable=True)
    d_moderada = Column(Text, nullable=True)
    d_alta = Column(Text, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    formulario = relationship("Formulario", back_populates="variaveis")
    perguntas = relationship("Pergunta", secondary="pergunta_variavel", back_populates="variaveis")
    pergunta_variaveis = relationship("PerguntaVariavel", back_populates="variavel")
