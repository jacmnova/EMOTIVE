<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 10px;">ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    <!-- Como Ler Seus Resultados -->
    <div style="margin-bottom: 40px;">
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">Como Ler Seus Resultados</h2>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px;">
            Cada dimensão é apresentada em faixas de pontuação que indicam seu estado atual:
        </p>
        <ul style="line-height: 2; padding-left: 25px; margin-bottom: 20px;">
            <li><strong>Faixa Baixa:</strong> equilíbrio emocional saudável, sem sinais de risco.</li>
            <li><strong>Faixa Moderada:</strong> pontos de atenção que merecem acompanhamento.</li>
            <li><strong>Faixa Alta:</strong> indica necessidade de reflexão e cuidado ativo.</li>
        </ul>
        
        <div style="background: #fff9e5; border-left: 4px solid #f4b400; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #856404;">Importante: Nenhum resultado define você.</p>
        </div>
        
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px;">
            Os resultados refletem seu estado atual diante das condições e demandas do ambiente. Nas próximas seções, você encontrará interpretações personalizadas, orientações práticas e sugestões de desenvolvimento.
        </p>
    </div>
    
    <!-- Dimensões (EXEM, DECI, REPR, FAPS, ASMO, EXTR) -->
    @php
        $ordemDimensoes = ['EXEM', 'DECI', 'REPR', 'FAPS', 'ASMO', 'EXTR'];
        $pontuacoesOrdenadas = [];
        foreach ($ordemDimensoes as $tag) {
            $ponto = collect($pontuacoes)->firstWhere('tag', $tag);
            if ($ponto) {
                $pontuacoesOrdenadas[] = $ponto;
            }
        }
    @endphp
    
    @foreach($pontuacoesOrdenadas as $ponto)
        @php
            $variavel = $variaveis->firstWhere('tag', strtoupper($ponto['tag']));
            if (!$variavel) continue;
            
            $faixaClass = 'faixa-' . strtolower($ponto['faixa']);
            $faixaColor = $ponto['faixa'] === 'Baixa' ? '#28a745' : ($ponto['faixa'] === 'Moderada' ? '#ffc107' : '#dc3545');
            $faixaBg = $ponto['faixa'] === 'Baixa' ? '#d4edda' : ($ponto['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da');
            
            // Buscar descrição según faixa
            $descricao = '';
            if ($ponto['faixa'] === 'Baixa') {
                $descricao = $variavel->baixa ?? 'Seu resultado indica um estado de equilíbrio saudável nesta dimensão.';
            } elseif ($ponto['faixa'] === 'Moderada') {
                $descricao = $variavel->moderada ?? 'Seu resultado mostra pontos de atenção que merecem acompanhamento.';
            } else {
                $descricao = $variavel->alta ?? 'Seu resultado indica necessidade de reflexão e cuidado ativo nesta dimensão.';
            }
        @endphp
        
        <div style="margin-bottom: 35px;">
            <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 10px;">{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h2>
            
            <div style="background: {{ $faixaBg }}; border-left: 4px solid {{ $faixaColor }}; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: bold; color: {{ $faixaColor }};">Faixa {{ $ponto['faixa'] }}</span>
                <span style="font-size: 1.5rem; font-weight: bold; color: #333;">{{ $ponto['valor'] }}</span>
            </div>
            
            <p style="text-align: justify; line-height: 1.8; color: #555;">
                {{ $descricao }}
            </p>
        </div>
    @endforeach
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 04</p>
        </div>
    </div>
</div>

