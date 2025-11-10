@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal; margin-bottom: 30px;">ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    <!-- Como Ler Seus Resultados -->
    <div style="margin-bottom: 40px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">Como Ler Seus Resultados</h2>
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
            Cada dimensão é apresentada em faixas de pontuação, representando níveis de atenção:
        </p>
        <ul style="line-height: 1.8; padding-left: 25px; margin-bottom: 20px; list-style: none;">
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;">Faixa Baixa: equilíbrio emocional saudável, sem sinais de risco.</li>
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;">Faixa Moderada: pontos de atenção que merecem acompanhamento.</li>
            <li style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 8px;">Faixa Alta: indica necessidade de reflexão e cuidado ativo.</li>
        </ul>
        
        <div style="background: #F8F5ED; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #000;font-size: 10px;font-style: normal;line-height: normal;">Importante: Nenhum resultado define você. Ele mostra apenas como você está neste momento, diante de condições específicas do ambiente e das demandas atuais.</p>
        </div>
        
        <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin-bottom: 15px;">
            As seções seguintes oferecem interpretações personalizadas, orientações práticas e sugestões de desenvolvimento. Cada texto foi cuidadosamente elaborado para promover autocompreensão, autocuidado e ação positiva.
        </p>
    </div>
    
    <!-- Dimensões (EXEM, DECI, REPR, FAPS, ASMO, EXTR) -->
    @php
        // Ordenar las variables según el orden deseado, pero solo las que tienen puntuaciones
        $ordemDimensoes = ['EXEM', 'DECI', 'REPR', 'FAPS', 'ASMO', 'EXTR'];
        $variaveisOrdenadas = [];
        
        // Ordenar variables según el orden deseado, solo si tienen puntuación (misma lógica que _resultado_emotive)
        foreach ($ordemDimensoes as $tag) {
            foreach($variaveis as $variavel) {
                if (mb_strtoupper($variavel->tag, 'UTF-8') === $tag) {
                    // Verificar si tiene puntuación (misma lógica que _resultado_emotive)
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
            'EXEM' => '#DED8C7',
            'DECI' => '#DED8C7',
            'REPR' => '#E8C97B',
            'FAPS' => '#E8C97B',
            'ASMO' => '#7BC9A8',
            'EXTR' => '#E8C97B'
        ];
    @endphp
    
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
                // Buscar la puntuación correspondiente (misma lógica que _resultado_emotive)
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
            
            <div style="margin-bottom: 35px;">
                <!-- Título de la dimensión -->
                <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal; margin-bottom: 15px;">
                    {{ $registro->nome }} ({{ mb_strtoupper($registro->tag, 'UTF-8') }})
                </h2>
                
                <!-- Barra de resultado con estilo de la imagen (fondo beige, texto blanco) -->
                <div style="background: {{ $barraBg }}; padding: 10px 15px; margin-bottom: 0; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <span style="font-weight: bold; color: #FFFFFF;font-size: 10px;font-style: normal;font-weight: 700;line-height: normal;">Faixa {{ $faixa ?? 'Indefinida' }}</span>
                    <span style="font-weight: bold; color: #FFFFFF;font-size: 10px; line-height: normal;">{{ $pontuacao ?? '–' }}</span>
                </div>
                
                <!-- Caja de texto blanca con bordes redondeados y sombra sutil -->
                <div style="background: #FFFFFF; padding: 15px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #E0E0E0; border-top: none;">
                    <p style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal; text-align: justify; margin: 0;">
                        {{ $classificacao }}
                    </p>
                </div>
            </div>
        @endforeach
    @endif
    
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
            <p style="font-size: 8px; color: #999; margin: 5px 0 0 0;">Pág. 04</p>
        </div>
    </div>
</div>

