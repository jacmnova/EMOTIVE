@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 30px;">ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h1>
    
    @php
        $eixos = [
            'eixo1' => $ejesAnaliticos['eixo1'],
            'eixo2' => $ejesAnaliticos['eixo2'],
            'eixo3' => $ejesAnaliticos['eixo3']
        ];
        
        // Definir colores específicos de badges según la dimensión y el eixo
        $coresBadges = [
            'eixo1' => [
                'EXEM' => '#DED8C7', // Exaustão Emocional
                'REPR' => '#E8C97B'   // Realização Profissional
            ],
            'eixo2' => [
                'DECI' => '#DED8C7', // Despersonalização / Cinismo
                'FAPS' => '#E8C97B'  // Fatores Psicossociais
            ],
            'eixo3' => [
                'EXTR' => '#E8C97B', // Excesso de Trabalho
                'ASMO' => '#7BC9A8'  // Assédio Moral
            ]
        ];
    @endphp
    
    @foreach($eixos as $key => $eixo)
        <div style="margin-bottom: 50px;">
            <!-- Título del eixo -->
            <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">{{ $eixo['nome'] }}</h2>
            
            <!-- Texto introductorio -->
            <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 20px;">
                {{ $eixo['descricao'] }}
            </p>
            
            <!-- Barra de resumen con fondo gris claro -->
            <div style="background: #F5F5F5; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center;">
                    <!-- Dimensão 1 -->
                    <div>
                        <div style="font-weight: bold; margin-bottom: 10px; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">{{ $eixo['dimensao1']['nome'] }}</div>
                        <div style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $coresBadges[$key][$eixo['dimensao1']['tag']] ?? '#DED8C7' }}; color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
                            Faixa {{ $eixo['dimensao1']['faixa'] }}
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 5px;">TOTAL</div>
                        <div style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $coresBadges[$key][$eixo['dimensao2']['tag']] ?? '#E8C97B' }}; color: #000;font-size: 14px;font-style: normal;font-weight: 400;line-height: normal;     margin-top: 5px;">{{ round($eixo['total']) }}</div>
                    </div>
                    
                    <!-- Dimensão 2 -->
                    <div style="text-align: right;">
                        <div style="font-weight: bold; margin-bottom: 10px; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">{{ $eixo['dimensao2']['nome'] }}</div>
                        <div style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $coresBadges[$key][$eixo['dimensao2']['tag']] ?? '#E8C97B' }}; color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
                            Faixa {{ $eixo['dimensao2']['faixa'] }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detalles de interpretación -->
            <div style="margin-bottom: 30px;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0 0 8px 0;">
                    <strong style="font-weight: 700;">Interpretação:</strong> {{ $eixo['interpretacao']['interpretacao'] }}
                </p>
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0 0 8px 0;">
                    <strong style="font-weight: 700;">Significado Psicológico:</strong> {{ $eixo['interpretacao']['significado'] }}
                </p>
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                    <strong style="font-weight: 700;">Orientações Práticas:</strong> {{ $eixo['interpretacao']['orientacoes'] }}
                </p>
            </div>
        </div>
    @endforeach
    
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
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 06</p>
        </div>
    </div>
</div>

