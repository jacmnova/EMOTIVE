<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px; text-align: center;">ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h1>
    
    @php
        $eixos = [
            'eixo1' => $ejesAnaliticos['eixo1'],
            'eixo2' => $ejesAnaliticos['eixo2'],
            'eixo3' => $ejesAnaliticos['eixo3']
        ];
    @endphp
    
    @foreach($eixos as $key => $eixo)
        <div style="margin-bottom: 50px;">
            <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">{{ $eixo['nome'] }}</h2>
            <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px; color: #555;">
                {{ $eixo['descricao'] }}
            </p>
            
            <!-- Box de Resultados -->
            <div style="background: #f0f0f0; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center;">
                    <!-- Dimensão 1 -->
                    <div>
                        <div style="font-weight: bold; margin-bottom: 10px; color: #333;">{{ $eixo['dimensao1']['nome'] }}</div>
                        <div class="faixa-badge faixa-{{ strtolower($eixo['dimensao1']['faixa']) }}" style="background: {{ $eixo['dimensao1']['faixa'] === 'Baixa' ? '#d4edda' : ($eixo['dimensao1']['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $eixo['dimensao1']['faixa'] === 'Baixa' ? '#155724' : ($eixo['dimensao1']['faixa'] === 'Moderada' ? '#856404' : '#721c24') }};">
                            Faixa {{ $eixo['dimensao1']['faixa'] }}
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold; font-size: 1.2rem; color: #333; margin-bottom: 5px;">TOTAL</div>
                        <div style="font-size: 2rem; font-weight: bold; color: #008ca5;">{{ round($eixo['total']) }}</div>
                    </div>
                    
                    <!-- Dimensão 2 -->
                    <div style="text-align: right;">
                        <div style="font-weight: bold; margin-bottom: 10px; color: #333;">{{ $eixo['dimensao2']['nome'] }}</div>
                        <div class="faixa-badge faixa-{{ strtolower($eixo['dimensao2']['faixa']) }}" style="background: {{ $eixo['dimensao2']['faixa'] === 'Baixa' ? '#d4edda' : ($eixo['dimensao2']['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $eixo['dimensao2']['faixa'] === 'Baixa' ? '#155724' : ($eixo['dimensao2']['faixa'] === 'Moderada' ? '#856404' : '#721c24') }};">
                            Faixa {{ $eixo['dimensao2']['faixa'] }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Interpretação -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #008ca5;">
                <p style="margin: 0 0 10px 0;"><strong>Interpretação:</strong> {{ $eixo['interpretacao']['interpretacao'] }}</p>
                <p style="margin: 0 0 10px 0;"><strong>Significado Psicológico:</strong> {{ $eixo['interpretacao']['significado'] }}</p>
                <p style="margin: 0;"><strong>Orientações Práticas:</strong> {{ $eixo['interpretacao']['orientacoes'] }}</p>
            </div>
        </div>
    @endforeach
    
    <!-- Síntese Geral -->
    <div style="margin-top: 50px; padding: 30px; background: #e8f4f8; border-radius: 12px;">
        <h3 style="color: #008ca5; margin-bottom: 20px; font-size: 1.3rem;">🔄 Síntese Geral dos Eixos</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: white;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Eixo</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Quando Equilibrado</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Quando em Risco</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Energia Emocional</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Vitalidade, propósito e produtividade sustentável.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Fadiga, desânimo e queda de performance.</td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Propósito e Relações</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Engajamento e empatia nas relações.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Isolamento, cinismo e falta de confiança.</td>
                </tr>
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Sustentabilidade Ocupacional</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Respeito, ritmo equilibrado e apoio mútuo.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Sobrecarga, desrespeito e desgaste emocional.</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 06</p>
        </div>
    </div>
</div>

