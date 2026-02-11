"""
Endpoints de generación de PDFs
"""
from io import BytesIO
from fastapi import APIRouter, Depends, HTTPException, status, Query
from fastapi.responses import Response
from sqlalchemy.orm import Session, joinedload
from app.database import get_db
from app.models import User, Formulario, Resposta, Variavel, Analise
from app.api.v1.auth import get_current_user_token
from app.services.calculos import (
    calcular_puntuacion_dimension,
    calcular_indices_desde_respostas,
    calcular_ejes_analiticos,
    calcular_iid,
    determinar_nivel_risco,
    get_plan_desenvolvimento,
)
from app.services.pdf import generar_pdf_desde_html, generar_pdf_desde_url
from datetime import datetime
from typing import Optional

router = APIRouter()

@router.get("/relatorio")
async def gerar_pdf_relatorio(
    formulario_id: int = Query(...),
    usuario_id: int = Query(...),
    use_external_service: bool = Query(False, description="Usar servicio externo si está disponible"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db)
):
    """
    Genera PDF del reporte completo.
    Puede usar WeasyPrint (local) o servicio externo (como Laravel).
    """
    # Permisos
    if current_user.id != usuario_id:
        if not (current_user.admin or current_user.sa):
            if not current_user.gestor:
                raise HTTPException(status_code=403, detail="No tienes permiso")
            other = db.query(User).filter(User.id == usuario_id).first()
            if not other or other.cliente_id != current_user.cliente_id:
                raise HTTPException(status_code=403, detail="No tienes permiso")

    user = db.query(User).filter(User.id == usuario_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    formulario = db.query(Formulario).options(
        joinedload(Formulario.perguntas)
    ).filter(Formulario.id == formulario_id).first()
    if not formulario:
        raise HTTPException(status_code=404, detail="Formulario no encontrado")

    # Obtener datos del reporte (reutilizar lógica de reportes)
    pergunta_ids = [p.id for p in formulario.perguntas]
    respostas_list = db.query(Resposta).filter(
        Resposta.user_id == usuario_id,
        Resposta.pergunta_id.in_(pergunta_ids)
    ).all()
    respostas_usuario = {r.pergunta_id: r for r in respostas_list}

    if current_user.id == usuario_id:
        respostas_com_valor = [r for r in respostas_list if r.valor_resposta is not None]
        if len(respostas_com_valor) < len(pergunta_ids):
            raise HTTPException(
                status_code=403,
                detail="Complete todas as perguntas do questionário para ver o relatório."
            )

    variaveis = db.query(Variavel).options(
        joinedload(Variavel.perguntas)
    ).filter(Variavel.formulario_id == formulario_id).all()

    # Calcular puntuaciones
    pontuacoes = []
    for variavel in variaveis:
        p = calcular_puntuacion_dimension(db, variavel, respostas_usuario, formulario_id)
        if p:
            pontuacoes.append(p)

    # Calcular índices y ejes
    indices = calcular_indices_desde_respostas(db, respostas_usuario, formulario_id)
    ejes_analiticos = calcular_ejes_analiticos(pontuacoes, indices)
    iid = calcular_iid(ejes_analiticos)
    nivel_risco = determinar_nivel_risco(iid)
    plan_desenvolvimento = get_plan_desenvolvimento(nivel_risco)

    # Análisis
    analise = db.query(Analise).filter(
        Analise.user_id == usuario_id,
        Analise.formulario_id == formulario_id
    ).first()
    analise_texto = analise.texto if analise else None
    promedio_indices = (indices.get("EE", 0) + indices.get("PR", 0) + indices.get("SO", 0)) / 3

    # Preparar datos para HTML
    formulario_data = {
        "nome": formulario.nome,
        "label": formulario.label,
        "data_geracao": datetime.now().strftime("%d/%m/%Y")
    }
    user_data = {
        "name": user.name,
        "email": user.email
    }

    # Generar HTML
    from app.services.pdf import generar_html_relatorio_pdf
    html_content = generar_html_relatorio_pdf(
        formulario_data,
        user_data,
        pontuacoes,
        ejes_analiticos,
        iid,
        nivel_risco,
        plan_desenvolvimento,
        analise_texto,
        promedio_indices
    )

    # Generar PDF
    if use_external_service:
        # Usar servicio externo (requiere que el frontend esté corriendo)
        from app.core.config import settings
        base_url = settings.APP_URL if hasattr(settings, 'APP_URL') else "http://localhost:3000"
        report_url = f"{base_url}/reportes?formulario_id={formulario_id}&usuario_id={usuario_id}"
        pdf_bytes = await generar_pdf_desde_url(report_url)
    else:
        # Usar WeasyPrint local
        pdf_bytes = await generar_pdf_desde_html(html_content)

    if not pdf_bytes:
        raise HTTPException(
            status_code=500,
            detail="Error al generar el PDF"
        )

    # Nombre del archivo
    filename = f"relatorio_emotive_{user.name.replace(' ', '_')}.pdf"

    return Response(
        content=pdf_bytes,
        media_type="application/pdf",
        headers={
            "Content-Disposition": f'attachment; filename="{filename}"',
            "Content-Length": str(len(pdf_bytes))
        }
    )


@router.get("/relatorio-corporativo")
async def gerar_pdf_relatorio_corporativo(
    cliente_id: int = Query(..., description="Cliente"),
    periodo_id: int = Query(..., description="Período/onda"),
    formulario_id: int = Query(..., description="Formulário para IID"),
    unidade: Optional[str] = Query(None, description="Filtro por unidade (opcional)"),
    area: Optional[str] = Query(None, description="Filtro por área (opcional)"),
    nivel_jerarquico: Optional[str] = Query(None, description="Filtro por nível hierárquico (opcional)"),
    tempo_empresa: Optional[str] = Query(None, description="Filtro por tempo de empresa (opcional)"),
    modelo_trabalho: Optional[str] = Query(None, description="Filtro por modelo de trabalho (opcional)"),
    current_user: User = Depends(get_current_user_token),
    db: Session = Depends(get_db),
):
    """
    Gera PDF do relatório corporativo (KPIs e concentração de riscos por IID).
    Respeita os filtros opcionais (unidade, area, etc.) para gerar o PDF só do subconjunto selecionado.
    Requer gestor/admin/sa; gestor só do seu cliente.
    """
    if not (current_user.gestor or current_user.admin or current_user.sa):
        raise HTTPException(status_code=403, detail="Sem permissão")
    from app.api.v1.reportes import _cliente_id_for_user, get_kpis_iid_data
    cid = _cliente_id_for_user(current_user, db, cliente_id)
    data = get_kpis_iid_data(
        db, cid, periodo_id, formulario_id,
        unidade=unidade, area=area, nivel_jerarquico=nivel_jerarquico,
        tempo_empresa=tempo_empresa, modelo_trabalho=modelo_trabalho,
    )
    from reportlab.lib.pagesizes import A4
    from reportlab.pdfgen import canvas
    from reportlab.lib.units import cm
    buf = BytesIO()
    c = canvas.Canvas(buf, pagesize=A4)
    w, h = A4
    y = h - 2 * cm
    c.setFont("Helvetica-Bold", 16)
    c.drawString(2 * cm, y, "Relatório Corporativo - E.MO.TI.VE")
    y -= 1.2 * cm
    c.setFont("Helvetica", 10)
    c.drawString(2 * cm, y, f"Colaboradores em risco: {data.get('colaboradores_em_risco', 0)} ({data.get('percentual_risco', 0)}%)")
    y -= 0.6 * cm
    c.drawString(2 * cm, y, f"Total respondentes: {data.get('total_respondentes', 0)}  |  Alta: {data.get('total_alta', 0)}  |  Crítico: {data.get('total_critico', 0)}")
    y -= 1 * cm
    c.setFont("Helvetica-Bold", 12)
    c.drawString(2 * cm, y, "Setores críticos (área)")
    y -= 0.6 * cm
    c.setFont("Helvetica", 9)
    for s in (data.get("setores_criticos_area") or [])[:15]:
        c.drawString(2 * cm, y, f"  {s.get('valor', '')}: total={s.get('total')}  IID médio={s.get('iid_medio')}  Alta={s.get('alta')}  Crítico={s.get('critico')}")
        y -= 0.45 * cm
        if y < 2 * cm:
            c.showPage()
            y = h - 2 * cm
    y -= 0.8 * cm
    c.setFont("Helvetica-Bold", 12)
    c.drawString(2 * cm, y, "Onde se concentram os riscos (unidade -> áreas)")
    y -= 0.6 * cm
    c.setFont("Helvetica", 9)
    for item in (data.get("onde_se_concentram") or [])[:20]:
        c.drawString(2 * cm, y, f"  {item.get('unidade', '')} (total={item.get('total')}, IID médio={item.get('iid_medio')})")
        y -= 0.4 * cm
        for ar in (item.get("areas") or [])[:5]:
            c.drawString(2.5 * cm, y, f"    - {ar.get('area', '')}: Baixa={ar.get('baixa')} Moderada={ar.get('moderada')} Alta={ar.get('alta')} Crítico={ar.get('critico')}")
            y -= 0.4 * cm
            if y < 2 * cm:
                c.showPage()
                y = h - 2 * cm
        if y < 3 * cm:
            c.showPage()
            y = h - 2 * cm
    c.save()
    buf.seek(0)
    pdf_bytes = buf.getvalue()
    filename = "relatorio_corporativo_emotive.pdf"
    return Response(
        content=pdf_bytes,
        media_type="application/pdf",
        headers={
            "Content-Disposition": f'attachment; filename="{filename}"',
            "Content-Length": str(len(pdf_bytes)),
        },
    )
