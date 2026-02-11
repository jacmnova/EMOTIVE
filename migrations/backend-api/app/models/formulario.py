"""
Modelo Formulario
"""
from sqlalchemy import Column, Integer, String, Text, Boolean, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.database import Base

class Formulario(Base):
    __tablename__ = "formularios"
    
    id = Column(Integer, primary_key=True, index=True)
    nome = Column(String(255), nullable=False)
    label = Column(String(255), nullable=False)
    descricao = Column(Text, nullable=False)
    instrucoes = Column(Text, nullable=False)
    score_ini = Column(Integer, nullable=False)
    score_fim = Column(Integer, nullable=False)
    calculo_id = Column(Integer, ForeignKey("tipo_calculo.id"), nullable=True)
    status = Column(Boolean, default=False)
    
    # Timestamps
    created_at = Column(DateTime, server_default=func.now())
    updated_at = Column(DateTime, server_default=func.now(), onupdate=func.now())
    
    # Relationships
    tipo_calculo = relationship("TipoCalculo", back_populates="formularios")
    perguntas = relationship("Pergunta", back_populates="formulario", cascade="all, delete-orphan")
    variaveis = relationship("Variavel", back_populates="formulario", cascade="all, delete-orphan")
    etapas = relationship("FormularioEtapa", back_populates="formulario", cascade="all, delete-orphan")
    cliente_formularios = relationship("ClienteFormulario", back_populates="formulario")
    usuario_formularios = relationship("UsuarioFormulario", back_populates="formulario")
    midias = relationship("Midia", back_populates="formulario", cascade="all, delete-orphan")
    relatorios_gerados = relationship("RelatorioGerado", back_populates="formulario")
