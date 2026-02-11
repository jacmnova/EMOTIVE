"""
Servicio de integración con OpenAI
"""
import httpx
from typing import Optional, Dict, List
from app.core.config import settings
from app.models import User, Variavel, Pergunta, Resposta, Formulario

async def generar_analise_openai(
    user: User,
    formulario: Formulario,
    variaveis: List[Variavel],
    pontuacoes: List[Dict[str, any]],
    respostas_usuario: Dict[int, Resposta]
) -> Optional[str]:
    """
    Genera análisis usando OpenAI GPT-4o
    Basado en la lógica de AnaliseController de Laravel
    """
    if not settings.OPENAI_API_KEY:
        return None
    
    # Construir prompt
    prompt = """Você é um assistente especializado em saúde emocional, bem-estar no trabalho e aconselhamento motivacional.

Escreva um relatório analítico detalhado direcionado à pessoa que respondeu este questionário, com **no mínimo 1200 palavras** (mínimo 7500 caracteres).

O relatório deve obrigatoriamente conter:
- Um Resumo Geral Motivacional (mínimo 8 parágrafos).
- Uma Análise Profunda das Respostas (mínimo 8 parágrafos), detalhando padrões, vulnerabilidades, pontos fortes e inconsistências.
- Sugestões Personalizadas (mínimo 8 parágrafos), com recomendações práticas.
- Uma Conclusão Inspiradora.

Evite frases curtas. Expanda cada seção com exemplos, detalhes, reflexões. Não resuma. Não termine cedo. Gere um texto robusto e humano, sem economizar palavras.

Aqui estão os resultados por dimensão:\n"""

    # Agregar puntuaciones por dimensión
    for variavel in variaveis:
        pontuacao_data = next(
            (p for p in pontuacoes if p.get("tag", "").upper() == variavel.tag.upper()),
            None
        )
        if pontuacao_data:
            valor = pontuacao_data.get("valor", 0)
            faixa = pontuacao_data.get("faixa", "Baixa")
            prompt += f"{variavel.nome} ({variavel.tag}): {valor} pontos, Faixa {faixa}.\n"

    # Agregar respuestas individuales
    prompt += "\nAqui estão as respostas individuais fornecidas:\n"
    for pergunta in formulario.perguntas:
        resposta = respostas_usuario.get(pergunta.id)
        valor = resposta.valor_resposta if resposta else "não respondida"
        prompt += f"Pergunta {pergunta.id} ({pergunta.pergunta}): Resposta = {valor}\n"

    # Detectar inconsistencias
    inconsistencias = []
    for variavel in variaveis:
        respostas_na_variavel = {}
        for pergunta in variavel.perguntas:
            resposta = respostas_usuario.get(pergunta.id)
            if resposta:
                respostas_na_variavel[pergunta.id] = resposta.valor_resposta
        
        pergunta_ids = list(respostas_na_variavel.keys())
        for i in range(len(pergunta_ids)):
            for j in range(i + 1, len(pergunta_ids)):
                p1 = pergunta_ids[i]
                p2 = pergunta_ids[j]
                diff = abs(respostas_na_variavel[p1] - respostas_na_variavel[p2])
                if diff >= 3:
                    inconsistencias.append(
                        f"Na variável {variavel.nome}, a Pergunta {p1} teve resposta {respostas_na_variavel[p1]} "
                        f"e a Pergunta {p2} teve resposta {respostas_na_variavel[p2]} (diferença de {diff})."
                    )

    if inconsistencias:
        prompt += "\n\nDetectamos as seguintes inconsistências:\n"
        for item in inconsistencias:
            prompt += f"- {item}\n"
    else:
        prompt += "\n\nNenhuma inconsistência grande foi detectada."

    prompt += "\n\n⚠️ Gere no mínimo 1200 palavras. Cada bloco deve ter no mínimo 8 parágrafos. Seja completo, detalhado e profundo."

    # Llamar a OpenAI
    max_tries = 3
    for try_num in range(max_tries):
        try:
            async with httpx.AsyncClient(timeout=60.0) as client:
                response = await client.post(
                    settings.OPENAI_API_URL,
                    headers={
                        "Authorization": f"Bearer {settings.OPENAI_API_KEY}",
                        "Content-Type": "application/json"
                    },
                    json={
                        "model": settings.OPENAI_MODEL,
                        "messages": [
                            {
                                "role": "system",
                                "content": "Você é um assistente especializado em saúde emocional e aconselhamento motivacional."
                            },
                            {
                                "role": "user",
                                "content": prompt
                            }
                        ],
                        "temperature": 0.7,
                        "max_tokens": 4000
                    }
                )
                
                if response.status_code == 200:
                    data = response.json()
                    analise_texto = data["choices"][0]["message"]["content"]
                    
                    # Verificar longitud mínima (1200 palabras)
                    word_count = len(analise_texto.split())
                    if word_count >= 1200:
                        return analise_texto
                    elif try_num < max_tries - 1:
                        # Reintentar si no alcanza el mínimo
                        continue
                    else:
                        return analise_texto  # Retornar aunque sea corto
                else:
                    if try_num < max_tries - 1:
                        continue
                    return f"Erro ao gerar análise: {response.status_code} - {response.text}"
        except Exception as e:
            if try_num < max_tries - 1:
                continue
            return f"Erro ao gerar análise: {str(e)}"
    
    return None


async def chat_gpt(mensagem: str) -> Optional[str]:
    """
    Envía mensaje a ChatGPT y retorna respuesta
    """
    if not settings.OPENAI_API_KEY:
        return None
    
    try:
        async with httpx.AsyncClient(timeout=30.0) as client:
            response = await client.post(
                settings.OPENAI_API_URL,
                headers={
                    "Authorization": f"Bearer {settings.OPENAI_API_KEY}",
                    "Content-Type": "application/json"
                },
                json={
                    "model": "gpt-3.5-turbo",  # Modelo más rápido para chat
                    "messages": [
                        {
                            "role": "user",
                            "content": mensagem
                        }
                    ],
                    "temperature": 0.7,
                    "max_tokens": 1000
                }
            )
            
            if response.status_code == 200:
                data = response.json()
                return data["choices"][0]["message"]["content"]
            else:
                return f"Erro ao consultar o ChatGPT: {response.status_code} - {response.text}"
    except Exception as e:
        return f"Erro ao consultar o ChatGPT: {str(e)}"
