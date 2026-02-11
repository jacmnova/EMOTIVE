<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>E.MO.TI.VE | Acesso disponível</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ config('app.url') }}/img/logo_emotive.png" alt="E.MO.TI.VE" style="height: 60px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
            <h2 style="color: #0087a0; margin: 0;">E.MO.TI.VE | Acesso disponível</h2>
        </div>

        <p style="font-size: 16px; color: #333;"><strong>Olá, {{ $usuario->name }}!</strong></p>

        <p style="color: #555; line-height: 1.6;">
            Seja muito bem-vindo(a) ao <strong>E.MO.TI.VE</strong>! Estamos felizes em ter você por aqui para uma jornada de autoconhecimento e bem-estar. Nosso objetivo é investir em um ambiente de trabalho mais saudável, e sua participação é muito importante.
        </p>

        <p style="color: #555; line-height: 1.6;">
            Para começar, preparamos seu acesso à plataforma:
        </p>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #0087a0; margin: 20px 0;">
            <p style="margin: 5px 0; color: #333;"><strong>Email:</strong> <a href="mailto:{{ $usuario->email }}" style="color: #0087a0; text-decoration: none;">{{ $usuario->email }}</a></p>
            <p style="margin: 5px 0; color: #333;"><strong>Senha provisória:</strong> <span style="font-family: monospace; background-color: #fff; padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd;">{{ $senhaTemporaria }}</span></p>
        </div>

        <p style="color: #555; line-height: 1.6;">
            Recomendamos que você altere sua senha após o primeiro login.
        </p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background-color: #0087a0; color: #fff; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(0,135,160,0.3);">
                Acessar o sistema
            </a>
        </p>

        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0 0 10px 0; color: #856404; font-weight: bold;">Importante:</p>
            <ul style="margin: 0; padding-left: 20px; color: #856404;">
                <li style="margin-bottom: 5px;">Sua senha é pessoal e intransferível.</li>
                <li style="margin-bottom: 5px;">Recomendamos utilizar letras maiúsculas, minúsculas, números e símbolos para maior segurança.</li>
                <li style="margin-bottom: 5px;">Em caso de dúvidas, entre em contato com nosso time de suporte: <a href="mailto:instrumentos@fellipelli.com.br" style="color: #0087a0;">instrumentos@fellipelli.com.br</a></li>
                <li>Qualquer dúvida, estamos à disposição!</li>
            </ul>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #777; line-height: 1.6;">
            Abraços,<br>
            <strong>Equipe Fellipelli Consultoria</strong>
        </p>
    </div>
</body>
</html>

