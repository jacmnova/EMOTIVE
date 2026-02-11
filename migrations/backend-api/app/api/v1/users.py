"""
Endpoints de usuarios
"""
import csv
import io
import secrets
from datetime import datetime, timezone, timedelta
from fastapi import APIRouter, Depends, File, HTTPException, status, Query, UploadFile
from pydantic import BaseModel
from sqlalchemy.orm import Session
from typing import List, Optional
from app.database import get_db
from app.models import User, UsuarioFormulario
from app.schemas.user import UserCreate, UserUpdate, UserResponse
from app.core.permissions import get_current_user, require_admin, require_gestor
from app.core.security import get_password_hash
from app.services.email import send_reset_password_email

router = APIRouter()

@router.get("/", response_model=List[UserResponse])
async def list_users(
    skip: int = Query(0, ge=0),
    limit: int = Query(100, ge=1, le=500),
    cliente_id: Optional[int] = None,
    ativo: Optional[bool] = None,
    unidade: Optional[str] = Query(None, description="Filtrar por unidade (população)"),
    area: Optional[str] = Query(None, description="Filtrar por área"),
    nivel_jerarquico: Optional[str] = Query(None, description="Filtrar por nível hierárquico"),
    tempo_empresa: Optional[str] = Query(None, description="Filtrar por tempo de empresa"),
    modelo_trabalho: Optional[str] = Query(None, description="Filtrar por modelo de trabalho"),
    periodo_id: Optional[int] = Query(None, description="Filtrar usuários com atribuição neste período"),
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db)
):
    """
    Lista usuarios. Admin/SA ven todos; gestor solo los de su cliente.
    Filtros opcionales de población: unidade, area, nivel_jerarquico, tempo_empresa, modelo_trabalho.
    """
    query = db.query(User).filter(User.deleted_at == None)

    if current_user.gestor and not current_user.admin and not current_user.sa:
        query = query.filter(User.cliente_id == current_user.cliente_id)

    if cliente_id is not None:
        if current_user.gestor and not current_user.admin and not current_user.sa:
            if cliente_id != current_user.cliente_id:
                raise HTTPException(status_code=403, detail="Só pode listar usuários do seu cliente")
        query = query.filter(User.cliente_id == cliente_id)

    if ativo is not None:
        query = query.filter(User.ativo == ativo)
    if unidade is not None and unidade.strip() != "":
        query = query.filter(User.unidade == unidade.strip())
    if area is not None and area.strip() != "":
        query = query.filter(User.area == area.strip())
    if nivel_jerarquico is not None and nivel_jerarquico.strip() != "":
        query = query.filter(User.nivel_jerarquico == nivel_jerarquico.strip())
    if tempo_empresa is not None and tempo_empresa.strip() != "":
        query = query.filter(User.tempo_empresa == tempo_empresa.strip())
    if modelo_trabalho is not None and modelo_trabalho.strip() != "":
        query = query.filter(User.modelo_trabalho == modelo_trabalho.strip())

    if periodo_id is not None:
        query = query.join(UsuarioFormulario, UsuarioFormulario.usuario_id == User.id).filter(
            UsuarioFormulario.periodo_id == periodo_id,
            UsuarioFormulario.deleted_at == None,
        ).distinct()

    users = query.offset(skip).limit(limit).all()
    return [UserResponse.model_validate(u) for u in users]

@router.get("/{user_id}", response_model=UserResponse)
async def get_user(
    user_id: int,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Obtiene un usuario por ID
    """
    user = db.query(User).filter(User.id == user_id).first()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Usuario no encontrado"
        )
    
    # Verificar permisos
    if current_user.id != user_id:
        if not (current_user.admin or current_user.sa):
            if current_user.gestor and current_user.cliente_id != user.cliente_id:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="No tienes permiso para ver este usuario"
                )
    
    return UserResponse.model_validate(user)

@router.post("/", response_model=UserResponse)
async def create_user(
    user_data: UserCreate,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """
    Crea un nuevo usuario (solo admin)
    """
    # Verificar email único
    existing = db.query(User).filter(User.email == user_data.email).first()
    if existing:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="El email ya está registrado"
        )
    
    hashed_password = get_password_hash(user_data.password)
    verification_token = None  # Se puede generar si se necesita
    
    user = User(
        name=user_data.name,
        email=user_data.email,
        password=hashed_password,
        verification_token=verification_token,
        cliente_id=user_data.cliente_id,
        sa=user_data.sa,
        admin=user_data.admin,
        gestor=user_data.gestor,
        usuario=user_data.usuario,
        ativo=True
    )
    
    db.add(user)
    db.commit()
    db.refresh(user)
    
    return UserResponse.model_validate(user)

@router.put("/{user_id}", response_model=UserResponse)
async def update_user(
    user_id: int,
    user_data: UserUpdate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Actualiza un usuario
    """
    user = db.query(User).filter(User.id == user_id).first()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Usuario no encontrado"
        )
    
    # Verificar permisos
    if current_user.id != user_id:
        if not (current_user.admin or current_user.sa):
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="No tienes permiso para editar este usuario"
            )
    
    # Actualizar campos
    update_data = user_data.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(user, field, value)
    
    db.commit()
    db.refresh(user)
    
    return UserResponse.model_validate(user)

@router.delete("/{user_id}")
async def delete_user(
    user_id: int,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """
    Elimina un usuario (soft delete)
    """
    user = db.query(User).filter(User.id == user_id).first()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Usuario no encontrado"
        )
    
    from datetime import datetime
    user.deleted_at = datetime.utcnow()
    user.deleted_by = current_user.id
    user.ativo = False
    
    db.commit()
    
    return {"message": "Usuario eliminado correctamente"}

@router.post("/importar")
async def importar_usuarios_csv(
    arquivo: UploadFile = File(...),
    current_user: User = Depends(require_gestor),
    db: Session = Depends(get_db),
):
    """
    Importa usuários em lote via CSV (apenas gestor). CSV: cabeçalho na primeira linha,
    colunas email, nome. Usuários criados com cliente_id do gestor e senha padrão.
    """
    if not current_user.cliente_id:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Gestor sem cliente associado",
        )
    if not arquivo.filename or not (arquivo.filename.lower().endswith(".csv") or arquivo.filename.lower().endswith(".txt")):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Envie um arquivo CSV ou TXT",
        )
    content = await arquivo.read()
    try:
        text = content.decode("utf-8")
    except UnicodeDecodeError:
        text = content.decode("latin-1")
    reader = csv.reader(io.StringIO(text))
    rows = list(reader)
    if not rows:
        return {"message": "Arquivo vazio.", "cadastrados": 0}
    # Pular cabeçalho
    senha_padrao = "mudar@123"
    hashed = get_password_hash(senha_padrao)
    cadastrados = 0
    for row in rows[1:]:
        if len(row) < 2:
            continue
        email = (row[0] or "").strip()
        nome = (row[1] or "").strip()
        if not email or not nome:
            continue
        if db.query(User).filter(User.email == email, User.deleted_at == None).first():
            continue
        user = User(
            name=nome,
            email=email,
            password=hashed,
            email_verified_at=datetime.now(timezone.utc),
            cliente_id=current_user.cliente_id,
            usuario=True,
            gestor=False,
            admin=False,
            sa=False,
            ativo=True,
        )
        db.add(user)
        cadastrados += 1
    db.commit()
    return {"message": f"{cadastrados} usuários importados com sucesso.", "cadastrados": cadastrados}


@router.post("/{user_id}/password/initiate")
async def admin_password_initiate(
    user_id: int,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Admin envia email de recuperação de senha para o usuário (link para definir nova senha)."""
    user = db.query(User).filter(User.id == user_id, User.deleted_at == None).first()
    if not user:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Usuario no encontrado")
    token = secrets.token_urlsafe(32)
    user.password_reset_token = token
    user.password_reset_expires = datetime.now(timezone.utc) + timedelta(hours=1)
    db.commit()
    send_reset_password_email(user.email, user.name, token)
    return {"message": "E-mail de recuperação de senha enviado ao usuário."}


class AdminPasswordUpdateBody(BaseModel):
    new_password: str

@router.post("/{user_id}/password/update")
async def admin_password_update(
    user_id: int,
    body: AdminPasswordUpdateBody,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Admin define nova senha para o usuário diretamente."""
    if len(body.new_password) < 8:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Senha deve ter no mínimo 8 caracteres")
    user = db.query(User).filter(User.id == user_id, User.deleted_at == None).first()
    if not user:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Usuario no encontrado")
    user.password = get_password_hash(body.new_password)
    user.password_reset_token = None
    user.password_reset_expires = None
    db.commit()
    return {"message": "Senha atualizada com sucesso."}


@router.put("/{user_id}/status")
async def toggle_user_status(
    user_id: int,
    current_user: User = Depends(require_admin),
    db: Session = Depends(get_db)
):
    """
    Activa/desactiva un usuario
    """
    user = db.query(User).filter(User.id == user_id).first()
    
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Usuario no encontrado"
        )
    
    user.ativo = not user.ativo
    db.commit()
    
    return {"message": f"Usuario {'activado' if user.ativo else 'desactivado'} correctamente", "ativo": user.ativo}
