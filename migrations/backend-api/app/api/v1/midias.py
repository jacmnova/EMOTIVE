"""
CRUD de Midias (vídeo ou URL por formulário).
"""
from fastapi import APIRouter, Depends, File, Form, HTTPException, status, UploadFile
from sqlalchemy.orm import Session
from typing import List, Optional

from app.database import get_db
from app.models import Midia, Formulario
from app.models.midia import TipoMidia
from app.schemas.midia import MidiaCreate, MidiaUpdate, MidiaResponse
from app.core.permissions import require_admin
from app.api.v1.auth import get_current_user_token
from app.services.files import save_upload_file
from app.core.config import settings

router = APIRouter()


def _tipo_value(t) -> str:
    return t.value if hasattr(t, "value") else str(t)


@router.get("/", response_model=List[MidiaResponse])
async def list_midias(
    formulario_id: Optional[int] = None,
    current_user=Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Lista mídias, opcionalmente filtradas por formulario_id."""
    query = db.query(Midia)
    if formulario_id is not None:
        query = query.filter(Midia.formulario_id == formulario_id)
    items = query.order_by(Midia.id).all()
    return [
        MidiaResponse(
            id=x.id,
            titulo=x.titulo,
            tipo=_tipo_value(x.tipo),
            formulario_id=x.formulario_id,
            url=x.url,
            arquivo=x.arquivo,
            created_at=x.created_at,
            updated_at=x.updated_at,
        )
        for x in items
    ]


@router.get("/{midia_id}", response_model=MidiaResponse)
async def get_midia(
    midia_id: int,
    current_user=Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Obtiene una mídia por ID."""
    item = db.query(Midia).filter(Midia.id == midia_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Mídia no encontrada")
    return MidiaResponse(
        id=item.id,
        titulo=item.titulo,
        tipo=_tipo_value(item.tipo),
        formulario_id=item.formulario_id,
        url=item.url,
        arquivo=item.arquivo,
        created_at=item.created_at,
        updated_at=item.updated_at,
    )


@router.post("/", response_model=MidiaResponse)
async def create_midia(
    titulo: str = Form(...),
    tipo: str = Form(...),
    formulario_id: int = Form(...),
    url: Optional[str] = Form(None),
    arquivo: Optional[UploadFile] = File(None),
    current_user=Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Crea una mídia (solo admin). tipo=url requiere url; tipo=video requiere arquivo."""
    if tipo not in ("video", "url"):
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="tipo debe ser 'video' o 'url'")
    form = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not form:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Formulário no encontrado")
    arquivo_path = None
    if tipo == "url":
        if not url:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="url obrigatória para tipo url")
    else:
        if not arquivo:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Arquivo de vídeo obrigatório para tipo video")
        arquivo_path = await save_upload_file(arquivo, "videos")
    item = Midia(
        titulo=titulo,
        tipo=TipoMidia.VIDEO if tipo == "video" else TipoMidia.URL,
        formulario_id=formulario_id,
        url=url if tipo == "url" else None,
        arquivo=arquivo_path if tipo == "video" else None,
    )
    db.add(item)
    db.commit()
    db.refresh(item)
    return MidiaResponse(
        id=item.id,
        titulo=item.titulo,
        tipo=_tipo_value(item.tipo),
        formulario_id=item.formulario_id,
        url=item.url,
        arquivo=item.arquivo,
        created_at=item.created_at,
        updated_at=item.updated_at,
    )


@router.put("/{midia_id}", response_model=MidiaResponse)
async def update_midia(
  midia_id: int,
  titulo: Optional[str] = None,
  tipo: Optional[str] = None,
  formulario_id: Optional[int] = None,
  url: Optional[str] = None,
  arquivo: Optional[UploadFile] = File(None),
  current_user=Depends(require_admin),
  db: Session = Depends(get_db),
):
    """Actualiza una mídia (solo admin)."""
    item = db.query(Midia).filter(Midia.id == midia_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Mídia no encontrada")
    if titulo is not None:
        item.titulo = titulo
    if tipo is not None:
        if tipo not in ("video", "url"):
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="tipo debe ser 'video' o 'url'")
        item.tipo = TipoMidia.VIDEO if tipo == "video" else TipoMidia.URL
    if formulario_id is not None:
        item.formulario_id = formulario_id
    if url is not None:
        item.url = url
    if arquivo:
        path = await save_upload_file(arquivo, "videos")
        item.arquivo = path
    db.commit()
    db.refresh(item)
    return MidiaResponse(
        id=item.id,
        titulo=item.titulo,
        tipo=_tipo_value(item.tipo),
        formulario_id=item.formulario_id,
        url=item.url,
        arquivo=item.arquivo,
        created_at=item.created_at,
        updated_at=item.updated_at,
    )


@router.delete("/{midia_id}")
async def delete_midia(
    midia_id: int,
    current_user=Depends(require_admin),
    db: Session = Depends(get_db),
):
    """Elimina una mídia (solo admin)."""
    item = db.query(Midia).filter(Midia.id == midia_id).first()
    if not item:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Mídia no encontrada")
    db.delete(item)
    db.commit()
    return {"message": "Mídia removida"}
