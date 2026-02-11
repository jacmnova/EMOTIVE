"""
Endpoints de cuestionarios del usuario (meus-questionarios)
"""
from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session, joinedload
from app.database import get_db
from app.models import User, UsuarioFormulario, Formulario, Resposta
from app.api.v1.auth import get_current_user_token
from app.core.config import settings

router = APIRouter()


def _midia_payload(form):
    """Primera mídia del formulario con URL de arquivo si aplica."""
    if not form.midias:
        return None
    m = form.midias[0]
    tipo = getattr(m.tipo, "value", str(m.tipo))
    url = m.url
    if m.arquivo:
        url = f"/uploads/{m.arquivo}" if not m.arquivo.startswith("/") else m.arquivo
    return {
        "id": m.id,
        "titulo": m.titulo,
        "tipo": tipo,
        "url": url,
    }

@router.get("/meus-questionarios")
async def meus_questionarios(
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Lista los cuestionarios (formularios) asignados al usuario actual.
    Incluye formulario, etapas, y estado de avance (etapa actual, % completado).
    """
    ufs = db.query(UsuarioFormulario).options(
        joinedload(UsuarioFormulario.formulario).joinedload(Formulario.perguntas),
        joinedload(UsuarioFormulario.formulario).joinedload(Formulario.etapas),
        joinedload(UsuarioFormulario.formulario).joinedload(Formulario.midias),
    ).filter(
        UsuarioFormulario.usuario_id == current_user.id,
        UsuarioFormulario.deleted_at == None
    ).all()

    result = []
    for uf in ufs:
        form = uf.formulario
        if not form:
            continue
        perguntas = form.perguntas
        etapas = sorted(form.etapas, key=lambda e: e.etapa) if form.etapas else []
        pergunta_ids = [p.id for p in perguntas]

        respostas = db.query(Resposta).filter(
            Resposta.user_id == current_user.id,
            Resposta.pergunta_id.in_(pergunta_ids),
            Resposta.valor_resposta.isnot(None)
        ).all()
        respostas_by_pergunta = {r.pergunta_id: r for r in respostas}

        # Determinar etapa actual (primera no completada)
        etapa_atual_numero = None
        etapa_atual_nome = "Sem Etapa"
        for etapa in etapas:
            perguntas_etapa = [p for p in perguntas if etapa.de <= p.id <= etapa.ate]
            todas_respondidas = all(
                p.id in respostas_by_pergunta for p in perguntas_etapa
            )
            if not todas_respondidas:
                etapa_atual_numero = etapa.etapa
                etapa_atual_nome = f"Etapa {etapa.etapa}"
                break
        if etapa_atual_numero is None and etapas:
            etapa_atual_numero = etapas[-1].etapa
            etapa_atual_nome = f"Etapa {etapas[-1].etapa}"

        total = len(perguntas)
        respondidas = len(respostas_by_pergunta)
        percentual = round((respondidas / total) * 100) if total > 0 else 0

        result.append({
            "usuario_formulario_id": uf.id,
            "formulario_id": form.id,
            "formulario_nome": form.nome,
            "formulario_label": form.label,
            "status": uf.status,
            "data_limite": uf.data_limite.isoformat() if uf.data_limite else None,
            "video_assistido": uf.video_assistido,
            "etapa_atual_numero": etapa_atual_numero,
            "etapa_atual_nome": etapa_atual_nome,
            "total_perguntas": total,
            "respostas_count": respondidas,
            "percentual": percentual,
            "midia": _midia_payload(form) if form and form.midias else None
        })

    return {"questionarios": result}
