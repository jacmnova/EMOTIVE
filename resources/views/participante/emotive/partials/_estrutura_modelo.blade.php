<div class="page-break" style="padding: 40px; max-width: 1000px; margin: 0 auto; background: white;">
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
            <img src="{{ asset('img/circle-page2.png') }}" alt="E.MO.TI.VE" style="width: 200px; height: 200px; object-fit: contain;">
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
