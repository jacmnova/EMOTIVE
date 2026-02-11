"""
Endpoints de chat con ChatGPT
"""
from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from app.api.v1.auth import get_current_user_token
from app.models import User
from app.services.openai import chat_gpt

router = APIRouter()

class ChatMessage(BaseModel):
    question: str

class ChatResponse(BaseModel):
    answer: str

@router.post("/", response_model=ChatResponse)
async def chat(
    message: ChatMessage,
    current_user: User = Depends(get_current_user_token)
):
    """
    Envía mensaje a ChatGPT y retorna respuesta
    Solo usuarios autenticados
    """
    if not message.question or len(message.question.strip()) == 0:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="La pregunta no puede estar vacía"
        )
    
    if len(message.question) > 1000:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="La pregunta no puede exceder 1000 caracteres"
        )
    
    answer = await chat_gpt(message.question)
    
    if not answer:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Error al consultar ChatGPT. Verifique la configuración de OpenAI."
        )
    
    return ChatResponse(answer=answer)
