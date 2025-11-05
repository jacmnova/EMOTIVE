@if(isset($eixos) && isset($eixos['iid']))
<div class="card">
    <div class="card-header border-0">
        <h2 class="card-title">RISCO DE DESCARRILAMENTO EMOCIONAL E OCUPACIONAL</h2>
    </div>
    <div class="card-body">
        <p>O risco de descarrilamento representa a probabilidade de perda de equilíbrio emocional, motivacional e funcional no trabalho, a partir das interações entre os três eixos analíticos do modelo E.MO.TI.VE®:</p>
        <ol>
            <li><strong>Energia Emocional</strong> — capacidade de sustentar vitalidade e propósito.</li>
            <li><strong>Propósito e Relações</strong> — qualidade das conexões e do engajamento social.</li>
            <li><strong>Sustentabilidade Ocupacional</strong> — equilíbrio entre esforço e suporte recebido.</li>
        </ol>
        <p>Cada eixo gera um índice individual (0 a 100) e, ao serem combinados, formam o Índice Integrado de Descarrilamento (IID).</p>

        @php
            $iid = $eixos['iid'];
            $percentual = $iid['valor'];
            $indicatorWidth = min(100, ($percentual / 100) * 100);
            $riscoColor = $iid['nivel_risco'] == 'Baixo' ? '#4CAF50' : ($iid['nivel_risco'] == 'Médio' ? '#FFC107' : ($iid['nivel_risco'] == 'Atenção' ? '#FF9800' : '#F44336'));
        @endphp

        <div class="mt-4">
            <h4>Classificação do Risco</h4>
            <div style="width: 100%; height: 50px; background-color: #e0e0e0; border-radius: 25px; position: relative; margin: 20px 0; display: flex; overflow: hidden;">
                <div style="position: absolute; height: 100%; width: {{ $indicatorWidth }}%; background-color: {{ $riscoColor }}; border-radius: 25px; left: 0; z-index: 1;"></div>
                <div style="flex: 1; border-right: 2px solid #333; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #333; z-index: 2; position: relative;">Baixo<br>(0-40)</div>
                <div style="flex: 1; border-right: 2px solid #333; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #333; z-index: 2; position: relative;">Médio<br>(41-65)</div>
                <div style="flex: 1; border-right: 2px solid #333; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #333; z-index: 2; position: relative;">Atenção<br>(66-89)</div>
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #333; z-index: 2; position: relative;">Alto<br>(90-100)</div>
            </div>
            
            <div style="text-align: center; margin: 25px 0;">
                <span style="font-size: 20px; color: {{ $riscoColor }}; font-weight: bold;">
                    Pontuação = {{ round($iid['valor']) }} - {{ $iid['zona'] }}
                </span>
            </div>
        </div>

        <div class="mt-3">
            <p><strong>{{ $iid['descricao'] }}</strong></p>
            <p>{{ $iid['interpretacao'] }}</p>
            <p>{{ $iid['acao'] }}</p>
        </div>

        <div style="background-color: #B2DFDB; padding: 20px; border-radius: 8px; margin-top: 25px; border-left: 4px solid #008080;">
            <p style="font-style: italic; color: #333; margin: 0;">
                "O descarrilamento emocional raramente ocorre de forma súbita — ele é o resultado de pequenas desconexões acumuladas. Reconhecer os sinais precoces é o maior ato de autocuidado e responsabilidade profissional."
            </p>
        </div>
    </div>
</div>
@endif

