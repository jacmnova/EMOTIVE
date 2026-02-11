"""
CRUD de tipos de cálculo (tipo_calculo). Usado en formularios (calculo_id).
"""
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List

from app.database import get_db
from app.models import TipoCalculo
from app.schemas.tipo_calculo import TipoCalculoCreate, TipoCalculoUpdate, TipoCalculoResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()


@router.get("/", response_model=List[TipoCalculoResponse])
async def list_calculos(
    current_user=Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Lista todos los tipos de cálculo."""
    items = db.query(TipoCalculo).order_by(TipoCalculo.id).all()
    return [TipoCalculoResponse.model_validate(x) for x in items]


@router.get("/{calculo_id}", response_model=TipoCalculoResponse)
async def get_calculo(
    calculo_id: int,
    current_user=Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Obtiene un tipo de cálculo por ID."""
    item = db.query(TipoCalculo).filter(TipoCalculo.id == calculo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Tipo de cálculo no encontrado")
    return TipoCalculoResponse.model_validate(item)


@router.post("/", response_model=TipoCalculoResponse)
async def create_calculo(
    data: TipoCalculoCreate,
    current_user=Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Crea un tipo de cálculo (solo admin)."""
    item = TipoCalculo(
        nome=data.nome,
        descricao=data.descricao,
        operador=data.operador,
        formula=data.formula,
    )
    db.add(item)
    db.commit()
    db.refresh(item)
    return TipoCalculoResponse.model_validate(item)


@router.put("/{calculo_id}", response_model=TipoCalculoResponse)
async def update_calculo(
    calculo_id: int,
    data: TipoCalculoUpdate,
    current_user=Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Actualiza un tipo de cálculo (solo admin)."""
    item = db.query(TipoCalculo).filter(TipoCalculo.id == calculo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Tipo de cálculo no encontrado")
    for k, v in data.model_dump(exclude_unset=True).items():
        setattr(item, k, v)
    db.commit()
    db.refresh(item)
    return TipoCalculoResponse.model_validate(item)


@router.delete("/{calculo_id}")
async def delete_calculo(
    calculo_id: int,
    current_user=Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Elimina un tipo de cálculo (solo admin)."""
    item = db.query(TipoCalculo).filter(TipoCalculo.id == calculo_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Tipo de cálculo no encontrado")
    db.delete(item)
    db.commit()
    return {"message": "Tipo de cálculo eliminado"}
