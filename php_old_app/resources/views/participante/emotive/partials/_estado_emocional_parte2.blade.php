@php
    $isPdfMode = isset($isPdf) && $isPdf;
    $padding = $isPdfMode ? '18pt' : '40px';
    $marginBottom = $isPdfMode ? '12pt' : '30px';
    $titleSize = $isPdfMode ? '18pt' : '24px';
    $subtitleSize = $isPdfMode ? '12pt' : '16px';
    $textSize = $isPdfMode ? '10pt' : '10px';
    $footerMargin = $isPdfMode ? '20pt' : '50px';
    
    // Ordenar las variables según el orden deseado, solo las últimas 3: FAPS, ASMO, EXTR
    $ordemDimensoes = ['FAPS', 'ASMO', 'EXTR'];
    $variaveisOrdenadas = [];
    
    // Ordenar variables según el orden deseado, solo si tienen puntuación
    foreach ($ordemDimensoes as $tag) {
        foreach($variaveis as $variavel) {
            if (mb_strtoupper($variavel->tag, 'UTF-8') === $tag) {
                // Verificar si tiene puntuación
                $temPontuacao = false;
                foreach($pontuacoes as $pontos) {
                    if (mb_strtoupper($variavel->tag, 'UTF-8') === mb_strtoupper($pontos['tag'] ?? '', 'UTF-8')) {
                        $temPontuacao = true;
                        break;
                    }
                }
                if ($temPontuacao) {
                    $variaveisOrdenadas[] = $variavel;
                }
                break;
            }
        }
    }
    
    // Definir colores de barras según la dimensión (fallback)
    $coresBarras = [
        'FAPS' => '#E8C97B',
        'ASMO' => '#7BC9A8',
        'EXTR' => '#E8C97B'
    ];
@endphp
@if($isPdfMode)
<div class="page-a4">
    <div class="page-a4-content">
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $marginBottom }} 0; font-family: 'DejaVu Sans', sans-serif;">ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
@else
<div class="section-pdf-large" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $marginBottom }} 0; font-family: 'DejaVu Sans', sans-serif;">ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
@endif
    
    <!-- Dimensões (FAPS, ASMO, EXTR) -->
    @if(empty($variaveisOrdenadas) || empty($pontuacoes))
        <div style="background: #FFF3CD; padding: 20px; border-radius: 4px; border-left: 4px solid #FFC107; margin: 30px 0;">
            <p style="color: #856404; font-size: 12px; font-weight: 600; margin: 0 0 10px 0;">
                ⚠️ Informação não disponível
            </p>
            <p style="color: #856404; font-size: 10px; margin: 0; text-align: justify;">
                Não foram encontradas respostas suficientes para calcular as dimensões do estado emocional e psicossocial. 
                Por favor, certifique-se de ter completado todas as perguntas do questionário antes de gerar o relatório.
            </p>
        </div>
    @else
        @foreach($variaveisOrdenadas as $registro)
            @php
                // Buscar la puntuación correspondiente
                $pontuacao = null;
                $faixa = null;
                foreach($pontuacoes as $pontos) {
                    if (mb_strtoupper($registro->tag, 'UTF-8') === mb_strtoupper($pontos['tag'] ?? '', 'UTF-8')) {
                        $pontuacao = $pontos['valor'] ?? null;
                        $faixa = $pontos['faixa'] ?? null;
                        break;
                    }
                }

                // Usar la faixa de las puntuaciones, o calcular si no existe
                if ($faixa === null && $pontuacao !== null) {
                    if ($pontuacao <= $registro->B) {
                        $faixa = 'Baixa';
                    } elseif ($pontuacao <= $registro->M) {
                        $faixa = 'Moderada';
                    } else {
                        $faixa = 'Alta';
                    }
                }
                
                // Obtener la clasificación según la faixa
                if ($pontuacao !== null && $faixa) {
                    if ($faixa === 'Baixa') {
                        $classificacao = $registro->baixa ?? 'Seu resultado indica um estado de equilíbrio saudável nesta dimensão.';
                    } elseif ($faixa === 'Moderada') {
                        $classificacao = $registro->moderada ?? 'Seu resultado mostra pontos de atenção que merecem acompanhamento.';
                    } else {
                        $classificacao = $registro->alta ?? 'Seu resultado indica necessidade de reflexão e cuidado ativo nesta dimensão.';
                    }
                } else {
                    $faixa = null;
                    $classificacao = 'Sem dados disponíveis para esta dimensão.';
                }
                
                // Color de la barra según a faixa (dinámico)
                if ($faixa === 'Alta') {
                    $barraBg = '#dc3545'; // rojo
                } elseif ($faixa === 'Moderada') {
                    $barraBg = '#D9BC5D'; // amarillo
                } elseif ($faixa === 'Baixa') {
                    $barraBg = '#5DD986'; // verde
                } else {
                    // fallback por dimensión si no hay faixa
                    $barraBg = $coresBarras[mb_strtoupper($registro->tag, 'UTF-8')] ?? '#DED8C7';
                }
            @endphp
            
            <div style="margin-bottom: {{ $isPdfMode ? '15pt' : '35px' }}; page-break-inside: avoid;">
                <!-- Título de la dimensión -->
                <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">
                    {{ $registro->nome }} ({{ mb_strtoupper($registro->tag, 'UTF-8') }})
                </h2>
                
                <!-- Barra de resultado con estilo de la imagen (fondo beige, texto blanco) -->
                <div style="background: {{ $barraBg }}; padding: {{ $isPdfMode ? '6pt 10pt' : '10px 15px' }}; margin-bottom: 0; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; font-family: 'DejaVu Sans', sans-serif;">
                    <span style="font-weight: bold; color: #FFFFFF; font-size: {{ $textSize }}; font-style: normal; font-weight: 700; line-height: 1.2; font-family: 'DejaVu Sans', sans-serif;">Faixa {{ $faixa ?? 'Indefinida' }}</span>
                    <span style="font-weight: bold; color: #FFFFFF; font-size: {{ $textSize }}; line-height: 1.2; font-family: 'DejaVu Sans', sans-serif;">{{ $pontuacao ?? '–' }}</span>
                </div>
                
                <!-- Caja de texto blanca con bordes redondeados y sombra sutil -->
                <div style="background: #FFFFFF; padding: {{ $isPdfMode ? '10pt' : '15px' }}; border-radius: 0 0 8px 8px; border: 1px solid #E0E0E0; border-top: none; font-family: 'DejaVu Sans', sans-serif;">
                    <p style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; text-align: justify; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
                        {{ $classificacao }}
                    </p>
                </div>
            </div>
        @endforeach
    @endif
    @if($isPdfMode)
    </div>
    @include('participante.emotive.partials._footer_pdf', ['pageNumber' => '05'])
    @else
    <!-- Footer -->
    <div class="section-pdf-footer" style="margin-top: {{ $footerMargin }}; padding-top: {{ $isPdfMode ? '12pt' : '20px' }}; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; page-break-inside: avoid; font-family: 'DejaVu Sans', sans-serif;">
        <div style="display: flex; gap: {{ $isPdfMode ? '10pt' : '20px' }}; align-items: center;">
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
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: {{ $isPdfMode ? '20pt' : '30px' }}; max-height: 20pt;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: {{ $isPdfMode ? '20pt' : '30px' }}; max-height: 20pt;">
        </div>
        <div style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
            <p style="font-size: {{ $isPdfMode ? '6pt' : '8px' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: {{ $isPdfMode ? '6pt' : '8px' }}; color: #999; margin: 2pt 0 0 0; font-family: 'DejaVu Sans', sans-serif;">Pág. 05</p>
        </div>
    </div>
    @endif
</div>

