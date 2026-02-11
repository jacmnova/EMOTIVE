"""
Modelo Periodo (onda / série de aplicação para relatório corporativo)
"""
from sqlalchemy import Column, Integer, String, Date, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base


class Periodo(Base):
    __tablename__ = "periodos"

    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=False)
    projeto_id = Column(Integer, ForeignKey("projetos.id"), nullable=True)
    nome = Column(String(255), nullable=False)
    descricao = Column(String(500), nullable=True)
    data_inicio = Column(Date, nullable=True)
    data_fim = Column(Date, nullable=True)

    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())

    cliente = relationship("Cliente", back_populates="periodos")
    projeto = relationship("Projeto", back_populates="periodos", foreign_keys=[projeto_id])
    usuario_formularios = relationship(
        "UsuarioFormulario",
        back_populates="periodo",
        foreign_keys="UsuarioFormulario.periodo_id",
    )
    relatorios_gerados = relationship("RelatorioGerado", back_populates="periodo")
