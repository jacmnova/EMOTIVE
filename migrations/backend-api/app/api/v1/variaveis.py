"""
Endpoints de variables (dimensiones)
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.models import Formulario, Variavel
from app.schemas.variavel import VariavelCreate, VariavelUpdate, VariavelResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[VariavelResponse])
async def list_variaveis(
    formulario_id: Optional[int] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=100),
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Lista variables, opcionalmente filtradas por formulario_id"""
    query = db.query(Variavel)
    if formulario_id is not None:
        query = query.filter(Variavel.formulario_id == formulario_id)
    variaveis = query.order_by(Variavel.tag).offset(skip).limit(limit).all()
    return [VariavelResponse.model_validate(v) for v in variaveis]

@router.get("/formulario/{formulario_id}", response_model=List[VariavelResponse])
async def get_variaveis_por_formulario(
    formulario_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Obtiene todas las variables de un formulario"""
    variaveis = db.query(Variavel).filter(Variavel.formulario_id == formulario_id).order_by(Variavel.tag).all()
    return [VariavelResponse.model_validate(v) for v in variaveis]

@router.get("/{variavel_id}", response_model=VariavelResponse)
async def get_variavel(
    variavel_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Obtiene una variable por ID"""
    variavel = db.query(Variavel).filter(Variavel.id == variavel_id).first()
    if not variavel:
        raise HTTPException(status_code=404, detail="Variable no encontrada")
    return VariavelResponse.model_validate(variavel)

@router.post("/", response_model=VariavelResponse)
async def create_variavel(
    data: VariavelCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Crea una variable (solo admin)"""
    f = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not f:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    variavel = Variavel(**data.model_dump())
    db.add(variavel)
    db.commit()
    db.refresh(variavel)
    return VariavelResponse.model_validate(variavel)

@router.put("/{variavel_id}", response_model=VariavelResponse)
async def update_variavel(
    variavel_id: int,
    data: VariavelUpdate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Actualiza una variable (solo admin)"""
    variavel = db.query(Variavel).filter(Variavel.id == variavel_id).first()
    if not variavel:
        raise HTTPException(status_code=404, detail="Variable no encontrada")
    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(variavel, field, value)
    db.commit()
    db.refresh(variavel)
    return VariavelResponse.model_validate(variavel)

@router.delete("/{variavel_id}")
async def delete_variavel(
    variavel_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Elimina una variable (solo admin)"""
    variavel = db.query(Variavel).filter(Variavel.id == variavel_id).first()
    if not variavel:
        raise HTTPException(status_code=404, detail="Variable no encontrada")
    db.delete(variavel)
    db.commit()
    return {"message": "Variable eliminada correctamente"}
