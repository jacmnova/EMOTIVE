"""
Servicio de manejo de archivos (upload)
"""
import os
import shutil
from pathlib import Path
from typing import Optional
from fastapi import UploadFile
from app.core.config import settings

def ensure_upload_dir(subdir: str = "") -> Path:
    """Asegura que el directorio de upload existe"""
    upload_path = Path(settings.UPLOAD_DIR) / subdir
    upload_path.mkdir(parents=True, exist_ok=True)
    return upload_path

async def save_upload_file(
    file: UploadFile,
    subdir: str = "",
    filename: Optional[str] = None
) -> str:
    """
    Guarda un archivo subido
    
    Args:
        file: Archivo subido
        subdir: Subdirectorio (ej: 'avatars', 'logos', 'midias')
        filename: Nombre opcional del archivo (si no se proporciona, usa el original)
    
    Returns:
        Ruta relativa del archivo guardado (para almacenar en BD)
    """
    # Validar tamaño
    contents = await file.read()
    if len(contents) > settings.MAX_UPLOAD_SIZE:
        raise ValueError(f"El archivo excede el tamaño máximo de {settings.MAX_UPLOAD_SIZE} bytes")
    
    # Determinar nombre
    if not filename:
        filename = file.filename
        if not filename:
            raise ValueError("No se puede determinar el nombre del archivo")
    
    # Asegurar directorio
    upload_dir = ensure_upload_dir(subdir)
    
    # Generar nombre único si el archivo ya existe
    file_path = upload_dir / filename
    counter = 1
    while file_path.exists():
        name_parts = filename.rsplit('.', 1)
        if len(name_parts) == 2:
            new_filename = f"{name_parts[0]}_{counter}.{name_parts[1]}"
        else:
            new_filename = f"{filename}_{counter}"
        file_path = upload_dir / new_filename
        counter += 1
    
    # Guardar archivo
    with open(file_path, "wb") as f:
        f.write(contents)
    
    # Retornar ruta relativa (subdir/filename)
    relative_path = f"{subdir}/{file_path.name}" if subdir else file_path.name
    return relative_path

def delete_file(relative_path: str) -> bool:
    """
    Elimina un archivo por su ruta relativa
    
    Returns:
        True si se eliminó, False si no existía
    """
    file_path = Path(settings.UPLOAD_DIR) / relative_path
    if file_path.exists() and file_path.is_file():
        file_path.unlink()
        return True
    return False

def get_file_url(relative_path: str) -> str:
    """
    Genera URL pública para un archivo
    """
    # En producción, esto debería apuntar a un CDN o storage público
    # Por ahora, retornamos ruta relativa que el frontend puede usar
    return f"/uploads/{relative_path}"
