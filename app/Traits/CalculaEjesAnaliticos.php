<?php

namespace App\Traits;

trait CalculaEjesAnaliticos
{
    /**
     * Calcula os índices EE, PR y SO directamente desde las respuestas según el CSV
     */
    protected function calcularIndicesDesdeRespostas($respostasUsuario, $formularioId): array
    {
        // Usar helper para identificar preguntas invertidas por texto
        
        // Agrupaciones según el CSV ALE (usando numero_da_pergunta)
        // Extraídas del archivo: EMULADOR - EMOTIVE ALE - perguntas_completas_99 (1).csv
        // EE = EXEM ∪ REPR (unión de preguntas de Exaustão Emocional y Realização Profissional)
        // PR = DECI ∪ FAPS (unión de preguntas de Cinismo y Fatores Psicossociais)
        // SO = EXTR ∪ ASMO (unión de preguntas de Excesso de Trabalho y Assédio Moral)
        $indices = [
            'EE' => [28, 29, 30, 33, 34, 37, 38, 39, 40, 41, 43, 44, 45, 47, 55, 56, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99], // 29 preguntas (EXEM ∪ REPR)
            'PR' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 48, 49, 50, 51, 52, 53, 54, 55, 56, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87], // 40 preguntas (DECI ∪ FAPS)
            'SO' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77], // 31 preguntas (EXTR ∪ ASMO)
        ];
        
        // Cargar todas las preguntas indexadas por numero_da_pergunta
        $perguntas = \App\Models\Pergunta::where('formulario_id', $formularioId)
            ->get()
            ->keyBy('numero_da_pergunta');
        
        $resultados = [];
        
        foreach ($indices as $indice => $numeroPerguntas) {
            $pontuacao = 0;
            $preguntasProcesadas = 0;
            $preguntasFaltantes = [];
            $preguntasSinRespuesta = [];
            $detallesCalculo = []; // Para debugging
            
            foreach ($numeroPerguntas as $numeroPergunta) {
                // Buscar la pregunta por numero_da_pergunta
                $pergunta = $perguntas->get($numeroPergunta);
                if (!$pergunta) {
                    $preguntasFaltantes[] = $numeroPergunta;
                    \Log::warning('Pregunta no encontrada por numero_da_pergunta', [
                        'indice' => $indice,
                        'numero_da_pergunta' => $numeroPergunta,
                        'formulario_id' => $formularioId
                    ]);
                    continue;
                }
                
                // Buscar la respuesta por pregunta_id (ID de la base de datos)
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    $preguntasSinRespuesta[] = $numeroPergunta;
                    \Log::debug('Respuesta no encontrada o nula', [
                        'indice' => $indice,
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A'
                    ]);
                    continue;
                }
                
                $valorOriginal = (int)$resposta->valor_resposta;
                
                // Validar que el valor esté en el rango correcto (0-6)
                if ($valorOriginal < 0 || $valorOriginal > 6) {
                    \Log::warning('Valor de respuesta fuera de rango', [
                        'indice' => $indice,
                        'numero_da_pergunta' => $numeroPergunta,
                        'valor_original' => $valorOriginal
                    ]);
                    continue;
                }
                
                // Verificar si requiere inversión usando helper por texto
                $necesitaInversion = \App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta);
                
                // Invertir el valor: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
                // En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                
                // Guardar detalles para debugging
                $detallesCalculo[] = [
                    'numero' => $numeroPergunta,
                    'valor_original' => $valorOriginal,
                    'invertida' => $necesitaInversion,
                    'valor_usado' => $valorUsado
                ];
                
                $pontuacao += $valorUsado;
                $preguntasProcesadas++;
            }
            
            \Log::info('Índice calculado', [
                'indice' => $indice,
                'total_preguntas_esperadas' => count($numeroPerguntas),
                'preguntas_procesadas' => $preguntasProcesadas,
                'preguntas_faltantes' => count($preguntasFaltantes),
                'preguntas_faltantes_lista' => $preguntasFaltantes,
                'preguntas_sin_respuesta' => count($preguntasSinRespuesta),
                'preguntas_sin_respuesta_lista' => $preguntasSinRespuesta,
                'pontuacao_total' => $pontuacao,
                'maximo_teorico' => count($numeroPerguntas) * 6
            ]);
            
            // Validar que se procesaron todas las preguntas esperadas
            if ($preguntasProcesadas < count($numeroPerguntas)) {
                \Log::warning('No se procesaron todas las preguntas esperadas', [
                    'indice' => $indice,
                    'esperadas' => count($numeroPerguntas),
                    'procesadas' => $preguntasProcesadas,
                    'faltantes' => count($preguntasFaltantes),
                    'sin_respuesta' => count($preguntasSinRespuesta)
                ]);
            }
            
            $resultados[$indice] = $pontuacao;
        }
        
        return $resultados;
    }

    /**
     * Calcula os eixos analíticos do modelo E.MO.TI.VE
     */
    protected function calcularEjesAnaliticos($pontuacoes, $indices = null): array
    {
        // Mapear pontuações por tag
        $pontosPorTag = [];
        foreach ($pontuacoes as $ponto) {
            $tag = is_array($ponto) ? strtoupper($ponto['tag'] ?? $ponto['tag'] ?? '') : '';
            $valor = is_array($ponto) ? ($ponto['valor'] ?? $ponto['pontuacao'] ?? 0) : 0;
            $faixa = is_array($ponto) ? ($ponto['faixa'] ?? 'Baixa') : 'Baixa';
            
            $pontosPorTag[$tag] = ['valor' => $valor, 'faixa' => $faixa];
        }

        // Obtener valores de dimensiones para las interpretaciones
        $exaustao = $pontosPorTag['EXEM'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        $realizacao = $pontosPorTag['REPR'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        $cinismo = $pontosPorTag['DECI'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        $fatores = $pontosPorTag['FAPS'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        $excesso = $pontosPorTag['EXTR'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        $assedio = $pontosPorTag['ASMO'] ?? ['valor' => 0, 'faixa' => 'Baixa'];
        
        // Si se proporcionan índices calculados directamente, usarlos
        // Si no, calcular usando las fórmulas originales como fallback
        if ($indices && isset($indices['EE']) && isset($indices['PR']) && isset($indices['SO'])) {
            $eixo1Total = $indices['EE'];
            $eixo2Total = $indices['PR'];
            $eixo3Total = $indices['SO'];
        } else {
            // Fallback a fórmulas originales si no hay índices directos
            $eixo1Total = max(0, min(100, ($realizacao['valor'] - $exaustao['valor'] + 100) / 2));
            $eixo2Total = max(0, min(100, ($fatores['valor'] - $cinismo['valor'] + 100) / 2));
            $eixo3Total = max(0, min(100, 100 - (($excesso['valor'] + $assedio['valor']) / 2)));
        }
        
        $eixo1 = [
            'nome' => 'ENERGIA EMOCIONAL',
            'descricao' => 'Este eixo mostra o quanto sua energia emocional está sendo renovada ou drenada no trabalho. Ele representa o equilíbrio entre vitalidade e propósito.',
            'dimensao1' => [
                'nome' => 'Exaustão Emocional',
                'tag' => 'EXEM',
                'valor' => $exaustao['valor'],
                'faixa' => $exaustao['faixa']
            ],
            'dimensao2' => [
                'nome' => 'Realização Profissional',
                'tag' => 'REPR',
                'valor' => $realizacao['valor'],
                'faixa' => $realizacao['faixa']
            ],
            'total' => round($eixo1Total, 0),
            'interpretacao' => $this->interpretarEixo1($exaustao['faixa'], $realizacao['faixa'])
        ];

        // EIXO 2: PROPÓSITO E RELAÇÕES
        $eixo2 = [
            'nome' => 'PROPÓSITO E RELAÇÕES',
            'descricao' => 'Este eixo avalia o grau de conexão emocional e relacional com o ambiente de trabalho — ou seja, se o participante sente pertencimento, confiança e reciprocidade.',
            'dimensao1' => [
                'nome' => 'Despersonalização / Cinismo',
                'tag' => 'DECI',
                'valor' => $cinismo['valor'],
                'faixa' => $cinismo['faixa']
            ],
            'dimensao2' => [
                'nome' => 'Fatores Psicossociais',
                'tag' => 'FAPS',
                'valor' => $fatores['valor'],
                'faixa' => $fatores['faixa']
            ],
            'total' => round($eixo2Total, 0),
            'interpretacao' => $this->interpretarEixo2($cinismo['faixa'], $fatores['faixa'])
        ];

        // EIXO 3: SUSTENTABILIDADE OCUPACIONAL
        $eixo3Total = isset($indices['SO']) ? $indices['SO'] : $eixo3Total;
        $eixo3 = [
            'nome' => 'SUSTENTABILIDADE OCUPACIONAL',
            'descricao' => 'Este eixo reflete a relação entre o esforço exigido e o suporte ético e emocional oferecido pelo ambiente. Mostra se o trabalho é sustentável — isto é, se há equilíbrio entre pressão e respeito.',
            'dimensao1' => [
                'nome' => 'Excesso de Trabalho',
                'tag' => 'EXTR',
                'valor' => $excesso['valor'],
                'faixa' => $excesso['faixa']
            ],
            'dimensao2' => [
                'nome' => 'Assédio Moral',
                'tag' => 'ASMO',
                'valor' => $assedio['valor'],
                'faixa' => $assedio['faixa']
            ],
            'total' => round($eixo3Total, 0),
            'interpretacao' => $this->interpretarEixo3($excesso['faixa'], $assedio['faixa'])
        ];

        return [
            'eixo1' => $eixo1,
            'eixo2' => $eixo2,
            'eixo3' => $eixo3
        ];
    }

    /**
     * Calcula o Índice Integrado de Descarrilamento (IID) / Índice Global de Saúde Emocional (IGSE)
     * 
     * El IID se calcula como: (Promedio de EE, PR, SO) / (Promedio de máximos) * 100
     * 
     * Máximos según CSV ALE (línea 5):
     * - EE: 276
     * - PR: 234
     * - SO: 186
     * Promedio de máximos: (276 + 234 + 186) / 3 = 232
     * 
     * NOTA: Estos máximos son valores de referencia del CSV, no los máximos teóricos
     * de la suma simple (19×6=114, 12×6=72, 14×6=84).
     */
    protected function calcularIID($ejesAnaliticos): float
    {
        // Valores de los índices EE, PR, SO
        $ee = $ejesAnaliticos['eixo1']['total'];
        $pr = $ejesAnaliticos['eixo2']['total'];
        $so = $ejesAnaliticos['eixo3']['total'];
        
        // Promedio de EE, PR, SO
        $promedioIndices = ($ee + $pr + $so) / 3;
        
        // Máximos según el CSV ALE (valores de referencia)
        $maxEE = 276;
        $maxPR = 234;
        $maxSO = 186;
        
        // Promedio de máximos
        $promedioMaximos = ($maxEE + $maxPR + $maxSO) / 3; // = 232
        
        // IID como porcentaje: (promedio índices / promedio máximos) * 100
        $iid = ($promedioIndices / $promedioMaximos) * 100;
        
        return round($iid, 2);
    }

    /**
     * Determina o nível de risco baseado no IID
     */
    protected function determinarNivelRisco($iid): array
    {
        if ($iid <= 40) {
            return [
                'nivel' => 'Baixo',
                'zona' => 'Zona de Equilíbrio Emocional',
                'cor' => 'success',
                'cor_hex' => '#28a745'
            ];
        } elseif ($iid <= 65) {
            return [
                'nivel' => 'Médio',
                'zona' => 'Zona de Atenção Preventiva',
                'cor' => 'warning',
                'cor_hex' => '#ffc107'
            ];
        } elseif ($iid <= 89) {
            return [
                'nivel' => 'Atenção',
                'zona' => 'Zona de Vulnerabilidade',
                'cor' => 'danger',
                'cor_hex' => '#fd7e14'
            ];
        } else {
            return [
                'nivel' => 'Alto',
                'zona' => 'Zona Crítica',
                'cor' => 'danger',
                'cor_hex' => '#dc3545'
            ];
        }
    }

    /**
     * Retorna o plano de desenvolvimento baseado no nível de risco
     */
    protected function getPlanDesenvolvimento($nivelRisco): array
    {
        $planos = [
            'Baixo' => [
                'objetivo' => 'Preservar hábitos saudáveis e fortalecer a resiliência emocional, garantindo sustentabilidade no longo prazo.',
                'acoes' => [
                    'Continuar praticando hábitos que promovem bem-estar (sono, lazer, pausas e alimentação equilibrada).',
                    'Manter conversas regulares de alinhamento e reconhecimento com a liderança.',
                    'Engajar-se em projetos que ampliem o senso de propósito e desafio saudável.'
                ],
                'indicador' => 'Sensação de equilíbrio mantida, com boa energia e motivação estável.'
            ],
            'Médio' => [
                'objetivo' => 'Evitar o acúmulo de estresse e reequilibrar a rotina para prevenir sobrecarga.',
                'acoes' => [
                    'Revisar compromissos e priorizar o essencial, delegando ou reorganizando prazos.',
                    'Incluir pausas ativas diárias (respiração, caminhada curta, desconexão digital).',
                    'Buscar feedback sobre performance e bem-estar, promovendo diálogo transparente com pares e liderança.'
                ],
                'indicador' => 'Redução de momentos de tensão e aumento da clareza sobre prioridades.'
            ],
            'Atenção' => [
                'objetivo' => 'Restabelecer energia emocional, reforçar suporte social e realinhar expectativas profissionais.',
                'acoes' => [
                    'Identificar fontes de exaustão e negociar ajustes de carga ou tarefas críticas.',
                    'Buscar apoio psicológico, coaching ou mentoria para reorganizar metas e propósito.',
                    'Retomar vínculos sociais e práticas que gerem prazer e pertencimento no trabalho.'
                ],
                'indicador' => 'Recuperação gradual de vitalidade e engajamento, com percepção de apoio e controle.'
            ],
            'Alto' => [
                'objetivo' => 'Promover recuperação emocional imediata e restaurar equilíbrio ocupacional.',
                'acoes' => [
                    'Interromper sobrecargas e alinhar, junto ao RH/liderança, um plano de pausa ou redistribuição de demandas.',
                    'Buscar acompanhamento psicológico ou médico especializado.',
                    'Redefinir metas de curto prazo com foco em autocuidado e reabilitação emocional.'
                ],
                'indicador' => 'Redução dos sintomas de esgotamento e sensação de segurança psicológica restabelecida.'
            ]
        ];

        return $planos[$nivelRisco['nivel']] ?? $planos['Médio'];
    }

    /**
     * Classifica pontuação em faixa
     */
    protected function classificarPontuacao($valor, $variavel): string
    {
        if ($valor <= $variavel->B) {
            return 'Baixa';
        } elseif ($valor <= $variavel->M) {
            return 'Moderada';
        } else {
            return 'Alta';
        }
    }

    /**
     * Interpreta o Eixo 1 baseado nas combinações de faixas
     */
    protected function interpretarEixo1($exaustaoFaixa, $realizacaoFaixa): array
    {
        $interpretacoes = [
            'Alta-Alta' => [
                'interpretacao' => 'Engajamento em Excesso',
                'significado' => 'Energia e propósito coexistem, mas o corpo pode estar pagando o preço.',
                'orientacoes' => 'Valorize pausas, reconheça sinais de fadiga e equilibre ambição com autocuidado.'
            ],
            'Alta-Moderada' => [
                'interpretacao' => 'Estado de Esforço Contínuo',
                'significado' => 'Há sobrecarga, mas o propósito ainda motiva. O risco é ultrapassar o limite sem perceber.',
                'orientacoes' => 'Preserve seus espaços de recuperação e delegue tarefas. Sustente a motivação sem comprometer a saúde.'
            ],
            'Alta-Baixa' => [
                'interpretacao' => '⚠️ Estado Crítico',
                'significado' => 'Alto risco de esgotamento. A sensação de impotência e perda de propósito indica necessidade de pausa e apoio.',
                'orientacoes' => 'Reduza o ritmo, priorize descanso, converse com sua liderança e reflita sobre o que dá sentido ao seu trabalho.'
            ],
            'Moderada-Alta' => [
                'interpretacao' => 'Equilíbrio Dinâmico',
                'significado' => 'Boa realização com cansaço controlado. Indica produtividade saudável.',
                'orientacoes' => 'Mantenha rituais de descanso e reconheça conquistas. Esse é um ponto ótimo.'
            ],
            'Moderada-Moderada' => [
                'interpretacao' => 'Estado de Manutenção',
                'significado' => 'Equilíbrio funcional. Nem sobrecarregado, nem entediado.',
                'orientacoes' => 'Continue cuidando do ritmo e do engajamento. Práticas de gratidão ajudam a fortalecer esse equilíbrio.'
            ],
            'Moderada-Baixa' => [
                'interpretacao' => 'Desânimo Progressivo',
                'significado' => 'Esforço emocional sem retorno de propósito. Pode evoluir para desmotivação.',
                'orientacoes' => 'Busque feedbacks e alinhe expectativas. Reencontre significado nas atividades.'
            ],
            'Baixa-Alta' => [
                'interpretacao' => '💚 Zona de Vitalidade',
                'significado' => 'Estado ideal. Boa energia e satisfação no trabalho.',
                'orientacoes' => 'Continue praticando hábitos saudáveis, compartilhando boas práticas e inspirando colegas.'
            ],
            'Baixa-Moderada' => [
                'interpretacao' => 'Tranquilidade Operacional',
                'significado' => 'Rotina estável, mas com espaço para mais propósito.',
                'orientacoes' => 'Defina novos desafios e metas inspiradoras.'
            ],
            'Baixa-Baixa' => [
                'interpretacao' => 'Apatia Emocional',
                'significado' => 'Baixo estresse, mas também baixo envolvimento. Indica tédio ou falta de desafio.',
                'orientacoes' => 'Reavalie seus objetivos e busque oportunidades que reativem seu entusiasmo.'
            ]
        ];

        $chave = $exaustaoFaixa . '-' . $realizacaoFaixa;
        return $interpretacoes[$chave] ?? $interpretacoes['Moderada-Moderada'];
    }

    /**
     * Interpreta o Eixo 2 baseado nas combinações de faixas
     */
    protected function interpretarEixo2($cinismoFaixa, $fatoresFaixa): array
    {
        $interpretacoes = [
            'Alta-Alta' => [
                'interpretacao' => 'Cansaço Relacional',
                'significado' => 'O ambiente é bom, mas há esgotamento pessoal. O cinismo pode vir de excesso de exposição ou idealismo frustrado.',
                'orientacoes' => 'Tire pausas de interação, sem se isolar. Retome o propósito em pequenas vitórias.'
            ],
            'Alta-Moderada' => [
                'interpretacao' => 'Proteção Emocional',
                'significado' => 'Tentativa de se proteger de tensões. O ambiente oferece algum suporte, mas há barreiras emocionais.',
                'orientacoes' => 'Trabalhe a empatia e reforce vínculos leves e sinceros.'
            ],
            'Alta-Baixa' => [
                'interpretacao' => '⚠️ Isolamento e Desconfiança',
                'significado' => 'Indica desgaste relacional e perda de vínculo com o ambiente. Pode haver sensação de injustiça ou frieza no time.',
                'orientacoes' => 'Reabra canais de diálogo. Se possível, busque apoio em pessoas de confiança e em práticas colaborativas.'
            ],
            'Moderada-Alta' => [
                'interpretacao' => 'Conexão Consciente',
                'significado' => 'Relacionamento saudável com limites claros.',
                'orientacoes' => 'Mantenha equilíbrio e evite absorver tensões alheias.'
            ],
            'Moderada-Moderada' => [
                'interpretacao' => 'Relações Neutras',
                'significado' => 'Conexões estáveis, porém pouco afetivas.',
                'orientacoes' => 'Estimule momentos de reconhecimento e humanização nas relações.'
            ],
            'Moderada-Baixa' => [
                'interpretacao' => 'Desencanto',
                'significado' => 'Sensação de distância emocional e falta de suporte.',
                'orientacoes' => 'Invista em comunicação e peça clareza sobre expectativas.'
            ],
            'Baixa-Alta' => [
                'interpretacao' => '💚 Pertencimento Saudável',
                'significado' => 'Relações de confiança, empatia e apoio mútuo.',
                'orientacoes' => 'Continue nutrindo o ambiente com colaboração e reconhecimento.'
            ],
            'Baixa-Moderada' => [
                'interpretacao' => 'Equilíbrio Social',
                'significado' => 'Boa convivência, ainda que nem sempre profunda.',
                'orientacoes' => 'Cultive pequenas atitudes de escuta e feedbacks positivos.'
            ],
            'Baixa-Baixa' => [
                'interpretacao' => 'Engajamento Solitário',
                'significado' => 'Você se mantém aberto e positivo mesmo em contextos frios.',
                'orientacoes' => 'Proteja sua energia e incentive práticas coletivas de cooperação.'
            ]
        ];

        $chave = $cinismoFaixa . '-' . $fatoresFaixa;
        return $interpretacoes[$chave] ?? $interpretacoes['Moderada-Moderada'];
    }

    /**
     * Interpreta o Eixo 3 baseado nas combinações de faixas
     */
    protected function interpretarEixo3($excessoFaixa, $assedioFaixa): array
    {
        $interpretacoes = [
            'Alta-Alta' => [
                'interpretacao' => '⚠️ Risco Crítico',
                'significado' => 'Indica ambiente tóxico, com sobrecarga e desrespeito. Altíssimo risco psicossocial.',
                'orientacoes' => 'Acione canais formais de apoio. Nenhum resultado justifica adoecimento.'
            ],
            'Alta-Moderada' => [
                'interpretacao' => 'Sobrecarga Controlada',
                'significado' => 'Alta pressão, mas ainda com algum nível de segurança emocional.',
                'orientacoes' => 'Converse com a liderança sobre prazos e prioridades. Pratique pausas regenerativas.'
            ],
            'Alta-Baixa' => [
                'interpretacao' => 'Dedicação Intensa',
                'significado' => 'Carga alta em ambiente respeitoso. O risco é o corpo não acompanhar o ritmo.',
                'orientacoes' => 'Estabeleça limites de jornada e celebre pausas.'
            ],
            'Moderada-Alta' => [
                'interpretacao' => 'Ambiente Desgastante',
                'significado' => 'As demandas são gerenciáveis, mas o clima é hostil ou tenso.',
                'orientacoes' => 'Busque apoio institucional. Priorize relações seguras e comunicação assertiva.'
            ],
            'Moderada-Moderada' => [
                'interpretacao' => 'Zona de Atenção',
                'significado' => 'Indica ambiente exigente, com riscos pontuais de tensão.',
                'orientacoes' => 'Monitore sinais de estresse e pratique pausas semanais.'
            ],
            'Moderada-Baixa' => [
                'interpretacao' => '💚 Sustentabilidade Saudável',
                'significado' => 'Boa produtividade com respeito mútuo.',
                'orientacoes' => 'Mantenha práticas saudáveis e incentive o mesmo no grupo.'
            ],
            'Baixa-Alta' => [
                'interpretacao' => 'Ambiente Inseguro',
                'significado' => 'Baixa demanda, mas clima emocional ruim. O problema está nas relações, não na carga.',
                'orientacoes' => 'Não se isole. Procure espaços seguros e promova conversas francas.'
            ],
            'Baixa-Moderada' => [
                'interpretacao' => 'Cautela Social',
                'significado' => 'Carga leve, mas interações sensíveis.',
                'orientacoes' => 'Mantenha postura empática e evite conflitos desnecessários.'
            ],
            'Baixa-Baixa' => [
                'interpretacao' => 'Zona de Bem-Estar',
                'significado' => 'Ambiente saudável, equilibrado e ético.',
                'orientacoes' => 'Valorize e proteja esse equilíbrio. Compartilhe práticas positivas.'
            ]
        ];

        $chave = $excessoFaixa . '-' . $assedioFaixa;
        return $interpretacoes[$chave] ?? $interpretacoes['Moderada-Moderada'];
    }
}

