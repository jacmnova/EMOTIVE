"""
Endpoints de formularios
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from sqlalchemy import func
from typing import List, Optional
from app.database import get_db
from app.models import Formulario, FormularioEtapa, Pergunta, Variavel
from app.schemas.formulario import FormularioCreate, FormularioUpdate, FormularioResponse, EtapaCreate, EtapaResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[FormularioResponse])
async def list_formularios(
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=100),
    status: Optional[bool] = None,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Lista formularios (autenticado) con número de questões y dimensiones."""
    query = db.query(Formulario)
    if status is not None:
        query = query.filter(Formulario.status == status)
    formularios = query.order_by(Formulario.id).offset(skip).limit(limit).all()
    if not formularios:
        return []
    ids = [f.id for f in formularios]
    # Conteo de perguntas por formulario_id
    pergunta_counts = (
        db.query(Pergunta.formulario_id, func.count(Pergunta.id).label("cnt"))
        .filter(Pergunta.formulario_id.in_(ids))
        .group_by(Pergunta.formulario_id)
        .all()
    )
    pergunta_by_f = {r.formulario_id: r.cnt for r in pergunta_counts}
    # Conteo de variaveis (dimensiones) por formulario_id
    variavel_counts = (
        db.query(Variavel.formulario_id, func.count(Variavel.id).label("cnt"))
        .filter(Variavel.formulario_id.in_(ids))
        .group_by(Variavel.formulario_id)
        .all()
    )
    variavel_by_f = {r.formulario_id: r.cnt for r in variavel_counts}
    result = []
    for f in formularios:
        row = FormularioResponse.model_validate(f).model_dump()
        row["num_perguntas"] = pergunta_by_f.get(f.id, 0)
        row["num_variaveis"] = variavel_by_f.get(f.id, 0)
        result.append(FormularioResponse(**row))
    return result

@router.get("/{formulario_id}/etapas", response_model=List[EtapaResponse])
async def list_etapas(
    formulario_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Lista etapas do formulário"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    etapas = db.query(FormularioEtapa).filter(FormularioEtapa.formulario_id == formulario_id).order_by(FormularioEtapa.etapa).all()
    return [EtapaResponse.model_validate(e) for e in etapas]


@router.post("/{formulario_id}/etapas", response_model=EtapaResponse)
async def add_etapa(
    formulario_id: int,
    data: EtapaCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Adiciona uma etapa ao formulário"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    etapa = FormularioEtapa(formulario_id=formulario_id, etapa=data.etapa, de=data.de, ate=data.ate)
    db.add(etapa)
    db.commit()
    db.refresh(etapa)
    return EtapaResponse.model_validate(etapa)


@router.delete("/{formulario_id}/etapas/{etapa_id}")
async def remove_etapa(
    formulario_id: int,
    etapa_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Remove uma etapa do formulário"""
    etapa = db.query(FormularioEtapa).filter(
        FormularioEtapa.id == etapa_id,
        FormularioEtapa.formulario_id == formulario_id,
    ).first()
    if not etapa:
        raise HTTPException(status_code=404, detail="Etapa no encontrada")
    db.delete(etapa)
    db.commit()
    return {"message": "Etapa removida"}


@router.get("/{formulario_id}", response_model=FormularioResponse)
async def get_formulario(
    formulario_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Obtiene un formulario por ID"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    return FormularioResponse.model_validate(formulario)

@router.post("/", response_model=FormularioResponse)
async def create_formulario(
    data: FormularioCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Crea un formulario (solo admin)"""
    formulario = Formulario(**data.model_dump())
    db.add(formulario)
    db.commit()
    db.refresh(formulario)
    return FormularioResponse.model_validate(formulario)

@router.put("/{formulario_id}", response_model=FormularioResponse)
async def update_formulario(
    formulario_id: int,
    data: FormularioUpdate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Actualiza un formulario (solo admin)"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(formulario, field, value)
    db.commit()
    db.refresh(formulario)
    return FormularioResponse.model_validate(formulario)

@router.delete("/{formulario_id}")
async def delete_formulario(
    formulario_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Elimina un formulario (solo admin)"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    db.delete(formulario)
    db.commit()
    return {"message": "Formulario eliminado correctamente"}

@router.put("/{formulario_id}/status")
async def toggle_formulario_status(
    formulario_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Activa/desactiva un formulario"""
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    formulario.status = not formulario.status
    db.commit()
    return {"message": "Status actualizado", "status": formulario.status}
