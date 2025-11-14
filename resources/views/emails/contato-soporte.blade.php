<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova mensagem de contato - FAQ</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ config('app.url') }}/img/logo_emotive.png" alt="E.MO.TI.VE" style="height: 60px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
            <h2 style="color: #0087a0; margin: 0;">Nova mensagem de contato</h2>
        </div>

        <p style="color: #555; line-height: 1.6;">
            Você recebeu uma nova mensagem através do formulário de contato da página FAQ.
        </p>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #0087a0; margin: 20px 0;">
            <p style="margin: 5px 0; color: #333;"><strong>Nome:</strong> {{ $nome }}</p>
            <p style="margin: 5px 0; color: #333;"><strong>Email:</strong> <a href="mailto:{{ $email }}" style="color: #0087a0; text-decoration: none;">{{ $email }}</a></p>
        </div>

        <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; margin: 20px 0;">
            <p style="margin: 0 0 10px 0; color: #333; font-weight: bold;">Mensagem:</p>
            <p style="margin: 0; color: #555; line-height: 1.6; white-space: pre-wrap;">{{ $mensagem }}</p>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #777; line-height: 1.6;">
            Atenciosamente,<br>
            <strong>Sistema E.MO.TI.VE</strong>
        </p>
    </div>
</body>
</html>

