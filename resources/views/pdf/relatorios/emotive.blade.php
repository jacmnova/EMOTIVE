<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>RELATÓRIO E.MO.TI.VE® | {{ $user->name }}</title>
    <style>
        @page {
            margin: 0;
        }

        @page :not(:first) {
            margin-top: 80px;
            margin-bottom: 60px;
            margin-left: 60px;
            margin-right: 60px;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.6;
        }

        .page-break {
            page-break-after: always;
        }

        /* PORTADA */
        .capa {
            width: 100%;
            height: 100vh;
            position: relative;
            background: linear-gradient(180deg, #1a3a5a 0%, #008080 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
        }

        .logo-fellipelli {
            font-size: 24px;
            font-weight: normal;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .tagline-fellipelli {
            font-size: 10px;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .logo-emotive {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            margin-bottom: 10px;
        }

        .tagline-emotive {
            font-size: 14px;
            margin-bottom: 60px;
        }

        .waveform {
            width: 100%;
            height: 4px;
            background: #00CED1;
            margin: 40px 0;
            position: relative;
        }

        .waveform::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 10px,
                #00CED1 10px,
                #00CED1 12px
            );
        }

        .info-box {
            background: white;
            border-radius: 12px;
            padding: 30px;
            color: #333;
            text-align: center;
            max-width: 500px;
            margin-top: 40px;
        }

        .info-box .nome {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-box .titulo {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .info-box .data {
            font-size: 12px;
            color: #666;
        }

        /* ESTILOS GENERALES */
        h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: left;
        }

        h2 {
            font-size: 18px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 15px;
            text-align: left;
        }

        h3 {
            font-size: 16px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 12px;
        }

        h4 {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .section {
            margin-bottom: 30px;
        }

        /* BADGES Y FAIXAS */
        .faixa-baixa {
            background-color: #4CAF50;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-moderada {
            background-color: #FFC107;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-alta {
            background-color: #F44336;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        /* CAJAS DE RESULTADO */
        .result-box {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #FFC107;
        }

        .result-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .result-box-score {
            font-size: 24px;
            font-weight: bold;
            color: #FFC107;
            background-color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
        }

        /* EIXOS ANALÍTICOS */
        .eixo-box {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 5px solid #008080;
        }

        .eixo-header {
            font-size: 16px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 15px;
        }

        .eixo-dimensions {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
            background-color: #fff;
            padding: 15px;
            border-radius: 6px;
        }

        .dimension-item {
            text-align: center;
            flex: 1;
        }

        .dimension-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
        }

        .dimension-bar {
            height: 30px;
            background-color: #FFC107;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .dimension-total {
            background-color: #FFC107;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 10px auto;
            display: inline-block;
        }

        /* GRÁFICO DE RISCO */
        .risk-bar {
            width: 100%;
            height: 40px;
            background-color: #e0e0e0;
            border-radius: 20px;
            position: relative;
            margin: 20px 0;
            display: flex;
            overflow: hidden;
        }

        .risk-segment {
            flex: 1;
            border-right: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }

        .risk-segment:last-child {
            border-right: none;
        }

        .risk-indicator {
            position: absolute;
            height: 100%;
            background-color: #FFC107;
            border-radius: 20px;
            left: 0;
            z-index: 1;
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #008080;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* QUOTE BOXES */
        .quote-box-teal {
            background-color: #B2DFDB;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-style: italic;
            color: #333;
            border-left: 4px solid #008080;
        }

        .quote-box-gray {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-style: italic;
            color: #333;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #666;
        }

        .footer-logos {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-logo-text {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }

        .footer-page {
            text-align: right;
        }

        /* RADAR CHART */
        .radar-container {
            text-align: center;
            margin: 30px 0;
        }

        .radar-container img {
            max-width: 100%;
            height: auto;
        }

        /* LISTAS */
        ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        li {
            margin-bottom: 8px;
        }

        /* BULLET DORADO */
        .bullet-gold {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #FFC107;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* ZONA DE ATENÇÃO */
        .zona-box {
            background-color: #fff3cd;
            border-left: 4px solid #FFC107;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .zona-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

{{-- PÁGINA 1: PORTADA --}}
<div class="capa page-break">
    <div class="logo-fellipelli">fellipelli</div>
    <div class="tagline-fellipelli">desenvolvimento pessoal e organizacional</div>
    
    <div class="logo-emotive">E.MO.TI.VE</div>
    <div class="tagline-emotive">Burnout e Bem-estar</div>
    
    <div class="waveform"></div>
    
    <div class="info-box">
        <div class="nome">{{ $user->name }}</div>
        <div class="titulo">Relatório Questionário de Riscos Psicossociais</div>
        <div class="data">Respondido em {{ $dataResposta }}</div>
    </div>
</div>

{{-- PÁGINA 2: RELATÓRIO E.MO.TI.VE --}}
<div class="page-break">
    <h1>RELATÓRIO E.MO.TI.VE®</h1>
    
    <div class="section">
        <h2>Ferramenta de Autoconhecimento e Prevenção de Riscos Psicossociais</h2>
        <p>O E.MO.TI.VE® é uma ferramenta de autoconhecimento e prevenção de riscos psicossociais no trabalho, baseada em evidências científicas e nas normas regulamentadoras brasileiras. Este relatório apresenta uma análise detalhada das suas respostas, oferecendo insights sobre seu estado emocional, nível de engajamento e sustentabilidade ocupacional.</p>
        
        <p><strong>Mais do que medir</strong> — oferece uma jornada de autoconhecimento.</p>
        <p><strong>Mais do que um diagnóstico</strong> — fornece orientações práticas para o desenvolvimento pessoal e profissional.</p>
        
        <div class="quote-box-teal">
            "Saúde emocional não é ausência de estresse, mas a capacidade de reconhecê-lo e se fortalecer diante dele."
        </div>
    </div>

    <div class="section">
        <h2>Finalidade do Instrumento</h2>
        <p>O E.MO.TI.VE® identifica e avalia seis dimensões psicossociais fundamentais relacionadas ao bem-estar e ao risco de burnout no ambiente de trabalho. Com base nas suas respostas, este relatório ajuda você a:</p>
        <ul>
            <li>Compreender seu estado emocional e psicossocial atual</li>
            <li>Identificar áreas de força e vulnerabilidade</li>
            <li>Reconhecer sinais precoces de desequilíbrio</li>
            <li>Receber orientações práticas para autocuidado e desenvolvimento</li>
        </ul>
    </div>

    <div class="section">
        <h2>Base Normativa e Científica</h2>
        <ol>
            <li>Normativa brasileira: <strong>NR-1 (Portaria 6.730/2020)</strong> — regulamentação sobre riscos psicossociais no trabalho</li>
            <li>Modelo científico: <strong>Modelo MBI (Maslach Burnout Inventory)</strong> — referência internacional para avaliação de burnout</li>
        </ol>
    </div>

    <div class="section">
        <h2>Ética, Confidencialidade e Responsabilidade Compartilhada</h2>
        <p>Este instrumento é confidencial e não-clínico. Os dados coletados são utilizados exclusivamente para fins de autoconhecimento e desenvolvimento pessoal. A responsabilidade pelo bem-estar é compartilhada entre:</p>
        <ul>
            <li>Você: autoconhecimento, autocuidado e ações práticas de melhoria</li>
            <li>Organização: criação de ambientes saudáveis, suporte e recursos adequados</li>
            <li>Profissionais de saúde: acompanhamento quando necessário</li>
        </ul>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 01</div>
    </div>
</div>

{{-- PÁGINA 3: ESTRUTURA DO MODELO --}}
<div class="page-break">
    <h1>Estrutura do Modelo E.MO.TI.VE</h1>
    
    <p style="margin-bottom: 30px;">O modelo E.MO.TI.VE avalia seis dimensões principais que formam um "retrato de equilíbrio psicossocial":</p>

    <div class="section">
        <h3 style="color: #8B4513;">EIXO 1: ENERGIA EMOCIONAL</h3>
        <div style="margin-left: 20px; margin-bottom: 20px;">
            <h4 style="color: #654321;">• Exaustão Emocional (ExEm)</h4>
            <p><strong>Conceito:</strong> Desgaste intenso, sensação de não ter energia.</p>
            <p><strong>Prático:</strong> Pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.</p>
            <p><strong>Didático:</strong> Se estiver alta, pense em revisar prazos, negociar demandas e buscar apoio.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px;">
            <h4 style="color: #DAA520;">• Realização Profissional (RePr)</h4>
            <p><strong>Conceito:</strong> Sensação de competência e propósito no trabalho.</p>
            <p><strong>Prático:</strong> Quando baixa, pode gerar insegurança, desvalorização e até desmotivação.</p>
            <p><strong>Didático:</strong> Vale pedir feedbacks, investir em desenvolvimento e resgatar pequenas conquistas.</p>
        </div>
    </div>

    <div class="section">
        <h3 style="color: #FF8C00;">EIXO 2: PROPÓSITO E RELAÇÕES</h3>
        <div style="margin-left: 20px; margin-bottom: 20px;">
            <h4 style="color: #FF8C00;">• Despersonalização / Cinismo (DeCi)</h4>
            <p><strong>Conceito:</strong> Distanciamento emocional do trabalho e das pessoas.</p>
            <p><strong>Prático:</strong> Tratar colegas com frieza, indiferença ou ironia.</p>
            <p><strong>Didático:</strong> Se estiver alto, é importante reconectar com o propósito do que faz e retomar vínculos de confiança.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px;">
            <h4 style="color: #556B2F;">• Fatores Psicossociais (FaPs)</h4>
            <p><strong>Conceito:</strong> Condições organizacionais — clareza de papéis, suporte, comunicação.</p>
            <p><strong>Prático:</strong> Pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.</p>
            <p><strong>Didático:</strong> Tente identificar onde a comunicação falha e proponha pequenas ações de melhoria.</p>
        </div>
    </div>

    <div class="section">
        <h3 style="color: #4169E1;">EIXO 3: SUSTENTABILIDADE OCUPACIONAL</h3>
        <div style="margin-left: 20px; margin-bottom: 20px;">
            <h4 style="color: #191970;">• Assédio Moral (AsMo)</h4>
            <p><strong>Conceito:</strong> Comportamentos abusivos, constrangedores ou humilhantes.</p>
            <p><strong>Prático:</strong> Piadas ofensivas, isolamento, críticas públicas.</p>
            <p><strong>Didático:</strong> Se identificar sinais, não ignore. Busque diálogo e, se necessário, canais formais de apoio.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px;">
            <h4 style="color: #483D8B;">• Excesso de Trabalho (ExTr)</h4>
            <p><strong>Conceito:</strong> Quando a carga de tarefas excede os limites pessoais.</p>
            <p><strong>Prático:</strong> Longas jornadas, sem pausas, sem equilíbrio com vida pessoal.</p>
            <p><strong>Didático:</strong> É necessário revisar prioridades, delegar e resgatar tempo para descanso.</p>
        </div>
    </div>

    <div class="section">
        <p><strong>Essas dimensões se organizam em três eixos analíticos:</strong></p>
        <ul>
            <li><strong>Energia Emocional:</strong> entre o cansaço e a vitalidade.</li>
            <li><strong>Propósito e Relações:</strong> entre o engajamento e o distanciamento.</li>
            <li><strong>Sustentabilidade Ocupacional:</strong> entre o esforço e o suporte recebido.</li>
        </ul>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 02</div>
    </div>
</div>

{{-- PÁGINA 4: SEU RESULTADO E.MO.TI.VE --}}
<div class="page-break">
    <h1>SEU RESULTADO E.MO.TI.VE</h1>
    
    <div class="section">
        <h2>Dados do respondente</h2>
        <table>
            <tr>
                <th style="width: 30%;">Formulário</th>
                <td>{{ $formulario->label }} – {{ $formulario->nome }}</td>
            </tr>
            <tr>
                <th>Participante</th>
                <td>{{ $user->name }} ({{ $user->email }})</td>
            </tr>
            <tr>
                <th>Data</th>
                <td>{{ $dataResposta }}</td>
            </tr>
            <tr>
                <th>Respostas registradas</th>
                <td>{{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Dimensões avaliadas</th>
                <td>{{ $variaveis->pluck('nome')->join(', ') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Resumo por Faixa de Pontuação</h2>
        
        @php
            $grupoAlta = [];
            $grupoModerada = [];
            $grupoBaixa = [];
            
            foreach ($pontuacoes as $ponto) {
                if ($ponto['faixa'] == 'Alta') {
                    $grupoAlta[] = $ponto['nome'] . ' (' . $ponto['tag'] . ')';
                } elseif ($ponto['faixa'] == 'Moderada') {
                    $grupoModerada[] = $ponto['nome'] . ' (' . $ponto['tag'] . ')';
                } else {
                    $grupoBaixa[] = $ponto['nome'] . ' (' . $ponto['tag'] . ')';
                }
            }
        @endphp

        @if(count($grupoModerada) > 0)
        <div style="background-color: #DAA520; color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 14px;">Faixa Moderada</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoModerada as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoBaixa) > 0)
        <div style="background-color: #4CAF50; color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 14px;">Faixa Baixa</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoBaixa as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoAlta) > 0)
        <div style="background-color: #F44336; color: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 14px;">Faixa Alta</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoAlta as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="section">
        <h2>Radar E.MO.TI.VE</h2>
        <div class="radar-container">
            <img src="{{ public_path($imagemRadar) }}" alt="Radar E.MO.TI.VE" style="max-width: 100%; height: auto;">
        </div>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 03</div>
    </div>
</div>

{{-- PÁGINA 5: ESTADO EMOCIONAL E PSICOSSOCIAL --}}
<div class="page-break">
    <h1>ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    <div class="section">
        <h2>Como Ler Seus Resultados</h2>
        <p>As dimensões são apresentadas em faixas de pontuação que representam níveis de atenção:</p>
        <ul>
            <li><strong>Faixa Baixa:</strong> equilíbrio emocional saudável, sem sinais de risco.</li>
            <li><strong>Faixa Moderada:</strong> pontos de atenção que merecem acompanhamento.</li>
            <li><strong>Faixa Alta:</strong> indica necessidade de reflexão e cuidado ativo.</li>
        </ul>
        
        <div class="zona-box">
            <div class="zona-title">Importante:</div>
            <p style="margin: 0;"><strong>Nenhum resultado define você.</strong> Os resultados refletem o estado atual em condições específicas. As seções seguintes oferecem interpretações personalizadas e sugestões práticas.</p>
        </div>
    </div>

    @foreach($pontuacoes as $ponto)
    <div class="section">
        <h3>{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h3>
        <div class="result-box">
            <div class="result-box-header">
                <span class="faixa-{{ strtolower($ponto['faixa']) }}">{{ $ponto['faixa'] }}</span>
                <span class="result-box-score">{{ round($ponto['normalizada']) }}</span>
            </div>
            <p>
                @php
                    $variavel = $variaveis->firstWhere('tag', strtoupper($ponto['tag']));
                    $texto = '';
                    if ($variavel) {
                        if ($ponto['faixa'] == 'Baixa') {
                            $texto = $variavel->baixa ?? '';
                        } elseif ($ponto['faixa'] == 'Moderada') {
                            $texto = $variavel->moderada ?? '';
                        } else {
                            $texto = $variavel->alta ?? '';
                        }
                    }
                @endphp
                {{ $texto }}
            </p>
        </div>
    </div>
    @endforeach

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 04</div>
    </div>
</div>

{{-- PÁGINA 6: ÍNDICE EIXOS ANALÍTICOS --}}
<div class="page-break">
    <h1>ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h1>
    
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
    @endphp
    <div class="section">
        <h3 style="color: #008080; font-size: 18px;">{{ $eixoInfo['nome'] }}</h3>
        <p style="margin-bottom: 15px;">{{ $eixoInfo['desc'] }}</p>
        
        <div style="background-color: #f5f5f5; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-around; align-items: center;">
                <div style="text-align: center; flex: 1;">
                    <div style="font-size: 10px; color: #666; margin-bottom: 8px;">{{ $eixoInfo['dims'][0] }}</div>
                    <div class="dimension-bar" style="width: 80%; margin: 0 auto;">
                        <span class="faixa-{{ strtolower($eixo['faixa']) }}">Faixa {{ $eixo['faixa'] }}</span>
                    </div>
                </div>
                <div style="text-align: center; flex: 1;">
                    <div style="font-size: 10px; color: #666; margin-bottom: 8px;">TOTAL</div>
                    <div class="dimension-total">{{ round($eixo['valor']) }}</div>
                </div>
                <div style="text-align: center; flex: 1;">
                    <div style="font-size: 10px; color: #666; margin-bottom: 8px;">{{ $eixoInfo['dims'][1] }}</div>
                    <div class="dimension-bar" style="width: 80%; margin: 0 auto;">
                        <span class="faixa-{{ strtolower($eixo['faixa']) }}">Faixa {{ $eixo['faixa'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($interpretacao))
        <div style="margin-top: 15px;">
            <p><strong>Interpretação:</strong> {{ $interpretacao['interpretacao'] ?? '' }}</p>
            <p><strong>Significado Psicológico:</strong> {{ $interpretacao['significado'] ?? '' }}</p>
            <p><strong>Orientações Práticas:</strong> {{ $interpretacao['orientacao'] ?? '' }}</p>
        </div>
        @endif
    </div>
    @endforeach

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 06</div>
    </div>
</div>

{{-- PÁGINA 7: RISCO DE DESCARRILAMENTO --}}
<div class="page-break">
    <h1>RISCO DE DESCARRILAMENTO EMOCIONAL E OCUPACIONAL</h1>
    
    <div class="section">
        <p>O risco de descarrilamento representa a probabilidade de perda de equilíbrio emocional, motivacional e funcional no trabalho, a partir das interações entre os três eixos analíticos do modelo E.MO.TI.VE®:</p>
        <ol>
            <li><strong>Energia Emocional</strong> — capacidade de sustentar vitalidade e propósito.</li>
            <li><strong>Propósito e Relações</strong> — qualidade das conexões e do engajamento social.</li>
            <li><strong>Sustentabilidade Ocupacional</strong> — equilíbrio entre esforço e suporte recebido.</li>
        </ol>
        <p>Cada eixo gera um índice individual (0 a 100) e, ao serem combinados, formam o Índice Integrado de Descarrilamento (IID).</p>
    </div>

    @php
        $iid = $eixos['iid'];
        $percentual = $iid['valor'];
        $segmentoWidth = 25; // 25% por segmento
        $indicatorWidth = min(100, ($percentual / 100) * 100);
    @endphp

    <div class="section">
        <h3>Classificação do Risco</h3>
        <div class="risk-bar">
            <div class="risk-indicator" style="width: {{ $indicatorWidth }}%;"></div>
            <div class="risk-segment">Baixo<br>(0-40)</div>
            <div class="risk-segment">Médio<br>(41-65)</div>
            <div class="risk-segment">Atenção<br>(66-89)</div>
            <div class="risk-segment">Alto<br>(90-100)</div>
        </div>
        
        <div style="text-align: center; margin: 20px 0;">
            <span style="font-size: 18px; color: #FFC107; font-weight: bold;">
                Pontuação = {{ round($iid['valor']) }} - {{ $iid['zona'] }}
            </span>
        </div>
    </div>

    <div class="section">
        <p><strong>{{ $iid['descricao'] }}</strong></p>
        <p>{{ $iid['interpretacao'] }}</p>
        <p>{{ $iid['acao'] }}</p>
    </div>

    <div class="quote-box-teal" style="margin-top: 30px;">
        "O descarrilamento emocional raramente ocorre de forma súbita — ele é o resultado de pequenas desconexões acumuladas. Reconhecer os sinais precoces é o maior ato de autocuidado e responsabilidade profissional."
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 07</div>
    </div>
</div>

{{-- PÁGINA 8: SAÚDE EMOCIONAL - ANÁLISE GERAL --}}
<div class="page-break">
    <h1 style="color: #8B4513; font-size: 22px;">SAÚDE EMOCIONAL</h1>
    <h2 style="color: #008080; font-size: 16px; margin-bottom: 20px;">Análise Geral</h2>
    
    <div class="section">
        <p>Bem-vindo ao seu relatório de saúde emocional. Este documento foi criado para explorar seus resultados, destacar pontos fortes e oferecer orientações práticas para autocuidado e desenvolvimento pessoal.</p>
        
        <p>Com base nas suas respostas, identificamos padrões que refletem seu estado atual de bem-estar no trabalho. Lembre-se: esses resultados são um ponto de partida para reflexão e ação, não um diagnóstico definitivo.</p>
    </div>

    @php
        $dimensoesPrincipais = [
            ['tag' => 'EXEM', 'nome' => 'Exaustão Emocional (ExEm)', 'cor' => '#8B4513'],
            ['tag' => 'DECI', 'nome' => 'Despersonalização / Cinismo (DeCi)', 'cor' => '#FF8C00'],
            ['tag' => 'REPR', 'nome' => 'Realização Profissional (RePr)', 'cor' => '#DAA520'],
        ];
    @endphp

    @foreach($dimensoesPrincipais as $dimInfo)
    @php
        $ponto = collect($pontuacoes)->firstWhere('tag', $dimInfo['tag']);
        if (!$ponto) continue;
        $variavel = $variaveis->firstWhere('tag', $dimInfo['tag']);
    @endphp
    <div class="section">
        <div style="background-color: {{ $dimInfo['cor'] }}; color: white; padding: 12px; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold;">{{ $dimInfo['nome'] }}</span>
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">{{ $ponto['faixa'] }}</span>
        </div>
        <div style="background-color: #f5f5f5; padding: 15px; border-radius: 0 0 6px 6px; margin-bottom: 20px;">
            <p style="margin-bottom: 10px;">
                @if($ponto['faixa'] == 'Baixa')
                    Os resultados indicam um estado de {{ strtolower($ponto['nome']) }} em nível baixo, sugerindo boa gestão emocional e capacidade de recuperação.
                @elseif($ponto['faixa'] == 'Moderada')
                    Os resultados indicam um estado de {{ strtolower($ponto['nome']) }} em nível moderado, sugerindo atenção e monitoramento contínuo.
                @else
                    Os resultados indicam um estado de {{ strtolower($ponto['nome']) }} em nível alto, sugerindo necessidade de ações imediatas de cuidado.
                @endif
            </p>
            
            <p style="margin-bottom: 5px;"><strong>Ponto forte:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Você demonstra boa capacidade de autorregulação e resiliência.
                @elseif($ponto['faixa'] == 'Moderada')
                    Você está consciente de suas necessidades e busca equilíbrio.
                @else
                    Você reconhece a importância do autocuidado e está aberto a mudanças.
                @endif
            </p>
            
            <p style="margin-bottom: 5px;"><strong>Orientação prática:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Continue praticando hábitos saudáveis e compartilhando boas práticas com colegas.
                @elseif($ponto['faixa'] == 'Moderada')
                    Priorize pausas regulares, estabeleça limites claros e busque apoio quando necessário.
                @else
                    Considere buscar suporte profissional, revise suas demandas e implemente práticas de autocuidado imediatamente.
                @endif
            </p>
        </div>
    </div>
    @endforeach

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 08</div>
    </div>
</div>

{{-- PÁGINA 9: SAÚDE EMOCIONAL - DIMENSÕES DETALHADAS --}}
<div class="page-break">
    <h1 style="color: #8B4513; font-size: 22px;">SAÚDE EMOCIONAL</h1>
    <h2 style="color: #008080; font-size: 16px; margin-bottom: 20px;">Análise Geral</h2>

    @php
        $dimensoesSecundarias = [
            ['tag' => 'FAPS', 'nome' => 'Fatores Psicossociais (FaPs)', 'cor' => '#556B2F'],
            ['tag' => 'ASMO', 'nome' => 'Assédio Moral (AsMo)', 'cor' => '#191970'],
            ['tag' => 'EXTR', 'nome' => 'Excesso de Trabalho (ExTr)', 'cor' => '#483D8B'],
        ];
    @endphp

    @foreach($dimensoesSecundarias as $dimInfo)
    @php
        $ponto = collect($pontuacoes)->firstWhere('tag', $dimInfo['tag']);
        if (!$ponto) continue;
    @endphp
    <div class="section">
        <div style="background-color: {{ $dimInfo['cor'] }}; color: white; padding: 12px; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold;">{{ $dimInfo['nome'] }}</span>
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">{{ $ponto['faixa'] }}</span>
        </div>
        <div style="background-color: #f5f5f5; padding: 15px; border-radius: 0 0 6px 6px; margin-bottom: 20px;">
            <p style="margin-bottom: 10px;">
                @if($ponto['faixa'] == 'Baixa')
                    Os resultados indicam um estado de {{ strtolower($dimInfo['nome']) }} em nível baixo, sugerindo um ambiente de trabalho saudável e equilibrado.
                @elseif($ponto['faixa'] == 'Moderada')
                    Os resultados indicam um estado de {{ strtolower($dimInfo['nome']) }} em nível moderado, sugerindo atenção e monitoramento contínuo.
                @else
                    Os resultados indicam um estado de {{ strtolower($dimInfo['nome']) }} em nível alto, sugerindo necessidade de ações imediatas de cuidado.
                @endif
            </p>
            
            <p style="margin-bottom: 5px;"><strong>Ponto forte:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Você trabalha em um ambiente que oferece suporte adequado e condições favoráveis.
                @elseif($ponto['faixa'] == 'Moderada')
                    Você reconhece os desafios e busca equilibrar as demandas.
                @else
                    Você está consciente das situações que precisam de atenção e mudança.
                @endif
            </p>
            
            <p style="margin-bottom: 5px;"><strong>Orientação prática:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Continue valorizando e protegendo esse equilíbrio. Compartilhe práticas positivas.
                @elseif($ponto['faixa'] == 'Moderada')
                    Monitore sinais de estresse, pratique pausas regulares e busque diálogo sobre ajustes necessários.
                @else
                    Busque apoio institucional, estabeleça limites claros e considere canais formais de suporte.
                @endif
            </p>
        </div>
    </div>
    @endforeach

    <div class="section" style="margin-top: 30px;">
        <p style="font-weight: bold; font-size: 13px;">Em conclusão, você está em um ponto de equilíbrio que, embora desafiador, oferece inúmeras oportunidades de crescimento. Ao continuar investindo em autocuidado e desenvolvimento pessoal, você poderá transformar essas vulnerabilidades em áreas de força. Você já possui os recursos internos necessários para prosperar e alcançar um estado de bem-estar mais pleno. Continue a jornada com confiança e cuidado consigo mesmo.</p>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 09</div>
    </div>
</div>

{{-- PÁGINA 10: PLANO DE DESENVOLVIMENTO PESSOAL --}}
<div class="page-break">
    <h1>PLANO DE DESENVOLVIMENTO PESSOAL</h1>
    
    @php
        $nivelRisco = $iid['nivel_risco'];
        $zona = $iid['zona'];
    @endphp

    <div class="section">
        <h3 style="display: flex; align-items: center;">
            <span class="bullet-gold"></span>
            <span style="color: #FFC107; font-size: 16px;">{{ $zona }}</span>
        </h3>
        
        <div style="margin-left: 20px;">
            <h4 style="color: #008080; margin-bottom: 10px;">Objetivo:</h4>
            <p style="margin-bottom: 20px;">
                @if($nivelRisco == 'Baixo')
                    Manter o equilíbrio emocional e continuar desenvolvendo práticas saudáveis de bem-estar.
                @elseif($nivelRisco == 'Médio')
                    Evitar o acúmulo de estresse e reequilibrar a rotina para prevenir sobrecarga.
                @elseif($nivelRisco == 'Atenção')
                    Reduzir sinais de esgotamento e fortalecer estratégias de recuperação emocional.
                @else
                    Interromper o ciclo de desgaste e buscar suporte profissional imediato.
                @endif
            </p>

            <h4 style="color: #008080; margin-bottom: 10px;">Ações sugeridas:</h4>
            <ol style="margin-left: 20px; margin-bottom: 20px;">
                @if($nivelRisco == 'Baixo')
                    <li>Manter hábitos saudáveis de sono, alimentação e exercício físico.</li>
                    <li>Continuar praticando pausas regulares e momentos de desconexão.</li>
                    <li>Compartilhar boas práticas com colegas e contribuir para um ambiente positivo.</li>
                @elseif($nivelRisco == 'Médio')
                    <li>Revisar compromissos e priorizar o essencial, delegando ou reorganizando prazos.</li>
                    <li>Incluir pausas ativas diárias (respiração, caminhada curta, desconexão digital).</li>
                    <li>Buscar feedback sobre performance e bem-estar, promovendo diálogo transparente com pares e liderança.</li>
                @elseif($nivelRisco == 'Atenção')
                    <li>Acionar estratégias de suporte imediato (RH, liderança, coaching).</li>
                    <li>Revisar carga de trabalho e negociar prazos e prioridades.</li>
                    <li>Implementar práticas de recuperação emocional e buscar apoio profissional se necessário.</li>
                @else
                    <li>Intervenção imediata: pausa, revisão de carga e suporte psicológico.</li>
                    <li>Buscar acompanhamento profissional especializado.</li>
                    <li>Priorizar saúde e bem-estar acima de qualquer demanda profissional.</li>
                @endif
            </ol>

            <h4 style="color: #008080; margin-bottom: 10px;">Indicador de progresso:</h4>
            <p style="margin-bottom: 20px;">
                @if($nivelRisco == 'Baixo')
                    Manutenção de níveis baixos de estresse e alta satisfação profissional.
                @elseif($nivelRisco == 'Médio')
                    Redução de momentos de tensão e aumento da clareza sobre prioridades.
                @elseif($nivelRisco == 'Atenção')
                    Redução de sinais de esgotamento e melhoria na capacidade de recuperação.
                @else
                    Redução imediata de sintomas de estresse e início de processo de recuperação.
                @endif
            </p>
        </div>
    </div>

    <div class="quote-box-teal" style="margin-top: 30px;">
        "O equilíbrio não é ausência de desafios, mas a capacidade de se manter inteiro diante deles."
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 10</div>
    </div>
</div>

{{-- PÁGINA 11: CONCLUSÃO GERAL --}}
<div class="page-break">
    <h1>CONCLUSÃO GERAL</h1>
    
    <div class="section">
        <p>Este relatório foi criado para oferecer uma visão abrangente do seu estado emocional e psicossocial no trabalho. Os resultados apresentados refletem seu estado atual e oferecem um ponto de partida para reflexão e ação.</p>
        
        <p>Lembre-se que o bem-estar é um processo contínuo, não um destino final. Cada ação que você toma para cuidar de si mesmo, por menor que seja, contribui para um estado de maior equilíbrio e satisfação.</p>
        
        <p>As dimensões avaliadas — Exaustão Emocional, Despersonalização/Cinismo, Realização Profissional, Fatores Psicossociais, Assédio Moral e Excesso de Trabalho — interagem constantemente, criando um perfil único de equilíbrio ou desequilíbrio.</p>
        
        <p>Os três eixos analíticos — Energia Emocional, Propósito e Relações, e Sustentabilidade Ocupacional — fornecem uma visão integrada do seu estado de bem-estar. O Índice Integrado de Descarrilamento (IID) oferece uma medida geral do seu risco atual.</p>
        
        <p>Independentemente dos resultados, lembre-se que você tem capacidade de mudança e crescimento. O autocuidado não é egoísmo — é uma responsabilidade consigo mesmo e com aqueles que dependem de você.</p>
    </div>

    <div class="quote-box-teal" style="margin: 30px 0;">
        "Cuidar de si é um ato de liderança silenciosa: quando você se equilibra, o ambiente ao seu redor também muda."
    </div>

    <div class="quote-box-gray" style="margin: 30px 0;">
        "A mudança começa quando nos olhamos com gentileza."
    </div>

    <div class="section" style="margin-top: 40px;">
        <h3 style="color: #008080;">Fellipelli Consultoria</h3>
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Transformando autoconhecimento em desenvolvimento humano.</p>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 11</div>
    </div>
</div>

</body>
</html>

