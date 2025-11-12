@php
    // Obtener faixas de las dimensiones desde ejesAnaliticos
    $eixo1 = $ejesAnaliticos['eixo1'] ?? null;
    $eixo2 = $ejesAnaliticos['eixo2'] ?? null;
    $eixo3 = $ejesAnaliticos['eixo3'] ?? null;
    
    // Obtener interpretaciones de los eixos
    $interpEixo1 = [];
    $interpEixo2 = [];
    $interpEixo3 = [];
    
    if ($eixo1 && isset($eixo1['interpretacao'])) {
        $interpEixo1 = [
            'significado' => $eixo1['interpretacao']['significado'] ?? '',
            'orientacoes' => $eixo1['interpretacao']['orientacoes'] ?? ''
        ];
    }
    
    if ($eixo2 && isset($eixo2['interpretacao'])) {
        $interpEixo2 = [
            'significado' => $eixo2['interpretacao']['significado'] ?? '',
            'orientacoes' => $eixo2['interpretacao']['orientacoes'] ?? ''
        ];
    }
    
    if ($eixo3 && isset($eixo3['interpretacao'])) {
        $interpEixo3 = [
            'significado' => $eixo3['interpretacao']['significado'] ?? '',
            'orientacoes' => $eixo3['interpretacao']['orientacoes'] ?? ''
        ];
    }
    
    // Función para obtener datos de una dimensión (misma lógica que _resultado_emotive)
    $dadosEXEM = null;
    $dadosDECI = null;
    $dadosREPR = null;
    $dadosFAPS = null;
    $dadosASMO = null;
    $dadosEXTR = null;
    
    // Procesar cada dimensión usando la misma lógica que _resultado_emotive
    foreach ($variaveis as $registro) {
        // Buscar la puntuación correspondiente (misma lógica que _resultado_emotive)
        $pontuacao = null;
        $faixa = null;
        foreach($pontuacoes as $pontos) {
            if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
                $pontuacao = $pontos['valor'] ?? null;
                $faixa = $pontos['faixa'] ?? null;
                break;
            }
        }
        
        // Si no se encontró faixa pero hay puntuación, calcularla
        if ($faixa === null && $pontuacao !== null) {
            if ($pontuacao <= $registro->B) {
                $faixa = 'Baixa';
            } elseif ($pontuacao <= $registro->M) {
                $faixa = 'Moderada';
            } else {
                $faixa = 'Alta';
            }
        }
        
        // Obtener descripción según faixa
        $descricao = '';
        if ($pontuacao !== null && $faixa) {
            if ($faixa === 'Baixa') {
                $descricao = $registro->baixa ?? '';
            } elseif ($faixa === 'Moderada') {
                $descricao = $registro->moderada ?? '';
            } else {
                $descricao = $registro->alta ?? '';
            }
        }
        
        // Asignar según el tag
        $tagUpper = mb_strtoupper($registro->tag, 'UTF-8');
        if ($tagUpper === 'EXEM') {
            $dadosEXEM = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        } elseif ($tagUpper === 'DECI') {
            $dadosDECI = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        } elseif ($tagUpper === 'REPR') {
            $dadosREPR = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        } elseif ($tagUpper === 'FAPS') {
            $dadosFAPS = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        } elseif ($tagUpper === 'ASMO') {
            $dadosASMO = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        } elseif ($tagUpper === 'EXTR') {
            $dadosEXTR = ['variavel' => $registro, 'faixa' => $faixa, 'pontuacao' => $pontuacao, 'descricao' => $descricao];
        }
    }
    
    // Función para obtener color del badge según faixa
    function obterCorBadge($faixa) {
        if ($faixa === 'Baixa') return '#7BC9A8';
        if ($faixa === 'Moderada') return '#E8C97B';
        return '#DC3545'; // Alta
    }
@endphp

@php
    $isPdfMode = isset($isPdf) && $isPdf;
    $padding = $isPdfMode ? '18pt' : '40px';
    $marginBottom = $isPdfMode ? '12pt' : '30px';
    $titleSize = $isPdfMode ? '18pt' : '24px';
    $subtitleSize = $isPdfMode ? '12pt' : '16px';
    $textSize = $isPdfMode ? '10pt' : '10px';
    $footerMargin = $isPdfMode ? '20pt' : '50px';
@endphp
<!-- PRIMERA PÁGINA: EXEM, DECI, REPR -->
@if($isPdfMode)
<div class="section-pdf-large" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box; font-family: 'DejaVu Sans', sans-serif;">
@else
<div class="section-pdf-large" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $isPdfMode ? '10pt' : '15px' }} 0; font-family: 'DejaVu Sans', sans-serif;">SAÚDE EMOCIONAL</h1>
    <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 {{ $isPdfMode ? '10pt' : '20px' }} 0; font-family: 'DejaVu Sans', sans-serif;">Análise Geral</h2>
    
    <div style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; text-align: justify; margin: 0 0 {{ $isPdfMode ? '10pt' : '20px' }} 0; font-family: 'DejaVu Sans', sans-serif;">
            Olá, {{ $user->name }}. Este relatório é um espaço para consciência e crescimento. Nosso objetivo é destacar seus pontos fortes, apontar vulnerabilidades e oferecer orientações práticas para autocuidado e desenvolvimento pessoal.
        </p>
    </div>
    
    <!-- EXAUSTÃO EMOCIONAL (EXEM) -->
    @if($dadosEXEM && $dadosEXEM['faixa'])
        <div style="margin-bottom: {{ $isPdfMode ? '15pt' : '35px' }}; page-break-inside: avoid;">
            <!-- Barra de título -->
            <div style="background: #4F3F23; padding: {{ $isPdfMode ? '8pt 10pt' : '12px 15px' }}; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; font-family: 'DejaVu Sans', sans-serif;">
                <h3 style="color: #FFFFFF; font-size: {{ $isPdfMode ? '10pt' : '14px' }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Exaustão Emocional (ExEm)</h3>
                <div style="display: flex; align-items: center; gap: {{ $isPdfMode ? '3pt' : '4px' }}; color: #FFFFFF; padding: {{ $isPdfMode ? '4pt 8pt' : '5px 12px' }}; border-radius: 20px; font-size: {{ $textSize }}; font-weight: 700; font-family: 'DejaVu Sans', sans-serif;">
                <div style="background: {{ obterCorBadge($dadosEXEM['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>     
                Faixa {{ $dadosEXEM['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: {{ $isPdfMode ? '12pt' : '20px' }}; border-radius: 0 0 8px 8px; border: 1px solid #E0E0E0; border-top: none; font-family: 'DejaVu Sans', sans-serif;">
                <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; text-align: justify; margin: 0 0 {{ $isPdfMode ? '10pt' : '15px' }} 0; font-family: 'DejaVu Sans', sans-serif;">
                    {{ $dadosEXEM['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: {{ $isPdfMode ? '10pt' : '15px' }}; font-family: 'DejaVu Sans', sans-serif;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: {{ $isPdfMode ? '15pt' : '20px' }}; height: {{ $isPdfMode ? '15pt' : '20px' }}; margin-right: {{ $isPdfMode ? '8pt' : '10px' }}; flex-shrink: 0;">
                    <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; text-align: justify; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
                        <strong style="font-weight: 700; font-family: 'DejaVu Sans', sans-serif;">Ponto forte:</strong> {{ $interpEixo1['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start; font-family: 'DejaVu Sans', sans-serif;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: {{ $isPdfMode ? '15pt' : '20px' }}; height: {{ $isPdfMode ? '15pt' : '20px' }}; margin-right: {{ $isPdfMode ? '8pt' : '10px' }}; flex-shrink: 0;">
                    <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; text-align: justify; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
                        <strong style="font-weight: 700; font-family: 'DejaVu Sans', sans-serif;">Orientação prática:</strong> {{ $interpEixo1['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- DESPERSONALIZAÇÃO / CINISMO (DECI) -->
    @if($dadosDECI && $dadosDECI['faixa'])
        <div style="margin-bottom: 35px;">
            <!-- Barra de título -->
            <div style="background: #D79648; padding: 12px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal; margin: 0;">Despersonalização / Cinismo (DeCi)</h3>
                <div style="display: flex;align-items: center;gap: 4px; color: #FFFFFF; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;">
                <div style="background: {{ obterCorBadge($dadosDECI['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>     
                Faixa {{ $dadosDECI['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: 20px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
                    {{ $dadosDECI['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Ponto forte:</strong> {{ $interpEixo2['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Orientação prática:</strong> {{ $interpEixo2['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- REALIZAÇÃO PROFISSIONAL (REPR) -->
    @if($dadosREPR && $dadosREPR['faixa'])
        <div style="margin-bottom: 35px;">
            <!-- Barra de título -->
            <div style="background: #A59880; padding: 12px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal; margin: 0;">Realização Profissional (RePr)</h3>
                <div style="display: flex;align-items: center;gap: 4px; color: #FFFFFF; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;">
                <div style="background: {{ obterCorBadge($dadosREPR['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>     
                Faixa {{ $dadosREPR['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: 20px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
                    {{ $dadosREPR['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Ponto forte:</strong> {{ $interpEixo1['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Orientação prática:</strong> {{ $interpEixo1['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 20px; align-items: center;">
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: 30px;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: 30px;">
        </div>
        <div style="text-align: right;">
            <p style="font-size: 8px; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 08</p>
        </div>
    </div>
</div>

<!-- SEGUNDA PÁGINA: FAPS, ASMO, EXTR -->
@if($isPdfMode)
<div class="section-pdf-large" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box; font-family: 'DejaVu Sans', sans-serif;">
@else
<div class="section-pdf-large" style="padding: {{ $padding }}; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $isPdfMode ? '10pt' : '15px' }} 0; font-family: 'DejaVu Sans', sans-serif;">SAÚDE EMOCIONAL</h1>
    <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 20px;">Análise Geral</h2>
    
    <!-- FATORES PSICOSSOCIAIS (FAPS) -->
    @if($dadosFAPS && $dadosFAPS['faixa'])
        <div style="margin-bottom: 35px;">
            <!-- Barra de título -->
            <div style="background: #62807C; padding: 12px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal; margin: 0;">Fatores Psicossociais (FaPs)</h3>
                <div style="display: flex;align-items: center;gap: 4px; color: #FFFFFF; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;">
                <div style="background: {{ obterCorBadge($dadosFAPS['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>   
                    Faixa {{ $dadosFAPS['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: 20px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
                    {{ $dadosFAPS['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Ponto forte:</strong> {{ $interpEixo2['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Orientação prática:</strong> {{ $interpEixo2['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- ASSÉDIO MORAL (ASMO) -->
    @if($dadosASMO && $dadosASMO['faixa'])
        <div style="margin-bottom: 35px;">
            <!-- Barra de título -->
            <div style="background: #0D6486; padding: 12px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal; margin: 0;">Assédio Moral (AsMo)</h3>
                <div style="display: flex;align-items: center;gap: 4px; color: #FFFFFF; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;">
                <div style="background: {{ obterCorBadge($dadosASMO['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>    
                    Faixa {{ $dadosASMO['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: 20px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
                    {{ $dadosASMO['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Ponto forte:</strong> {{ $interpEixo3['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Orientação prática:</strong> {{ $interpEixo3['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- EXCESSO DE TRABALHO (EXTR) -->
    @if($dadosEXTR && $dadosEXTR['faixa'])
        <div style="margin-bottom: 35px;">
            <!-- Barra de título -->
            <div style="background: #636A99; padding: 12px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: #FFFFFF;font-size: 14px;font-style: normal;font-weight: 700;line-height: normal; margin: 0;">Excesso de Trabalho (ExTr)</h3>
                <div style="display: flex;align-items: center;gap: 4px; color: #FFFFFF; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700;">
                <div style="background: {{ obterCorBadge($dadosEXTR['faixa']) }};width: 10px;height: 10px;border-radius: 100px;"></div>    
                Faixa {{ $dadosEXTR['faixa'] }}
                </div>
            </div>
            
            <!-- Contenido -->
            <div style="background: #F6F6F6; padding: 20px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
                    {{ $dadosEXTR['descricao'] }}
                </p>
                
                <!-- Ponto forte -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
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
                    <img src="{{ $imgPath('img/icon_saude_1.png') }}" alt="Ponto forte" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Ponto forte:</strong> {{ $interpEixo3['significado'] }}
                    </p>
                </div>
                
                <!-- Orientação prática -->
                <div style="display: flex; align-items: flex-start;">
                    <img src="{{ $imgPath('img/icon_saude_2.png') }}" alt="Orientação prática" style="width: 20px; height: 20px; margin-right: 10px; flex-shrink: 0;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        <strong style="font-weight: 700;">Orientação prática:</strong> {{ $interpEixo3['orientacoes'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Conclusão -->
    <div style="margin-top: 40px; margin-bottom: 30px;">
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
            Em conclusão, você está em um ponto de equilíbrio que, embora desafiador, oferece inúmeras oportunidades de crescimento. Ao continuar investindo em autocuidado e desenvolvimento pessoal, você poderá transformar essas vulnerabilidades em áreas de força. Você já possui os recursos internos necessários para prosperar e alcançar um estado de bem-estar mais pleno. Continue a jornada com confiança e cuidado consigo mesmo.
        </p>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 20px; align-items: center;">
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: 30px;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: 30px;">
        </div>
        <div style="text-align: right;">
            <p style="font-size: 8px; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 09</p>
        </div>
    </div>
</div>
