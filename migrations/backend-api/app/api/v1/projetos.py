"""
CRUD de Projetos (agrupam períodos/ondas). Gestor só do seu cliente; admin/sa todos.
"""
from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy.orm import Session
from typing import List, Optional

from app.database import get_db
from app.models import Projeto, Cliente, User
from app.schemas.projeto import ProjetoCreate, ProjetoUpdate, ProjetoResponse
from app.api.v1.auth import get_current_user_token

router = APIRouter()


def _require_gestor(current_user: User) -> None:
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Requerido rol Gestor, Admin ou SA")


def _cliente_ids_for_user(current_user: User, db: Session) -> Optional[List[int]]:
    if current_user.admin or current_user.sa:
        return None
    if current_user.gestor:
        return [c.id for c in db.query(Cliente.id).filter(Cliente.usuario_id == current_user.id).all()]
    return []


@router.get("/", response_model=List[ProjetoResponse])
async def list_projetos(
    cliente_id: Optional[int] = Query(None, description="Filtrar por cliente"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    _require_gestor(current_user)
    query = db.query(Projeto)
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and len(allowed) == 0:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para listar projetos")
    if allowed is not None:
        query = query.filter(Projeto.cliente_id.in_(allowed))
    if cliente_id is not None:
        if allowed is not None and cliente_id not in allowed:
            raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Cliente não permitido")
        query = query.filter(Projeto.cliente_id == cliente_id)
    items = query.order_by(Projeto.nome).all()
    return [ProjetoResponse.model_validate(x) for x in items]


@router.get("/{projeto_id}", response_model=ProjetoResponse)
async def get_projeto(
    projeto_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    _require_gestor(current_user)
    item = db.query(Projeto).filter(Projeto.id == projeto_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Projeto não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este projeto")
    return ProjetoResponse.model_validate(item)


@router.post("/", response_model=ProjetoResponse)
async def create_projeto(
    data: ProjetoCreate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    _require_gestor(current_user)
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and data.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Cliente não permitido")
    cliente = db.query(Cliente).filter(Cliente.id == data.cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Cliente não encontrado")
    item = Projeto(cliente_id=data.cliente_id, nome=data.nome, descricao=data.descricao)
    db.add(item)
    db.commit()
    db.refresh(item)
    return ProjetoResponse.model_validate(item)


@router.put("/{projeto_id}", response_model=ProjetoResponse)
async def update_projeto(
    projeto_id: int,
    data: ProjetoUpdate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    _require_gestor(current_user)
    item = db.query(Projeto).filter(Projeto.id == projeto_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Projeto não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este projeto")
    for k, v in data.model_dump(exclude_unset=True).items():
        setattr(item, k, v)
    db.commit()
    db.refresh(item)
    return ProjetoResponse.model_validate(item)


@router.delete("/{projeto_id}")
async def delete_projeto(
    projeto_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    _require_gestor(current_user)
    item = db.query(Projeto).filter(Projeto.id == projeto_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Projeto não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este projeto")
    db.delete(item)
    db.commit()
    return {"message": "Projeto eliminado"}
