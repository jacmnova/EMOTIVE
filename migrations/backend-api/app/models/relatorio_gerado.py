# -*- coding: utf-8 -*-
"""
Modelo RelatorioGerado (registo de relatórios corporativos gerados para listar na aba Relatórios)
"""
from sqlalchemy import Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base


class RelatorioGerado(Base):
    __tablename__ = "relatorio_gerado"

    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=False)
    periodo_id = Column(Integer, ForeignKey("periodos.id"), nullable=False)
    formulario_id = Column(Integer, ForeignKey("formularios.id"), nullable=False)
    tipo = Column(String(50), nullable=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)

    created_at = Column(DateTime, server_default=func.now())

    cliente = relationship("Cliente", back_populates="relatorios_gerados")
    periodo = relationship("Periodo", back_populates="relatorios_gerados")
    formulario = relationship("Formulario", back_populates="relatorios_gerados")
    user = relationship("User", back_populates="relatorios_gerados")
