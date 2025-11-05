<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">PLANO DE DESENVOLVIMENTO PESSOAL</h1>
    
    <!-- Zona de Risco -->
    <div style="margin-bottom: 40px;">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="width: 12px; height: 12px; background: {{ $nivelRisco['cor_hex'] }}; border-radius: 50%; margin-right: 10px;"></div>
            <h2 style="color: {{ $nivelRisco['cor_hex'] }}; font-size: 1.3rem; margin: 0;">{{ $nivelRisco['zona'] }}</h2>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Objetivo:</h3>
            <p style="text-align: justify; line-height: 1.8; color: #555; margin-bottom: 20px;">
                {{ $planDesenvolvimento['objetivo'] }}
            </p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Ações sugeridas:</h3>
            <ol style="line-height: 2; padding-left: 25px; color: #555;">
                @foreach($planDesenvolvimento['acoes'] as $acao)
                    <li>{{ $acao }}</li>
                @endforeach
            </ol>
        </div>
        
        <div>
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Indicador de progresso:</h3>
            <p style="text-align: justify; line-height: 1.8; color: #555; margin: 0;">
                {{ $planDesenvolvimento['indicador'] }}
            </p>
        </div>
    </div>
    
    <!-- Citação -->
    <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 25px; margin: 40px 0; text-align: center;">
        <p style="margin: 0; font-size: 1.1rem; font-style: italic; color: #333; line-height: 1.8;">
            "O equilíbrio não é ausência de desafios, mas a capacidade de se manter inteiro diante deles."
        </p>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 10</p>
        </div>
    </div>
</div>

