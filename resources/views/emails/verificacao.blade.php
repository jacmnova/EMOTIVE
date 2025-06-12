<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de E-mail</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #222; margin-bottom: 10px;">SISTEMA <span style="color:#007bff;">BURNOUT</span></h2>

            <p style="font-size: 16px; color: #333;">Olá {{ $usuario->name ?? 'Usuário' }},</p>

            <p style="font-size: 15px; color: #555;">
                Clique no botão abaixo para confirmar seu endereço de e-mail e ativar sua conta.
            </p>

            <p style="margin: 30px 0;">
                <a href="{{ $url }}"
                   style="background: #242424; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    Confirmar E-mail
                </a>
            </p>

            <p style="font-size: 14px; color: #777;">
                Se você não criou esta conta, apenas ignore esta mensagem.
            </p>
        </div>

        <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 13px; color: #888;">
            © {{ now()->year }} Burnout | FELLIPELLI Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
