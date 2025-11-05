<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">SEU RESULTADO E.MO.TI.VE</h1>
    
    <!-- Dados do Respondente -->
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">Dados do respondente</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
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
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 20px;">Resumo por Faixa de Pontuação</h2>
        
        @php
            $grupoAlta = [];
            $grupoModerada = [];
            $grupoBaixa = [];
            
            foreach ($variaveis as $registro) {
                foreach ($pontuacoes as $pontos) {
                    if (mb_strtoupper($registro->tag, 'UTF-8') === $pontos['tag']) {
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
                <div style="background: #fff9e5; border-left: 4px solid #f4b400; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #856404; margin-bottom: 15px; font-size: 1.1rem;">Faixa Moderada</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #333;">
                        @foreach($grupoModerada as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoBaixa))
                <div style="background: #e8f1fa; border-left: 4px solid #1a73e8; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #155724; margin-bottom: 15px; font-size: 1.1rem;">Faixa Baixa</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #333;">
                        @foreach($grupoBaixa as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoAlta))
                <div style="background: #fdecea; border-left: 4px solid #d93025; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #721c24; margin-bottom: 15px; font-size: 1.1rem;">Faixa Alta</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #333;">
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
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 20px; text-align: center;">Radar E.MO.TI.VE</h2>
        <div style="text-align: center; background: #f8f9fa; padding: 30px; border-radius: 12px;">
            <canvas id="graficoRadarEmotive" width="500" height="500"></canvas>
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
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 03</p>
        </div>
    </div>
</div>

