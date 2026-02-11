"""
Endpoints de clientes
"""
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.models import Cliente, User
from app.schemas.cliente import ClienteCreate, ClienteUpdate, ClienteResponse
from app.core.permissions import get_current_user, require_admin
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[ClienteResponse])
async def list_clientes(
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Lista clientes.
    Admin ve todos, gestor solo ve su cliente.
    """
    query = db.query(Cliente).filter(Cliente.deleted_at == None)
    
    if current_user.gestor and not current_user.admin:
        # Gestor solo ve su cliente
        query = query.filter(Cliente.usuario_id == current_user.id)
    elif not (current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="No tienes permiso para listar clientes")
    
    clientes = query.order_by(Cliente.nome_fantasia).all()
    return [ClienteResponse.model_validate(c) for c in clientes]

@router.get("/{cliente_id}", response_model=ClienteResponse)
async def get_cliente(
    cliente_id: int,
    current_user = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """Obtiene un cliente"""
    cliente = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    # Verificar permisos
    if current_user.gestor and not current_user.admin:
        if cliente.usuario_id != current_user.id:
            raise HTTPException(status_code=403, detail="No tienes permiso para ver este cliente")
    
    return ClienteResponse.model_validate(cliente)

@router.post("/", response_model=ClienteResponse)
async def create_cliente(
    data: ClienteCreate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Crea un cliente (solo admin)"""
    # Verificar CPF/CNPJ único
    existing = db.query(Cliente).filter(Cliente.cpf_cnpj == data.cpf_cnpj).first()
    if existing:
        raise HTTPException(
            status_code=400,
            detail="CPF/CNPJ já cadastrado"
        )
    
    # Verificar usuario_id si se proporciona
    if data.usuario_id:
        user = db.query(User).filter(User.id == data.usuario_id).first()
        if not user:
            raise HTTPException(status_code=404, detail="Usuario não encontrado")
    
    cliente = Cliente(**data.model_dump())
    db.add(cliente)
    db.commit()
    db.refresh(cliente)
    
    return ClienteResponse.model_validate(cliente)

@router.put("/{cliente_id}", response_model=ClienteResponse)
async def update_cliente(
    cliente_id: int,
    data: ClienteUpdate,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Actualiza un cliente (solo admin)"""
    cliente = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    # Verificar CPF/CNPJ único si se cambia
    if data.cpf_cnpj and data.cpf_cnpj != cliente.cpf_cnpj:
        existing = db.query(Cliente).filter(Cliente.cpf_cnpj == data.cpf_cnpj).first()
        if existing:
            raise HTTPException(status_code=400, detail="CPF/CNPJ já cadastrado")
    
    # Verificar usuario_id si se cambia
    if data.usuario_id and data.usuario_id != cliente.usuario_id:
        user = db.query(User).filter(User.id == data.usuario_id).first()
        if not user:
            raise HTTPException(status_code=404, detail="Usuario não encontrado")
    
    # Actualizar campos
    for field, value in data.model_dump(exclude_unset=True).items():
        setattr(cliente, field, value)
    
    db.commit()
    db.refresh(cliente)
    
    return ClienteResponse.model_validate(cliente)

@router.delete("/{cliente_id}")
async def delete_cliente(
    cliente_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Elimina un cliente (soft delete, solo admin)"""
    cliente = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    from datetime import datetime
    cliente.deleted_at = datetime.utcnow()
    cliente.deleted_by = current_user.id
    cliente.ativo = False
    
    db.commit()
    
    return {"message": "Cliente eliminado correctamente"}

@router.put("/{cliente_id}/status")
async def toggle_cliente_status(
    cliente_id: int,
    current_user = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """Activa/desactiva un cliente"""
    cliente = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    cliente.ativo = not cliente.ativo
    db.commit()
    
    return {"message": f"Cliente {'activado' if cliente.ativo else 'desactivado'} correctamente", "ativo": cliente.ativo}
