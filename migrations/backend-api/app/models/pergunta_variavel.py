"""
Modelo Pivot PerguntaVariavel
"""
from sqlalchemy import Column, Integer, ForeignKey, DateTime
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class PerguntaVariavel(Base):
    __tablename__ = "pergunta_variavel"
    
    id = Column(Integer, primary_key=True, index=True)
    pergunta_id = Column(Integer, ForeignKey("perguntas.id"), nullable=False)
    variavel_id = Column(Integer, ForeignKey("variaveis.id"), nullable=False)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    pergunta = relationship("Pergunta", back_populates="pergunta_variaveis")
    variavel = relationship("Variavel", back_populates="pergunta_variaveis")
