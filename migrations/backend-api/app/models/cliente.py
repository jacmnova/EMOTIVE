"""
Modelo Cliente
"""
from sqlalchemy import Column, Integer, String, Boolean, DateTime, ForeignKey, Enum
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
import enum
from app.database import Base

class TipoCliente(str, enum.Enum):
    CNPJ = "cnpj"
    CPF = "cpf"
    INTERNACIONAL = "internacional"

class Cliente(Base):
    __tablename__ = "clientes"
    
    id = Column(Integer, primary_key=True, index=True)
    usuario_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    tipo = Column(Enum(TipoCliente), nullable=False)
    cpf_cnpj = Column(String(255), unique=True, nullable=False)
    nome_fantasia = Column(String(255), nullable=True)
    razao_social = Column(String(255), nullable=True)
    logo_url = Column(String(255), default="../vendor/adminlte/dist/img/client.png", nullable=False)
    email = Column(String(255), nullable=True)
    contato = Column(String(255), nullable=True)
    telefone = Column(String(255), nullable=True)
    ativo = Column(Boolean, default=True)
    deleted_by = Column(Integer, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    deleted_at = Column(DateTime, nullable=True)  # Soft delete
    
    # Relationships
    gestor = relationship("User", foreign_keys=[usuario_id], back_populates="cliente_gestor")
    usuarios = relationship("User", foreign_keys="User.cliente_id", back_populates="cliente")
    cliente_formularios = relationship("ClienteFormulario", back_populates="cliente")
    periodos = relationship("Periodo", back_populates="cliente")
    projetos = relationship("Projeto", back_populates="cliente")
    grupos = relationship("Grupo", back_populates="cliente")
    relatorios_gerados = relationship("RelatorioGerado", back_populates="cliente")
