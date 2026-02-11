"""
Servicio de cálculos - Lógica de negocio para puntuaciones, índices, ejes e IID
Migrado desde Laravel CalculaEjesAnaliticos trait
"""
from typing import List, Dict, Any, Optional
from sqlalchemy.orm import Session
from app.models import Variavel, Pergunta, Resposta, Formulario
from app.utils.inversao import precisa_inversao

def calcular_puntuacion_dimension(
    db: Session,
    variavel: Variavel,
    respostas_usuario: Dict[int, Resposta],
    formulario_id: int
) -> Dict[str, Any]:
    """
    Calcula la puntuación de una dimensión (variable)
    
    Args:
        db: Sesión de base de datos
        variavel: Variable/Dimensión a calcular
        respostas_usuario: Diccionario {pergunta_id: Resposta}
        formulario_id: ID del formulario
    
    Returns:
        Dict con tag, nome, valor, faixa, recomendacao, badge, etc.
    """
    pontuacao = 0
    total_respostas = 0
    preguntas_procesadas = []
    preguntas_sin_resposta = []
    
    # Caso especial: EXTR necesita contar una pregunta dos veces
    es_extr = (variavel.tag.upper() == "EXTR")
    pregunta_duplicada_extr_id = None
    
    if es_extr:
        pregunta_duplicada = db.query(Pergunta).filter(
            Pergunta.formulario_id == formulario_id,
            Pergunta.pergunta.like("%Recebo novas demandas antes de conseguir concluir%")
        ).first()
        if pregunta_duplicada:
            pregunta_duplicada_extr_id = pregunta_duplicada.id
    
    # Procesar cada pregunta de la variable
    for pergunta in variavel.perguntas:
        contar_dos_veces = (
            es_extr and 
            pregunta_duplicada_extr_id and 
            pergunta.id == pregunta_duplicada_extr_id
        )
        
        resposta = respostas_usuario.get(pergunta.id)
        
        if not resposta or resposta.valor_resposta is None:
            preguntas_sin_resposta.append({
                "pergunta_id": pergunta.id,
                "numero_da_pergunta": pergunta.numero_da_pergunta
            })
            continue
        
        # Aplicar inversión si corresponde
        valor_original = int(resposta.valor_resposta)
        valor_usado = aplicar_inversion(pergunta, valor_original)
        
        if contar_dos_veces:
            pontuacao += valor_usado * 2
            total_respostas += 2
        else:
            pontuacao += valor_usado
            total_respostas += 1
        
        preguntas_procesadas.append({
            "pergunta_id": pergunta.id,
            "valor_original": valor_original,
            "valor_usado": valor_usado,
            "contada_dos_veces": contar_dos_veces
        })
    
    if total_respostas == 0:
        return None
    
    # Clasificar en faixa
    faixa = classificar_pontuacao(pontuacao, variavel)
    
    # Determinar recomendación y badge
    if faixa == "Baixa":
        recomendacao = variavel.r_baixa or "Sem dados."
        badge = "info"
    elif faixa == "Moderada":
        recomendacao = variavel.r_moderada or "Sem dados."
        badge = "warning"
    else:  # Alta
        recomendacao = variavel.r_alta or "Sem dados."
        badge = "danger"
    
    # Calcular máximo del gráfico
    maximo_posible = total_respostas * 6
    maximo_grafico = 200 if maximo_posible > 100 else 100
    
    return {
        "tag": variavel.tag.upper(),
        "nome": variavel.nome,
        "valor": float(pontuacao),
        "maximo_grafico": maximo_grafico,
        "faixa": faixa,
        "recomendacao": recomendacao,
        "badge": badge,
        "b": float(variavel.B or 0),
        "m": float(variavel.M or 0),
        "a": float(variavel.A or 0),
    }

def calcular_indices_desde_respostas(
    db: Session,
    respostas_usuario: Dict[int, Resposta],
    formulario_id: int
) -> Dict[str, float]:
    """
    Calcula los índices EE, PR, SO directamente desde respuestas
    
    EE = EXEM ∪ REPR
    PR = DECI ∪ FAPS
    SO = EXTR ∪ ASMO
    """
    variaveis = db.query(Variavel).filter(
        Variavel.formulario_id == formulario_id
    ).all()
    
    variaveis_por_tag = {v.tag: v for v in variaveis}
    
    mapeo_ejes = {
        "EE": ["ExEm", "RePr"],
        "PR": ["DeCi", "FaPs"],
        "SO": ["ExTr", "AsMo"],
    }
    
    resultados = {}
    es_so = False
    pregunta_duplicada_so_id = None
    
    for indice, tags_dimensiones in mapeo_ejes.items():
        es_so = (indice == "SO")
        
        if es_so:
            pregunta_duplicada = db.query(Pergunta).filter(
                Pergunta.formulario_id == formulario_id,
                Pergunta.pergunta.like("%Recebo novas demandas antes de conseguir concluir%")
            ).first()
            if pregunta_duplicada:
                pregunta_duplicada_so_id = pregunta_duplicada.id
        
        pontuacao = 0
        preguntas_ids_unicas = []
        
        for tag_dimension in tags_dimensiones:
            variavel = variaveis_por_tag.get(tag_dimension)
            if not variavel:
                continue
            
            for pergunta in variavel.perguntas:
                contar_dos_veces = (
                    es_so and 
                    pregunta_duplicada_so_id and 
                    pergunta.id == pregunta_duplicada_so_id
                )
                
                if not contar_dos_veces and pergunta.id in preguntas_ids_unicas:
                    continue
                
                if not contar_dos_veces:
                    preguntas_ids_unicas.append(pergunta.id)
                
                resposta = respostas_usuario.get(pergunta.id)
                
                if not resposta or resposta.valor_resposta is None:
                    valor_original = 0
                else:
                    valor_original = int(resposta.valor_resposta)
                
                if valor_original < 0 or valor_original > 6:
                    continue
                
                valor_usado = aplicar_inversion(pergunta, valor_original)
                
                if contar_dos_veces:
                    pontuacao += valor_usado * 2
                else:
                    pontuacao += valor_usado
        
        resultados[indice] = float(pontuacao)
    
    return resultados

def calcular_ejes_analiticos(
    pontuacoes: List[Dict[str, Any]],
    indices: Dict[str, float]
) -> Dict[str, Any]:
    """
    Calcula los ejes analíticos del modelo E.MO.TI.VE
    """
    pontos_por_tag = {}
    for ponto in pontuacoes:
        tag = ponto.get("tag", "").upper()
        pontos_por_tag[tag] = {
            "valor": ponto.get("valor", 0),
            "faixa": ponto.get("faixa", "Baixa"),
            "b": ponto.get("b", 0),
            "m": ponto.get("m", 0),
        }
    
    # Obtener valores de dimensiones
    exaustao = pontos_por_tag.get("EXEM", {"valor": 0, "faixa": "Baixa"})
    realizacao = pontos_por_tag.get("REPR", {"valor": 0, "faixa": "Baixa"})
    cinismo = pontos_por_tag.get("DECI", {"valor": 0, "faixa": "Baixa"})
    fatores = pontos_por_tag.get("FAPS", {"valor": 0, "faixa": "Baixa"})
    excesso = pontos_por_tag.get("EXTR", {"valor": 0, "faixa": "Baixa"})
    assedio = pontos_por_tag.get("ASMO", {"valor": 0, "faixa": "Baixa"})
    
    # Usar índices calculados directamente
    eixo1_total = indices.get("EE", 0)
    eixo2_total = indices.get("PR", 0)
    eixo3_total = indices.get("SO", 0)
    
    # Límites dinámicos
    ee_lim_b = float(exaustao.get("b", 0)) + float(realizacao.get("b", 0))
    ee_lim_m = float(exaustao.get("m", 0)) + float(realizacao.get("m", 0))
    faixa_total_ee = classificar_indice_dinamico(eixo1_total, ee_lim_b, ee_lim_m, "EE")
    
    eixo1 = {
        "nome": "ENERGIA EMOCIONAL",
        "descricao": "Este eixo mostra o quanto sua energia emocional está sendo renovada ou drenada no trabalho.",
        "dimensao1": {
            "nome": "Exaustão Emocional",
            "tag": "EXEM",
            "valor": exaustao["valor"],
            "faixa": exaustao["faixa"]
        },
        "dimensao2": {
            "nome": "Realização Profissional",
            "tag": "REPR",
            "valor": realizacao["valor"],
            "faixa": realizacao["faixa"]
        },
        "total": round(eixo1_total, 0),
        "faixa_total": faixa_total_ee,
        "interpretacao": interpretar_eixo1(exaustao["faixa"], realizacao["faixa"])
    }
    
    pr_lim_b = float(cinismo.get("b", 0)) + float(fatores.get("b", 0))
    pr_lim_m = float(cinismo.get("m", 0)) + float(fatores.get("m", 0))
    faixa_total_pr = classificar_indice_dinamico(eixo2_total, pr_lim_b, pr_lim_m, "PR")
    
    eixo2 = {
        "nome": "PROPÓSITO E RELAÇÕES",
        "descricao": "Este eixo avalia o grau de conexão emocional e relacional com o ambiente de trabalho.",
        "dimensao1": {
            "nome": "Despersonalização / Cinismo",
            "tag": "DECI",
            "valor": cinismo["valor"],
            "faixa": cinismo["faixa"]
        },
        "dimensao2": {
            "nome": "Fatores Psicossociais",
            "tag": "FAPS",
            "valor": fatores["valor"],
            "faixa": fatores["faixa"]
        },
        "total": round(eixo2_total, 0),
        "faixa_total": faixa_total_pr,
        "interpretacao": interpretar_eixo2(cinismo["faixa"], fatores["faixa"])
    }
    
    so_lim_b = float(excesso.get("b", 0)) + float(assedio.get("b", 0))
    so_lim_m = float(excesso.get("m", 0)) + float(assedio.get("m", 0))
    faixa_total_so = classificar_indice_dinamico(eixo3_total, so_lim_b, so_lim_m, "SO")
    
    eixo3 = {
        "nome": "SUSTENTABILIDADE OCUPACIONAL",
        "descricao": "Este eixo reflete a relação entre o esforço exigido e o suporte ético e emocional oferecido.",
        "dimensao1": {
            "nome": "Excesso de Trabalho",
            "tag": "EXTR",
            "valor": excesso["valor"],
            "faixa": excesso["faixa"]
        },
        "dimensao2": {
            "nome": "Assédio Moral",
            "tag": "ASMO",
            "valor": assedio["valor"],
            "faixa": assedio["faixa"]
        },
        "total": round(eixo3_total, 0),
        "faixa_total": faixa_total_so,
        "interpretacao": interpretar_eixo3(excesso["faixa"], assedio["faixa"])
    }
    
    return {
        "eixo1": eixo1,
        "eixo2": eixo2,
        "eixo3": eixo3
    }

def calcular_iid(ejes_analiticos: Dict[str, Any]) -> float:
    """
    Calcula el Índice Integrado de Descarrilamento (IID)
    """
    ee = ejes_analiticos["eixo1"]["total"]
    pr = ejes_analiticos["eixo2"]["total"]
    so = ejes_analiticos["eixo3"]["total"]
    
    promedio_indices = (ee + pr + so) / 3
    
    # Máximos según CSV MAX
    max_ee = 276
    max_pr = 234
    max_so = 186
    promedio_maximos = (max_ee + max_pr + max_so) / 3  # = 232
    
    iid = (promedio_indices / promedio_maximos) * 100
    
    return round(iid, 2)

def faixa_iid_design(iid: float) -> str:
    """
    Faixa do design do relatório corporativo (0-25 Baixa, 25-50 Moderada, 50-75 Alta, 75-100 Crítico).
    """
    if iid <= 25:
        return "baixa"
    if iid <= 50:
        return "moderada"
    if iid <= 75:
        return "alta"
    return "critico"


def determinar_nivel_risco(iid: float) -> Dict[str, Any]:
    """
    Determina el nivel de riesgo basado en IID
    """
    if iid <= 40:
        return {
            "nivel": "Baixo",
            "zona": "Zona de Equilíbrio Emocional",
            "cor": "success",
            "cor_hex": "#28a745"
        }
    elif iid <= 65:
        return {
            "nivel": "Médio",
            "zona": "Zona de Atenção Preventiva",
            "cor": "warning",
            "cor_hex": "#ffc107"
        }
    elif iid <= 89:
        return {
            "nivel": "Atenção",
            "zona": "Zona de Vulnerabilidade",
            "cor": "danger",
            "cor_hex": "#fd7e14"
        }
    else:
        return {
            "nivel": "Alto",
            "zona": "Zona Crítica",
            "cor": "danger",
            "cor_hex": "#dc3545"
        }

# Funciones auxiliares

def aplicar_inversion(pergunta: Pergunta, valor: int) -> int:
    """Aplica inversión si la pregunta lo requiere"""
    if precisa_inversao(pergunta):
        return 6 - valor
    return valor

def classificar_pontuacao(valor: float, variavel: Variavel) -> str:
    """Clasifica puntuación en faixa"""
    b = float(variavel.B or 0)
    m = float(variavel.M or 0)
    
    if valor <= b:
        return "Baixa"
    elif valor <= m:
        return "Moderada"
    else:
        return "Alta"

def classificar_indice_dinamico(total: float, lim_baixo: float, lim_medio: float, indice: str) -> str:
    """Clasificación dinámica por eixo"""
    if lim_baixo > 0 and lim_medio > 0 and lim_medio >= lim_baixo:
        if total <= lim_baixo:
            return "Baixa"
        elif total <= lim_medio:
            return "Moderada"
        else:
            return "Alta"
    return classificar_indice_por_csv(indice, total)

def classificar_indice_por_csv(indice: str, valor: float) -> str:
    """Clasificación por límites del CSV"""
    limites = {
        "EE": {"baixa": 92, "media": 184},
        "PR": {"baixa": 78, "media": 170},
        "SO": {"baixa": 62, "media": 154},
    }
    
    ind = indice.upper()
    if ind not in limites:
        return "Moderada"
    
    lim = limites[ind]
    if valor <= lim["baixa"]:
        return "Baixa"
    elif valor <= lim["media"]:
        return "Moderada"
    else:
        return "Alta"

def interpretar_eixo1(exaustao_faixa: str, realizacao_faixa: str) -> Dict[str, str]:
    """Interpreta o Eixo 1 (Energia Emocional) baseado nas combinações de faixas EXEM x REPR."""
    interpretacoes = {
        "Alta-Alta": {
            "interpretacao": "⚠️ Estado Crítico",
            "significado": "Alto risco de esgotamento. A sensação de impotência e perda de propósito indica necessidade de pausa e apoio.",
            "orientacoes": "Reduza o ritmo, priorize descanso, converse com sua liderança e reflita sobre o que dá sentido ao seu trabalho."
        },
        "Alta-Moderada": {
            "interpretacao": "Estado de Esforço Contínuo",
            "significado": "Há sobrecarga, mas o propósito ainda motiva. O risco é ultrapassar o limite sem perceber.",
            "orientacoes": "Preserve seus espaços de recuperação e delegue tarefas. Sustente a motivação sem comprometer a saúde."
        },
        "Alta-Baixa": {
            "interpretacao": "Engajamento em Excesso",
            "significado": "Energia e propósito coexistem, mas o corpo pode estar pagando o preço.",
            "orientacoes": "Valorize pausas, reconheça sinais de fadiga e equilibre ambição com autocuidado."
        },
        "Moderada-Baixa": {
            "interpretacao": "Equilíbrio Dinâmico",
            "significado": "Boa realização com cansaço controlado. Indica produtividade saudável.",
            "orientacoes": "Mantenha rituais de descanso e reconheça conquistas. Esse é um ponto ótimo."
        },
        "Moderada-Alta": {
            "interpretacao": "Desânimo Progressivo",
            "significado": "Esforço emocional sem retorno de propósito. Pode evoluir para desmotivação.",
            "orientacoes": "Busque feedbacks e alinhe expectativas. Reencontre significado nas atividades."
        },
        "Moderada-Moderada": {
            "interpretacao": "Estado de Manutenção",
            "significado": "Equilíbrio funcional. Nem sobrecarregado, nem entediado.",
            "orientacoes": "Continue cuidando do ritmo e do engajamento. Práticas de gratidão ajudam a fortalecer esse equilíbrio."
        },
        "Baixa-Baixa": {
            "interpretacao": "💚 Zona de Vitalidade",
            "significado": "Estado ideal. Boa energia e satisfação no trabalho.",
            "orientacoes": "Continue praticando hábitos saudáveis, compartilhando boas práticas e inspirando colegas."
        },
        "Baixa-Moderada": {
            "interpretacao": "Tranquilidade Operacional",
            "significado": "Rotina estável, mas com espaço para mais propósito.",
            "orientacoes": "Defina novos desafios e metas inspiradoras."
        },
        "Baixa-Alta": {
            "interpretacao": "Apatia Emocional",
            "significado": "Baixo estresse, mas também baixo envolvimento. Indica tédio ou falta de desafio.",
            "orientacoes": "Reavalie seus objetivos e busque oportunidades que reativem seu entusiasmo."
        }
    }
    chave = f"{exaustao_faixa}-{realizacao_faixa}"
    return interpretacoes.get(chave, interpretacoes["Moderada-Moderada"])


def interpretar_eixo2(cinismo_faixa: str, fatores_faixa: str) -> Dict[str, str]:
    """Interpreta o Eixo 2 (Propósito e Relações) baseado nas combinações DECI x FAPS."""
    interpretacoes = {
        "Alta-Baixa": {
            "interpretacao": "⚠️ Isolamento e Desconfiança",
            "significado": "Indica desgaste relacional e perda de vínculo com o ambiente. Pode haver sensação de injustiça ou frieza no time.",
            "orientacoes": "Reabra canais de diálogo. Se possível, busque apoio em pessoas de confiança e em práticas colaborativas."
        },
        "Alta-Moderada": {
            "interpretacao": "Proteção Emocional",
            "significado": "Tentativa de se proteger de tensões. O ambiente oferece algum suporte, mas há barreiras emocionais.",
            "orientacoes": "Trabalhe a empatia e reforce vínculos leves e sinceros."
        },
        "Alta-Alta": {
            "interpretacao": "Cansaço Relacional",
            "significado": "O ambiente é bom, mas há esgotamento pessoal. O cinismo pode vir de excesso de exposição ou idealismo frustrado.",
            "orientacoes": "Tire pausas de interação, sem se isolar. Retome o propósito em pequenas vitórias."
        },
        "Moderada-Baixa": {
            "interpretacao": "Conexão Consciente",
            "significado": "Relacionamento saudável com limites claros.",
            "orientacoes": "Mantenha equilíbrio e evite absorver tensões alheias."
        },
        "Moderada-Moderada": {
            "interpretacao": "Relações Neutras",
            "significado": "Conexões estáveis, porém pouco afetivas.",
            "orientacoes": "Estimule momentos de reconhecimento e humanização nas relações."
        },
        "Moderada-Alta": {
            "interpretacao": "Desencanto",
            "significado": "Sensação de distância emocional e falta de suporte.",
            "orientacoes": "Invista em comunicação e peça clareza sobre expectativas."
        },
        "Baixa-Baixa": {
            "interpretacao": "💚 Pertencimento Saudável",
            "significado": "Relações de confiança, empatia e apoio mútuo.",
            "orientacoes": "Continue nutrindo o ambiente com colaboração e reconhecimento."
        },
        "Baixa-Moderada": {
            "interpretacao": "Equilíbrio Social",
            "significado": "Boa convivência, ainda que nem sempre profunda.",
            "orientacoes": "Cultive pequenas atitudes de escuta e feedbacks positivos."
        },
        "Baixa-Alta": {
            "interpretacao": "Engajamento Solitário",
            "significado": "Você se mantém aberto e positivo mesmo em contextos frios.",
            "orientacoes": "Proteja sua energia e incentive práticas coletivas de cooperação."
        }
    }
    chave = f"{cinismo_faixa}-{fatores_faixa}"
    return interpretacoes.get(chave, interpretacoes["Moderada-Moderada"])


def interpretar_eixo3(excesso_faixa: str, assedio_faixa: str) -> Dict[str, str]:
    """Interpreta o Eixo 3 (Sustentabilidade Ocupacional) baseado nas combinações EXTR x ASMO."""
    interpretacoes = {
        "Alta-Alta": {
            "interpretacao": "⚠️ Risco Crítico",
            "significado": "Indica ambiente tóxico, com sobrecarga e desrespeito. Altíssimo risco psicossocial.",
            "orientacoes": "Acione canais formais de apoio. Nenhum resultado justifica adoecimento."
        },
        "Alta-Moderada": {
            "interpretacao": "Sobrecarga Controlada",
            "significado": "Alta pressão, mas ainda com algum nível de segurança emocional.",
            "orientacoes": "Converse com a liderança sobre prazos e prioridades. Pratique pausas regenerativas."
        },
        "Alta-Baixa": {
            "interpretacao": "Dedicação Intensa",
            "significado": "Carga alta em ambiente respeitoso. O risco é o corpo não acompanhar o ritmo.",
            "orientacoes": "Estabeleça limites de jornada e celebre pausas."
        },
        "Moderada-Alta": {
            "interpretacao": "Ambiente Desgastante",
            "significado": "As demandas são gerenciáveis, mas o clima é hostil ou tenso.",
            "orientacoes": "Busque apoio institucional. Priorize relações seguras e comunicação assertiva."
        },
        "Moderada-Moderada": {
            "interpretacao": "Zona de Atenção",
            "significado": "Indica ambiente exigente, com riscos pontuais de tensão.",
            "orientacoes": "Monitore sinais de estresse e pratique pausas semanais."
        },
        "Moderada-Baixa": {
            "interpretacao": "💚 Sustentabilidade Saudável",
            "significado": "Boa produtividade com respeito mútuo.",
            "orientacoes": "Mantenha práticas saudáveis e incentive o mesmo no grupo."
        },
        "Baixa-Alta": {
            "interpretacao": "Ambiente Inseguro",
            "significado": "Baixa demanda, mas clima emocional ruim. O problema está nas relações, não na carga.",
            "orientacoes": "Não se isole. Procure espaços seguros e promova conversas francas."
        },
        "Baixa-Moderada": {
            "interpretacao": "Cautela Social",
            "significado": "Carga leve, mas interações sensíveis.",
            "orientacoes": "Mantenha postura empática e evite conflitos desnecessários."
        },
        "Baixa-Baixa": {
            "interpretacao": "Zona de Bem-Estar",
            "significado": "Ambiente saudável, equilibrado e ético.",
            "orientacoes": "Valorize e proteja esse equilíbrio. Compartilhe práticas positivas."
        }
    }
    chave = f"{excesso_faixa}-{assedio_faixa}"
    return interpretacoes.get(chave, interpretacoes["Moderada-Moderada"])

def get_plan_desenvolvimento(nivel_risco: Dict[str, Any]) -> Dict[str, Any]:
    """Retorna el plan de desarrollo según nivel de riesgo"""
    planos = {
        "Baixo": {
            "objetivo": "Preservar hábitos saudáveis e fortalecer a resiliência emocional.",
            "acoes": [
                "Continuar praticando hábitos que promovem bem-estar.",
                "Manter conversas regulares de alinhamento com a liderança.",
                "Engajar-se em projetos que ampliem o senso de propósito."
            ],
            "indicador": "Sensação de equilíbrio mantida, com boa energia e motivação estável."
        },
        "Médio": {
            "objetivo": "Evitar o acúmulo de estresse e reequilibrar a rotina.",
            "acoes": [
                "Revisar compromissos e priorizar o essencial.",
                "Incluir pausas ativas diárias.",
                "Buscar feedback sobre performance e bem-estar."
            ],
            "indicador": "Redução de momentos de tensão e aumento da clareza sobre prioridades."
        },
        "Atenção": {
            "objetivo": "Restabelecer energia emocional e reforçar suporte social.",
            "acoes": [
                "Identificar fontes de exaustão e negociar ajustes.",
                "Buscar apoio psicológico, coaching ou mentoria.",
                "Retomar vínculos sociais e práticas que gerem prazer."
            ],
            "indicador": "Recuperação gradual de vitalidade e engajamento."
        },
        "Alto": {
            "objetivo": "Promover recuperação emocional imediata.",
            "acoes": [
                "Interromper sobrecargas e alinhar plano com RH/liderança.",
                "Buscar acompanhamento psicológico ou médico especializado.",
                "Redefinir metas de curto prazo com foco em autocuidado."
            ],
            "indicador": "Redução dos sintomas de esgotamento."
        }
    }
    nivel = nivel_risco.get("nivel", "Médio")
    return planos.get(nivel, planos["Médio"])
