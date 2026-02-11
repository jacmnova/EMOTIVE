# -*- coding: utf-8 -*-
"""
Modelo Grupo: lista nomeada de usuários para atribuição em massa e monitor.
Os usuários são adicionados explicitamente ao grupo (tabela usuario_grupo).
Opcionalmente o grupo pode ter filtros (unidade, area, etc.) para adicionar usuários em massa.
"""
from sqlalchemy import Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base


class Grupo(Base):
    __tablename__ = "grupos"

    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=False)
    nome = Column(String(255), nullable=False)
    unidade = Column(String(255), nullable=True)
    area = Column(String(255), nullable=True)
    nivel_jerarquico = Column(String(255), nullable=True)
    tempo_empresa = Column(String(255), nullable=True)
    modelo_trabalho = Column(String(255), nullable=True)

    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())

    cliente = relationship("Cliente", back_populates="grupos")
    # Usuários que pertencem a este grupo (lista explícita)
    usuarios = relationship("UsuarioGrupo", back_populates="grupo", cascade="all, delete-orphan")
