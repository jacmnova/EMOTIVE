@if(isset($eixos))
<div class="card">
    <div class="card-header border-0">
        <h2 class="card-title">ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h2>
    </div>
    <div class="card-body">
        @php
            $eixosLista = [
                ['key' => 'eixo1', 'nome' => 'ENERGIA EMOCIONAL', 'desc' => 'Este eixo mostra o quanto sua energia emocional está sendo renovada ou drenada no trabalho. Ele representa o equilíbrio entre vitalidade e propósito.', 'dims' => ['Exaustão Emocional', 'Realização Profissional']],
                ['key' => 'eixo2', 'nome' => 'PROPÓSITO E RELAÇÕES', 'desc' => 'Este eixo avalia o grau de conexão emocional e relacional com o ambiente de trabalho — ou seja, se o participante sente pertencimento, confiança e reciprocidade.', 'dims' => ['Despersonalização / Cinismo', 'Fatores Psicossociais']],
                ['key' => 'eixo3', 'nome' => 'SUSTENTABILIDADE OCUPACIONAL', 'desc' => 'Este eixo reflete a relação entre o esforço exigido e o suporte ético e emocional oferecido pelo ambiente. Mostra se o trabalho é sustentável — isto é, se há equilíbrio entre pressão e respeito.', 'dims' => ['Excesso de Trabalho', 'Assédio Moral']],
            ];
        @endphp

        @foreach($eixosLista as $eixoInfo)
        @php
            $eixo = $eixos[$eixoInfo['key']];
            $interpretacao = $eixo['interpretacao_detalhada'] ?? [];
            $faixaColor = $eixo['faixa'] == 'Baixa' ? '#4CAF50' : ($eixo['faixa'] == 'Moderada' ? '#FFC107' : '#F44336');
        @endphp
        <div class="mb-4" style="border-left: 5px solid #008080; padding-left: 20px; margin-bottom: 30px;">
            <h3 style="color: #008080; font-size: 18px; margin-bottom: 10px;">{{ $eixoInfo['nome'] }}</h3>
            <p style="margin-bottom: 15px; color: #666;">{{ $eixoInfo['desc'] }}</p>
            
            <div style="background-color: #f5f5f5; border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div style="font-size: 11px; color: #666; margin-bottom: 8px;">{{ $eixoInfo['dims'][0] }}</div>
                        <div style="background-color: {{ $faixaColor }}; color: white; padding: 8px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; display: inline-block;">
                            Faixa {{ $eixo['faixa'] }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size: 11px; color: #666; margin-bottom: 8px;">TOTAL</div>
                        <div style="background-color: {{ $faixaColor }}; color: white; padding: 12px 20px; border-radius: 6px; font-size: 20px; font-weight: bold; display: inline-block;">
                            {{ round($eixo['valor']) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size: 11px; color: #666; margin-bottom: 8px;">{{ $eixoInfo['dims'][1] }}</div>
                        <div style="background-color: {{ $faixaColor }}; color: white; padding: 8px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; display: inline-block;">
                            Faixa {{ $eixo['faixa'] }}
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($interpretacao))
            <div style="margin-top: 15px; padding: 15px; background-color: #fafafa; border-radius: 6px;">
                <p style="margin-bottom: 8px;"><strong>Interpretação:</strong> {{ $interpretacao['interpretacao'] ?? '' }}</p>
                <p style="margin-bottom: 8px;"><strong>Significado Psicológico:</strong> {{ $interpretacao['significado'] ?? '' }}</p>
                <p style="margin-bottom: 0;"><strong>Orientações Práticas:</strong> {{ $interpretacao['orientacao'] ?? '' }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

