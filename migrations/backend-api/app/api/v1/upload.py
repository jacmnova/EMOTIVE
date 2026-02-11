"""
Endpoints de upload de archivos
"""
from fastapi import APIRouter, Depends, HTTPException, status, UploadFile, File
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import User
from app.api.v1.auth import get_current_user_token
from app.services.files import save_upload_file, delete_file, get_file_url
from pydantic import BaseModel

router = APIRouter()

class UploadResponse(BaseModel):
    url: str
    path: str
    message: str

@router.post("/imagem/usuario", response_model=UploadResponse)
async def upload_imagem_usuario(
    file: UploadFile = File(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Sube avatar de usuario
    """
    # Validar tipo de archivo
    if not file.content_type or not file.content_type.startswith("image/"):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="El archivo debe ser una imagen"
        )
    
    try:
        # Eliminar avatar anterior si existe
        if current_user.avatar and current_user.avatar != "../vendor/adminlte/dist/img/user.png":
            delete_file(current_user.avatar)
        
        # Guardar nuevo archivo
        relative_path = await save_upload_file(file, subdir="avatars")
        
        # Actualizar usuario
        current_user.avatar = relative_path
        db.commit()
        
        url = get_file_url(relative_path)
        
        return UploadResponse(
            url=url,
            path=relative_path,
            message="Avatar actualizado correctamente"
        )
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al subir archivo: {str(e)}")

@router.post("/imagem/cliente", response_model=UploadResponse)
async def upload_imagem_cliente(
    cliente_id: int,
    file: UploadFile = File(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Sube logo de cliente (solo admin/gestor)
    """
    from app.models import Cliente
    
    if not (current_user.admin or current_user.sa or current_user.gestor):
        raise HTTPException(status_code=403, detail="No tienes permiso para subir logos")
    
    # Validar tipo
    if not file.content_type or not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="El archivo debe ser una imagen")
    
    cliente = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    
    # Verificar permisos de gestor
    if current_user.gestor and not current_user.admin:
        if cliente.usuario_id != current_user.id:
            raise HTTPException(status_code=403, detail="No tienes permiso para este cliente")
    
    try:
        # Eliminar logo anterior si existe
        if cliente.logo_url and cliente.logo_url != "../vendor/adminlte/dist/img/client.png":
            # Extraer ruta relativa si es necesario
            if "/" in cliente.logo_url:
                delete_file(cliente.logo_url)
        
        # Guardar nuevo archivo
        relative_path = await save_upload_file(file, subdir="logos", filename=f"cliente_{cliente_id}.{file.filename.split('.')[-1]}")
        
        # Actualizar cliente
        cliente.logo_url = relative_path
        db.commit()
        
        url = get_file_url(relative_path)
        
        return UploadResponse(
            url=url,
            path=relative_path,
            message="Logo actualizado correctamente"
        )
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error al subir archivo: {str(e)}")
