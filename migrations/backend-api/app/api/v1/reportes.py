"""
Endpoints de reportes
"""
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session, joinedload
from datetime import date as date_type
from typing import Optional, List
from collections import defaultdict
from app.database import get_db
from app.models import User, Formulario, Resposta, Variavel, Analise, UsuarioFormulario, Cliente, RelatorioGerado, Periodo, Grupo, UsuarioGrupo
from app.api.v1.auth import get_current_user_token
from app.services.calculos import (
    calcular_puntuacion_dimension,
    calcular_indices_desde_respostas,
    calcular_ejes_analiticos,
    calcular_iid,
    determinar_nivel_risco,
    get_plan_desenvolvimento,
)
from app.services.openai import generar_analise_openai
from app.services.relatorio_corporativo_iid import (
    compute_iid_por_usuario,
    agregar_por_unidade_e_area,
    evolucao_iid_por_periodo,
)

router = APIRouter()

@router.get("/")
async def get_relatorio(
    formulario_id: int = Query(...),
    usuario_id: int = Query(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Obtiene el reporte completo para un usuario y formulario.
    Incluye puntuaciones por dimensión, índices EE/PR/SO, ejes analíticos, IID y análisis (si existe).
    """
    # Permisos: el usuario ve su propio reporte; admin/gestor/sa pueden ver cualquier reporte
    if current_user.id != usuario_id:
        if not (current_user.admin or current_user.sa):
            if not current_user.gestor:
                raise HTTPException(status_code=403, detail="No tienes permiso para ver este reporte")
            # Gestor solo ve usuarios de su cliente
            other = db.query(User).filter(User.id == usuario_id).first()
            if not other or other.cliente_id != current_user.cliente_id:
                raise HTTPException(status_code=403, detail="No tienes permiso para ver este reporte")

    user = db.query(User).filter(User.id == usuario_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    formulario = db.query(Formulario).options(
        joinedload(Formulario.perguntas),
        joinedload(Formulario.etapas)
    ).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")

    if not formulario.perguntas:
        raise HTTPException(status_code=400, detail="El formulario no tiene preguntas")

    pergunta_ids = [p.id for p in formulario.perguntas]
    respostas_list = db.query(Resposta).filter(
        Resposta.user_id == usuario_id,
        Resposta.pergunta_id.in_(pergunta_ids)
    ).all()
    respostas_usuario = {r.pergunta_id: r for r in respostas_list}

    # Participante solo puede ver su relatório com 100% das perguntas respondidas
    if current_user.id == usuario_id:
        respostas_com_valor = [r for r in respostas_list if r.valor_resposta is not None]
        if len(respostas_com_valor) < len(pergunta_ids):
            raise HTTPException(
                status_code=403,
                detail="Complete todas as perguntas do questionário para ver o relatório."
            )

    # Variables con preguntas cargadas
    variaveis = db.query(Variavel).options(
        joinedload(Variavel.perguntas)
    ).filter(Variavel.formulario_id == formulario_id).all()

    if not variaveis:
        raise HTTPException(status_code=400, detail="El formulario no tiene variables configuradas")

    # Calcular puntuaciones por dimensión
    pontuacoes = []
    for variavel in variaveis:
        p = calcular_puntuacion_dimension(db, variavel, respostas_usuario, formulario_id)
        if p:
            pontuacoes.append(p)

    # Calcular índices EE, PR, SO
    indices = calcular_indices_desde_respostas(db, respostas_usuario, formulario_id)

    # Calcular ejes analíticos
    ejes_analiticos = calcular_ejes_analiticos(pontuacoes, indices)

    # Calcular IID
    iid = calcular_iid(ejes_analiticos)

    # Nivel de riesgo
    nivel_risco = determinar_nivel_risco(iid)

    # Plan de desarrollo
    plan_desenvolvimento = get_plan_desenvolvimento(nivel_risco)

    # Promedio de índices (para mostrar en UI)
    promedio_indices = (indices.get("EE", 0) + indices.get("PR", 0) + indices.get("SO", 0)) / 3

    # Análisis (texto generado por OpenAI si existe)
    analise = db.query(Analise).filter(
        Analise.user_id == usuario_id,
        Analise.formulario_id == formulario_id
    ).first()
    
    analise_texto = None
    analise_data = None
    
    if analise:
        analise_texto = analise.texto
        analise_data = analise.created_at
    else:
        # Generar análisis automáticamente con OpenAI si no existe
        try:
            texto = await generar_analise_openai(user, formulario, variaveis, pontuacoes, respostas_usuario)
            if texto:
                analise = Analise(user_id=usuario_id, formulario_id=formulario_id, texto=texto)
                db.add(analise)
                db.commit()
                db.refresh(analise)
                analise_texto = analise.texto
                analise_data = analise.created_at
        except Exception:
            pass  # Si falla OpenAI, el reporte se devuelve sin análisis

    # Respuestas para la vista (pergunta_id -> valor)
    respostas_para_vista = {
        str(r.pergunta_id): r.valor_resposta for r in respostas_list
    }

    return {
        "formulario": {
            "id": formulario.id,
            "nome": formulario.nome,
            "label": formulario.label,
            "descricao": formulario.descricao,
            "instrucoes": formulario.instrucoes,
            "score_ini": formulario.score_ini,
            "score_fim": formulario.score_fim,
        },
        "user": {
            "id": user.id,
            "name": user.name,
            "email": user.email,
        },
        "respostas_usuario": respostas_para_vista,
        "pontuacoes": pontuacoes,
        "indices": indices,
        "ejes_analiticos": ejes_analiticos,
        "iid": iid,
        "nivel_risco": nivel_risco,
        "plan_desenvolvimento": plan_desenvolvimento,
        "promedio_indices": round(promedio_indices, 2),
        "analise_texto": analise_texto,
        "analise_data": analise_data.isoformat() if analise_data else None,
    }

@router.post("/regenerar-analise")
async def regenerar_analise(
    formulario_id: int = Query(...),
    usuario_id: int = Query(...),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Elimina el análisis existente para forzar regeneración (solo admin).
    La próxima vez que se consulte el reporte se generará uno nuevo vía OpenAI.
    """
    if not current_user.admin and not current_user.sa:
        raise HTTPException(status_code=403, detail="Solo administradores pueden regenerar el análisis")

    deleted = db.query(Analise).filter(
        Analise.user_id == usuario_id,
        Analise.formulario_id == formulario_id
    ).delete()
    db.commit()

    return {"message": "Análise eliminada. Será regenerada al consultar o reporte.", "deleted": deleted}


def _cliente_id_for_user(current_user: User, db: Session, cliente_id: Optional[int]) -> int:
    """Determina cliente_id: gestor solo su cliente; admin/sa pueden pasar cliente_id."""
    if current_user.gestor and not current_user.admin and not current_user.sa:
        if current_user.cliente_id is None:
            raise HTTPException(status_code=400, detail="Gestor sem cliente associado")
        return current_user.cliente_id
    if cliente_id is None:
        raise HTTPException(status_code=400, detail="cliente_id é obrigatório para relatório corporativo")
    # Admin/SA: verificar que el cliente existe
    c = db.query(Cliente).filter(Cliente.id == cliente_id).first()
    if not c:
        raise HTTPException(status_code=404, detail="Cliente não encontrado")
    return cliente_id


@router.get("/agregado-grupo")
async def get_agregado_grupo(
    cliente_id: Optional[int] = Query(None, description="Cliente (obrigatório para admin/sa)"),
    periodo_id: Optional[int] = Query(None, description="Filtrar por período/onda"),
    formulario_id: Optional[int] = Query(None, description="Filtrar por formulário"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Agregado por grupo (unidade, área, nível, etc.): participação e % completo.
    Gestor vê só o seu cliente; admin/sa passam cliente_id.
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")

    cid = _cliente_id_for_user(current_user, db, cliente_id)
    # Usuarios do cliente (ativo, não deletado)
    users_query = db.query(User).filter(
        User.cliente_id == cid,
        User.ativo == True,
        User.deleted_at == None,
    )
    user_ids = [u.id for u in users_query.all()]
    if not user_ids:
        return {
            "por_unidade": [],
            "por_area": [],
            "por_nivel_jerarquico": [],
            "por_tempo_empresa": [],
            "por_modelo_trabalho": [],
            "resumo": {"total_asignaciones": 0, "total_completos": 0},
        }

    # Asignaciones de esos usuarios
    q = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id.in_(user_ids),
        UsuarioFormulario.deleted_at == None,
    )
    if periodo_id is not None:
        q = q.filter(UsuarioFormulario.periodo_id == periodo_id)
    if formulario_id is not None:
        q = q.filter(UsuarioFormulario.formulario_id == formulario_id)
    uf_list = q.all()
    users_by_id = {u.id: u for u in db.query(User).filter(User.id.in_(user_ids)).all()}

    def group_by_field(get_val: callable) -> List[dict]:
        """Agrupa UFs por valor del campo del usuario; valor None -> '(não informado)'."""
        totals: dict[str, list] = defaultdict(lambda: [0, 0])  # total, completos
        for uf in uf_list:
            u = users_by_id.get(uf.usuario_id)
            val = (get_val(u) or "").strip() or "(não informado)"
            totals[val][0] += 1
            if (uf.status or "").lower() == "completo":
                totals[val][1] += 1
        return [
            {"valor": v, "total": t[0], "completos": t[1], "percentual": round(100 * t[1] / t[0], 1) if t[0] else 0}
            for v, t in sorted(totals.items(), key=lambda x: -x[1][0])
        ]

    por_unidade = group_by_field(lambda u: u.unidade if u else None)
    por_area = group_by_field(lambda u: u.area if u else None)
    por_nivel = group_by_field(lambda u: u.nivel_jerarquico if u else None)
    por_tempo = group_by_field(lambda u: u.tempo_empresa if u else None)
    por_modelo = group_by_field(lambda u: u.modelo_trabalho if u else None)

    total_asignaciones = len(uf_list)
    total_completos = sum(1 for uf in uf_list if (uf.status or "").lower() == "completo")

    return {
        "por_unidade": por_unidade,
        "por_area": por_area,
        "por_nivel_jerarquico": por_nivel,
        "por_tempo_empresa": por_tempo,
        "por_modelo_trabalho": por_modelo,
        "resumo": {
            "total_asignaciones": total_asignaciones,
            "total_completos": total_completos,
            "percentual_geral": round(100 * total_completos / total_asignaciones, 1) if total_asignaciones else 0,
        },
    }


@router.get("/kpis")
async def get_kpis(
    cliente_id: Optional[int] = Query(None),
    periodo_id: Optional[int] = Query(None),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    KPIs para relatório corporativo: colaboradores em risco (atribuição incompleta e data_limite vencida)
    e setores críticos (grupos com menor % completo e/ou mais em risco).
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    today = date_type.today()
    users_query = db.query(User).filter(User.cliente_id == cid, User.ativo == True, User.deleted_at == None)
    user_ids = [u.id for u in users_query.all()]
    users_by_id = {u.id: u for u in db.query(User).filter(User.id.in_(user_ids)).all()} if user_ids else {}

    q = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id.in_(user_ids),
        UsuarioFormulario.deleted_at == None,
    )
    if periodo_id is not None:
        q = q.filter(UsuarioFormulario.periodo_id == periodo_id)
    uf_list = q.all()

    # Em risco: usuário com pelo menos uma UF não completa e data_limite já passada
    em_risco_ids = set()
    for uf in uf_list:
        if (uf.status or "").lower() != "completo" and uf.data_limite and uf.data_limite < today:
            em_risco_ids.add(uf.usuario_id)

    def setores_criticos(get_val) -> List[dict]:
        totals = defaultdict(lambda: [0, 0, 0])  # total, completos, em_risco
        for uf in uf_list:
            u = users_by_id.get(uf.usuario_id)
            val = (get_val(u) if u else None) or "(não informado)"
            val = (val or "").strip() or "(não informado)"
            totals[val][0] += 1
            if (uf.status or "").lower() == "completo":
                totals[val][1] += 1
            if (uf.status or "").lower() != "completo" and uf.data_limite and uf.data_limite < today:
                totals[val][2] += 1
        return [
            {
                "valor": v,
                "total": t[0],
                "completos": t[1],
                "percentual": round(100 * t[1] / t[0], 1) if t[0] else 0,
                "em_risco": t[2],
            }
            for v, t in sorted(totals.items(), key=lambda x: (x[1][2], -x[1][1]))  # mais em risco e menor % primeiro
        ][:10]

    return {
        "colaboradores_em_risco": len(em_risco_ids),
        "setores_criticos_unidade": setores_criticos(lambda u: u.unidade if u else None),
        "setores_criticos_area": setores_criticos(lambda u: u.area if u else None),
    }


@router.get("/evolucao")
async def get_evolucao(
    periodo_id_1: int = Query(..., description="Primeiro período"),
    periodo_id_2: int = Query(..., description="Segundo período"),
    cliente_id: Optional[int] = Query(None),
    grupo: Optional[str] = Query("unidade", description="Agrupar por: unidade | area | nivel_jerarquico"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Compara participação e % completo do mesmo grupo em dois períodos (evolução temporal).
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    user_ids = [u.id for u in db.query(User).filter(User.cliente_id == cid, User.ativo == True, User.deleted_at == None).all()]
    if not user_ids:
        return {"periodo_1": {}, "periodo_2": {}, "comparacao": []}

    get_field = lambda u, g: (u.unidade if g == "unidade" else u.area if g == "area" else u.nivel_jerarquico if g == "nivel_jerarquico" else u.unidade)
    users_by_id = {u.id: u for u in db.query(User).filter(User.id.in_(user_ids)).all()}

    def stats_for_period(pid: Optional[int]) -> dict:
        q = db.query(UsuarioFormulario).filter(
            UsuarioFormulario.usuario_id.in_(user_ids),
            UsuarioFormulario.deleted_at == None,
        )
        if pid is not None:
            q = q.filter(UsuarioFormulario.periodo_id == pid)
        ufs = q.all()
        by_val = defaultdict(lambda: [0, 0])
        for uf in ufs:
            u = users_by_id.get(uf.usuario_id)
            val = (get_field(u, grupo or "unidade") if u else None) or "(não informado)"
            val = (val or "").strip() or "(não informado)"
            by_val[val][0] += 1
            if (uf.status or "").lower() == "completo":
                by_val[val][1] += 1
        return {v: {"total": t[0], "completos": t[1], "percentual": round(100 * t[1] / t[0], 1) if t[0] else 0} for v, t in by_val.items()}

    s1 = stats_for_period(periodo_id_1)
    s2 = stats_for_period(periodo_id_2)
    all_vals = sorted(set(s1.keys()) | set(s2.keys()))
    comparacao = []
    for v in all_vals:
        p1 = s1.get(v, {"total": 0, "completos": 0, "percentual": 0})
        p2 = s2.get(v, {"total": 0, "completos": 0, "percentual": 0})
        variacao = round((p2["percentual"] - p1["percentual"]), 1) if p1 else p2["percentual"]
        comparacao.append({"valor": v, "periodo_1": p1, "periodo_2": p2, "variacao_percentual": variacao})
    return {"periodo_1_id": periodo_id_1, "periodo_2_id": periodo_id_2, "periodo_1": s1, "periodo_2": s2, "comparacao": comparacao}


def get_kpis_iid_data(
    db: Session,
    cid: int,
    periodo_id: Optional[int],
    formulario_id: int,
    unidade: Optional[str] = None,
    area: Optional[str] = None,
    nivel_jerarquico: Optional[str] = None,
    tempo_empresa: Optional[str] = None,
    modelo_trabalho: Optional[str] = None,
) -> dict:
    """
    Retorna dados de KPIs IID para relatório corporativo (usado por endpoint e por PDF).
    Filtros opcionais (unidade, area, etc.) restringem o conjunto de usuários.
    """
    q_users = db.query(User).filter(User.cliente_id == cid, User.ativo == True, User.deleted_at == None)
    if unidade:
        q_users = q_users.filter(User.unidade == unidade)
    if area:
        q_users = q_users.filter(User.area == area)
    if nivel_jerarquico:
        q_users = q_users.filter(User.nivel_jerarquico == nivel_jerarquico)
    if tempo_empresa:
        q_users = q_users.filter(User.tempo_empresa == tempo_empresa)
    if modelo_trabalho:
        q_users = q_users.filter(User.modelo_trabalho == modelo_trabalho)
    user_ids = [u.id for u in q_users.all()]
    if not user_ids:
        return {
            "colaboradores_em_risco": 0,
            "total_alta": 0,
            "total_critico": 0,
            "total_respondentes": 0,
            "percentual_risco": 0,
            "setores_criticos_unidade": [],
            "setores_criticos_area": [],
            "onde_se_concentram": [],
        }
    q = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id.in_(user_ids),
        UsuarioFormulario.formulario_id == formulario_id,
        UsuarioFormulario.deleted_at == None,
        UsuarioFormulario.status.ilike("completo"),
    )
    if periodo_id is not None:
        q = q.filter(UsuarioFormulario.periodo_id == periodo_id)
    ufs = q.all()
    completed_ids = list({uf.usuario_id for uf in ufs})
    if not completed_ids:
        return {
            "colaboradores_em_risco": 0,
            "total_alta": 0,
            "total_critico": 0,
            "total_respondentes": 0,
            "percentual_risco": 0,
            "setores_criticos_unidade": [],
            "setores_criticos_area": [],
            "onde_se_concentram": [],
        }
    registros = compute_iid_por_usuario(db, completed_ids, formulario_id)
    if not registros:
        return {
            "colaboradores_em_risco": 0,
            "total_alta": 0,
            "total_critico": 0,
            "total_respondentes": 0,
            "percentual_risco": 0,
            "setores_criticos_unidade": [],
            "setores_criticos_area": [],
            "onde_se_concentram": [],
        }
    return agregar_por_unidade_e_area(registros)


@router.get("/kpis-iid")
async def get_kpis_iid(
    cliente_id: Optional[int] = Query(None),
    periodo_id: Optional[int] = Query(None, description="Período (obrigatório para ter dados por período)"),
    formulario_id: Optional[int] = Query(..., description="Formulário para calcular IID"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    KPIs do relatório corporativo baseados em IID (quem completou o formulário).
    Faixas do design: Baixa (0-25), Moderada (25-50), Alta (50-75), Crítico (75-100).
    Colaboradores em risco = Alta + Crítico. Inclui hierarquia unidade -> áreas (onde se concentram).
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    return get_kpis_iid_data(db, cid, periodo_id, formulario_id)


@router.get("/evolucao-iid")
async def get_evolucao_iid(
    cliente_id: Optional[int] = Query(None),
    formulario_id: int = Query(...),
    periodo_ids: Optional[str] = Query(None, description="IDs dos períodos separados por vírgula, ex: 1,2,3,4"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Evolução do IID médio por período (para o gráfico "Estamos Melhorando ou Piorando?").
    Retorna lista com periodo_nome, iid_medio e total_respondentes por período.
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    user_ids = [u.id for u in db.query(User).filter(User.cliente_id == cid, User.ativo == True, User.deleted_at == None).all()]
    if not user_ids:
        return {"evolucao": []}
    pids = [int(x) for x in (periodo_ids or "").split(",") if x.strip().isdigit()]
    if not pids:
        return {"evolucao": []}
    evolucao = evolucao_iid_por_periodo(db, user_ids, formulario_id, pids)
    return {"evolucao": evolucao}


# --- Monitor de respondentes (lista por período; opcionalmente filtrado por grupo) ---
@router.get("/monitor-respondentes")
async def get_monitor_respondentes(
    cliente_id: Optional[int] = Query(None),
    periodo_id: int = Query(..., description="Período/onda"),
    formulario_id: int = Query(..., description="Formulário"),
    grupo_id: Optional[int] = Query(None, description="Filtrar por grupo (só usuários que cumprem os filtros do grupo)"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Lista usuários com atribuição no período+formulário para o cliente.
    Se grupo_id for informado, restringe aos usuários que cumprem os filtros do grupo (unidade, área, etc.).
    Cada item tem status (pendente / en curso / completo), data_limite e dados do usuário.
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    if grupo_id is not None:
        grupo = db.query(Grupo).filter(Grupo.id == grupo_id, Grupo.cliente_id == cid).first()
        if not grupo:
            return {"respondentes": []}
        # Grupos têm lista explícita de usuários (usuario_grupo)
        user_ids = [ug.usuario_id for ug in db.query(UsuarioGrupo).filter(UsuarioGrupo.grupo_id == grupo_id).all()]
        if not user_ids:
            return {"respondentes": []}
        user_ids = [u.id for u in db.query(User).filter(User.id.in_(user_ids), User.cliente_id == cid, User.ativo == True, User.deleted_at == None).all()]
    else:
        user_ids = [u.id for u in db.query(User).filter(User.cliente_id == cid, User.ativo == True, User.deleted_at == None).all()]
    if not user_ids:
        return {"respondentes": []}
    ufs = db.query(UsuarioFormulario).filter(
        UsuarioFormulario.usuario_id.in_(user_ids),
        UsuarioFormulario.formulario_id == formulario_id,
        UsuarioFormulario.periodo_id == periodo_id,
        UsuarioFormulario.deleted_at == None,
    ).all()
    users_by_id = {u.id: u for u in db.query(User).filter(User.id.in_([uf.usuario_id for uf in ufs])).all()}
    out = []
    for uf in ufs:
        u = users_by_id.get(uf.usuario_id)
        status = (uf.status or "").lower()
        if status == "completo":
            estado = "completo"
        elif status in ("novo", "pendente"):
            estado = "pendente"
        else:
            estado = status or "pendente"
        out.append({
            "usuario_formulario_id": uf.id,
            "usuario_id": uf.usuario_id,
            "name": u.name if u else "",
            "email": u.email if u else "",
            "status": estado,
            "data_limite": uf.data_limite.isoformat() if uf.data_limite else None,
        })
    out.sort(key=lambda x: (0 if x["status"] == "pendente" else 1, x["name"].lower()))
    return {"respondentes": out}


# --- Relatórios gerados (lista e registo para aba Relatórios) ---
from pydantic import BaseModel as PydanticBaseModel


class RelatorioGeradoCreate(PydanticBaseModel):
    cliente_id: int
    periodo_id: int
    formulario_id: int
    tipo: Optional[str] = None


@router.get("/gerados")
async def list_relatorios_gerados(
    cliente_id: Optional[int] = Query(None),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Lista relatórios corporativos gerados (para a aba Relatórios). Gestor vê só do seu cliente."""
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    q = db.query(RelatorioGerado).filter(RelatorioGerado.cliente_id == cid)
    q = q.order_by(RelatorioGerado.created_at.desc()).limit(50)
    rows = q.all()
    out = []
    for r in rows:
        periodo = db.query(Periodo).filter(Periodo.id == r.periodo_id).first()
        form = db.query(Formulario).filter(Formulario.id == r.formulario_id).first()
        out.append({
            "id": r.id,
            "cliente_id": r.cliente_id,
            "periodo_id": r.periodo_id,
            "periodo_nome": periodo.nome if periodo else str(r.periodo_id),
            "formulario_id": r.formulario_id,
            "formulario_nome": (form.nome or form.label) if form else str(r.formulario_id),
            "tipo": r.tipo,
            "created_at": r.created_at.isoformat() if r.created_at else None,
        })
    return {"relatorios": out}


@router.post("/gerados")
async def create_relatorio_gerado(
    data: RelatorioGeradoCreate,
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """Regista um relatório corporativo gerado (chamado pelo frontend após BAIXAR)."""
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Requerido rol Gestor, Admin ou SA")
    cid = _cliente_id_for_user(current_user, db, data.cliente_id)
    r = RelatorioGerado(
        cliente_id=cid,
        periodo_id=data.periodo_id,
        formulario_id=data.formulario_id,
        tipo=data.tipo,
        user_id=current_user.id,
    )
    db.add(r)
    db.commit()
    db.refresh(r)
    return {"id": r.id, "message": "Registado"}
