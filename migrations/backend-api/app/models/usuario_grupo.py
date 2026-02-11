# -*- coding: utf-8 -*-
"""
Tabla de pertenencia: usuário pertence a um grupo.
Un grupo tiene una lista explícita de usuarios; se asignan encuestas al grupo = a esos usuarios.
"""
from sqlalchemy import Column, Integer, ForeignKey, UniqueConstraint
from sqlalchemy.orm import relationship
from app.database import Base


class UsuarioGrupo(Base):
    __tablename__ = "usuario_grupo"
    __table_args__ = (UniqueConstraint("usuario_id", "grupo_id", name="uq_usuario_grupo"),)

    id = Column(Integer, primary_key=True, index=True)
    usuario_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    grupo_id = Column(Integer, ForeignKey("grupos.id", ondelete="CASCADE"), nullable=False)

    usuario = relationship("User", back_populates="grupos")
    grupo = relationship("Grupo", back_populates="usuarios")
