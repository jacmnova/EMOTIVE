<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">RISCO DE DESCARRILAMENTO EMOCIONAL E OCUPACIONAL</h1>
    
    <div style="margin-bottom: 30px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px;">
            O <strong>risco de descarrilamento</strong> representa a probabilidade de perder o equilíbrio emocional, motivacional e funcional no trabalho. Este índice é derivado das interações entre os três eixos analíticos do modelo E.MO.TI.VE:
        </p>
        <ol style="line-height: 2; padding-left: 25px; margin-bottom: 20px;">
            <li><strong>Energia Emocional:</strong> capacidade de sustentar vitalidade e propósito</li>
            <li><strong>Propósito e Relações:</strong> qualidade das conexões e do engajamento social</li>
            <li><strong>Sustentabilidade Ocupacional:</strong> equilíbrio entre esforço e suporte recebido</li>
        </ol>
        <p style="text-align: justify; line-height: 1.8;">
            Cada eixo gera um índice individual (0 a 100) e, quando combinados, formam o <strong>Índice Integrado de Descarrilamento (IID)</strong>.
        </p>
    </div>
    
    <!-- Gráfico de Risco -->
    <div style="margin: 40px 0;">
        <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; position: relative;">
            <!-- Barra de risco -->
            <div style="position: relative; height: 60px; background: linear-gradient(to right, #28a745 0%, #28a745 25%, #ffc107 25%, #ffc107 65%, #fd7e14 65%, #fd7e14 89%, #dc3545 89%, #dc3545 100%); border-radius: 30px; margin: 20px 0;">
                <!-- Labels -->
                <div style="position: absolute; top: -25px; left: 0; width: 100%; display: flex; justify-content: space-between;">
                    <span style="font-size: 0.85rem; font-weight: bold;">Baixo</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Médio</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Atenção</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Alto</span>
                </div>
                
                <!-- Indicador -->
                @php
                    $posicao = ($iid / 100) * 100;
                    if ($iid <= 40) {
                        $posicao = ($iid / 40) * 25;
                    } elseif ($iid <= 65) {
                        $posicao = 25 + (($iid - 40) / 25) * 40;
                    } elseif ($iid <= 89) {
                        $posicao = 65 + (($iid - 65) / 24) * 24;
                    } else {
                        $posicao = 89 + (($iid - 89) / 11) * 11;
                    }
                @endphp
                <div style="position: absolute; left: {{ $posicao }}%; top: -10px; transform: translateX(-50%);">
                    <div style="width: 3px; height: 80px; background: #000; margin: 0 auto;"></div>
                    <div style="width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 12px solid #000; margin: 0 auto;"></div>
                </div>
            </div>
            
            <!-- Pontuação e Interpretação -->
            <div style="text-align: center; margin-top: 30px;">
                <p style="font-size: 1.3rem; font-weight: bold; color: {{ $nivelRisco['cor_hex'] }}; margin-bottom: 15px;">
                    Pontuação = {{ $iid }} - {{ $nivelRisco['zona'] }}
                </p>
                
                @php
                    $interpretacaoIID = '';
                    if ($iid <= 40) {
                        $interpretacaoIID = 'Seu equilíbrio emocional está em uma zona saudável. Continue mantendo hábitos que promovem bem-estar e sustentabilidade.';
                    } elseif ($iid <= 65) {
                        $interpretacaoIID = 'Pequenas oscilações de energia e propósito, mas ainda sem impacto funcional. Pode haver início de fadiga ou leve desconexão emocional. Reequilibrar rotinas e priorizar autocuidado. Conversar sobre sobrecarga antes que se intensifique.';
                    } elseif ($iid <= 89) {
                        $interpretacaoIID = 'Sinais de vulnerabilidade emocional estão presentes. Há necessidade de atenção e ajustes para prevenir o agravamento. Busque apoio e reorganize prioridades.';
                    } else {
                        $interpretacaoIID = 'Risco crítico identificado. É importante buscar apoio profissional imediato e revisar condições de trabalho. Nenhum resultado justifica adoecimento.';
                    }
                @endphp
                
                <ul style="text-align: left; display: inline-block; line-height: 2; margin: 20px 0;">
                    <li>{{ $interpretacaoIID }}</li>
                </ul>
            </div>
        </div>
        
        <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 20px; margin: 30px 0;">
            <p style="margin: 0; color: #333; font-size: 1.05rem; line-height: 1.8;">
                "O descarrilamento emocional raramente ocorre de forma súbita - ele é o resultado de pequenas desconexões acumuladas."
            </p>
            <p style="margin: 10px 0 0 0; color: #333; font-size: 1.05rem; line-height: 1.8;">
                "Reconhecer os sinais precoces é o maior ato de autocuidado e responsabilidade profissional."
            </p>
        </div>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 07</p>
        </div>
    </div>
</div>

