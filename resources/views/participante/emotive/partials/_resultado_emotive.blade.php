@if(isset($isPdf) && $isPdf)
<div class="page-break" style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;page-break-after: always;page-break-inside: avoid;">
@else
<div style="padding: 40px; max-width: 595.28pt; width: 100%; margin: 0 auto; box-sizing: border-box;">
@endif
    <h1 class="section-title" style="color: #A4977F;font-size: 24px;font-style: normal;font-weight: 700;line-height: normal;">SEU RESULTADO E.MO.TI.VE</h1>
    
    <!-- Dados do Respondente -->
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Dados do respondente</h2>
        <div style="color: #000;font-size: 10px;font-style: normal;font-weight: 400;line-height: normal;">
            <p style="margin: 5px 0;"><strong>Formulário:</strong> {{ $formulario->label }} – {{ $formulario->nome }}</p>
            <p style="margin: 5px 0;"><strong>Participante:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p style="margin: 5px 0;"><strong>Data:</strong> {{ \Carbon\Carbon::parse($respostasUsuario->first()->created_at ?? now())->format('d/m/Y') }}</p>
            <p style="margin: 5px 0;"><strong>Respostas registradas:</strong> {{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() }}</p>
            <p style="margin: 5px 0;"><strong>Dimensões avaliadas:</strong> 
                @foreach($variaveis as $index => $var)
                    {{ $var->nome }}@if($index < $variaveis->count() - 1), @endif
                @endforeach.
            </p>
        </div>
    </div>
    
    <!-- Resumo por Faixa -->
    <div style="margin-bottom: 40px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;">Resumo por Faixa de Pontuação</h2>
        
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
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            @if(count($grupoModerada))
                <div style="margin-top: 1rem;border-radius: 10px;background: #F6F6F6;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #D9BC5D; color: #FFF; text-align: center; font-size: 14px; font-style: normal; font-weight: 700; line-height: normal; padding: 7px;">
                        Faixa Moderada
                    </h3>
                    <ul style="margin: 0;padding-left: 20px;color: #333;font-size: 11px;margin-left: 12px;">
                        @foreach($grupoModerada as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoBaixa))
                <div style="margin-top: 1rem;border-radius: 10px;background: #F6F6F6;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #5DD986; color: #FFF; text-align: center; font-size: 14px; font-style: normal; font-weight: 700; line-height: normal; padding: 7px;">Faixa Baixa</h3>
                    <ul style="margin: 0;padding-left: 20px;color: #333;font-size: 11px;margin-left: 12px;">
                        @foreach($grupoBaixa as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoAlta))
                <div style="margin-top: 1rem;border-radius: 10px;background: #F6F6F6;">
                    <h3 style="border-radius: 10px 10px 0 0; background: #dc3545; color: #FFF; text-align: center; font-size: 14px; font-style: normal; font-weight: 700; line-height: normal; padding: 7px;">Faixa Alta</h3>
                    <ul style="margin: 0;padding-left: 20px;color: #333;font-size: 11px;margin-left: 12px;">
                        @foreach($grupoAlta as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Radar E.MO.TI.VE -->
    <div style="margin-bottom: 40px;">
        <h2 class="section-subtitle" style="color: #2E9196;font-size: 16px;font-style: normal;font-weight: 400;line-height: normal;text-align: center;">Radar E.MO.TI.VE</h2>
        <div style="text-align: center; background: #f8f9fa; padding: 30px; border-radius: 12px;">
            @if(isset($isPdf) && $isPdf && isset($imagemRadar))
                @php
                    // Para PDF, la ruta viene como 'storage/graficos/...' pero necesitamos la ruta completa
                    $radarPath = str_replace('storage/', storage_path('app/public/'), $imagemRadar);
                    // Verificar que el archivo existe
                    if (!file_exists($radarPath)) {
                        $radarPath = '';
                    }
                @endphp
                @if($radarPath)
                    <img src="{{ $radarPath }}" alt="Gráfico Radar E.MO.TI.VE" style="max-width: 100%; height: auto;">
                @else
                    <p style="text-align: center; color: #666; font-size: 10px;">Gráfico no disponible</p>
                @endif
            @else
                <canvas id="graficoRadarEmotive" width="500" height="500"></canvas>
            @endif
        </div>
    </div>
    
    <!-- Footer -->
    @php
        $imgPath = function($path) {
            if (isset($isPdf) && $isPdf) {
                $fullPath = public_path($path);
                return file_exists($fullPath) ? $fullPath : '';
            }
            return asset($path);
        };
    @endphp
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 2vh;">
        <img src="{{ $imgPath('img/felipelli-logo.png') }}" alt="Fellipelli Consultoria">
        <img src="{{ $imgPath('img/emotive-logo.png') }}" alt="Descripción">
        </div>
        <div style="text-align: right;display: flex;gap: 2vh;align-items: baseline;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 03</p>
        </div>
    </div>
</div>
 
