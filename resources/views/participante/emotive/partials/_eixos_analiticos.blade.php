@php
    $isPdfMode = isset($isPdf) && $isPdf;
    $padding = $isPdfMode ? '18pt' : '40px';
    $marginBottom = $isPdfMode ? '12pt' : '30px';
    $titleSize = $isPdfMode ? '18pt' : '24px';
    $subtitleSize = $isPdfMode ? '12pt' : '16px';
    $textSize = $isPdfMode ? '10pt' : '10px';
    $footerMargin = $isPdfMode ? '20pt' : '50px';
@endphp
@if($isPdfMode)
<div class="section-pdf" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box; font-family: 'DejaVu Sans', sans-serif;">
@else
<div class="section-pdf" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $marginBottom }} 0; font-family: 'DejaVu Sans', sans-serif;">ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h1>
    
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
                @php 
                    // Usar el nombre del eixo (no el de la dimensão) para definir el fondo del card
                    $name = $eixo['nome'] ?? '';
                    $color_bg_card = '#F5F5F5'; // fallback
                    if ($name === 'ENERGIA EMOCIONAL') { 
                        $color_bg_card = '#A5988033';
                    } elseif ($name === 'PROPÓSITO E RELAÇÕES') { 
                        $color_bg_card = '#32403E33';
                    } elseif ($name === 'SUSTENTABILIDADE OCUPACIONAL') { 
                        $color_bg_card = '#636A9933';
                    }
                @endphp
            <!-- Barra de resumen con fondo dinámico por eixo -->
            <div style="background: {{ $color_bg_card }}; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center;">
                    <!-- Dimensão 1 -->
                    <div>
                        <div style="font-weight: bold; margin-bottom: 10px; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">{{ $eixo['dimensao1']['nome'] }}</div>
                        @php
                            $faixa1 = $eixo['dimensao1']['faixa'] ?? null;
                            if ($faixa1 === 'Alta') {
                                $bgFaixa1 = '#dc3545';
                            } elseif ($faixa1 === 'Moderada') {
                                $bgFaixa1 = '#D9BC5D';
                            } elseif ($faixa1 === 'Baixa') {
                                $bgFaixa1 = '#5DD986';
                            } else {
                                $bgFaixa1 = $coresBadges[$key][$eixo['dimensao1']['tag']] ?? '#DED8C7';
                            }
                        @endphp
                        <div class="fail-pdf" style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $bgFaixa1 }}; color: #FFFFFF;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">
                            Faixa {{ $eixo['dimensao1']['faixa'] }}
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 5px;">TOTAL</div>
                        @php
                            // Determinar cor dinâmica do TOTAL conforme a faixa (Alta/Moderada/Baixa)
                            // Espera-se que venha em $eixo['faixa_total']; caso não exista, mantém amarelo (#D9BC5D)
                            $faixaTotal = $eixo['faixa_total'] ?? null;
                            if ($faixaTotal === 'Alta') {
                                $bgTotal = '#dc3545';
                            } elseif ($faixaTotal === 'Moderada') {
                                $bgTotal = '#D9BC5D';
                            } elseif ($faixaTotal === 'Baixa') {
                                $bgTotal = '#5DD986';
                            } else {
                                $bgTotal = '#D9BC5D'; // fallback
                            }
                        @endphp
                        <div style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $bgTotal }}; color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal;     margin-top: 5px;">{{ round($eixo['total']) }}</div>
                    </div>
                    
                    <!-- Dimensão 2 -->
                    <div style="text-align: right;">
                        <div style="font-weight: bold; margin-bottom: 10px; color: #000;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">{{ $eixo['dimensao2']['nome'] }}</div>
                        @php
                            $faixa2 = $eixo['dimensao2']['faixa'] ?? null;
                            if ($faixa2 === 'Alta') {
                                $bgFaixa2 = '#dc3545';
                            } elseif ($faixa2 === 'Moderada') {
                                $bgFaixa2 = '#D9BC5D';
                            } elseif ($faixa2 === 'Baixa') {
                                $bgFaixa2 = '#5DD986';
                            } else {
                                $bgFaixa2 = $coresBadges[$key][$eixo['dimensao2']['tag']] ?? '#E8C97B';
                            }
                        @endphp
                        <div class="fail-pdf" style="display: inline-block; padding: 5px 12px; border-radius: 10px; width: 100%; text-align: center; background: {{ $bgFaixa2 }}; color: #FFFFFF;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">
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
                        if (file_exists($fullPath)) {
                            // Usar ruta relativa desde base_path() (chroot)
                            $basePath = str_replace('\\', '/', realpath(base_path()));
                            $fullPathNormalized = str_replace('\\', '/', realpath($fullPath));
                            return str_replace($basePath . '/', '', $fullPathNormalized);
                        }
                        return '';
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

