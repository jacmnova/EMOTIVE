"""
Endpoints de respuestas
"""
from datetime import date
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from typing import List
from app.database import get_db
from app.models import User, Formulario, Pergunta, Resposta, UsuarioFormulario, Periodo
from app.schemas.resposta import RespostasSalvar, RespostaResponse
from app.api.v1.auth import get_current_user_token

router = APIRouter()

@router.get("/", response_model=List[RespostaResponse])
async def list_respostas(
    usuario_id: int = Query(...),
    formulario_id: int = Query(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Obtiene las respuestas de un usuario para un formulario.
    El usuario solo puede ver sus propias respuestas, salvo admin/gestor.
    """
    if current_user.id != usuario_id:
        if not (current_user.admin or current_user.sa or current_user.gestor):
            raise HTTPException(status_code=403, detail="No tienes permiso para ver estas respuestas")
        if current_user.gestor and current_user.cliente_id:
            # Gestor solo ve usuarios de su cliente
            other = db.query(User).filter(User.id == usuario_id).first()
            if not other or other.cliente_id != current_user.cliente_id:
                raise HTTPException(status_code=403, detail="No tienes permiso")
    
    formulario = db.query(Formulario).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    
    pergunta_ids = [p.id for p in formulario.perguntas]
    respostas = db.query(Resposta).filter(
        Resposta.user_id == usuario_id,
        Resposta.pergunta_id.in_(pergunta_ids)
    ).all()
    
    return [RespostaResponse.model_validate(r) for r in respostas]

@router.post("/salvar")
async def salvar_respostas(
    data: RespostasSalvar,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Guarda o actualiza respuestas de un usuario para un formulario.
    Acepta un diccionario {pergunta_id: valor_resposta}.
    Actualiza el status de usuario_formulario si todas las preguntas están respondidas.
    """
    formulario = db.query(Formulario).filter(Formulario.id == data.formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")
    
    # Verificar acceso al formulario
    uf = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id == current_user.id,
        UsuarioFormulario.formulario_id == data.formulario_id
    ).first()
    
    if not uf and not (current_user.admin or current_user.gestor or current_user.sa):
        raise HTTPException(status_code=403, detail="No tienes permiso para responder este formulario")

    # Cierre de onda: si el periodo tiene data_fim en el pasado, no permitir más respuestas
    if uf and uf.periodo_id:
        periodo = db.query(Periodo).filter(Periodo.id == uf.periodo_id).first()
        if periodo and periodo.data_fim and date.today() > periodo.data_fim:
            raise HTTPException(
                status_code=400,
                detail="Este período (onda) já foi encerrado; não é possível enviar mais respostas.",
            )

    pergunta_ids = {p.id for p in formulario.perguntas}
    
    for pergunta_id_str, valor in data.respostas.items():
        try:
            pergunta_id = int(pergunta_id_str)
        except (ValueError, TypeError):
            continue
        if pergunta_id not in pergunta_ids:
            continue
        if valor is None or valor < 0 or valor > 6:
            continue
        
        resposta = db.query(Resposta).filter(
            Resposta.user_id == current_user.id,
            Resposta.pergunta_id == pergunta_id
        ).first()
        
        if resposta:
            resposta.valor_resposta = valor
        else:
            resposta = Resposta(
                user_id=current_user.id,
                pergunta_id=pergunta_id,
                valor_resposta=valor
            )
            db.add(resposta)
    
    db.commit()
    
    # Contar respuestas y actualizar status de usuario_formulario
    total_perguntas = len(formulario.perguntas)
    respostas_count = db.query(Resposta).filter(
        Resposta.user_id == current_user.id,
        Resposta.pergunta_id.in_(pergunta_ids),
        Resposta.valor_resposta.isnot(None)
    ).count()
    
    if uf:
        uf.status = "completo" if (total_perguntas > 0 and respostas_count >= total_perguntas) else "pendente"
        db.commit()
    
    percentual = round((respostas_count / total_perguntas) * 100) if total_perguntas > 0 else 0
    
    return {
        "status": "completo" if respostas_count >= total_perguntas else "pendente",
        "percentual": percentual,
        "respostas_guardadas": len(data.respostas),
        "total_perguntas": total_perguntas
    }
