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
            margin: 100px 80px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.6;
        }

        .page-break {
            page-break-after: always;
        }

        .capa {
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            position: relative;
        }

        .section-title {
            color: #008ca5;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .section-subtitle {
            color: #00a8b5;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .highlight-box {
            background-color: #e8f4f8;
            border-left: 4px solid #008ca5;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }

        .quote-box {
            background-color: #e8f4f8;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            font-style: italic;
        }

        .faixa-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.85rem;
        }

        .faixa-baixa {
            background-color: #d4edda;
            color: #155724;
        }

        .faixa-moderada {
            background-color: #fff3cd;
            color: #856404;
        }

        .faixa-alta {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>

{{-- CAPA --}}
<div class="container container-portada" style="width: 100%; height: 100vh; background: #333; background-size: cover; display: flex; flex-direction: column; gap: 30vh; background-image: url('{{ public_path('img/Emotive-bg.png') }}'); background-position: bottom; background-repeat: no-repeat; position: relative;">
    <div style="position: relative; z-index: 1; padding: 40px;">
        <div style="text-align: center; margin-top: 40px;">
            <h1 style="font-size: 36px; font-weight: bold; margin: 0; letter-spacing: 2px; color: white;">fellipelli</h1>
            <p style="font-size: 10px; margin-top: 5px; color: rgba(255,255,255,0.8);">desenvolvimento pessoal e organizacional</p>
        </div>
        
        <div style="text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; margin-top: 10vh;">
            <div style="width: 100px; height: 100px; border: 3px solid #00a8b5; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 20px; background: rgba(0, 168, 181, 0.1);">
                <div style="width: 60px; height: 40px; border: 2px solid #00a8b5; border-radius: 30px 30px 0 0;"></div>
            </div>
            <h2 style="font-size: 42px; font-weight: bold; margin: 0; letter-spacing: 5px; color: white;">E.MO.TI.VE</h2>
            <p style="font-size: 14px; margin-top: 10px; color: #00a8b5;">Burnout e Bem-estar</p>
        </div>
        
        <div style="background: white; border-radius: 10px; padding: 20px; margin-top: 40px; color: #333; position: relative; z-index: 1;">
            <h3 style="font-size: 16px; font-weight: bold; margin: 0 0 8px 0; color: #1e3c72;">{{ $user->name }}</h3>
            <p style="font-size: 10px; margin: 4px 0; color: #666;">Relatório Questionário de Riscos Psicossociais</p>
            <p style="font-size: 9px; margin: 4px 0; color: #888;">Respondido em {{ $hoje }}</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- INTRODUÇÃO --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">RELATÓRIO E.MO.TI.VE®</h1>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5;">Ferramenta de Autoconhecimento e Prevenção de Riscos Psicossociais</h2>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            O E.MO.TI.VE® é uma ferramenta de avaliação desenvolvida para identificar sinais de desequilíbrio emocional e ocupacional no ambiente de trabalho. Baseado em evidências científicas e normas regulatórias, este instrumento oferece uma análise personalizada do seu estado emocional e psicossocial.
        </p>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            <strong>Mais do que medir</strong> — este relatório busca promover reflexão e autoconhecimento sobre fatores que influenciam seu bem-estar profissional.
        </p>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            <strong>Mais do que um diagnóstico</strong> — oferece orientações práticas para fortalecer sua resiliência emocional e melhorar sua relação com o trabalho.
        </p>
        
        <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 20px; margin: 25px 0; border-left: 4px solid #008ca5;">
            <p style="margin: 0; font-style: italic; color: #333; font-size: 11px;">
                "Saúde emocional não é ausência de estresse, mas a capacidade de reconhecê-lo e se fortalecer diante dele."
            </p>
        </div>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5;">Finalidade do Instrumento</h2>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            Este relatório identifica dimensões relacionadas ao bem-estar emocional e ocupacional, permitindo que você:
        </p>
        <ul style="line-height: 2; padding-left: 25px; font-size: 11px;">
            <li>Reconheça áreas de equilíbrio e vulnerabilidade emocional</li>
            <li>Identifique sinais precoces de desgaste ou sobrecarga</li>
            <li>Compreenda como fatores organizacionais e pessoais se interrelacionam</li>
            <li>Receba orientações práticas para promover autocuidado e desenvolvimento</li>
        </ul>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5;">Base Normativa e Científica</h2>
        <ol style="line-height: 2; padding-left: 25px; font-size: 11px;">
            <li><strong>NR-1 (Portaria 6.730/2020):</strong> Estabelece diretrizes para avaliação de riscos psicossociais no trabalho, exigindo que organizações identifiquem e previnam fatores que possam comprometer a saúde mental dos trabalhadores.</li>
            <li><strong>Modelo de Maslach (Burnout):</strong> Baseado em décadas de pesquisa científica, o modelo identifica três dimensões principais: exaustão emocional, despersonalização e redução da realização profissional.</li>
        </ol>
    </div>
    
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5;">Ética, Confidencialidade e Responsabilidade Compartilhada</h2>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            Este relatório é confidencial e não deve ser utilizado como diagnóstico clínico. Os resultados refletem uma avaliação do momento atual e podem variar conforme mudanças no ambiente de trabalho e nas condições pessoais.
        </p>
        <ul style="line-height: 2; padding-left: 25px; font-size: 11px;">
            <li>Você é responsável por refletir sobre os resultados e buscar apoio quando necessário</li>
            <li>A empresa deve assegurar condições saudáveis e equilibradas de trabalho</li>
            <li>Este instrumento visa promover prevenção e bem-estar, não substituir avaliações clínicas especializadas</li>
        </ul>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 01</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- ESTRUTURA DO MODELO --}}
<div style="padding: 40px; max-width: 1000px; margin: 0 auto; background: white;">
    <h1 class="section-title" style="color: #333; font-size: 2rem; margin-bottom: 20px; text-align: center; font-weight: bold;">Estrutura do Modelo E.MO.TI.VE</h1>
    
    <p style="text-align: left; margin-bottom: 40px; color: #333; font-size: 1rem; line-height: 1.6;">
        O instrumento avalia seis dimensões principais que, juntas, formam um <strong>retrato do seu equilíbrio psicossocial</strong>:
    </p>
    
    <!-- Contenedor principal con layout de dos columnas -->
    <div style="position: relative; min-height: 750px; margin: 40px 0; padding: 50px 0;">
        
        <!-- Etiquetas de Ejes -->
        <div style="position: absolute; top: 0; left: 0; font-size: 0.85rem; font-weight: bold; color: #333; z-index: 10;">
            EIXO 3 SUSTENTABILIDADE OCUPACIONAL
        </div>
        <div style="position: absolute; top: 0; right: 0; font-size: 0.85rem; font-weight: bold; color: #333; z-index: 10;">
            EIXO 1 ENERGIA EMOCIONAL
        </div>
        <div style="position: absolute; bottom: 5%; right: 0; font-size: 0.85rem; font-weight: bold; color: #333; z-index: 10;">
            EIXO 2 PROPÓSITO E RELAÇÕES
        </div>
        
        <!-- Imagen central circle-page2.png -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1;">
            <img src="{{ public_path('img/circle-page2.png') }}" alt="E.MO.TI.VE" style="width: 200px; height: 200px; object-fit: contain;">
        </div>
        
        <!-- SVG contenedor para puntos de conexión y líneas -->
        <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; pointer-events: none;">
            <!-- Puntos de conexión alrededor de la imagen central (6 puntos) -->
            <!-- La imagen está centrada a 500px (50% de 1000px), con 200px de ancho (400px-600px) -->
            <!-- Izquierda - posicionados alrededor del círculo -->
            <circle cx="350" cy="150" r="5" fill="#4169E1"/>  <!-- ASMO - arriba izquierda -->
            <circle cx="350" cy="310" r="5" fill="#9370DB"/>  <!-- EXTR - medio izquierda -->
            <circle cx="350" cy="490" r="5" fill="#90EE90"/>  <!-- FAPS - abajo izquierda -->
            <!-- Derecha - posicionados alrededor del círculo -->
            <circle cx="650" cy="150" r="5" fill="#8B4513"/>  <!-- EXEM - arriba derecha -->
            <circle cx="650" cy="310" r="5" fill="#D2691E"/>  <!-- REPR - medio derecha -->
            <circle cx="650" cy="490" r="5" fill="#FF8C00"/>  <!-- DECI - abajo derecha -->
            
            <!-- Líneas de conexión desde los puntos hacia las cajas de dimensiones -->
            <!-- ASMO (izquierda arriba) - desde punto hasta caja -->
            <line x1="350" y1="150" x2="280" y2="100" stroke="#666" stroke-width="1" opacity="0.3"/>
            <!-- EXTR (izquierda medio) -->
            <line x1="350" y1="310" x2="280" y2="280" stroke="#666" stroke-width="1" opacity="0.3"/>
            <!-- FAPS (izquierda abajo) -->
            <line x1="350" y1="490" x2="280" y2="460" stroke="#666" stroke-width="1" opacity="0.3"/>
            <!-- EXEM (derecha arriba) -->
            <line x1="650" y1="150" x2="720" y2="100" stroke="#666" stroke-width="1" opacity="0.3"/>
            <!-- REPR (derecha medio) -->
            <line x1="650" y1="310" x2="720" y2="280" stroke="#666" stroke-width="1" opacity="0.3"/>
            <!-- DECI (derecha abajo) -->
            <line x1="650" y1="490" x2="720" y2="460" stroke="#666" stroke-width="1" opacity="0.3"/>
        </svg>
        
        <!-- Layout de dos columnas -->
        <div style="display: flex; justify-content: space-between; position: relative; z-index: 3; margin-top: 20px;">
            
            <!-- Columna Izquierda -->
            <div style="width: 280px;">
                @php
                    $dimensoesEsquerda = [
                        [
                            'tag' => 'ASMO',
                            'nome' => 'Assédio Moral',
                            'cor' => '#4169E1',
                            'conceito' => 'comportamentos abusivos, constrangedores ou humilhantes.',
                            'pratico' => 'piadas ofensivas, isolamento, críticas públicas.',
                            'didatico' => 'Se identificar sinais, não ignore. Busque diálogo e, se necessário, canais formais de apoio.'
                        ],
                        [
                            'tag' => 'EXTR',
                            'nome' => 'Excesso de Trabalho',
                            'cor' => '#9370DB',
                            'conceito' => 'quando a carga de tarefas ultrapassa os limites pessoais.',
                            'pratico' => 'longas horas, sem pausas, sem equilíbrio com a vida pessoal.',
                            'didatico' => 'É preciso revisar prioridades, delegar e resgatar tempo para descanso.'
                        ],
                        [
                            'tag' => 'FAPS',
                            'nome' => 'Fatores Psicossociais',
                            'cor' => '#90EE90',
                            'conceito' => 'condições organizacionais - clareza de papéis, apoio, comunicação.',
                            'pratico' => 'pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.',
                            'didatico' => 'Procure identificar onde a comunicação falha e proponha pequenas ações de melhoria.'
                        ]
                    ];
                @endphp
                
                @foreach($dimensoesEsquerda as $index => $dim)
                    <div style="margin-bottom: 40px; position: relative;">
                        <!-- Caja de dimensión -->
                        <div style="background: white; border: 2px solid {{ $dim['cor'] }}; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                            <!-- Header -->
                            <div style="background: {{ $dim['cor'] }}; padding: 12px; color: white; font-weight: bold; font-size: 0.95rem;">
                                {{ $dim['nome'] }}
                            </div>
                            <!-- Contenido -->
                            <div style="padding: 15px;">
                                <div style="margin-bottom: 12px;">
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Conceito:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">{{ $dim['conceito'] }}</div>
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Prático:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">{{ $dim['pratico'] }}</div>
                                </div>
                                <div>
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Didático:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">"{{ $dim['didatico'] }}"</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Columna Derecha -->
            <div style="width: 280px;">
                @php
                    $dimensoesDireita = [
                        [
                            'tag' => 'EXEM',
                            'nome' => 'Exaustão Emocional',
                            'cor' => '#8B4513',
                            'conceito' => 'desgaste intenso, sensação de não ter energia.',
                            'pratico' => 'pode aparecer como irritabilidade, falta de motivação ou sintomas físicos.',
                            'didatico' => 'Se está alto, pense em revisar prazos, negociar demandas e buscar apoio.'
                        ],
                        [
                            'tag' => 'REPR',
                            'nome' => 'Realização Profissional',
                            'cor' => '#D2691E',
                            'conceito' => 'sensação de competência e propósito no trabalho.',
                            'pratico' => 'quando baixa, pode gerar insegurança, desvalorização e até desmotivação.',
                            'didatico' => 'Aqui vale pedir feedbacks, investir em desenvolvimento e resgatar pequenas conquistas.'
                        ],
                        [
                            'tag' => 'DECI',
                            'nome' => 'Despersonalização / Cinismo',
                            'cor' => '#FF8C00',
                            'conceito' => 'distanciamento afetivo do trabalho e das pessoas.',
                            'pratico' => 'tratar colegas com frieza, indiferença ou ironia.',
                            'didatico' => 'Se esse ponto estiver alto, é importante reconectar-se ao propósito do que faz e retomar vínculos de confiança.'
                        ]
                    ];
                @endphp
                
                @foreach($dimensoesDireita as $index => $dim)
                    <div style="margin-bottom: 40px; position: relative;">
                        <!-- Caja de dimensión -->
                        <div style="background: white; border: 2px solid {{ $dim['cor'] }}; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                            <!-- Header -->
                            <div style="background: {{ $dim['cor'] }}; padding: 12px; color: white; font-weight: bold; font-size: 0.95rem;">
                                {{ $dim['nome'] }}
                            </div>
                            <!-- Contenido -->
                            <div style="padding: 15px;">
                                <div style="margin-bottom: 12px;">
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Conceito:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">{{ $dim['conceito'] }}</div>
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Prático:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">{{ $dim['pratico'] }}</div>
                                </div>
                                <div>
                                    <div style="font-weight: bold; color: #333; font-size: 0.85rem; margin-bottom: 5px;">Didático:</div>
                                    <div style="color: #333; font-size: 0.85rem; line-height: 1.5;">"{{ $dim['didatico'] }}"</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Sección de Eixos Analíticos -->
    <div style="margin-top: 60px; padding: 30px; background: #f8f9fa; border-radius: 12px;">
        <h3 style="color: #333; margin-bottom: 20px; font-size: 1.2rem; font-weight: bold;">
            Essas dimensões se organizam em três eixos analíticos:
        </h3>
        
        <ul style="list-style: none; padding: 0; margin: 0; line-height: 2;">
            <li style="margin-bottom: 10px; color: #333; font-size: 1rem;">
                • <strong>Energia Emocional:</strong> entre o cansaço e a vitalidade.
            </li>
            <li style="margin-bottom: 10px; color: #333; font-size: 1rem;">
                • <strong>Propósito e Relações:</strong> entre o engajamento e o distanciamento.
            </li>
            <li style="margin-bottom: 10px; color: #333; font-size: 1rem;">
                • <strong>Sustentabilidade Ocupacional:</strong> entre o esforço e o suporte recebido.
            </li>
        </ul>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <p style="font-size: 0.9rem; color: #666; margin: 0; font-weight: bold;">fellipelli</p>
            <div style="width: 30px; height: 30px; border: 2px solid #00a8b5; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(0, 168, 181, 0.1);">
                <svg width="20" height="15" viewBox="0 0 20 15" style="stroke: #00a8b5; fill: none; stroke-width: 1.5;">
                    <path d="M2,7 Q5,3 8,7 T14,7" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: #666; margin: 0; font-weight: bold;">E.MO.TI.VE</p>
                <p style="font-size: 0.75rem; color: #888; margin: 0;">Burnout e Bem-estar</p>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 02</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- RESULTADO E.MO.TI.VE --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">SEU RESULTADO E.MO.TI.VE</h1>
    
    <!-- Dados do Respondente -->
    <div style="margin-bottom: 30px;">
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">Dados do respondente</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <p style="margin: 5px 0; font-size: 11px;"><strong>Formulário:</strong> {{ $formulario->label }} – {{ $formulario->nome }}</p>
            <p style="margin: 5px 0; font-size: 11px;"><strong>Participante:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p style="margin: 5px 0; font-size: 11px;"><strong>Data:</strong> {{ \Carbon\Carbon::parse($respostasUsuario->first()->created_at ?? now())->format('d/m/Y') }}</p>
            <p style="margin: 5px 0; font-size: 11px;"><strong>Respostas registradas:</strong> {{ $respostasUsuario->count() }} de {{ $formulario->perguntas->count() }}</p>
            <p style="margin: 5px 0; font-size: 11px;"><strong>Dimensões avaliadas:</strong> 
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
                    <ul style="margin: 0; padding-left: 20px; color: #333; font-size: 11px;">
                        @foreach($grupoModerada as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoBaixa))
                <div style="background: #e8f1fa; border-left: 4px solid #1a73e8; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #155724; margin-bottom: 15px; font-size: 1.1rem;">Faixa Baixa</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #333; font-size: 11px;">
                        @foreach($grupoBaixa as $dim)
                            <li style="margin: 5px 0;">{{ $dim }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($grupoAlta))
                <div style="background: #fdecea; border-left: 4px solid #d93025; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #721c24; margin-bottom: 15px; font-size: 1.1rem;">Faixa Alta</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #333; font-size: 11px;">
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
            <img src="{{ public_path($imagemRadar) }}" alt="Gráfico Radar" style="max-width: 100%; height: auto;">
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

<div class="page-break"></div>

{{-- ESTADO EMOCIONAL E PSICOSSOCIAL --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 10px;">ESTADO EMOCIONAL E PSICOSSOCIAL</h1>
    
    <!-- Como Ler Seus Resultados -->
    <div style="margin-bottom: 40px;">
        <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">Como Ler Seus Resultados</h2>
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            Cada dimensão é apresentada em faixas de pontuação que indicam seu estado atual:
        </p>
        <ul style="line-height: 2; padding-left: 25px; margin-bottom: 20px; font-size: 11px;">
            <li><strong>Faixa Baixa:</strong> equilíbrio emocional saudável, sem sinais de risco.</li>
            <li><strong>Faixa Moderada:</strong> pontos de atenção que merecem acompanhamento.</li>
            <li><strong>Faixa Alta:</strong> indica necessidade de reflexão e cuidado ativo.</li>
        </ul>
        
        <div style="background: #fff9e5; border-left: 4px solid #f4b400; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #856404; font-size: 11px;">Importante: Nenhum resultado define você.</p>
        </div>
        
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
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
                <span style="font-weight: bold; color: {{ $faixaColor }}; font-size: 11px;">Faixa {{ $ponto['faixa'] }}</span>
                <span style="font-size: 1.5rem; font-weight: bold; color: #333;">{{ $ponto['valor'] }}</span>
            </div>
            
            <p style="text-align: justify; line-height: 1.8; color: #555; font-size: 11px;">
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

<div class="page-break"></div>

{{-- EIXOS ANALÍTICOS --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px; text-align: center;">ÍNDICE EIXOS ANALÍTICOS E.MO.TI.VE</h1>
    
    @php
        $eixos = [
            'eixo1' => $ejesAnaliticos['eixo1'],
            'eixo2' => $ejesAnaliticos['eixo2'],
            'eixo3' => $ejesAnaliticos['eixo3']
        ];
    @endphp
    
    @foreach($eixos as $key => $eixo)
        <div style="margin-bottom: 50px;">
            <h2 class="section-subtitle" style="color: #00a8b5; margin-bottom: 15px;">{{ $eixo['nome'] }}</h2>
            <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px; color: #555; font-size: 11px;">
                {{ $eixo['descricao'] }}
            </p>
            
            <!-- Box de Resultados -->
            <div style="background: #f0f0f0; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 20px; align-items: center;">
                    <!-- Dimensão 1 -->
                    <div>
                        <div style="font-weight: bold; margin-bottom: 10px; color: #333; font-size: 11px;">{{ $eixo['dimensao1']['nome'] }}</div>
                        <div class="faixa-badge faixa-{{ strtolower($eixo['dimensao1']['faixa']) }}" style="background: {{ $eixo['dimensao1']['faixa'] === 'Baixa' ? '#d4edda' : ($eixo['dimensao1']['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $eixo['dimensao1']['faixa'] === 'Baixa' ? '#155724' : ($eixo['dimensao1']['faixa'] === 'Moderada' ? '#856404' : '#721c24') }}; font-size: 11px; padding: 5px 15px;">
                            Faixa {{ $eixo['dimensao1']['faixa'] }}
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div style="text-align: center;">
                        <div style="font-weight: bold; font-size: 1.2rem; color: #333; margin-bottom: 5px; font-size: 14px;">TOTAL</div>
                        <div style="font-size: 2rem; font-weight: bold; color: #008ca5;">{{ round($eixo['total']) }}</div>
                    </div>
                    
                    <!-- Dimensão 2 -->
                    <div style="text-align: right;">
                        <div style="font-weight: bold; margin-bottom: 10px; color: #333; font-size: 11px;">{{ $eixo['dimensao2']['nome'] }}</div>
                        <div class="faixa-badge faixa-{{ strtolower($eixo['dimensao2']['faixa']) }}" style="background: {{ $eixo['dimensao2']['faixa'] === 'Baixa' ? '#d4edda' : ($eixo['dimensao2']['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $eixo['dimensao2']['faixa'] === 'Baixa' ? '#155724' : ($eixo['dimensao2']['faixa'] === 'Moderada' ? '#856404' : '#721c24') }}; font-size: 11px; padding: 5px 15px;">
                            Faixa {{ $eixo['dimensao2']['faixa'] }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Interpretação -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #008ca5;">
                <p style="margin: 0 0 10px 0; font-size: 11px;"><strong>Interpretação:</strong> {{ $eixo['interpretacao']['interpretacao'] }}</p>
                <p style="margin: 0 0 10px 0; font-size: 11px;"><strong>Significado Psicológico:</strong> {{ $eixo['interpretacao']['significado'] }}</p>
                <p style="margin: 0; font-size: 11px;"><strong>Orientações Práticas:</strong> {{ $eixo['interpretacao']['orientacoes'] }}</p>
            </div>
        </div>
    @endforeach
    
    <!-- Síntese Geral -->
    <div style="margin-top: 50px; padding: 30px; background: #e8f4f8; border-radius: 12px;">
        <h3 style="color: #008ca5; margin-bottom: 20px; font-size: 1.3rem;">🔄 Síntese Geral dos Eixos</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background: white;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Eixo</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Quando Equilibrado</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Quando em Risco</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Energia Emocional</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Vitalidade, propósito e produtividade sustentável.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Fadiga, desânimo e queda de performance.</td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Propósito e Relações</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Engajamento e empatia nas relações.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Isolamento, cinismo e falta de confiança.</td>
                </tr>
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Sustentabilidade Ocupacional</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Respeito, ritmo equilibrado e apoio mútuo.</td>
                    <td style="padding: 12px; border: 1px solid #ddd;">Sobrecarga, desrespeito e desgaste emocional.</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 0.9rem; color: #666; margin: 0;"><strong>fellipelli</strong></p>
            <p style="font-size: 0.85rem; color: #888; margin: 5px 0 0 0;">E.MO.TI.VE | Burnout e Bem-estar</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.8rem; color: #999; margin: 0;">Todos os direitos reservados a Fellipelli Consultoria</p>
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 05</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- RISCO DE DESCARRILAMENTO --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">RISCO DE DESCARRILAMENTO EMOCIONAL E OCUPACIONAL</h1>
    
    <div style="margin-bottom: 30px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; font-size: 11px;">
            O <strong>risco de descarrilamento</strong> representa a probabilidade de perder o equilíbrio emocional, motivacional e funcional no trabalho. Este índice é derivado das interações entre os três eixos analíticos do modelo E.MO.TI.VE:
        </p>
        <ol style="line-height: 2; padding-left: 25px; margin-bottom: 20px; font-size: 11px;">
            <li><strong>Energia Emocional:</strong> capacidade de sustentar vitalidade e propósito</li>
            <li><strong>Propósito e Relações:</strong> qualidade das conexões e do engajamento social</li>
            <li><strong>Sustentabilidade Ocupacional:</strong> equilíbrio entre esforço e suporte recebido</li>
        </ol>
        <p style="text-align: justify; line-height: 1.8; font-size: 11px;">
            Cada eixo gera um índice individual (0 a 100) e, quando combinados, formam o <strong>Índice Integrado de Descarrilamento (IID)</strong>.
        </p>
    </div>
    
    <!-- Gráfico de Risco -->
    <div style="margin: 40px 0;">
        <div style="background: #f8f9fa; padding: 30px; border-radius: 12px; position: relative;">
            <!-- Barra de risco -->
            <div style="position: relative; height: 60px; background: linear-gradient(to right, #28a745 0%, #28a745 25%, #ffc107 25%, #ffc107 65%, #fd7e14 65%, #fd7e14 89%, #dc3545 89%, #dc3545 100%); border-radius: 30px; margin: 20px 0;">
                <!-- Labels -->
                <div style="position: absolute; top: -25px; left: 0; width: 100%; display: flex; justify-content: space-between;">
                    <span style="font-size: 0.85rem; font-weight: bold;">Baixo</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Médio</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Atenção</span>
                    <span style="font-size: 0.85rem; font-weight: bold;">Alto</span>
                </div>
                
                <!-- Indicador -->
                @php
                    $posicao = ($iid / 100) * 100;
                    if ($iid <= 40) {
                        $posicao = ($iid / 40) * 25;
                    } elseif ($iid <= 65) {
                        $posicao = 25 + (($iid - 40) / 25) * 40;
                    } elseif ($iid <= 89) {
                        $posicao = 65 + (($iid - 65) / 24) * 24;
                    } else {
                        $posicao = 89 + (($iid - 89) / 11) * 11;
                    }
                @endphp
                <div style="position: absolute; left: {{ $posicao }}%; top: -10px; transform: translateX(-50%);">
                    <div style="width: 3px; height: 80px; background: #000; margin: 0 auto;"></div>
                    <div style="width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 12px solid #000; margin: 0 auto;"></div>
                </div>
            </div>
            
            <!-- Pontuação e Interpretação -->
            <div style="text-align: center; margin-top: 30px;">
                <p style="font-size: 1.3rem; font-weight: bold; color: {{ $nivelRisco['cor_hex'] }}; margin-bottom: 15px;">
                    Pontuação = {{ $iid }} - {{ $nivelRisco['zona'] }}
                </p>
                
                @php
                    $interpretacaoIID = '';
                    if ($iid <= 40) {
                        $interpretacaoIID = 'Seu equilíbrio emocional está em uma zona saudável. Continue mantendo hábitos que promovem bem-estar e sustentabilidade.';
                    } elseif ($iid <= 65) {
                        $interpretacaoIID = 'Pequenas oscilações de energia e propósito, mas ainda sem impacto funcional. Pode haver início de fadiga ou leve desconexão emocional. Reequilibrar rotinas e priorizar autocuidado. Conversar sobre sobrecarga antes que se intensifique.';
                    } elseif ($iid <= 89) {
                        $interpretacaoIID = 'Sinais de vulnerabilidade emocional estão presentes. Há necessidade de atenção e ajustes para prevenir o agravamento. Busque apoio e reorganize prioridades.';
                    } else {
                        $interpretacaoIID = 'Risco crítico identificado. É importante buscar apoio profissional imediato e revisar condições de trabalho. Nenhum resultado justifica adoecimento.';
                    }
                @endphp
                
                <ul style="text-align: left; display: inline-block; line-height: 2; margin: 20px 0; font-size: 11px;">
                    <li>{{ $interpretacaoIID }}</li>
                </ul>
            </div>
        </div>
        
        <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 20px; margin: 30px 0;">
            <p style="margin: 0; color: #333; font-size: 11px; line-height: 1.8;">
                "O descarrilamento emocional raramente ocorre de forma súbita - ele é o resultado de pequenas desconexões acumuladas."
            </p>
            <p style="margin: 10px 0 0 0; color: #333; font-size: 11px; line-height: 1.8;">
                "Reconhecer os sinais precoces é o maior ato de autocuidado e responsabilidade profissional."
            </p>
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
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 06</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- SAÚDE EMOCIONAL --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #8B4513; font-size: 2rem; margin-bottom: 10px;">SAÚDE EMOCIONAL</h1>
    <h2 class="section-subtitle" style="color: #90EE90; font-size: 1.5rem; margin-bottom: 30px;">Análise Geral</h2>
    
    <div style="margin-bottom: 30px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px; font-size: 11px;">
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
                <h3 style="color: {{ $dimInfo['cor'] }}; font-size: 1.3rem; margin: 0; font-size: 14px;">{{ $ponto['nome'] }} ({{ $ponto['tag'] }})</h3>
                <div class="faixa-badge faixa-{{ strtolower($ponto['faixa']) }}" style="background: {{ $ponto['faixa'] === 'Baixa' ? '#d4edda' : ($ponto['faixa'] === 'Moderada' ? '#fff3cd' : '#f8d7da') }}; color: {{ $ponto['faixa'] === 'Baixa' ? '#155724' : ($ponto['faixa'] === 'Moderada' ? '#856404' : '#721c24') }}; font-size: 11px; padding: 5px 15px;">
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
            
            <p style="text-align: justify; line-height: 1.8; margin-bottom: 15px; color: #555; font-size: 11px;">
                {{ $descricao }}
            </p>
            
            <div style="margin-top: 15px;">
                <p style="margin: 8px 0; font-size: 11px;"><strong>Ponto forte:</strong> {{ $pontoForte }}</p>
                <p style="margin: 8px 0; font-size: 11px;"><strong>Orientação prática:</strong> {{ $orientacao }}</p>
            </div>
        </div>
    @endforeach
    
    <!-- Conclusão -->
    <div style="margin-top: 40px; padding: 25px; background: #f8f9fa; border-radius: 12px;">
        <p style="text-align: justify; line-height: 1.8; font-size: 11px; font-weight: bold; color: #333; margin: 0;">
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
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 07</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- PLANO DE DESENVOLVIMENTO --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">PLANO DE DESENVOLVIMENTO PESSOAL</h1>
    
    <!-- Zona de Risco -->
    <div style="margin-bottom: 40px;">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="width: 12px; height: 12px; background: {{ $nivelRisco['cor_hex'] }}; border-radius: 50%; margin-right: 10px;"></div>
            <h2 style="color: {{ $nivelRisco['cor_hex'] }}; font-size: 1.3rem; margin: 0;">{{ $nivelRisco['zona'] }}</h2>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Objetivo:</h3>
            <p style="text-align: justify; line-height: 1.8; color: #555; margin-bottom: 20px; font-size: 11px;">
                {{ $planDesenvolvimento['objetivo'] }}
            </p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Ações sugeridas:</h3>
            <ol style="line-height: 2; padding-left: 25px; color: #555; font-size: 11px;">
                @foreach($planDesenvolvimento['acoes'] as $acao)
                    <li>{{ $acao }}</li>
                @endforeach
            </ol>
        </div>
        
        <div>
            <h3 style="color: #00a8b5; margin-bottom: 10px; font-size: 1.1rem;">Indicador de progresso:</h3>
            <p style="text-align: justify; line-height: 1.8; color: #555; margin: 0; font-size: 11px;">
                {{ $planDesenvolvimento['indicador'] }}
            </p>
        </div>
    </div>
    
    <!-- Citação -->
    <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 25px; margin: 40px 0; text-align: center;">
        <p style="margin: 0; font-size: 11px; font-style: italic; color: #333; line-height: 1.8;">
            "O equilíbrio não é ausência de desafios, mas a capacidade de se manter inteiro diante deles."
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
            <p style="font-size: 0.8rem; color: #999; margin: 5px 0 0 0;">Pág. 08</p>
        </div>
    </div>
</div>

<div class="page-break"></div>

{{-- CONCLUSÃO --}}
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 class="section-title" style="color: #008ca5; font-size: 2rem; margin-bottom: 30px;">CONCLUSÃO GERAL</h1>
    
    <!-- Autodesenvolvimento -->
    <div style="margin-bottom: 40px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px; color: #555; font-size: 11px;">
            O autodesenvolvimento é um processo contínuo e intencional. O E.MO.TI.VE oferece insights sobre fatores que influenciam seu equilíbrio emocional no trabalho, destacando áreas de força e vulnerabilidade. Ao construir uma rotina mais autoconhecedora, saudável e produtiva, você fortalece seu bem-estar e performance.
        </p>
        
        <div class="quote-box" style="background-color: #e8f4f8; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <p style="margin: 0; font-style: italic; color: #333; font-size: 11px; line-height: 1.8;">
                "Cuidar de si é um ato de liderança silenciosa: quando você se equilibra, o ambiente ao seu redor também muda."
            </p>
        </div>
    </div>
    
    <!-- Passo Importante -->
    <div style="margin-bottom: 40px;">
        <p style="text-align: justify; line-height: 1.8; margin-bottom: 20px; color: #555; font-size: 11px;">
            Pausar, observar e compreender suas emoções é um passo importante para o crescimento. O equilíbrio emocional é uma prática contínua que requer atenção, respeito por si mesmo e cuidado ativo. Ao desenvolver essas habilidades, você constrói uma base sólida para melhor performance e bem-estar sustentável.
        </p>
        
        <div class="quote-box" style="background-color: #f0f0f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <p style="margin: 0; font-style: italic; color: #333; font-size: 11px; line-height: 1.8;">
                "A mudança começa quando nos olhamos com gentileza."
            </p>
        </div>
    </div>
    
    <!-- Consultoria -->
    <div style="margin-top: 60px; padding-top: 30px; border-top: 2px solid #008ca5;">
        <h2 style="color: #008ca5; font-size: 1.5rem; margin-bottom: 10px;">Fellipelli Consultoria</h2>
        <p style="color: #666; font-size: 11px; margin: 0;">
            Transformando autoconhecimento em desenvolvimento humano.
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

@if (isset($pdf))
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 8;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT} — {{ $user->name }} — {{ $hoje }}";
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = (595.28 - $width) / 2;
            $y = 820;
            $pdf->text($x, $y, $text, $font, $size);
        }
    </script>
@endif

</body>
</html>

