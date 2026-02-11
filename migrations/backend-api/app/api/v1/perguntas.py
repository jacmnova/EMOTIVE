"""
Endpoints de preguntas
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.models import Formulario, Pergunta
from app.schemas.pergunta import PerguntaCreate, PerguntaUpdate, PerguntaResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[PerguntaResponse])
async def list_perguntas(
    formulario_id: Optional[int] = Query(None),
    skip: int = Query(0, ge=0),
    limit: int = Query(500, ge=1, le=500),
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Lista preguntas, opcionalmente filtradas por formulario_id"""
    query = db.query(Pergunta)
    if formulario_id is not None:
        query = query.filter(Pergunta.formulario_id == formulario_id)
    perguntas = query.order_by(Pergunta.numero_da_pergunta).offset(skip).limit(limit).all()
    return [PerguntaResponse.model_validate(p) for p in perguntas]

@router.get("/{pergunta_id}", response_model=PerguntaResponse)
async def get_pergunta(
    pergunta_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Obtiene una pregunta por ID"""
    pergunta = db.query(Pergunta).filter(Pergunta.id == pergunta_id).first()
    if not pergunta:
        raise HTTPException(status_code=404, detail="Pregunta no encontrada")
    return PerguntaResponse.model_validate(pergunta)

@router.post("/", response_model=PerguntaResponse)
async def create_pergunta(
    data: PerguntaCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Crea una pregunta (solo admin)"""
    # Verificar que el formulario existe
    f = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not f:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    pergunta = Pergunta(**data.model_dump())
    db.add(pergunta)
    db.commit()
    db.refresh(pergunta)
    return PerguntaResponse.model_validate(pergunta)

@router.put("/{pergunta_id}", response_model=PerguntaResponse)
async def update_pergunta(
    pergunta_id: int,
    data: PerguntaUpdate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Actualiza una pregunta (solo admin)"""
    pergunta = db.query(Pergunta).filter(Pergunta.id == pergunta_id).first()
    if not pergunta:
        raise HTTPException(status_code=404, detail="Pregunta no encontrada")
    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(pergunta, field, value)
    db.commit()
    db.refresh(pergunta)
    return PerguntaResponse.model_validate(pergunta)

@router.delete("/{pergunta_id}")
async def delete_pergunta(
    pergunta_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Elimina una pregunta (solo admin)"""
    pergunta = db.query(Pergunta).filter(Pergunta.id == pergunta_id).first()
    if not pergunta:
        raise HTTPException(status_code=404, detail="Pregunta no encontrada")
    db.delete(pergunta)
    db.commit()
    return {"message": "Pregunta eliminada correctamente"}
