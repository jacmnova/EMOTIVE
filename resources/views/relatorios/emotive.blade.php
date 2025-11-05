<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RELATÓRIO E.MO.TI.VE® | {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .page {
            padding: 40px;
            margin-bottom: 30px;
            page-break-after: always;
        }

        @media print {
            .no-print {
                display: none;
            }
            .page {
                page-break-after: always;
            }
        }

        /* PORTADA */
        .capa {
            width: 100%;
            min-height: 100vh;
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
            font-size: 28px;
            font-weight: normal;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .tagline-fellipelli {
            font-size: 12px;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .logo-emotive {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 4px;
            margin-bottom: 10px;
        }

        .tagline-emotive {
            font-size: 16px;
            margin-bottom: 60px;
        }

        .waveform {
            width: 100%;
            height: 4px;
            background: #00CED1;
            margin: 40px 0;
            position: relative;
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
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-box .titulo {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .info-box .data {
            font-size: 14px;
            color: #666;
        }

        /* ESTILOS GENERALES */
        h1 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: left;
        }

        h2 {
            font-size: 22px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 15px;
            text-align: left;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 12px;
        }

        h4 {
            font-size: 16px;
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
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-moderada {
            background-color: #FFC107;
            color: white;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-alta {
            background-color: #F44336;
            color: white;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        /* CAJAS DE RESULTADO */
        .result-box {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #FFC107;
        }

        .result-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-box-score {
            font-size: 28px;
            font-weight: bold;
            color: #FFC107;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
        }

        /* EIXOS ANALÍTICOS */
        .eixo-box {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #008080;
        }

        .eixo-header {
            font-size: 18px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 15px;
        }

        .eixo-dimensions {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            background-color: #fff;
            padding: 20px;
            border-radius: 6px;
            flex-wrap: wrap;
        }

        .dimension-item {
            text-align: center;
            flex: 1;
            min-width: 150px;
            margin: 10px;
        }

        .dimension-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .dimension-bar {
            height: 40px;
            background-color: #FFC107;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .dimension-total {
            background-color: #FFC107;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 15px auto;
            display: inline-block;
        }

        /* GRÁFICO DE RISCO */
        .risk-bar {
            width: 100%;
            height: 50px;
            background-color: #e0e0e0;
            border-radius: 25px;
            position: relative;
            margin: 25px 0;
            display: flex;
            overflow: hidden;
        }

        .risk-segment {
            flex: 1;
            border-right: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #333;
            z-index: 2;
            position: relative;
        }

        .risk-segment:last-child {
            border-right: none;
        }

        .risk-indicator {
            position: absolute;
            height: 100%;
            background-color: #FFC107;
            border-radius: 25px;
            left: 0;
            z-index: 1;
            transition: width 0.3s ease;
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
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
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            font-style: italic;
            color: #333;
            border-left: 4px solid #008080;
            font-size: 15px;
        }

        .quote-box-gray {
            background-color: #f5f5f5;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            font-style: italic;
            color: #333;
            font-size: 15px;
        }

        /* FOOTER */
        .footer {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            margin-top: 40px;
        }

        .footer-logos {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-logo-text {
            font-size: 14px;
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
            border-radius: 8px;
        }

        /* LISTAS */
        ul, ol {
            margin-left: 25px;
            margin-bottom: 15px;
        }

        li {
            margin-bottom: 10px;
        }

        /* BULLET DORADO */
        .bullet-gold {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #FFC107;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* ZONA DE ATENÇÃO */
        .zona-box {
            background-color: #fff3cd;
            border-left: 4px solid #FFC107;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .zona-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }

        /* BOTÓN DE DESCARGA */
        .download-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #008080;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: #006666;
            color: white;
            text-decoration: none;
        }

        @media print {
            .download-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="download-btn no-print">
    <a href="{{ route('relatorio.pdf', ['user' => $user->id, 'formulario' => $formulario->id]) }}" 
       style="color: white; text-decoration: none;" 
       download>
        📥 Descargar PDF
    </a>
</div>

<div class="container">
{{-- PÁGINA 1: PORTADA --}}
<div class="page">
    <div class="capa">
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
</div>

{{-- Continuar con el resto de las páginas del PDF pero adaptadas para HTML --}}
{{-- Por brevedad, incluiré las páginas principales --}}

{{-- PÁGINA 2: RELATÓRIO E.MO.TI.VE --}}
<div class="page">
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
            <span style="font-size: 12px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 01</div>
    </div>
</div>

{{-- PÁGINA 4: SEU RESULTADO E.MO.TI.VE --}}
<div class="page">
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
        <div style="background-color: #DAA520; color: white; padding: 20px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 16px;">Faixa Moderada</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoModerada as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoBaixa) > 0)
        <div style="background-color: #4CAF50; color: white; padding: 20px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 16px;">Faixa Baixa</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoBaixa as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoAlta) > 0)
        <div style="background-color: #F44336; color: white; padding: 20px; border-radius: 6px; margin-bottom: 15px;">
            <strong style="font-size: 16px;">Faixa Alta</strong>
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
            <img src="{{ asset($imagemRadar) }}" alt="Radar E.MO.TI.VE" style="max-width: 100%; height: auto;">
        </div>
    </div>

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 12px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 03</div>
    </div>
</div>

{{-- PÁGINA 7: RISCO DE DESCARRILAMENTO --}}
<div class="page">
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
        
        <div style="text-align: center; margin: 25px 0;">
            <span style="font-size: 22px; color: #FFC107; font-weight: bold;">
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
            <span style="font-size: 12px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 07</div>
    </div>
</div>

{{-- PÁGINA 6: ÍNDICE EIXOS ANALÍTICOS --}}
<div class="page">
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
        <h3 style="color: #008080; font-size: 20px;">{{ $eixoInfo['nome'] }}</h3>
        <p style="margin-bottom: 15px;">{{ $eixoInfo['desc'] }}</p>
        
        <div style="background-color: #f5f5f5; border-radius: 8px; padding: 20px; margin-bottom: 15px;">
            <div class="eixo-dimensions">
                <div class="dimension-item">
                    <div class="dimension-label">{{ $eixoInfo['dims'][0] }}</div>
                    <div class="dimension-bar">
                        <span class="faixa-{{ strtolower($eixo['faixa']) }}">Faixa {{ $eixo['faixa'] }}</span>
                    </div>
                </div>
                <div class="dimension-item">
                    <div class="dimension-label">TOTAL</div>
                    <div class="dimension-total">{{ round($eixo['valor']) }}</div>
                </div>
                <div class="dimension-item">
                    <div class="dimension-label">{{ $eixoInfo['dims'][1] }}</div>
                    <div class="dimension-bar">
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
            <span style="font-size: 12px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 06</div>
    </div>
</div>

</div> {{-- fin container --}}

</body>
</html>

