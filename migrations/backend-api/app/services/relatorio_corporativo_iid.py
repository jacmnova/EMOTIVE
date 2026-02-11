# -*- coding: utf-8 -*-
"""
Serviço para relatório corporativo baseado em IID (Índice de Descarrilamento).
Calcula IID por usuário (formulário completo), faixas do design e agregações por unidade/área.
"""
from typing import List, Dict, Any, Optional, Tuple
from collections import defaultdict
from sqlalchemy.orm import Session, joinedload
from app.models import User, Formulario, Resposta, Variavel, UsuarioFormulario, Periodo
from app.services.calculos import (
    calcular_puntuacion_dimension,
    calcular_indices_desde_respostas,
    calcular_ejes_analiticos,
    calcular_iid,
    faixa_iid_design,
)


def _compute_iid_for_user(
    db: Session,
    user_id: int,
    formulario_id: int,
    pergunta_ids: List[int],
    variaveis: List[Variavel],
) -> Optional[float]:
    """Calcula IID para um usuário com respostas completas do formulário."""
    respostas_list = db.query(Resposta).filter(
        Resposta.user_id == user_id,
        Resposta.pergunta_id.in_(pergunta_ids),
    ).all()
    respostas_usuario = {r.pergunta_id: r for r in respostas_list}
    if len(respostas_usuario) < len(pergunta_ids):
        return None
    if any(r.valor_resposta is None for r in respostas_list):
        return None
    pontuacoes = []
    for variavel in variaveis:
        p = calcular_puntuacion_dimension(db, variavel, respostas_usuario, formulario_id)
        if p:
            pontuacoes.append(p)
    if not pontuacoes:
        return None
    indices = calcular_indices_desde_respostas(db, respostas_usuario, formulario_id)
    ejes = calcular_ejes_analiticos(pontuacoes, indices)
    return calcular_iid(ejes)


def compute_iid_por_usuario(
    db: Session,
    user_ids: List[int],
    formulario_id: int,
) -> List[Tuple[int, float, str, Optional[User]]]:
    """
    Para cada user_id em user_ids que tem respostas completas, calcula IID e faixa design.
    Retorna lista de (user_id, iid, faixa, user_obj).
    """
    formulario = db.query(Formulario).options(
        joinedload(Formulario.perguntas),
    ).filter(Formulario.id == formulario_id).first()
    if not formulario or not formulario.perguntas:
        return []
    pergunta_ids = [p.id for p in formulario.perguntas]
    variaveis = db.query(Variavel).options(
        joinedload(Variavel.perguntas),
    ).filter(Variavel.formulario_id == formulario_id).all()
    if not variaveis:
        return []
    users_by_id = {u.id: u for u in db.query(User).filter(User.id.in_(user_ids)).all()}
    result = []
    for uid in user_ids:
        iid = _compute_iid_for_user(db, uid, formulario_id, pergunta_ids, variaveis)
        if iid is None:
            continue
        faixa = faixa_iid_design(iid)
        result.append((uid, iid, faixa, users_by_id.get(uid)))
    return result


def agregar_por_unidade_e_area(
    registros: List[Tuple[int, float, str, Optional[User]]],
) -> Dict[str, Any]:
    """
    Agrega (user_id, iid, faixa, user) por unidade e dentro de cada unidade por área.
    Retorna:
    - colaboradores_em_risco: count(alta + critico)
    - total_respondentes: len(registros)
    - percentual_risco: 100 * colaboradores_em_risco / total_respondentes
    - setores_criticos_unidade: [ { valor, total, baixa, moderada, alta, critico, iid_medio } ] onde iid_medio >= 50
    - setores_criticos_area: idem por área
    - onde_se_concentram: [ { unidade, total, iid_medio, areas: [ { area, total, baixa, moderada, alta, critico } ] } ]
    """
    # Por unidade: (total, baixa, moderada, alta, critico, soma_iid)
    by_unidade = defaultdict(lambda: [0, 0, 0, 0, 0, 0.0])
    # Por área: idem
    by_area = defaultdict(lambda: [0, 0, 0, 0, 0, 0.0])
    # Hierarquia unidade -> área: (unidade, area) -> (total, baixa, moderada, alta, critico, soma_iid)
    by_unidade_area = defaultdict(lambda: [0, 0, 0, 0, 0, 0.0])

    faixa_idx = {"baixa": 0, "moderada": 1, "alta": 2, "critico": 3}

    for _uid, iid, faixa, user in registros:
        unidade = ((user.unidade or "").strip() if user else "") or "(não informado)"
        area = ((user.area or "").strip() if user else "") or "(não informado)"
        idx = faixa_idx.get(faixa, 0)
        # unidade
        by_unidade[unidade][0] += 1
        by_unidade[unidade][idx + 1] += 1
        by_unidade[unidade][5] += iid
        # area
        by_area[area][0] += 1
        by_area[area][idx + 1] += 1
        by_area[area][5] += iid
        # unidade + area
        key = (unidade, area)
        by_unidade_area[key][0] += 1
        by_unidade_area[key][idx + 1] += 1
        by_unidade_area[key][5] += iid

    total_respondentes = len(registros)
    total_alta = sum(1 for _u, _i, f, _ in registros if f == "alta")
    total_critico = sum(1 for _u, _i, f, _ in registros if f == "critico")
    colaboradores_em_risco = total_alta + total_critico
    percentual_risco = round(100 * colaboradores_em_risco / total_respondentes, 1) if total_respondentes else 0

    def row_agg(dct, key):
        total, baixa, moderada, alta, critico, soma_iid = dct[key]
        iid_medio = round(soma_iid / total, 1) if total else 0
        return {
            "valor": key if isinstance(key, str) else key[0],
            "total": total,
            "baixa": baixa,
            "moderada": moderada,
            "alta": alta,
            "critico": critico,
            "iid_medio": iid_medio,
        }

    setores_criticos_unidade = [
        row_agg(by_unidade, k) for k, v in by_unidade.items()
        if v[0] and (v[5] / v[0]) >= 50
    ]
    setores_criticos_unidade.sort(key=lambda x: (-x["alta"] - x["critico"], -x["iid_medio"]))

    setores_criticos_area = [
        row_agg(by_area, k) for k, v in by_area.items()
        if v[0] and (v[5] / v[0]) >= 50
    ]
    setores_criticos_area.sort(key=lambda x: (-x["alta"] - x["critico"], -x["iid_medio"]))

    # Onde se concentram: hierarquia unidade -> áreas (cada (unidade, area) tem totais em by_unidade_area)
    unidades_ordem = sorted(by_unidade.keys(), key=lambda u: (-by_unidade[u][2] - by_unidade[u][3], -by_unidade[u][5]))
    onde_se_concentram = []
    for unidade in unidades_ordem:
        areas_in_unidade = [a for (u, a), v in by_unidade_area.items() if u == unidade]
        areas_list = []
        for area in sorted(areas_in_unidade):
            total_a, b_a, m_a, a_a, c_a, _ = by_unidade_area[(unidade, area)]
            areas_list.append({
                "area": area,
                "total": total_a,
                "baixa": b_a,
                "moderada": m_a,
                "alta": a_a,
                "critico": c_a,
            })
        total_u, b_u, m_u, a_u, c_u, soma_u = by_unidade[unidade]
        iid_medio_u = round(soma_u / total_u, 1) if total_u else 0
        onde_se_concentram.append({
            "unidade": unidade,
            "total": total_u,
            "baixa": b_u,
            "moderada": m_u,
            "alta": a_u,
            "critico": c_u,
            "iid_medio": iid_medio_u,
            "areas": areas_list,
        })

    return {
        "colaboradores_em_risco": colaboradores_em_risco,
        "total_alta": total_alta,
        "total_critico": total_critico,
        "total_respondentes": total_respondentes,
        "percentual_risco": percentual_risco,
        "setores_criticos_unidade": setores_criticos_unidade[:10],
        "setores_criticos_area": setores_criticos_area[:10],
        "onde_se_concentram": onde_se_concentram,
    }


def evolucao_iid_por_periodo(
    db: Session,
    user_ids: List[int],
    formulario_id: int,
    periodo_ids: List[int],
) -> List[Dict[str, Any]]:
    """
    Para cada periodo_id, obtém usuários com UF completo nesse período, calcula IID de cada um,
    retorna iid_medio e total_respondentes.
    """
    periodos = {p.id: p for p in db.query(Periodo).filter(Periodo.id.in_(periodo_ids)).all()}
    result = []
    for pid in periodo_ids:
        periodo = periodos.get(pid)
        q = db.query(UsuarioFormulario).filter(
            UsuarioFormulario.usuario_id.in_(user_ids),
            UsuarioFormulario.periodo_id == pid,
            UsuarioFormulario.deleted_at == None,
            UsuarioFormulario.status == "completo",
        )
        ufs = q.all()
        uids = list({uf.usuario_id for uf in ufs})
        registros = compute_iid_por_usuario(db, uids, formulario_id)
        if not registros:
            result.append({
                "periodo_id": pid,
                "periodo_nome": periodo.nome if periodo else str(pid),
                "iid_medio": 0,
                "total_respondentes": 0,
                "iid_pontos": [],
            })
            continue
        iid_pontos = [round(r[1], 1) for r in registros]
        iid_medio = round(sum(iid_pontos) / len(iid_pontos), 1)
        result.append({
            "periodo_id": pid,
            "periodo_nome": periodo.nome if periodo else str(pid),
            "iid_medio": iid_medio,
            "total_respondentes": len(registros),
            "iid_pontos": iid_pontos,
        })
    return result
