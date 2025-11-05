<div class="page-break" style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #8B4513; font-size: 2rem; margin-bottom: 10px;">SAÚDE EMOCIONAL</h1>
    <h2 class="section-subtitle" style="color: #90EE90; font-size: 1.5rem; margin-bottom: 30px;">Análise Geral</h2>
    
    <div style="margin-bottom: 30px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px;">
            Olá, {{ $user->name }}. Este relatório é um espaço para consciência e crescimento. Nosso objetivo é destacar seus pontos fortes, apontar vulnerabilidades e oferecer orientações práticas para autocuidado e desenvolvimento pessoal.
        </p>
    </div>
    
    <!-- Seções de Saúde Emocional -->
    @php
        $dimensoesPrincipais = [
            ['tag' => 'EXEM', 'nome' => 'Exaustão Emocional', 'cor' => '#8B4513'],
            ['tag' => 'DECI', 'nome' => 'Despersonalização / Cinismo', 'cor' => '#FF8C00'],
            ['tag' => 'REPR', 'nome' => 'Realização Profissional', 'cor' => '#A9A9A9'],
            ['tag' => 'FAPS', 'nome' => 'Fatores Psicossociais', 'cor' => '#90EE90'],
            ['tag' => 'ASMO', 'nome' => 'Assédio Moral', 'cor' => '#4169E1'],
            ['tag' => 'EXTR', 'nome' => 'Excesso de Trabalho', 'cor' => '#9370DB']
        ];
    @endphp
    
    @foreach($dimensoesPrincipais as $dimInfo)
        @php
            $ponto = collect($pontuacoes)->firstWhere('tag', $dimInfo['tag']);
            if (!$ponto) continue;
            
            $variavel = $variaveis->firstWhere('tag', strtoupper($dimInfo['tag']));
            if (!$variavel) continue;
        @endphp
        
        <div style="margin-bottom: 40px; background: {{ $dimInfo['cor'] }}20; border-radius: 12px; padding: 25px; border-left: 6px solid {{ $dimInfo['cor'] }};">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="color: {{ $dimInfo['cor'] }}; font-size: 1.3rem; margin: 0;">{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h3>
                <div class="faixa-badge faixa-{{ strtolower($ponto['faixa']) }}" style="background: {{ $ponto['faixa'] === 'Baixa' ? '#d4edda' : ($ponto['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $ponto['faixa'] === 'Baixa' ? '#155724' : ($ponto['faixa'] === 'Moderada' ? '#856404' : '#721c24') }};">
                    Faixa {{ $ponto['faixa'] }}
                </div>
            </div>
            
            @php
                $descricao = '';
                $pontoForte = '';
                $orientacao = '';
                
                if ($ponto['faixa'] === 'Baixa') {
                    $descricao = $variavel->baixa ?? 'Seu resultado indica um estado de equilíbrio saudável nesta dimensão.';
                    $pontoForte = 'Você mantém um equilíbrio saudável nesta área.';
                    $orientacao = $ponto['recomendacao'] ?? 'Continue praticando hábitos saudáveis e busque apoio quando necessário.';
                } elseif ($ponto['faixa'] === 'Moderada') {
                    $descricao = $variavel->moderada ?? 'Seu resultado mostra pontos de atenção que merecem acompanhamento.';
                    if ($ponto['tag'] === 'EXEM') {
                        $descricao = 'A exaustão emocional reflete o quanto você se sente esgotado pelas demandas emocionais do trabalho. Estar na faixa moderada significa que você está lidando, mas precisa prestar atenção aos sinais de burnout.';
                        $pontoForte = 'Você está ciente de suas emoções e reconhece a necessidade de cuidar delas.';
                        $orientacao = 'Priorize momentos de descanso e autocuidado. Atividades como meditação, exercício físico regular e prática de hobbies podem ajudar a recarregar sua energia emocional.';
                    } elseif ($ponto['tag'] === 'DECI') {
                        $descricao = 'Estar na faixa moderada para despersonalização indica que você pode estar se distanciando emocionalmente do trabalho ou das pessoas ao seu redor.';
                        $pontoForte = 'Isso pode indicar um desenvolvimento saudável de autoproteção, onde você está tentando se proteger de estresse excessivo.';
                        $orientacao = 'Tente criar um equilíbrio entre proteção emocional e conexão genuína. Pequenos gestos de empatia e gratidão podem ajudar a reviver o senso de propósito e conexão.';
                    } elseif ($ponto['tag'] === 'REPR') {
                        $descricao = 'O senso de realização profissional está em um nível moderado, o que significa que você está fazendo um bom trabalho, mas há espaço para crescimento mais profundo.';
                        $pontoForte = 'Você tem uma base sólida em sua jornada profissional.';
                        $orientacao = 'Defina metas claras e mensuráveis para si mesmo. Invista em desenvolvimento pessoal e profissional participando de workshops ou buscando mentoria para melhorar suas habilidades.';
                    } elseif ($ponto['tag'] === 'FAPS') {
                        $descricao = 'Os fatores psicossociais no trabalho estão em um nível moderado, sugerindo que você está lidando com algumas pressões do ambiente de trabalho.';
                        $pontoForte = 'Você tem a resiliência necessária para enfrentar desafios.';
                        $orientacao = 'Cultive um ambiente de suporte social, buscando apoio de colegas e compartilhe suas experiências. A comunicação aberta pode melhorar significativamente seu bem-estar psicológico.';
                    } elseif ($ponto['tag'] === 'ASMO') {
                        $descricao = 'Na faixa baixa para assédio moral, é encorajador saber que você não está enfrentando esse tipo de situação no momento.';
                        $pontoForte = 'Um ambiente mais seguro emocionalmente.';
                        $orientacao = 'Continue contribuindo para um ambiente de trabalho respeitoso e solidário, e esteja atento para apoiar colegas que possam precisar.';
                    } elseif ($ponto['tag'] === 'EXTR') {
                        $descricao = 'O excesso de trabalho em nível moderado indica que você está lidando com uma carga de trabalho significativa, mas ainda gerenciável.';
                        $pontoForte = 'Sua capacidade de lidar com responsabilidades.';
                        $orientacao = 'Aprenda a delegar tarefas quando possível e defina limites claros entre trabalho e vida pessoal. Não hesite em comunicar suas necessidades e buscar ajustes quando necessário.';
                    }
                } else {
                    $descricao = $variavel->alta ?? 'Seu resultado indica necessidade de reflexão e cuidado ativo nesta dimensão.';
                    $pontoForte = 'Você reconhece a importância de buscar apoio e fazer mudanças.';
                    $orientacao = $ponto['recomendacao'] ?? 'Busque apoio profissional e faça ajustes necessários para melhorar seu bem-estar.';
                }
            @endphp
            
            <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; color: #555;">
                {{ $descricao }}
            </p>
            
            <div style="margin-top: 15px;">
                <p style="margin: 8px 0;"><strong>Ponto forte:</strong> {{ $pontoForte }}</p>
                <p style="margin: 8px 0;"><strong>Orientação prática:</strong> {{ $orientacao }}</p>
            </div>
        </div>
    @endforeach
    
    <!-- Conclusão -->
    <div style="margin-top: 40px; padding: 25px; background: #f8f9fa; border-radius: 12px;">
        <p style="text-align: justify; line-height: 1.8; font-size: 1.1rem; font-weight: bold; color: #333; margin: 0;">
            Em conclusão, você está em um ponto de equilíbrio que, embora desafiador, oferece inúmeras oportunidades de crescimento. Ao continuar investindo em autocuidado e desenvolvimento pessoal, você poderá transformar essas vulnerabilidades em áreas de força. Você já possui os recursos internos necessários para prosperar e alcançar um estado de bem-estar mais pleno. Continue a jornada com confiança e cuidado consigo mesmo.
        </p>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 09</p>
        </div>
    </div>
</div>

