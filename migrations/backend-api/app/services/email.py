"""
Servicio de envío de emails (verificación, reset de contraseña, etc.)
"""
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from typing import Optional

from app.core.config import settings


def _get_smtp_connection():
    """Crea conexión SMTP (misma lógica que Laravel: MAIL_ENCRYPTION tls|ssl|null)."""
    encryption = (getattr(settings, "MAIL_ENCRYPTION", None) or "").lower().strip() or "tls"
    if encryption == "ssl":
        server = smtplib.SMTP_SSL(settings.MAIL_HOST, settings.MAIL_PORT)
    else:
        server = smtplib.SMTP(settings.MAIL_HOST, settings.MAIL_PORT)
        if encryption == "tls":
            server.starttls()
    if settings.MAIL_USERNAME and settings.MAIL_PASSWORD:
        server.login(settings.MAIL_USERNAME, settings.MAIL_PASSWORD)
    return server


def send_email(
    to: str,
    subject: str,
    html_body: str,
    from_email: Optional[str] = None,
    reply_to: Optional[str] = None,
) -> bool:
    """
    Envía un email por SMTP.
    Retorna True si se envió correctamente, False si no está configurado el mail o falla.
    """
    from_addr = (
        from_email
        or getattr(settings, "MAIL_FROM_ADDRESS", None)
        or settings.MAIL_FROM
        or settings.MAIL_USERNAME
    )
    if not from_addr:
        return False
    from_name = getattr(settings, "MAIL_FROM_NAME", None) or ""
    if from_name:
        from_header = f"{from_name} <{from_addr}>"
    else:
        from_header = from_addr
    msg = MIMEMultipart("alternative")
    msg["Subject"] = subject
    msg["From"] = from_header
    msg["To"] = to
    if reply_to:
        msg["Reply-To"] = reply_to
    part = MIMEText(html_body, "html", "utf-8")
    msg.attach(part)
    try:
        server = _get_smtp_connection()
        server.sendmail(from_addr, [to], msg.as_string())
        server.quit()
        return True
    except Exception:
        return False


def html_verificacion_email(usuario_name: str, url: str) -> str:
    """Genera HTML para email de verificación de cuenta."""
    return f"""
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de E-mail - E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #222; margin-bottom: 10px;">E.MO.TI.VE</h2>
            <p style="font-size: 16px; color: #333;">Olá {usuario_name or 'Usuário'},</p>
            <p style="font-size: 15px; color: #555;">Para concluir seu cadastro, confirme seu e-mail clicando no botão abaixo:</p>
            <p style="margin: 30px 0;">
                <a href="{url}" style="background: #242424; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;">Confirmar E-mail</a>
            </p>
            <p style="font-size: 14px; color: #777;">Se você não realizou esse cadastro, ignore esta mensagem.</p>
        </div>
        <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 13px; color: #888;">
            © E.MO.TI.VE - Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
"""


def html_reset_password(usuario_name: str, url: str) -> str:
    """Genera HTML para email de recuperación de contraseña."""
    return f"""
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha - E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
            <h2 style="color: #444;">Recuperação de Senha</h2>
        </div>
        <p>Olá {usuario_name},</p>
        <p>Recebemos uma solicitação para redefinir sua senha no <strong>E.MO.TI.VE</strong>.</p>
        <p style="text-align: center;">
            <a href="{url}" style="background-color: #2d89ef; color: #fff; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Redefinir minha senha</a>
        </p>
        <p style="color: #555; font-size: 14px;">Este link expirará em 60 minutos. Se você não solicitou a alteração, ignore este e-mail.</p>
        <p style="margin-top: 30px; font-size: 14px; color: #777;">Atenciosamente,<br><strong>Equipe E.MO.TI.VE</strong></p>
    </div>
</body>
</html>
"""


def send_verification_email(to: str, usuario_name: str, token: str) -> bool:
    """Envía email de verificación de cuenta."""
    url = f"{settings.FRONTEND_URL}/auth/verify-email?token={token}"
    subject = "Confirmação de E-mail - E.MO.TI.VE"
    html = html_verificacion_email(usuario_name, url)
    return send_email(to, subject, html)


def send_reset_password_email(to: str, usuario_name: str, token: str) -> bool:
    """Envía email con enlace para redefinir contraseña."""
    url = f"{settings.FRONTEND_URL}/auth/reset-password?token={token}&email={to}"
    subject = "Recuperação de Senha - E.MO.TI.VE"
    html = html_reset_password(usuario_name, url)
    return send_email(to, subject, html)


def html_contato_soporte(nome: str, email: str, mensagem: str) -> str:
    """HTML para email de mensagem de contacto (FAQ)."""
    return f"""
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova mensagem de contato - FAQ E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #0087a0 0%, #006d82 100%); color: white; padding: 20px; text-align: center;">
            <h2 style="color: white; margin: 0;">Nova mensagem de contato</h2>
        </div>
        <div style="padding: 30px;">
            <p>Você recebeu uma nova mensagem através do formulário de contato da página FAQ.</p>
            <p><strong>Nome:</strong> {nome}</p>
            <p><strong>E-mail:</strong> {email}</p>
            <p><strong>Mensagem:</strong></p>
            <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin-top: 10px; white-space: pre-wrap;">{mensagem}</div>
            <p style="margin-top: 20px; font-size: 14px; color: #666;">Responda diretamente para: {email}</p>
        </div>
    </div>
</body>
</html>
"""


def send_contato_email(nome: str, email: str, mensagem: str) -> bool:
    """Envía al soporte el mensaje del formulario de contacto."""
    to = getattr(settings, "CONTACT_EMAIL", "instrumentos@fellipelli.com.br")
    subject = "Nova mensagem de contato - FAQ E.MO.TI.VE"
    html = html_contato_soporte(nome, email, mensagem)
    reply_to = f"{nome} <{email}>" if nome else email
    return send_email(to, subject, html, reply_to=reply_to)


def html_invitacao_questionario(usuario_name: str, formulario_nome: str, url: str) -> str:
    """Genera HTML para email de invitación a completar cuestionario."""
    return f"""
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo questionário disponível - E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #222; margin-bottom: 10px;">E.MO.TI.VE</h2>
            <p style="font-size: 16px; color: #333;">Olá {usuario_name or 'Usuário'},</p>
            <p style="font-size: 15px; color: #555;">Um novo questionário está disponível para você: <strong>{formulario_nome}</strong>.</p>
            <p style="font-size: 15px; color: #555;">Acesse sua área e responda quando puder.</p>
            <p style="margin: 30px 0;">
                <a href="{url}" style="background: #242424; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;">Acessar meus questionários</a>
            </p>
            <p style="font-size: 14px; color: #777;">Se você não esperava este convite, pode ignorar esta mensagem.</p>
        </div>
        <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 13px; color: #888;">
            © E.MO.TI.VE - Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
"""


def send_invitacao_questionario(to: str, usuario_name: str, formulario_nome: str, link_direto: Optional[str] = None) -> bool:
    """Envía email de invitación. Si link_direto está definido (ej. /responder?token=xxx), se usa; si no, link al dashboard."""
    url = link_direto if link_direto else f"{settings.FRONTEND_URL}/dashboard"
    if not url.startswith("http"):
        url = f"{settings.FRONTEND_URL}{url}" if url.startswith("/") else f"{settings.FRONTEND_URL}/{url}"
    subject = "Novo questionário disponível - E.MO.TI.VE"
    html = html_invitacao_questionario(usuario_name, formulario_nome, url)
    return send_email(to, subject, html)


def html_recordatorio_questionario(usuario_name: str, formulario_nome: str, url: str) -> str:
    """Genera HTML para email de recordatorio (questionário pendente)."""
    return f"""
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lembrete - Questionário pendente - E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #222; margin-bottom: 10px;">E.MO.TI.VE</h2>
            <p style="font-size: 16px; color: #333;">Olá {usuario_name or 'Usuário'},</p>
            <p style="font-size: 15px; color: #555;">Este é um lembrete: você ainda tem o questionário <strong>{formulario_nome}</strong> pendente.</p>
            <p style="font-size: 15px; color: #555;">Acesse sua área e responda quando puder.</p>
            <p style="margin: 30px 0;">
                <a href="{url}" style="background: #242424; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;">Acessar meus questionários</a>
            </p>
        </div>
        <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 13px; color: #888;">
            © E.MO.TI.VE - Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
"""


def send_recordatorio_questionario(to: str, usuario_name: str, formulario_nome: str, link_direto: Optional[str] = None) -> bool:
    """Envía email de recordatorio. Si link_direto está definido, se usa; si no, link al dashboard."""
    url = link_direto if link_direto else f"{settings.FRONTEND_URL}/dashboard"
    if url and not url.startswith("http"):
        url = f"{settings.FRONTEND_URL}{url}" if url.startswith("/") else f"{settings.FRONTEND_URL}/{url}"
    subject = "Lembrete: questionário pendente - E.MO.TI.VE"
    html = html_recordatorio_questionario(usuario_name, formulario_nome, url)
    return send_email(to, subject, html)
