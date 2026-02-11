"""
CRUD de Períodos (ondas / séries para relatório corporativo).
Gestor vê apenas períodos do seu cliente; admin/sa vê todos.
"""
from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy.orm import Session
from typing import List, Optional

from app.database import get_db
from app.models import Periodo, Cliente, User
from app.schemas.periodo import PeriodoCreate, PeriodoUpdate, PeriodoResponse
from app.api.v1.auth import get_current_user_token

router = APIRouter()


def _require_gestor(current_user: User) -> None:
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Requerido rol Gestor, Admin ou SA")


def _cliente_ids_for_user(current_user: User, db: Session) -> Optional[List[int]]:
    """Para gestor (não admin): IDs de clientes que pode acessar. None = pode ver todos."""
    if current_user.admin or current_user.sa:
        return None
    if current_user.gestor:
        return [c.id for c in db.query(Cliente.id).filter(Cliente.usuario_id == current_user.id).all()]
    return []


@router.get("/", response_model=List[PeriodoResponse])
async def list_periodos(
    cliente_id: Optional[int] = Query(None, description="Filtrar por cliente"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Lista períodos. Gestor só vê do seu cliente; admin/sa vê todos."""
    _require_gestor(current_user)
    query = db.query(Periodo)
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and len(allowed) == 0:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para listar períodos")
    if allowed is not None:
        query = query.filter(Periodo.cliente_id.in_(allowed))
    if cliente_id is not None:
        if allowed is not None and cliente_id not in allowed:
            raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Cliente não permitido")
        query = query.filter(Periodo.cliente_id == cliente_id)
    items = query.order_by(Periodo.data_inicio.desc().nulls_last(), Periodo.id.desc()).all()
    return [PeriodoResponse.model_validate(x) for x in items]


@router.get("/{periodo_id}", response_model=PeriodoResponse)
async def get_periodo(
    periodo_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Obtém um período por ID."""
    _require_gestor(current_user)
    item = db.query(Periodo).filter(Periodo.id == periodo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Período não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este período")
    return PeriodoResponse.model_validate(item)


@router.post("/", response_model=PeriodoResponse)
async def create_periodo(
    data: PeriodoCreate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Cria um período (gestor só do seu cliente)."""
    _require_gestor(current_user)
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and data.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Cliente não permitido")
    cliente = db.query(Cliente).filter(Cliente.id == data.cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Cliente não encontrado")
    item = Periodo(
        cliente_id=data.cliente_id,
        projeto_id=data.projeto_id,
        nome=data.nome,
        descricao=data.descricao,
        data_inicio=data.data_inicio,
        data_fim=data.data_fim,
    )
    db.add(item)
    db.commit()
    db.refresh(item)
    return PeriodoResponse.model_validate(item)


@router.put("/{periodo_id}", response_model=PeriodoResponse)
async def update_periodo(
    periodo_id: int,
    data: PeriodoUpdate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Atualiza um período."""
    _require_gestor(current_user)
    item = db.query(Periodo).filter(Periodo.id == periodo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Período não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este período")
    for k, v in data.model_dump(exclude_unset=True).items():
        setattr(item, k, v)
    db.commit()
    db.refresh(item)
    return PeriodoResponse.model_validate(item)


@router.delete("/{periodo_id}")
async def delete_periodo(
    periodo_id: int,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Remove um período. Atribuições (usuario_formulario) ficam com periodo_id NULL."""
    _require_gestor(current_user)
    item = db.query(Periodo).filter(Periodo.id == periodo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Período não encontrado")
    allowed = _cliente_ids_for_user(current_user, db)
    if allowed is not None and item.cliente_id not in allowed:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sem permissão para este período")
    db.delete(item)
    db.commit()
    return {"message": "Período eliminado"}
