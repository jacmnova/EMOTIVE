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
    <h1 style="color: #A4977F; font-size: {{ $titleSize }}; font-style: normal; font-weight: 700; line-height: 1.2; margin: 0 0 {{ $marginBottom }} 0; font-family: 'DejaVu Sans', sans-serif;">SEU RESULTADO E.MO.TI.VE</h1>
    
    <!-- Dados do Respondente -->
    <div style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Dados do respondente</h2>
        <div style="color: #000; font-size: {{ $textSize }}; font-style: normal; font-weight: 400; line-height: 1.4; font-family: 'DejaVu Sans', sans-serif;">
            <p style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;"><strong>Formulário:</strong> {{ $formulario->label }} – {{ $formulario->nome }}</p>
            <p style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;"><strong>Participante:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;"><strong>Data:</strong> {{ \Carbon\Carbon::parse($respostasUsuario->first()->created_at ?? now())->format('d/m/Y') }}</p>
            <p style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;"><strong>Respostas registradas:</strong> {{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() }}</p>
            <p style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;"><strong>Dimensões avaliadas:</strong> 
                @foreach($variaveis as $index => $var)
                    {{ $var->nome }}@if($index < $variaveis->count() - 1), @endif
                @endforeach.
            </p>
        </div>
    </div>
    
    <!-- Resumo por Faixa -->
    <div style="margin-bottom: {{ $isPdfMode ? '15pt' : '40px' }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 8pt 0; font-family: 'DejaVu Sans', sans-serif;">Resumo por Faixa de Pontuação</h2>
        
        @php
            $grupoAlta = [];
            $grupoModerada = [];
            $grupoBaixa = [];
            
            foreach ($variaveis as $registro) {
                foreach ($pontuacoes as $pontos) {
                    // Comparar tags en mayúsculas para evitar problemas de case
                    $tagRegistro = mb_strtoupper(trim($registro->tag ?? ''), 'UTF-8');
                    $tagPontos = mb_strtoupper(trim($pontos['tag'] ?? ''), 'UTF-8');
                    
                    if ($tagRegistro === $tagPontos) {
                        if ($pontos['faixa'] === 'Alta') {
                            $grupoAlta[] = $registro->nome . ' (' . $registro->tag . ')';
                        } elseif ($pontos['faixa'] === 'Moderada') {
                            $grupoModerada[] = $registro->nome . ' (' . $registro->tag . ')';
                        } else {
                            $grupoBaixa[] = $registro->nome . ' (' . $registro->tag . ')';
                        }
                        break;
                    }
                }
            }
        @endphp
        
        <div style="display: {{ $isPdfMode ? 'block' : 'grid' }}; {{ !$isPdfMode ? 'grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;' : '' }}">
            @if(count($grupoModerada))
                <div style="margin-top: {{ $isPdfMode ? '10pt' : '1rem' }}; border-radius: 10px; background: #F6F6F6; {{ $isPdfMode ? 'margin-bottom: 10pt;' : '' }} page-break-inside: avoid;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #D9BC5D; color: #FFF; text-align: center; font-size: {{ $isPdfMode ? '11pt' : '14px' }}; font-style: normal; font-weight: 700; line-height: normal; padding: {{ $isPdfMode ? '6pt' : '7px' }}; margin: 0; font-family: 'DejaVu Sans', sans-serif;">
                        Faixa Moderada
                    </h3>
                    <ul style="margin: 0; padding-left: {{ $isPdfMode ? '18pt' : '20px' }}; color: #333; font-size: {{ $isPdfMode ? '9pt' : '11px' }}; margin-left: {{ $isPdfMode ? '10pt' : '12px' }}; padding-top: {{ $isPdfMode ? '6pt' : '8px' }}; padding-bottom: {{ $isPdfMode ? '6pt' : '8px' }}; font-family: 'DejaVu Sans', sans-serif;">
                        @foreach($grupoModerada as $dim)
                            <li style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoBaixa))
                <div style="margin-top: {{ $isPdfMode ? '10pt' : '1rem' }}; border-radius: 10px; background: #F6F6F6; {{ $isPdfMode ? 'margin-bottom: 10pt;' : '' }} page-break-inside: avoid;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #5DD986; color: #FFF; text-align: center; font-size: {{ $isPdfMode ? '11pt' : '14px' }}; font-style: normal; font-weight: 700; line-height: normal; padding: {{ $isPdfMode ? '6pt' : '7px' }}; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Faixa Baixa</h3>
                    <ul style="margin: 0; padding-left: {{ $isPdfMode ? '18pt' : '20px' }}; color: #333; font-size: {{ $isPdfMode ? '9pt' : '11px' }}; margin-left: {{ $isPdfMode ? '10pt' : '12px' }}; padding-top: {{ $isPdfMode ? '6pt' : '8px' }}; padding-bottom: {{ $isPdfMode ? '6pt' : '8px' }}; font-family: 'DejaVu Sans', sans-serif;">
                        @foreach($grupoBaixa as $dim)
                            <li style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoAlta))
                <div style="margin-top: {{ $isPdfMode ? '10pt' : '1rem' }}; border-radius: 10px; background: #F6F6F6; {{ $isPdfMode ? 'margin-bottom: 10pt;' : '' }} page-break-inside: avoid;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #dc3545; color: #FFF; text-align: center; font-size: {{ $isPdfMode ? '11pt' : '14px' }}; font-style: normal; font-weight: 700; line-height: normal; padding: {{ $isPdfMode ? '6pt' : '7px' }}; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Faixa Alta</h3>
                    <ul style="margin: 0; padding-left: {{ $isPdfMode ? '18pt' : '20px' }}; color: #333; font-size: {{ $isPdfMode ? '9pt' : '11px' }}; margin-left: {{ $isPdfMode ? '10pt' : '12px' }}; padding-top: {{ $isPdfMode ? '6pt' : '8px' }}; padding-bottom: {{ $isPdfMode ? '6pt' : '8px' }}; font-family: 'DejaVu Sans', sans-serif;">
                        @foreach($grupoAlta as $dim)
                            <li style="margin: {{ $isPdfMode ? '3pt' : '5px' }} 0; font-family: 'DejaVu Sans', sans-serif;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="graficos-container" style="margin-bottom: {{ $marginBottom }}; page-break-inside: avoid;">
        <h2 style="color: #2E9196; font-size: {{ $subtitleSize }}; font-style: normal; font-weight: 400; line-height: 1.3; margin: 0 0 12pt 0; font-family: 'DejaVu Sans', sans-serif;">Visualização Gráfica</h2>
        
        
        
        <!-- Gráfico de Radar -->
        @if(isset($isPdf) && $isPdf && isset($imagemRadar))
            <div style="text-align: center; page-break-inside: avoid; width: 100%; margin-top: {{ $isPdfMode ? '15pt' : '20px' }};">
                @php
                    // $imagemRadar viene como ruta relativa desde base_path() desde el controlador
                    $radarPath = $imagemRadar;
                    // Verificar existencia usando ruta absoluta
                    $radarRealPath = base_path($radarPath);
                    if (!file_exists($radarRealPath)) {
                        $radarPath = null;
                    }
                @endphp
                @if($radarPath)
                    <img src="{{ $radarPath }}" alt="Gráfico de Radar" style="width: 100%; max-width: 550pt; height: auto; display: block; margin: 0 auto; page-break-inside: avoid;">
                @else
                    <p style="color: #FFC107; font-size: {{ $textSize }}; font-weight: 600; margin: 10pt 0; font-family: 'DejaVu Sans', sans-serif;">Gráfico de Radar no disponible.</p>
                @endif
            </div>
        @else
            <div class="grafico-radar-emotive-pdf" style=" max-height: 500px; text-align: center; background: #f8f9fa; padding: {{ $isPdfMode ? '15pt' : '30px' }}; border-radius: 12px; page-break-inside: avoid;">
                <canvas id="graficoRadarEmotive"></canvas>
            </div>
        @endif
    </div>
    
    <!-- Footer -->
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
    <div style="margin-top: {{ $footerMargin }}; padding-top: {{ $isPdfMode ? '12pt' : '20px' }}; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; page-break-inside: avoid; font-family: 'DejaVu Sans', sans-serif;">
        <div style="display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: center;">
            <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
            <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="E.MO.TI.VE" style="height: {{ $isPdfMode ? '20pt' : 'auto' }}; max-height: 20pt;">
        </div>
        <div style="text-align: right; display: flex; gap: {{ $isPdfMode ? '10pt' : '2vh' }}; align-items: baseline; font-family: 'DejaVu Sans', sans-serif;">
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 0; font-family: 'DejaVu Sans', sans-serif;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: {{ $isPdfMode ? '6pt' : '0.8rem' }}; color: #999; margin: 2pt 0 0 0; font-family: 'DejaVu Sans', sans-serif;">Pág. 03</p>
        </div>
    </div>
</div>
 
