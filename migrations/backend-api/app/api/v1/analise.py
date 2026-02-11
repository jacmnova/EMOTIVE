"""
Endpoints para generar análisis con OpenAI
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session, joinedload
from app.database import get_db
from app.models import User, Formulario, Variavel, Resposta, Analise
from app.api.v1.auth import get_current_user_token
from app.services.openai import generar_analise_openai
from app.services.calculos import calcular_puntuacion_dimension

router = APIRouter()

@router.post("/gerar")
async def gerar_analise(
    formulario_id: int = Query(...),
    usuario_id: int = Query(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Genera análisis usando OpenAI para un usuario y formulario.
    Solo admin puede generar análisis para otros usuarios.
    """
    # Permisos
    if current_user.id != usuario_id:
        if not (current_user.admin or current_user.sa):
            raise HTTPException(status_code=403, detail="No tienes permiso")

    # Verificar si ya existe
    analise_existente = db.query(Analise).filter(
        Analise.user_id == usuario_id,
        Analise.formulario_id == formulario_id
    ).first()
    
    if analise_existente:
        return {
            "message": "Análise já existe",
            "analise_id": analise_existente.id,
            "texto": analise_existente.texto[:200] + "..." if len(analise_existente.texto) > 200 else analise_existente.texto
        }

    # Obtener datos necesarios
    user = db.query(User).filter(User.id == usuario_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    formulario = db.query(Formulario).options(
        joinedload(Formulario.perguntas)
    ).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")

    variaveis = db.query(Variavel).options(
        joinedload(Variavel.perguntas)
    ).filter(Variavel.formulario_id == formulario_id).all()

    pergunta_ids = [p.id for p in formulario.perguntas]
    respostas_list = db.query(Resposta).filter(
        Resposta.user_id == usuario_id,
        Resposta.pergunta_id.in_(pergunta_ids)
    ).all()
    respostas_usuario = {r.pergunta_id: r for r in respostas_list}

    # Calcular puntuaciones
    pontuacoes = []
    for variavel in variaveis:
        p = calcular_puntuacion_dimension(db, variavel, respostas_usuario, formulario_id)
        if p:
            pontuacoes.append(p)

    # Generar análisis
    analise_texto = await generar_analise_openai(
        user, formulario, variaveis, pontuacoes, respostas_usuario
    )

    if not analise_texto or analise_texto.startswith("Erro"):
        raise HTTPException(
            status_code=500,
            detail=f"Error al generar análisis: {analise_texto or 'Error desconocido'}"
        )

    # Guardar en BD
    analise = Analise(
        user_id=usuario_id,
        formulario_id=formulario_id,
        texto=analise_texto
    )
    db.add(analise)
    db.commit()
    db.refresh(analise)

    return {
        "message": "Análise gerada com sucesso",
        "analise_id": analise.id,
        "texto_preview": analise_texto[:200] + "..." if len(analise_texto) > 200 else analise_texto
    }
