"""
Modelo Projeto (agrupa períodos/ondas para relatório corporativo)
"""
from sqlalchemy import Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base


class Projeto(Base):
    __tablename__ = "projetos"

    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=False)
    nome = Column(String(255), nullable=False)
    descricao = Column(String(500), nullable=True)

    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())

    cliente = relationship("Cliente", back_populates="projetos")
    periodos = relationship("Periodo", back_populates="projeto", foreign_keys="Periodo.projeto_id")
