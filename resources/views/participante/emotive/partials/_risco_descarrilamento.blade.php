@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 30px;">RISCO DE DESCARRILAMENTO EMOCIONAL E OCUPACIONAL</h1>
    
    <!-- Texto introductorio -->
    <div style="margin-bottom: 30px;">
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
            O <strong style="font-weight: 700;">risco de descarrilamento</strong> representa a probabilidade de perder o equilíbrio emocional, motivacional e funcional no trabalho. Este índice é derivado das interações entre os três eixos analíticos do modelo E.MO.TI.VE:
        </p>
        <ul style="line-height: 1.8; padding-left: 25px; margin-bottom: 20px; list-style: none;">
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;"><strong style="font-weight: 700;">1. Energia Emocional:</strong> capacidade de sustentar vitalidade e propósito.</li>
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;"><strong style="font-weight: 700;">2. Propósito e Relações:</strong> qualidade das conexões e do engajamento social.</li>
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;"><strong style="font-weight: 700;">3. Sustentabilidade Ocupacional:</strong> equilíbrio entre esforço e suporte recebido.</li>
        </ul>
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify;">
            Cada eixo gera um índice individual (0 a 100) e, ao serem combinados, formam o <strong style="font-weight: 700;">Índice Integrado de Descarrilamento (IID)</strong>.
        </p>
    </div>
    
    <!-- Barra de porcentaje / Indicador de nivel de riesgo -->
    <div style="margin: 40px 0;">
        @php
            // Determinar en qué segmento está el IID y calcular posición
            $segmentoAtivo = 'Baixo';
            $posicaoPorcentaje = 0;
            $corSegmentoAtivo = '#E0E0E0'; // light gray por defecto
            
            if ($iid <= 40) {
                $segmentoAtivo = 'Baixo';
                $posicaoPorcentaje = ($iid / 40) * 25; // 0-25% del ancho total
                $corSegmentoAtivo = '#E0E0E0';
            } elseif ($iid <= 65) {
                $segmentoAtivo = 'Médio';
                $posicaoPorcentaje = 25 + (($iid - 40) / 25) * 40; // 25-65% del ancho total
                $corSegmentoAtivo = '#FFC107'; // golden yellow
            } elseif ($iid <= 89) {
                $segmentoAtivo = 'Atenção';
                $posicaoPorcentaje = 65 + (($iid - 65) / 24) * 24; // 65-89% del ancho total
                $corSegmentoAtivo = '#E0E0E0';
            } else {
                $segmentoAtivo = 'Alto';
                $posicaoPorcentaje = 89 + (($iid - 89) / 11) * 11; // 89-100% del ancho total
                $corSegmentoAtivo = '#E0E0E0';
            }
        @endphp
        
        <!-- Barra con segmentos individuales -->
        <div style="position: relative; width: 100%; margin-bottom: 30px;">
            <!-- Contenedor de la barra -->
            <div style="display: flex; width: 100%; height: 40px; border-radius: 4px; overflow: hidden;">
                <!-- Segmento Baixo (0-40) -->
                <div style="flex: 0 0 25%; background: {{ $segmentoAtivo === 'Baixo' ? '#FFC107' : '#E0E0E0' }}; height: 100%;"></div>
                <!-- Segmento Médio (40-65) -->
                <div style="flex: 0 0 40%; background: {{ $segmentoAtivo === 'Médio' ? '#FFC107' : '#E0E0E0' }}; height: 100%;"></div>
                <!-- Segmento Atenção (65-89) -->
                <div style="flex: 0 0 24%; background: {{ $segmentoAtivo === 'Atenção' ? '#FFC107' : '#E0E0E0' }}; height: 100%;"></div>
                <!-- Segmento Alto (89-100) -->
                <div style="flex: 0 0 11%; background: {{ $segmentoAtivo === 'Alto' ? '#FFC107' : '#E0E0E0' }}; height: 100%;"></div>
            </div>
            
            <!-- Indicador (línea vertical con círculo arriba) -->
            <div style="position: absolute; left: {{ $posicaoPorcentaje }}%; top: -15px; transform: translateX(-50%); z-index: 10;">
                <!-- Círculo arriba -->
                <div style="width: 12px; height: 12px; background: #333; border-radius: 50%; margin: 0 auto 0 auto;"></div>
                <!-- Línea vertical -->
                <div style="width: 2px; height: 55px; background: #333; margin: 0 auto;"></div>
            </div>
            
            <!-- Labels debajo de la barra, centradas -->
            <div style="display: flex; width: 100%; margin-top: 10px;">
                <div style="flex: 0 0 25%; text-align: center;">
                    <span style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">Baixo</span>
                </div>
                <div style="flex: 0 0 40%; text-align: center;">
                    <span style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">Médio</span>
                </div>
                <div style="flex: 0 0 24%; text-align: center;">
                    <span style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">Atenção</span>
                </div>
                <div style="flex: 0 0 11%; text-align: center;">
                    <span style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">Alto</span>
                </div>
            </div>
        </div>
        
        <!-- Pontuação y recomendaciones -->
        <div style="margin-bottom: 30px;">
            <p style="color: #FFC107;font-size: 12px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 15px;">
                Pontuação= {{ round($promedioIndices ?? (($ejesAnaliticos['eixo1']['total'] + $ejesAnaliticos['eixo2']['total'] + $ejesAnaliticos['eixo3']['total']) / 3)) }} - {{ $nivelRisco['zona'] }}.
            </p>
            
            @php
                $textosRecomendacao = [];
                if ($iid <= 40) {
                    $textosRecomendacao = [
                        'Seu equilíbrio emocional está em uma zona saudável.',
                        'Continue mantendo hábitos que promovem bem-estar e sustentabilidade.'
                    ];
                } elseif ($iid <= 65) {
                    $textosRecomendacao = [
                        'Pequenas oscilações de energia e propósito, mas ainda sem impacto funcional.',
                        'Pode haver início de fadiga ou leve desconexão emocional.',
                        'Reequilibrar rotinas e priorizar autocuidado. Conversar sobre sobrecarga antes que se intensifique.'
                    ];
                } elseif ($iid <= 89) {
                    $textosRecomendacao = [
                        'Sinais de vulnerabilidade emocional estão presentes.',
                        'Há necessidade de atenção e ajustes para prevenir o agravamento.',
                        'Busque apoio e reorganize prioridades.'
                    ];
                } else {
                    $textosRecomendacao = [
                        'Risco crítico identificado.',
                        'É importante buscar apoio profissional imediato e revisar condições de trabalho.',
                        'Nenhum resultado justifica adoecimento.'
                    ];
                }
            @endphp
            
            @foreach($textosRecomendacao as $texto)
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 10px;">
                    {{ $texto }}
                </p>
            @endforeach
        </div>
        
        <!-- Caja de cita con fondo teal/azul-verde -->
        <div style="background-color: #E8F4F8; border-radius: 8px; padding: 20px; margin: 30px 0;">
            <p style="color: #2E9196;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0 0 10px 0;">
                "O descarrilamento emocional raramente ocorre de forma súbita - ele é o resultado de pequenas desconexões acumuladas."
            </p>
            <p style="color: #2E9196;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                "Reconhecer os sinais precoces é o maior ato de autocuidado e responsabilidade profissional."
            </p>
        </div>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 20px; align-items: center;">
            @php
                $imgPath = function($path) {
                    if (isset($isPdf) && $isPdf) {
                        $fullPath = public_path($path);
                        return file_exists($fullPath) ? $fullPath : '';
                    }
                    return asset($path);
                };
            @endphp
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: 30px;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: 30px;">
        </div>
        <div style="text-align: right;">
            <p style="font-size: 8px; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 07</p>
        </div>
    </div>
</div>

