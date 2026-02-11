"""
Tests para generación de PDFs
"""
import pytest
from app.services.pdf import generar_html_relatorio_pdf


def test_generar_html_relatorio_pdf():
    """Test generación de HTML para PDF"""
    html = generar_html_relatorio_pdf(
        formulario_nome="Test Form",
        usuario_nome="Test User",
        pontuacoes=[{"nome": "EXEM", "valor": 15}],
        indices={"EE": 20, "PR": 19, "SO": 25},
        ejes_analiticos={"dimensao1": {"valor": 10}},
        iid=50,
        nivel_risco="Moderado",
        plan_desenvolvimento=["Item 1", "Item 2"],
        analise_texto="Test analysis"
    )
    
    assert isinstance(html, str)
    assert "Test Form" in html
    assert "Test User" in html
    assert "EXEM" in html
    assert "Moderado" in html
