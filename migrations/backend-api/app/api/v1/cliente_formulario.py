"""
Endpoints de ClienteFormulario (asignación de formularios a clientes)
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.models import Cliente, Formulario, ClienteFormulario
from app.schemas.cliente_formulario import ClienteFormularioCreate, ClienteFormularioUpdate, ClienteFormularioResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[ClienteFormularioResponse])
async def list_cliente_formularios(
    cliente_id: int = Query(None),
    formulario_id: int = Query(None),
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Lista asignaciones cliente-formulario"""
    query = db.query(ClienteFormulario).filter(ClienteFormulario.deleted_at == None)
    
    if cliente_id:
        query = query.filter(ClienteFormulario.cliente_id == cliente_id)
    if formulario_id:
        query = query.filter(ClienteFormulario.formulario_id == formulario_id)
    
    registros = query.all()
    return [ClienteFormularioResponse.model_validate(r) for r in registros]

@router.post("/", response_model=ClienteFormularioResponse)
async def asignar_formulario_cliente(
    data: ClienteFormularioCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Asigna un formulario a un cliente (solo admin)"""
    # Verificar que existen
    cliente = db.query(Cliente).filter(Cliente.id == data.cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    formulario = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    
    # Verificar si ya existe
    existing = db.query(ClienteFormulario).filter(
        ClienteFormulario.cliente_id == data.cliente_id,
        ClienteFormulario.formulario_id == data.formulario_id,
        ClienteFormulario.deleted_at == None
    ).first()
    
    if existing:
        raise HTTPException(
            status_code=400,
            detail="Este formulário já está atribuído a este cliente"
        )
    
    cf = ClienteFormulario(**data.model_dump())
    db.add(cf)
    db.commit()
    db.refresh(cf)
    
    return ClienteFormularioResponse.model_validate(cf)

@router.put("/{cliente_formulario_id}", response_model=ClienteFormularioResponse)
async def update_cliente_formulario(
    cliente_formulario_id: int,
    data: ClienteFormularioUpdate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Actualiza asignación cliente-formulario (solo admin)"""
    cf = db.query(ClienteFormulario).filter(
        ClienteFormulario.id == cliente_formulario_id
    ).first()
    
    if not cf:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    
    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(cf, field, value)
    
    db.commit()
    db.refresh(cf)
    
    return ClienteFormularioResponse.model_validate(cf)

@router.delete("/{cliente_formulario_id}")
async def delete_cliente_formulario(
    cliente_formulario_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Elimina asignación cliente-formulario (soft delete, solo admin)"""
    cf = db.query(ClienteFormulario).filter(
        ClienteFormulario.id == cliente_formulario_id
    ).first()
    
    if not cf:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    
    from datetime import datetime
    cf.deleted_at = datetime.utcnow()
    cf.deleted_by = current_user.id
    cf.ativo = False
    
    db.commit()
    
    return {"message": "Asignación eliminada correctamente"}
