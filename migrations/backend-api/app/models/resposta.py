"""
Modelo Resposta
"""
from sqlalchemy import Column, Integer, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Resposta(Base):
    __tablename__ = "respostas"
    
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    pergunta_id = Column(Integer, ForeignKey("perguntas.id"), nullable=False)
    valor_resposta = Column(Integer, nullable=False)  # 0-6
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    user = relationship("User", back_populates="respostas")
    pergunta = relationship("Pergunta", back_populates="respostas")
