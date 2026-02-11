"""
Servicio de generación de PDFs
Usa WeasyPrint para generar PDFs desde HTML
También soporta servicio externo (como en Laravel)
"""
import httpx
from pathlib import Path
from typing import Optional, Dict, Any
from weasyprint import HTML, CSS
from io import BytesIO
from app.core.config import settings
import logging

logger = logging.getLogger(__name__)

async def generar_pdf_desde_html(
    html_content: str,
    css_content: Optional[str] = None
) -> Optional[bytes]:
    """
    Genera PDF desde contenido HTML usando WeasyPrint
    
    Args:
        html_content: Contenido HTML
        css_content: CSS opcional (si None, usa estilos inline del HTML)
    
    Returns:
        Bytes del PDF o None si hay error
    """
    try:
        if css_content:
            css = CSS(string=css_content)
            pdf_bytes = HTML(string=html_content).write_pdf(stylesheets=[css])
        else:
            pdf_bytes = HTML(string=html_content).write_pdf()
        
        return pdf_bytes
    except Exception as e:
        logger.error(f"Error generando PDF con WeasyPrint: {str(e)}")
        return None

async def generar_pdf_desde_url(
    url: str,
    timeout: int = 180
) -> Optional[bytes]:
    """
    Genera PDF usando servicio externo (como en Laravel)
    El servicio navega a la URL y convierte el HTML a PDF
    
    Args:
        url: URL del reporte a convertir
        timeout: Timeout en segundos
    
    Returns:
        Bytes del PDF o None si hay error
    """
    if not settings.PDF_SERVICE_URL:
        logger.warning("PDF_SERVICE_URL no configurado")
        return None
    
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            response = await client.post(
                settings.PDF_SERVICE_URL,
                json={"url": url},
                headers={"Content-Type": "application/json"}
            )
            
            if response.status_code == 200:
                return response.content
            else:
                logger.error(f"Error del servicio PDF: {response.status_code} - {response.text}")
                return None
    except httpx.TimeoutException:
        logger.error("Timeout al generar PDF desde URL")
        return None
    except Exception as e:
        logger.error(f"Error al llamar servicio PDF: {str(e)}")
        return None

def generar_html_relatorio_pdf(
    formulario: Dict[str, Any],
    user: Dict[str, Any],
    pontuacoes: list,
    ejes_analiticos: Dict[str, Any],
    iid: float,
    nivel_risco: Dict[str, Any],
    plan_desenvolvimento: Dict[str, Any],
    analise_texto: Optional[str] = None,
    promedio_indices: float = 0
) -> str:
    """
    Genera HTML para el PDF del reporte
    Basado en la vista participante.relatorio_emotive_pdf de Laravel
    """
    html = f"""
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório E.MO.TI.VE - {user.get('name', '')}</title>
    <style>
        @page {{
            size: A4;
            margin: 2cm;
        }}
        body {{
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }}
        .header {{
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }}
        .header h1 {{
            margin: 0;
            font-size: 24pt;
            color: #2c3e50;
        }}
        .header p {{
            margin: 5px 0;
            color: #666;
        }}
        .section {{
            margin-bottom: 30px;
            page-break-inside: avoid;
        }}
        .section-title {{
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }}
        .dimension {{
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #3498db;
        }}
        .dimension-title {{
            font-weight: bold;
            font-size: 13pt;
            margin-bottom: 10px;
        }}
        .dimension-value {{
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
        }}
        .badge {{
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 10pt;
            margin-left: 10px;
        }}
        .badge-info {{
            background-color: #17a2b8;
            color: white;
        }}
        .badge-warning {{
            background-color: #ffc107;
            color: #333;
        }}
        .badge-danger {{
            background-color: #dc3545;
            color: white;
        }}
        .eje {{
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }}
        .eje-title {{
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }}
        .eje-total {{
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }}
        .iid-section {{
            text-align: center;
            padding: 20px;
            background-color: #e8f4f8;
            border-radius: 5px;
            margin: 30px 0;
        }}
        .iid-value {{
            font-size: 32pt;
            font-weight: bold;
            color: {nivel_risco.get('cor_hex', '#333')};
        }}
        .analise {{
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }}
        .analise h3 {{
            margin-top: 0;
            color: #2c3e50;
        }}
        table {{
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }}
        table th, table td {{
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }}
        table th {{
            background-color: #f5f5f5;
            font-weight: bold;
        }}
        .footer {{
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }}
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório E.MO.TI.VE</h1>
        <p><strong>Participante:</strong> {user.get('name', '')}</p>
        <p><strong>Formulário:</strong> {formulario.get('nome', '')}</p>
        <p><strong>Data:</strong> {formulario.get('data_geracao', '')}</p>
    </div>

    <div class="section">
        <div class="section-title">Puntuaciones por Dimensión</div>
"""
    
    # Agregar puntuaciones
    for p in pontuacoes:
        badge_class = p.get('badge', 'info')
        html += f"""
        <div class="dimension">
            <div class="dimension-title">{p.get('nome', '')} ({p.get('tag', '')})</div>
            <div class="dimension-value">
                {p.get('valor', 0):.1f} pontos
                <span class="badge badge-{badge_class}">{p.get('faixa', '')}</span>
            </div>
            <p><strong>Recomendação:</strong> {p.get('recomendacao', 'Sem dados.')}</p>
        </div>
"""
    
    html += """
    </div>

    <div class="section">
        <div class="section-title">Ejes Analíticos</div>
"""
    
    # Agregar ejes
    for key, eje in ejes_analiticos.items():
        html += f"""
        <div class="eje">
            <div class="eje-title">{eje.get('nome', '')}</div>
            <p>{eje.get('descricao', '')}</p>
            <div style="margin: 10px 0;">
                <strong>{eje.get('dimensao1', {}).get('nome', '')}:</strong> 
                {eje.get('dimensao1', {}).get('valor', 0):.1f} ({eje.get('dimensao1', {}).get('faixa', '')})
            </div>
            <div style="margin: 10px 0;">
                <strong>{eje.get('dimensao2', {}).get('nome', '')}:</strong> 
                {eje.get('dimensao2', {}).get('valor', 0):.1f} ({eje.get('dimensao2', {}).get('faixa', '')})
            </div>
            <div class="eje-total">Total: {eje.get('total', 0)} ({eje.get('faixa_total', '')})</div>
            <p><em>{eje.get('interpretacao', {}).get('significado', '')}</em></p>
        </div>
"""
    
    html += f"""
    </div>

    <div class="iid-section">
        <div class="section-title">Índice Integrado de Descarrilamento (IID)</div>
        <div class="iid-value">{iid:.2f}</div>
        <p><strong>{nivel_risco.get('nivel', '')}</strong> - {nivel_risco.get('zona', '')}</p>
    </div>

    <div class="section">
        <div class="section-title">Plan de Desenvolvimento</div>
        <p><strong>Objetivo:</strong> {plan_desenvolvimento.get('objetivo', '')}</p>
        <p><strong>Ações:</strong></p>
        <ul>
"""
    
    for acao in plan_desenvolvimento.get('acoes', []):
        html += f"<li>{acao}</li>"
    
    html += f"""
        </ul>
        <p><strong>Indicador:</strong> {plan_desenvolvimento.get('indicador', '')}</p>
    </div>
"""
    
    if analise_texto:
        html += f"""
    <div class="analise">
        <h3>Análise Personalizada</h3>
        <div>{analise_texto.replace(chr(10), '<br>')}</div>
    </div>
"""
    
    html += """
    <div class="footer">
        <p>Relatório gerado automaticamente pelo sistema E.MO.TI.VE</p>
        <p>© Fellipelli - Todos os direitos reservados</p>
    </div>
</body>
</html>
"""
    
    return html
