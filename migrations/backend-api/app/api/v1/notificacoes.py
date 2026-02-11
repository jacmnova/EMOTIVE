"""
Endpoints de notificaciones del usuario.
Por ahora solo 'marcar todas como leídas'; cuando exista modelo Notification se persistirá en BD.
"""
from fastapi import APIRouter, Depends

from app.api.v1.auth import get_current_user_token
from app.models import User

router = APIRouter()


@router.post("/marcar-todas")
async def marcar_todas_lidas(
    current_user: User = Depends(get_current_user_token),
):
    """
    Marca todas las notificaciones del usuario como leídas.
    En una versión futura con notificaciones en BD, aquí se actualizaría la tabla.
    """
    # TODO: cuando exista modelo Notification: actualizar read_at para current_user.id
    return {"message": "ok"}
