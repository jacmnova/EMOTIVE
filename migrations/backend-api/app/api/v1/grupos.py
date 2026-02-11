# -*- coding: utf-8 -*-
"""
CRUD de Grupos. Um grupo tem uma lista explícita de usuários (usuario_grupo).
Adicionar usuários ao grupo → depois atribuir formulário "por grupo" ou ver monitor "por grupo".
"""
from typing import List, Optional
from fastapi import APIRouter, Depends, HTTPException, Query
from pydantic import BaseModel
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import User, Grupo, Cliente, UsuarioGrupo
from sqlalchemy import func
from app.schemas.grupo import GrupoCreate, GrupoUpdate, GrupoResponse
from app.schemas.user import UserResponse
from app.core.permissions import require_gestor

router = APIRouter()


class GrupoUsuariosAdd(BaseModel):
    usuario_ids: List[int]


def _cliente_ok(current_user: User, db, cliente_id: int) -> bool:
    if current_user.admin or current_user.sa:
        return db.query(Cliente).filter(Cliente.id == cliente_id).first() is not None
    if current_user.gestor:
        return current_user.cliente_id == cliente_id
    return False


@router.get("/", response_model=List[GrupoResponse])
async def list_grupos(
    cliente_id: int = Query(..., description="Filtrar por cliente"),
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    if not _cliente_ok(current_user, db, cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão para este cliente")
    rows = db.query(Grupo).filter(Grupo.cliente_id == cliente_id).order_by(Grupo.nome).all()
    if not rows:
        return []
    counts = dict(
        db.query(UsuarioGrupo.grupo_id, func.count(UsuarioGrupo.usuario_id))
        .filter(UsuarioGrupo.grupo_id.in_([r.id for r in rows]))
        .group_by(UsuarioGrupo.grupo_id)
        .all()
    )
    out = []
    for r in rows:
        data = GrupoResponse.model_validate(r)
        data.num_usuarios = counts.get(r.id, 0)
        out.append(data)
    return out


@router.post("/", response_model=GrupoResponse)
async def create_grupo(
    data: GrupoCreate,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    if not _cliente_ok(current_user, db, data.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão para este cliente")
    g = Grupo(
        cliente_id=data.cliente_id,
        nome=data.nome,
        unidade=data.unidade,
        area=data.area,
        nivel_jerarquico=data.nivel_jerarquico,
        tempo_empresa=data.tempo_empresa,
        modelo_trabalho=data.modelo_trabalho,
    )
    db.add(g)
    db.commit()
    db.refresh(g)
    return GrupoResponse.model_validate(g)


@router.get("/{grupo_id}/usuarios", response_model=List[UserResponse])
async def list_grupo_usuarios(
    grupo_id: int,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """Lista os usuários que pertencem a este grupo."""
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    user_ids = [ug.usuario_id for ug in db.query(UsuarioGrupo).filter(UsuarioGrupo.grupo_id == grupo_id).all()]
    if not user_ids:
        return []
    users = db.query(User).filter(User.id.in_(user_ids), User.deleted_at == None).order_by(User.name).all()
    return [UserResponse.model_validate(u) for u in users]


@router.post("/{grupo_id}/usuarios")
async def add_grupo_usuarios(
    grupo_id: int,
    body: GrupoUsuariosAdd,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """Adiciona usuários ao grupo (por id). Ignora quem já está no grupo."""
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    # Só usuários do mesmo cliente
    users = db.query(User).filter(
        User.id.in_(body.usuario_ids),
        User.cliente_id == g.cliente_id,
        User.deleted_at == None,
        User.ativo == True,
    ).all()
    existing = {ug.usuario_id for ug in db.query(UsuarioGrupo).filter(UsuarioGrupo.grupo_id == grupo_id).all()}
    added = 0
    for u in users:
        if u.id not in existing:
            db.add(UsuarioGrupo(usuario_id=u.id, grupo_id=grupo_id))
            existing.add(u.id)
            added += 1
    db.commit()
    return {"message": f"{added} usuário(s) adicionado(s) ao grupo", "added": added}


@router.delete("/{grupo_id}/usuarios/{usuario_id}")
async def remove_grupo_usuario(
    grupo_id: int,
    usuario_id: int,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """Remove um usuário do grupo."""
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    ug = db.query(UsuarioGrupo).filter(
        UsuarioGrupo.grupo_id == grupo_id,
        UsuarioGrupo.usuario_id == usuario_id,
    ).first()
    if not ug:
        raise HTTPException(status_code=404, detail="Usuário não está neste grupo")
    db.delete(ug)
    db.commit()
    return {"message": "Usuário removido do grupo"}


@router.get("/{grupo_id}", response_model=GrupoResponse)
async def get_grupo(
    grupo_id: int,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    return GrupoResponse.model_validate(g)


@router.put("/{grupo_id}", response_model=GrupoResponse)
async def update_grupo(
    grupo_id: int,
    data: GrupoUpdate,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    if data.nome is not None:
        g.nome = data.nome
    if data.unidade is not None:
        g.unidade = data.unidade
    if data.area is not None:
        g.area = data.area
    if data.nivel_jerarquico is not None:
        g.nivel_jerarquico = data.nivel_jerarquico
    if data.tempo_empresa is not None:
        g.tempo_empresa = data.tempo_empresa
    if data.modelo_trabalho is not None:
        g.modelo_trabalho = data.modelo_trabalho
    db.commit()
    db.refresh(g)
    return GrupoResponse.model_validate(g)


@router.delete("/{grupo_id}")
async def delete_grupo(
    grupo_id: int,
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    g = db.query(Grupo).filter(Grupo.id == grupo_id).first()
    if not g:
        raise HTTPException(status_code=404, detail="Grupo não encontrado")
    if not _cliente_ok(current_user, db, g.cliente_id):
        raise HTTPException(status_code=403, detail="Sem permissão")
    db.delete(g)
    db.commit()
    return {"message": "Grupo eliminado"}
