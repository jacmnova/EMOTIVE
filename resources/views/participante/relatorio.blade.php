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
            font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        .page {
            background: white;
            margin: 20px auto;
            padding: 60px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 210mm;
            min-height: 297mm;
            position: relative;
        }

        @media print {
            body {
                background: white;
            }
            .page {
                margin: 0;
                box-shadow: none;
                page-break-after: always;
            }
            .no-print {
                display: none;
            }
        }

        /* PORTADA */
        .capa {
            width: 100%;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(180deg, #0a1a2e 0%, #1a3a5a 50%, #008080 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            color: white;
            padding: 60px 40px 40px;
            box-sizing: border-box;
        }

        .logo-fellipelli {
            font-size: 28px;
            font-weight: normal;
            margin-bottom: 8px;
            letter-spacing: 3px;
            text-transform: lowercase;
        }

        .tagline-fellipelli {
            font-size: 11px;
            margin-bottom: 80px;
            opacity: 0.9;
            text-align: center;
            line-height: 1.4;
        }

        .logo-emotive-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .logo-emotive-icon {
            width: 50px;
            height: 50px;
            border: 2px solid white;
            border-radius: 50%;
            position: relative;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                white 2px,
                white 4px
            );
        }

        .logo-emotive {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 6px;
        }

        .tagline-emotive {
            font-size: 16px;
            margin-bottom: 80px;
        }

        .waveform {
            width: 100%;
            height: 6px;
            background: #00CED1;
            margin: 40px 0;
            position: relative;
            border-radius: 3px;
        }

        .waveform::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                transparent 15%,
                #00CED1 15%,
                #00CED1 20%,
                transparent 20%,
                transparent 35%,
                #00CED1 35%,
                #00CED1 40%,
                transparent 40%,
                transparent 55%,
                #00CED1 55%,
                #00CED1 65%,
                transparent 65%,
                transparent 75%,
                #00CED1 75%,
                #00CED1 85%,
                transparent 85%
            );
        }

        .info-box {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            color: #333;
            text-align: left;
            width: 400px;
            position: absolute;
            bottom: 60px;
            right: 60px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .info-box .nome {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .info-box .titulo {
            font-size: 13px;
            margin-bottom: 6px;
            color: #555;
        }

        .info-box .data {
            font-size: 11px;
            color: #777;
        }

        /* ESTILOS GENERALES */
        h1 {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: left;
            text-transform: uppercase;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            color: #42B8D4;
            margin-bottom: 15px;
            text-align: left;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .section {
            margin-bottom: 25px;
        }

        /* BADGES Y FAIXAS */
        .faixa-baixa {
            background-color: #4CAF50;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-moderada {
            background-color: #D4B87D;
            color: #333;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .faixa-alta {
            background-color: #F44336;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        /* CAJAS DE RESULTADO */
        .dimension-box {
            margin-bottom: 20px;
        }

        .dimension-header {
            background-color: #6B5B4A;
            color: white;
            padding: 12px 15px;
            border-radius: 6px 6px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 12px;
        }

        .dimension-header.exem { background-color: #6B5B4A; }
        .dimension-header.deci { background-color: #C79F6B; }
        .dimension-header.repr { background-color: #8C8C8C; }
        .dimension-header.faps { background-color: #556B2F; }
        .dimension-header.asmo { background-color: #191970; }
        .dimension-header.extr { background-color: #6A5C8A; }

        .dimension-content {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 0 0 6px 6px;
        }

        .dimension-score-box {
            background-color: #F5F5EB;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dimension-score-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        /* FOOTER */
        .footer {
            position: absolute;
            bottom: 20px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .footer-logos {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .footer-logo-text {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }

        .footer-page {
            text-align: right;
            color: #999;
        }

        /* QUOTE BOXES */
        .quote-box-teal {
            background-color: #D9EDEE;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-style: italic;
            color: #333;
            text-align: center;
        }

        .quote-box-gray {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-style: italic;
            color: #333;
            text-align: center;
            font-size: 13px;
        }

        /* EIXOS ANALÍTICOS */
        .eixo-container {
            margin-bottom: 30px;
            border-left: 5px solid #008080;
            padding-left: 20px;
        }

        .eixo-title {
            font-size: 18px;
            font-weight: bold;
            color: #008080;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .eixo-data-table {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .eixo-dim-item {
            text-align: center;
            flex: 1;
        }

        .eixo-dim-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
        }

        .eixo-dim-badge {
            background-color: #D4B87D;
            color: #333;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .eixo-dim-badge.baixa {
            background-color: #4CAF50;
            color: white;
        }

        .eixo-total {
            background-color: #333;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        /* RISCO BAR */
        .risk-bar-container {
            width: 100%;
            height: 50px;
            background-color: #e0e0e0;
            border-radius: 25px;
            position: relative;
            margin: 20px 0;
            display: flex;
            overflow: hidden;
        }

        .risk-segment {
            flex: 1;
            border-right: 2px solid #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: #333;
            position: relative;
            z-index: 2;
        }

        .risk-segment:last-child {
            border-right: none;
        }

        .risk-indicator {
            position: absolute;
            height: 100%;
            background-color: #E6C25A;
            border-radius: 25px;
            left: 0;
            z-index: 1;
        }

        /* RADAR */
        .radar-container {
            text-align: center;
            margin: 30px 0;
        }

        .radar-container img {
            max-width: 100%;
            height: auto;
        }

        /* RESUMO POR FAIXA */
        .resumo-faixa {
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            color: white;
        }

        .resumo-faixa.moderada {
            background-color: #D4B87D;
        }

        .resumo-faixa.baixa {
            background-color: #82C99A;
        }

        .resumo-faixa.alta {
            background-color: #F44336;
        }

        /* ZONA BOX */
        .zona-box {
            background-color: #F5F5EB;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .zona-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            font-style: italic;
        }

        /* PLANO DESENVOLVIMENTO */
        .plano-zona {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .plano-bullet {
            width: 10px;
            height: 10px;
            background-color: #FFC107;
            border-radius: 50%;
        }

        .plano-zona-text {
            color: #FFC107;
            font-size: 16px;
            font-weight: bold;
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
            background-color: #f5f5f5;
            font-weight: bold;
        }

        ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        li {
            margin-bottom: 8px;
        }

        ol {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        /* Botón de descarga PDF */
        .download-pdf-btn {
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .download-pdf-btn:hover {
            background-color: #006666;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

<a href="{{ route('relatorio.pdf', ['user' => $user->id, 'formulario' => $formulario->id]) }}" class="download-pdf-btn no-print" download>
    📥 Descargar PDF
</a>

{{-- PÁGINA 1: PORTADA --}}
<div class="page capa" style="background: linear-gradient(180deg, #0a1a2e 0%, #1a3a5a 50%, #008080 100%);">
    <div class="logo-fellipelli">fellipelli</div>
    <div class="tagline-fellipelli">desenvolvimento pessoal<br>e organizacional</div>
    
    <div class="logo-emotive-container">
        <div class="logo-emotive-icon"></div>
        <div class="logo-emotive">E.MO.TI.VE</div>
    </div>
    <div class="tagline-emotive">Burnout e Bem-estar</div>
    
    <div class="waveform"></div>
    
    <div class="info-box">
        <div class="nome">{{ $user->name }}</div>
        <div class="titulo">Relatório Questionário de Riscos Psicossociais</div>
        <div class="data">Respondido em {{ $dataResposta }}</div>
    </div>
</div>

{{-- PÁGINA 2: RELATÓRIO E.MO.TI.VE --}}
<div class="page">
    <h1>RELATÓRIO E.MO.TI.VE®</h1>
    
    <div class="section">
        <h2>Ferramenta de Autoconhecimento e Prevenção de Riscos Psicossociais</h2>
        <p>(Baseada nas diretrizes da NR-1 e nos princípios da Psicologia Organizacional Positiva)</p>
        <p><strong>Mais do que medir</strong> — oferece uma jornada de autoconhecimento.</p>
        <p><strong>Mais do que um diagnóstico</strong> — fornece orientações práticas para o desenvolvimento pessoal e profissional.</p>
        
        <div class="quote-box-gray" style="border: 1px solid #42B8D4; background-color: #fafafa;">
            "Saúde emocional não é ausência de estresse, mas a capacidade de reconhecê-lo e se fortalecer diante dele."
        </div>
    </div>

    <div class="section">
        <h2>Finalidade do Instrumento</h2>
        <p>O E.MO.TI.VE® identifica e avalia seis dimensões psicossociais fundamentais relacionadas ao bem-estar e ao risco de burnout no ambiente de trabalho. Com base nas suas respostas, este relatório ajuda você a:</p>
        <ul>
            <li>Reconhecer áreas de equilíbrio e vulnerabilidade emocional;</li>
            <li>Fortalecer o autocuidado e a autorregulação emocional;</li>
            <li>Promover relações saudáveis e ambientes de confiança;</li>
            <li>Inspirar planos de ação pessoais e coletivos que reduzam o risco de burnout.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Base Normativa e Científica</h2>
        <ol>
            <li><strong>NR-1 (Portaria 6.730/2020)</strong> — que define a obrigatoriedade da gestão de riscos psicossociais nas organizações.</li>
            <li><strong>Modelo MBI (Maslach Burnout Inventory)</strong> — referência mundial para compreender exaustão, cinismo e realização profissional.</li>
        </ol>
    </div>

    <div class="section">
        <h2>Ética, Confidencialidade e Responsabilidade Compartilhada</h2>
        <ul>
            <li>A empresa deve assegurar condições saudáveis e equilibradas de trabalho.</li>
            <li>O participante pode adotar práticas de cuidado, comunicação e autorregulação.</li>
            <li>O RH e a liderança devem utilizar as informações de forma ética, para orientar ações preventivas e programas de bem-estar.</li>
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

{{-- PÁGINA 2: ESTRUTURA DO MODELO --}}
<div class="page">
    <h1>Estrutura do Modelo E.MO.TI.VE</h1>
    
    <p style="margin-bottom: 30px;">O instrumento avalia seis dimensões principais que, juntas, formam o retrato do seu equilíbrio psicossocial:</p>

    <div class="section">
        <h3 style="color: #333; margin-left: 0;">EIXO 1 ENERGIA EMOCIONAL</h3>
        <div style="margin-left: 20px; margin-bottom: 20px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #6B5B4A; font-size: 13px; margin-bottom: 8px;">• Exaustão Emocional (ExEm)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> desgaste intenso, sensação de não ter energia.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> Se está alto, pense em revisar prazos, negociar demandas e buscar apoio.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #DAA520; font-size: 13px; margin-bottom: 8px;">• Realização Profissional (RePr)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> sensação de competência e propósito no trabalho.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> quando baixa, pode gerar insegurança, desvalorização e até desmotivação.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> Aqui vale pedir feedbacks, investir em desenvolvimento e resgatar pequenas conquistas.</p>
        </div>
    </div>

    <div class="section">
        <h3 style="color: #333; margin-left: 0;">EIXO 2 PROPÓSITO E RELAÇÕES</h3>
        <div style="margin-left: 20px; margin-bottom: 20px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #FF8C00; font-size: 13px; margin-bottom: 8px;">• Despersonalização / Cinismo (DeCi)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> distanciamento afetivo do trabalho e das pessoas.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> tratar colegas com frieza, indiferença ou ironia.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> Se esse ponto estiver alto, é importante reconectar-se ao propósito do que faz e retomar vínculos de confiança.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #4169E1; font-size: 13px; margin-bottom: 8px;">• Fatores Psicossociais (FaPs)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> condições organizacionais - clareza de papéis, apoio, comunicação.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> Procure identificar onde a comunicação falha e proponha pequenas ações de melhoria.</p>
        </div>
    </div>

    <div class="section">
        <h3 style="color: #333; margin-left: 0;">EIXO 3 SUSTENTABILIDADE OCUPACIONAL</h3>
        <div style="margin-left: 20px; margin-bottom: 20px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #191970; font-size: 13px; margin-bottom: 8px;">• Assédio Moral (AsMo)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> comportamentos abusivos, constrangedores ou humilhantes.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> piadas ofensivas, isolamento, críticas públicas.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> Se identificar sinais, não ignore. Busque diálogo e, se necessário, canais formais de apoio.</p>
        </div>
        
        <div style="margin-left: 20px; margin-bottom: 30px; background-color: #f9f9f9; padding: 15px; border-radius: 6px;">
            <h4 style="color: #483D8B; font-size: 13px; margin-bottom: 8px;">• Excesso de Trabalho (ExTr)</h4>
            <p style="margin-bottom: 5px;"><strong>Conceito:</strong> quando a carga de tarefas ultrapassa os limites pessoais.</p>
            <p style="margin-bottom: 5px;"><strong>Prático:</strong> longas horas, sem pausas, sem equilíbrio com a vida pessoal.</p>
            <p style="margin-bottom: 0;"><strong>Didático:</strong> É preciso revisar prioridades, delegar e resgatar tempo para descanso.</p>
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

{{-- PÁGINA 3: SEU RESULTADO E.MO.TI.VE --}}
<div class="page">
    <h1>SEU RESULTADO E.MO.TI.VE</h1>
    
    <div class="section">
        <h2>Dados do respondente</h2>
        <p><strong>Formulário:</strong> {{ $formulario->label }} – {{ $formulario->nome }}</p>
        <p><strong>Participante:</strong> {{ $user->name }}</p>
        <p><strong>({{ $user->email }})</strong></p>
        <p><strong>Data:</strong> {{ $dataResposta }}</p>
        <p><strong>Respostas registradas:</strong> {{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() ?? 'N/A' }}</p>
        <p><strong>Dimensões avaliadas:</strong> {{ $variaveis->pluck('nome')->join(', ') }}</p>
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
        <div class="resumo-faixa moderada">
            <strong style="font-size: 14px;">Faixa Moderada</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoModerada as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoBaixa) > 0)
        <div class="resumo-faixa baixa">
            <strong style="font-size: 14px;">Faixa Baixa</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach($grupoBaixa as $dim)
                    <li>{{ $dim }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($grupoAlta) > 0)
        <div class="resumo-faixa alta">
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
            @if($imagemRadar)
                <img src="{{ $imagemRadar }}" alt="Radar E.MO.TI.VE" style="max-width: 100%; height: auto;">
            @else
                <p style="text-align: center; color: #999;">Gráfico no disponible</p>
            @endif
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

{{-- PÁGINA 4: ESTADO EMOCIONAL E PSICOSSOCIAL - COMO LER --}}
<div class="page">
    <h1>ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    <div class="section">
        <h2>Como Ler Seus Resultados</h2>
        <p>Cada dimensão é apresentada em faixas de pontuação, representando níveis de atenção:</p>
        <p style="margin-left: 20px;">Faixa Baixa: equilíbrio emocional saudável, sem sinais de risco.</p>
        <p style="margin-left: 20px;">• Faixa Moderada: pontos de atenção que merecem acompanhamento.</p>
        <p style="margin-left: 20px;">Faixa Alta: indica necessidade de reflexão e cuidado ativo.</p>
        
        <div class="zona-box">
            <div class="zona-title">Importante:</div>
            <p style="margin: 0;"><strong>Nenhum resultado define você.</strong> Os resultados refletem o estado atual em condições específicas. As seções seguintes oferecem interpretações personalizadas e sugestões práticas.</p>
        </div>
    </div>

    @foreach(array_slice($pontuacoes, 0, 2) as $ponto)
    <div class="section">
        <h3 style="color: #42B8D4;">{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h3>
        <div class="dimension-score-box">
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">Faixa {{ $ponto['faixa'] }}</span>
            <span class="dimension-score-value">{{ round($ponto['normalizada']) }}</span>
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

{{-- PÁGINA 5: ESTADO EMOCIONAL E PSICOSSOCIAL - CONTINUAÇÃO --}}
<div class="page">
    <h1>ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    @foreach(array_slice($pontuacoes, 2, 4) as $ponto)
    <div class="section">
        <h3 style="color: #42B8D4;">{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h3>
        <div class="dimension-score-box">
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">Faixa {{ $ponto['faixa'] }}</span>
            <span class="dimension-score-value">{{ round($ponto['normalizada']) }}</span>
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
    @endforeach

    <div class="footer">
        <div class="footer-logos">
            <span class="footer-logo-text">fellipelli</span>
            <span class="footer-logo-text">E.MO.TI.VE</span>
            <span style="font-size: 10px;">Burnout e Bem-estar</span>
        </div>
        <div class="footer-page">Todos os direitos reservados a Fellipelli Consultoria | Pág. 05</div>
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
        $eixo = $eixos[$eixoInfo['key']] ?? null;
        if (!$eixo) continue;
        $interpretacao = $eixo['interpretacao_detalhada'] ?? [];
    @endphp
    <div class="eixo-container">
        <h3 class="eixo-title">{{ $eixoInfo['nome'] }}</h3>
        <p style="margin-bottom: 15px;">{{ $eixoInfo['desc'] }}</p>
        
        <div class="eixo-data-table">
            <div class="eixo-dim-item">
                <div class="eixo-dim-label">{{ $eixoInfo['dims'][0] }}</div>
                <div class="eixo-dim-badge {{ strtolower($eixo['faixa']) }}">Faixa {{ $eixo['faixa'] }}</div>
            </div>
            <div class="eixo-dim-item">
                <div class="eixo-dim-label">TOTAL</div>
                <div class="eixo-total">{{ round($eixo['valor']) }}</div>
            </div>
            <div class="eixo-dim-item">
                <div class="eixo-dim-label">{{ $eixoInfo['dims'][1] }}</div>
                <div class="eixo-dim-badge {{ strtolower($eixo['faixa']) }}">Faixa {{ $eixo['faixa'] }}</div>
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
        $iid = $eixos['iid'] ?? null;
        if (!$iid) {
            $iid = ['valor' => 0, 'zona' => 'Zona de equilíbrio emocional', 'descricao' => '', 'interpretacao' => '', 'acao' => '', 'nivel_risco' => 'Baixo'];
        }
        $percentual = $iid['valor'];
        $indicatorWidth = min(100, max(0, ($percentual / 100) * 100));
        $riscoColor = $iid['nivel_risco'] == 'Baixo' ? '#4CAF50' : ($iid['nivel_risco'] == 'Médio' ? '#E6C25A' : ($iid['nivel_risco'] == 'Atenção' ? '#FF9800' : '#F44336'));
    @endphp

    <div class="section">
        <h3>Classificação do Risco</h3>
        <div class="risk-bar-container">
            <div class="risk-indicator" style="width: {{ $indicatorWidth }}%; background-color: {{ $riscoColor }};"></div>
            <div class="risk-segment">Baixo<br>(0-40)</div>
            <div class="risk-segment">Médio<br>(41-65)</div>
            <div class="risk-segment">Atenção<br>(66-89)</div>
            <div class="risk-segment">Alto<br>(90-100)</div>
        </div>
        
        <div style="text-align: center; margin: 25px 0;">
            <span style="font-size: 18px; color: {{ $riscoColor }}; font-weight: bold;">
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

{{-- PÁGINA 8: SAÚDE EMOCIONAL - ANÁLISE GERAL (Parte 1) --}}
<div class="page">
    <h1 style="color: #8B4513; font-size: 22px; text-transform: none;">SAÚDE EMOCIONAL</h1>
    <h2 style="color: #42B8D4; font-size: 16px; margin-bottom: 20px;">Análise Geral</h2>
    
    <div class="section">
        <p>Olá,</p>
        <p>Primeiramente, é importante reconhecer que você está em um espaço de conscientização e crescimento, o que já é um passo significativo em direção ao equilíbrio emocional e bem-estar. Vamos explorar cada um dos seus resultados, destacando seus pontos fortes e oferecendo orientações práticas para o autocuidado e desenvolvimento pessoal.</p>
    </div>

    @php
        $dimensoesPrincipais = [
            ['tag' => 'EXEM', 'nome' => 'Exaustão Emocional (ExEm)', 'cor' => '#6B5B4A'],
            ['tag' => 'DECI', 'nome' => 'Despersonalização / Cinismo (DeCi)', 'cor' => '#C79F6B'],
            ['tag' => 'REPR', 'nome' => 'Realização Profissional (RePr)', 'cor' => '#8C8C8C'],
        ];
    @endphp

    @foreach($dimensoesPrincipais as $dimInfo)
    @php
        $ponto = collect($pontuacoes)->firstWhere('tag', $dimInfo['tag']);
        if (!$ponto) continue;
        $variavel = $variaveis->firstWhere('tag', $dimInfo['tag']);
    @endphp
    <div class="dimension-box">
        <div class="dimension-header {{ strtolower($dimInfo['tag']) }}">
            <span>{{ $dimInfo['nome'] }}</span>
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">{{ $ponto['faixa'] }}</span>
        </div>
        <div class="dimension-content">
            <p style="margin-bottom: 10px;">
                @php
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
            
            <p style="margin-bottom: 5px;"><strong>Ponto forte:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Você está ciente de suas emoções e reconhece a necessidade de cuidar delas.
                @elseif($ponto['faixa'] == 'Moderada')
                    Você está ciente de suas emoções e reconhece a necessidade de cuidar delas.
                @else
                    Você está ciente de suas emoções e reconhece a necessidade de cuidar delas.
                @endif
            </p>
            
            <p style="margin-bottom: 0;"><strong>Orientação prática:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Continue praticando hábitos saudáveis, compartilhando boas práticas e inspirando colegas.
                @elseif($ponto['faixa'] == 'Moderada')
                    Priorize momentos de descanso e autocuidado. Atividades como meditação, exercícios físicos regulares e a prática de hobbies podem ajudar a recarregar suas energias emocionais.
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

{{-- PÁGINA 9: SAÚDE EMOCIONAL - ANÁLISE GERAL (Parte 2) --}}
<div class="page">
    <h1 style="color: #8B4513; font-size: 22px; text-transform: none;">SAÚDE EMOCIONAL</h1>
    <h2 style="color: #42B8D4; font-size: 16px; margin-bottom: 20px;">Análise Geral</h2>

    @php
        $dimensoesSecundarias = [
            ['tag' => 'FAPS', 'nome' => 'Fatores Psicossociais (FaPs)', 'cor' => '#556B2F'],
            ['tag' => 'ASMO', 'nome' => 'Assédio Moral (AsMo)', 'cor' => '#191970'],
            ['tag' => 'EXTR', 'nome' => 'Excesso de Trabalho (ExTr)', 'cor' => '#6A5C8A'],
        ];
    @endphp

    @foreach($dimensoesSecundarias as $dimInfo)
    @php
        $ponto = collect($pontuacoes)->firstWhere('tag', $dimInfo['tag']);
        if (!$ponto) continue;
        $variavel = $variaveis->firstWhere('tag', $dimInfo['tag']);
    @endphp
    <div class="dimension-box">
        <div class="dimension-header {{ strtolower($dimInfo['tag']) }}">
            <span>{{ $dimInfo['nome'] }}</span>
            <span class="faixa-{{ strtolower($ponto['faixa']) }}">{{ $ponto['faixa'] }}</span>
        </div>
        <div class="dimension-content">
            <p style="margin-bottom: 10px;">
                @php
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
            
            <p style="margin-bottom: 5px;"><strong>Ponto forte:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Um ambiente mais seguro emocionalmente.
                @elseif($ponto['faixa'] == 'Moderada')
                    Você tem a resiliência necessária para enfrentar desafios.
                @else
                    Você está consciente das situações que precisam de atenção e mudança.
                @endif
            </p>
            
            <p style="margin-bottom: 0;"><strong>Orientação prática:</strong> 
                @if($ponto['faixa'] == 'Baixa')
                    Continue contribuindo para um ambiente de trabalho respeitoso e solidário, e esteja atento para apoiar colegas que possam precisar.
                @elseif($ponto['faixa'] == 'Moderada')
                    Cultive um ambiente de suporte social, buscando apoio de colegas e compartilhe suas experiências. A comunicação aberta pode melhorar significativamente seu bem-estar psicológico.
                @else
                    Busque apoio institucional, estabeleça limites claros e considere canais formais de suporte.
                @endif
            </p>
        </div>
    </div>
    @endforeach

    <div class="section" style="margin-top: 30px;">
        <p style="font-weight: normal; font-size: 11px;">Em conclusão, você está em um ponto de equilíbrio que, embora desafiador, oferece inúmeras oportunidades de crescimento. Ao continuar investindo em autocuidado e desenvolvimento pessoal, você poderá transformar essas vulnerabilidades em áreas de força. Você já possui os recursos internos necessários para prosperar e alcançar um estado de bem-estar mais pleno. Continue a jornada com confiança e cuidado consigo mesmo.</p>
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
<div class="page">
    <h1>PLANO DE DESENVOLVIMENTO PESSOAL</h1>
    
    @php
        $iid = $eixos['iid'] ?? null;
        $nivelRisco = $iid['nivel_risco'] ?? 'Médio';
        $zona = $iid['zona'] ?? 'Zona de atenção preventiva';
    @endphp

    <div class="section">
        <div class="plano-zona">
            <div class="plano-bullet"></div>
            <div class="plano-zona-text">{{ $zona }}</div>
        </div>
        
        <div style="margin-left: 20px;">
            <h3 style="color: #42B8D4; font-size: 16px; margin-bottom: 10px;">Objetivo:</h3>
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

            <h3 style="color: #42B8D4; font-size: 16px; margin-bottom: 10px;">Ações sugeridas:</h3>
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

            <h3 style="color: #42B8D4; font-size: 16px; margin-bottom: 10px;">Indicador de progresso:</h3>
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
<div class="page">
    <h1>CONCLUSÃO GERAL</h1>
    
    <div class="section">
        <p>O autodesenvolvimento é um processo contínuo e intencional. Ele exige que você se comprometa com a prática diária de se conhecer melhor, de reconhecer seus limites e de investir em estratégias que fortaleçam seu bem-estar.</p>
        
        <p>A partir das reflexões e ações propostas neste relatório, você tem a oportunidade de transformar desafios em oportunidades de crescimento. Lembre-se: cada pequena ação conta, cada pausa que você faz, cada limite que você estabelece, cada conversa que você inicia — tudo isso contribui para um estado de maior equilíbrio e satisfação.</p>
        
        <div class="quote-box-teal" style="margin: 30px 0;">
            "Cuidar de si é um ato de liderança silenciosa: quando você se equilibra, o ambiente ao seu redor também muda."
        </div>
        
        <p>Você deu um passo importante: parar, olhar e compreender suas emoções. Esse gesto, simples e corajoso, é o primeiro movimento de quem busca crescer sem perder a essência.</p>
        
        <p>O equilíbrio emocional não é algo fixo, mas uma prática constante — como respirar fundo antes de seguir.</p>
        
        <p>Continue se observando, se respeitando e se cuidando.</p>
        
        <p>O seu bem-estar é a base para o seu melhor desempenho.</p>
        
        <div class="quote-box-gray" style="margin: 30px 0;">
            "A mudança começa quando nos olhamos com gentileza."
        </div>
    </div>

    <div class="section" style="margin-top: 40px;">
        <h3 style="color: #008080; font-size: 18px; margin-bottom: 5px;">Fellipelli Consultoria</h3>
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

