<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha - E.MO.TI.VE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
            <img src="https://www.fellipelli.com.br/wp-content/uploads/2021/10/fellipelli-logo.png" alt="Fellipelli" style="height: 50px; margin-bottom: 20px;">
            <h2 style="color: #444;">Recuperação de Senha</h2>
        </div>

        <p>Olá {{ $usuario->name }},</p>

        <p>Recebemos uma solicitação para redefinir sua senha no <strong>E.MO.TI.VE</strong>.</p>

        <p style="text-align: center;">
            <a href="{{ $url }}" style="background-color: #2d89ef; color: #fff; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                Redefinir minha senha
            </a>
        </p>

        <p style="color: #555; font-size: 14px;">
            Este link de recuperação expirará em 60 minutos.<br>
            Se você não solicitou a alteração, ignore este e-mail.
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #777;">
            Atenciosamente,<br>
            <strong>Equipe Fellipelli</strong>
        </p>
    </div>
</body>
</html>
