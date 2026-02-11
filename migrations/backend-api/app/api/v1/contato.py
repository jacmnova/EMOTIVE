"""
Endpoint público para el formulario de contacto (FAQ).
"""
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel, EmailStr, field_validator

from app.services.email import send_contato_email

router = APIRouter()


class ContatoEnviarRequest(BaseModel):
    nome: str
    email: EmailStr
    mensagem: str

    @field_validator("nome")
    @classmethod
    def nome_not_empty(cls, v: str) -> str:
        if not (v and v.strip()):
            raise ValueError("O nome é obrigatório.")
        if len(v) > 255:
            raise ValueError("Nome muito longo.")
        return v.strip()

    @field_validator("mensagem")
    @classmethod
    def mensagem_min_length(cls, v: str) -> str:
        if not (v and v.strip()):
            raise ValueError("A mensagem é obrigatória.")
        if len(v.strip()) < 10:
            raise ValueError("A mensagem deve ter pelo menos 10 caracteres.")
        return v.strip()


@router.post("/enviar")
async def enviar_mensagem(body: ContatoEnviarRequest):
    """Envía la mensaje del formulario de contacto al correo de soporte."""
    ok = send_contato_email(
        nome=body.nome,
        email=body.email,
        mensagem=body.mensagem,
    )
    if not ok:
        raise HTTPException(
            status_code=500,
            detail="Erro ao enviar mensagem. Por favor, tente novamente mais tarde.",
        )
    return {
        "message": "Mensagem enviada com sucesso! Entraremos em contato em breve.",
    }
