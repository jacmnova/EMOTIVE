# -*- coding: utf-8 -*-
"""
Modelo User
"""
from sqlalchemy import Column, Integer, String, Boolean, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class User(Base):
    __tablename__ = "users"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    email = Column(String(255), unique=True, nullable=False, index=True)
    password = Column(String(255), nullable=False)
    avatar = Column(String(255), nullable=True)
    email_verified_at = Column(DateTime, nullable=True)
    verification_token = Column(String(64), nullable=True)
    password_reset_token = Column(String(64), nullable=True)
    password_reset_expires = Column(DateTime, nullable=True)
    
    # Roles
    sa = Column(Boolean, default=False)
    admin = Column(Boolean, default=False)
    gestor = Column(Boolean, default=False)
    usuario = Column(Boolean, default=False)
    
    # Atributos para relatorio corporativo / grupo (unidade, area, nivel, etc.)
    unidade = Column(String(255), nullable=True)
    area = Column(String(255), nullable=True)
    nivel_jerarquico = Column(String(255), nullable=True)
    tempo_empresa = Column(String(255), nullable=True)
    modelo_trabalho = Column(String(255), nullable=True)

    # Relaciones
    cliente_id = Column(Integer, ForeignKey("clientes.id"), nullable=True)
    deleted_by = Column(Integer, nullable=True)
    ativo = Column(Boolean, default=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    deleted_at = Column(DateTime, nullable=True)  # Soft delete
    
    # Relationships
    cliente = relationship("Cliente", foreign_keys=[cliente_id], back_populates="usuarios")
    cliente_gestor = relationship("Cliente", foreign_keys="Cliente.usuario_id", back_populates="gestor")
    respostas = relationship("Resposta", back_populates="user")
    usuario_formularios = relationship("UsuarioFormulario", back_populates="usuario")
    analises = relationship("Analise", back_populates="user")
    relatorios_gerados = relationship("RelatorioGerado", back_populates="user")
    grupos = relationship("UsuarioGrupo", back_populates="usuario", cascade="all, delete-orphan")
