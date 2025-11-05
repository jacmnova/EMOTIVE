<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analise;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    public function regenerarAnalise(Request $request)
    {
        $request->validate([
            'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if (!auth()->user()->admin) {
            return redirect()->back()->with('msgError', 'Ação não autorizada.');
        }

        // Remove análise anterior
        Analise::where('user_id', $request->usuario_id)
            ->where('formulario_id', $request->formulario_id)
            ->delete();

        // Regenerar y enviar a la API de Python con la nueva estructura
        try {
            $dadosController = new \App\Http\Controllers\DadosController();
            $datosRelatorio = $this->prepararDadosParaRelatorio($request->usuario_id, $request->formulario_id);
            $resultado = $this->enviarDatosAPython($datosRelatorio);
            
            if (!$resultado['success']) {
                session()->flash('pythonApiError', true);
                session()->flash('pythonApiErrorData', json_encode($resultado['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                session()->flash('pythonApiErrorMessage', $resultado['error']);
            }
        } catch (\Exception $e) {
            \Log::error('Error al regenerar y enviar a la API de Python', [
                'user_id' => $request->usuario_id,
                'formulario_id' => $request->formulario_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('relatorio.show', [
            'formulario_id' => $request->formulario_id,
            'usuario_id' => $request->usuario_id,
        ])->with('msgSuccess', 'Análise regenerada com sucesso.');
    }

    /**
     * Prepara los datos del reporte en formato compatible con la API de Python
     * (Mismo método que en DadosController para mantener consistencia)
     */
    private function prepararDadosParaRelatorio($userId, $formularioId): array
    {
        $user = User::find($userId);
        $formulario = Formulario::with('perguntas')->findOrFail($formularioId);
        
        // Obtener respuestas del usuario
        $respostasUsuario = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get();
        
        // Obtener variables con sus límites
        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formularioId)
            ->get();
        
        // Calcular puntuaciones y organizar por secciones
        $sections = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->firstWhere('pergunta_id', $pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }
            
            // Clasificar faixa
            $faixa = 'Baixa';
            if ($pontuacao <= $variavel->B) {
                $faixa = 'Baixa';
            } elseif ($pontuacao <= $variavel->M) {
                $faixa = 'Moderada';
            } else {
                $faixa = 'Alta';
            }
            
            // Determinar recomendación según la faixa
            $recomendacao = '';
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? '';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? '';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? '';
                    break;
            }
            
            // Construir el body de la sección
            $body = "<h4>{$variavel->nome} ({$variavel->tag})</h4>";
            $body .= "<p><strong>Puntuación:</strong> {$pontuacao} puntos</p>";
            $body .= "<p><strong>Clasificación:</strong> <span class='badge badge-" . ($faixa == 'Baixa' ? 'info' : ($faixa == 'Moderada' ? 'warning' : 'danger')) . "'>{$faixa}</span></p>";
            $body .= "<p><strong>Límites:</strong> Baixa (≤{$variavel->B}), Moderada (≤{$variavel->M}), Alta (>{$variavel->M})</p>";
            if ($recomendacao) {
                $body .= "<div class='mt-3'><strong>Recomendación:</strong><br><p>{$recomendacao}</p></div>";
            }
            
            $sections[] = [
                'title' => $variavel->nome . " ({$variavel->tag})",
                'body' => $body
            ];
        }
        
        // Formato compatible con la API de Python
        return [
            'template_id' => str_pad($formularioId, 3, '0', STR_PAD_LEFT),
            'data' => [
                'header' => [
                    'title' => $formulario->nome . ' - ' . $formulario->label
                ],
                'welcome_screen' => [
                    'title' => 'Bienvenido, ' . $user->name,
                    'body' => '<p>Este es tu reporte personalizado del formulario <strong>' . $formulario->nome . '</strong>.</p><p>Fecha de generación: ' . now()->format('d/m/Y H:i') . '</p>',
                    'show_btn' => false,
                    'text_btn' => '',
                    'link_btn' => ''
                ],
                'explanation_screen' => [
                    'title' => 'Sobre este Reporte',
                    'body' => $formulario->descricao ?? '<p>Este reporte presenta el análisis de las dimensiones evaluadas.</p>',
                    'show_img' => false,
                    'img_link' => ''
                ],
                'respuestas' => [
                    'sections' => $sections
                ]
            ],
            'output_format' => 'both'
        ];
    }

    /**
     * Envía los datos del reporte a la API de Python
     */
    private function enviarDatosAPython($datos): array
    {
        $apiUrl = env('PYTHON_RELATORIO_API_URL', 'http://localhost:5000/generate');
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->post($apiUrl, $datos);
            
            if ($response->successful()) {
                \Log::info('Datos enviados exitosamente a la API de Python (regeneración)', [
                    'usuario_id' => $datos['data']['welcome_screen']['title'],
                    'formulario_id' => $datos['template_id'],
                ]);
                return ['success' => true, 'error' => null, 'datos' => null];
            } else {
                $error = "Error HTTP {$response->status()}: " . $response->body();
                \Log::error('Error al enviar datos a la API de Python (regeneración)', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [
                    'success' => false,
                    'error' => $error,
                    'datos' => $datos
                ];
            }
        } catch (\Exception $e) {
            $error = "Excepción: " . $e->getMessage();
            \Log::error('Excepción al enviar datos a la API de Python (regeneración)', [
                'message' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $error,
                'datos' => $datos
            ];
        }
    }

    /**
     * Normaliza una puntuación a escala 0-100 basado en los límites B, M, A
     */
    private function normalizarPuntuacion($puntuacion, $b, $m, $a): float
    {
        // Si la puntuación es menor o igual a B, está en zona baja (0-33)
        if ($puntuacion <= $b) {
            return round(($puntuacion / $b) * 33, 2);
        }
        // Si está entre B y M, está en zona moderada (34-66)
        elseif ($puntuacion <= $m) {
            return round(33 + (($puntuacion - $b) / ($m - $b)) * 33, 2);
        }
        // Si es mayor a M, está en zona alta (67-100)
        else {
            $max = $a > $m ? $a : ($m * 1.5); // Asegurar un máximo razonable
            return round(66 + (min($puntuacion, $max) - $m) / ($max - $m) * 34, 2);
        }
    }

    /**
     * Obtiene la faixa de una puntuación normalizada (0-100)
     */
    private function obtenerFaixaNormalizada($puntuacionNormalizada): string
    {
        if ($puntuacionNormalizada <= 33) {
            return 'Baixa';
        } elseif ($puntuacionNormalizada <= 66) {
            return 'Moderada';
        } else {
            return 'Alta';
        }
    }

    /**
     * Calcula los tres ejes analíticos y el IID
     */
    private function calcularEixosAnaliticos($pontuacoes): array
    {
        // Mapear dimensiones por tag
        $dimensoes = [];
        foreach ($pontuacoes as $ponto) {
            $dimensoes[$ponto['tag']] = $ponto;
        }

        // EJE 1: ENERGIA EMOCIONAL (Exaustão Emocional × Realização Profissional)
        $exEm = $dimensoes['EXEM']['normalizada'] ?? 0;
        $rePr = $dimensoes['REPR']['normalizada'] ?? 0;
        // Para Realização Profissional, invertimos la escala (mayor es mejor)
        $rePrInvertida = 100 - $rePr;
        $eixo1 = round(($exEm + $rePrInvertida) / 2, 2);

        // EJE 2: PROPÓSITO E RELAÇÕES (Despersonalização/Cinismo × Fatores Psicossociais)
        $deCi = $dimensoes['DECI']['normalizada'] ?? 0;
        $faPs = $dimensoes['FAPS']['normalizada'] ?? 0;
        // Para Fatores Psicossociais, invertimos la escala (mayor es mejor)
        $faPsInvertida = 100 - $faPs;
        $eixo2 = round(($deCi + $faPsInvertida) / 2, 2);

        // EJE 3: SUSTENTABILIDADE OCUPACIONAL (Excesso de Trabalho × Assédio Moral)
        $exTr = $dimensoes['EXTR']['normalizada'] ?? 0;
        $asMo = $dimensoes['ASMO']['normalizada'] ?? 0;
        // Para Assédio Moral, invertimos la escala (mayor es mejor)
        $asMoInvertida = 100 - $asMo;
        $eixo3 = round(($exTr + $asMoInvertida) / 2, 2);

        // IID (Índice Integrado de Descarrilamento)
        $iid = round(($eixo1 + $eixo2 + $eixo3) / 3, 2);

        // Clasificar riesgo del IID
        $nivelRisco = $this->clasificarRiscoIID($iid);

        return [
            'eixo1' => [
                'nome' => 'Energia Emocional',
                'valor' => $eixo1,
                'faixa' => $this->obtenerFaixaNormalizada($eixo1),
                'dimensoes' => [
                    'exaustao_emocional' => $exEm,
                    'realizacao_profissional' => $rePrInvertida,
                ]
            ],
            'eixo2' => [
                'nome' => 'Propósito e Relações',
                'valor' => $eixo2,
                'faixa' => $this->obtenerFaixaNormalizada($eixo2),
                'dimensoes' => [
                    'despersonalizacao_cinismo' => $deCi,
                    'fatores_psicossociais' => $faPsInvertida,
                ]
            ],
            'eixo3' => [
                'nome' => 'Sustentabilidade Ocupacional',
                'valor' => $eixo3,
                'faixa' => $this->obtenerFaixaNormalizada($eixo3),
                'dimensoes' => [
                    'excesso_trabalho' => $exTr,
                    'assedio_moral' => $asMoInvertida,
                ]
            ],
            'iid' => [
                'valor' => $iid,
                'nivel_risco' => $nivelRisco['nivel'],
                'zona' => $nivelRisco['zona'],
                'descricao' => $nivelRisco['descricao'],
                'interpretacao' => $nivelRisco['interpretacao'],
                'acao' => $nivelRisco['acao'],
            ]
        ];
    }

    /**
     * Clasifica el riesgo según el IID
     */
    private function clasificarRiscoIID($iid): array
    {
        if ($iid <= 40) {
            return [
                'nivel' => 'Baixo',
                'zona' => 'Zona de equilíbrio emocional',
                'descricao' => 'O participante demonstra autorregulação e boa adaptação ao ambiente.',
                'interpretacao' => 'Capacidade emocional adequada para lidar com desafios e mudanças.',
                'acao' => 'Manter hábitos saudáveis, pausas regulares e comunicação transparente.',
            ];
        } elseif ($iid <= 65) {
            return [
                'nivel' => 'Médio',
                'zona' => 'Zona de atenção preventiva',
                'descricao' => 'Pequenas oscilações de energia e propósito, mas ainda sem impacto funcional.',
                'interpretacao' => 'Pode haver início de fadiga ou leve desconexão emocional.',
                'acao' => 'Reequilibrar rotinas e priorizar autocuidado. Conversar sobre sobrecarga antes que se intensifique.',
            ];
        } elseif ($iid <= 89) {
            return [
                'nivel' => 'Atenção',
                'zona' => 'Zona de vulnerabilidade',
                'descricao' => 'Sinais de esgotamento, desânimo ou desconforto relacional já perceptíveis.',
                'interpretacao' => 'Indica acúmulo de estresse e risco de perda de engajamento.',
                'acao' => 'Acionar estratégias de suporte (RH, liderança, coaching). Evitar manter o mesmo ritmo.',
            ];
        } else {
            return [
                'nivel' => 'Alto',
                'zona' => 'Zona crítica',
                'descricao' => 'O equilíbrio emocional e ocupacional foi comprometido. Alto risco de burnout ou afastamento.',
                'interpretacao' => 'Indica exaustão, sensação de impotência e isolamento emocional.',
                'acao' => 'Intervenção imediata. Pausa, revisão de carga e suporte psicológico recomendado.',
            ];
        }
    }

    /**
     * Obtiene la interpretación detallada de un eje según las combinaciones
     */
    private function obtenerInterpretacaoEixo($eixo, $dimensoes, $pontuacoes): array
    {
        if ($eixo == 1) {
            // EJE 1: ENERGIA EMOCIONAL
            // Buscar dimensiones originales (sin invertir)
            $exEmOriginal = collect($pontuacoes)->firstWhere('tag', 'EXEM')['normalizada'] ?? 0;
            $rePrOriginal = collect($pontuacoes)->firstWhere('tag', 'REPR')['normalizada'] ?? 0;
            
            $exaustaoFaixa = $this->obtenerFaixaNormalizada($exEmOriginal);
            $realizacaoFaixa = $this->obtenerFaixaNormalizada($rePrOriginal);
            
            return $this->interpretarEixo1($exaustaoFaixa, $realizacaoFaixa);
        } elseif ($eixo == 2) {
            // EJE 2: PROPÓSITO E RELAÇÕES
            $deCiOriginal = collect($pontuacoes)->firstWhere('tag', 'DECI')['normalizada'] ?? 0;
            $faPsOriginal = collect($pontuacoes)->firstWhere('tag', 'FAPS')['normalizada'] ?? 0;
            
            $cinismoFaixa = $this->obtenerFaixaNormalizada($deCiOriginal);
            $fatoresFaixa = $this->obtenerFaixaNormalizada($faPsOriginal);
            
            return $this->interpretarEixo2($cinismoFaixa, $fatoresFaixa);
        } else {
            // EJE 3: SUSTENTABILIDADE OCUPACIONAL
            $exTrOriginal = collect($pontuacoes)->firstWhere('tag', 'EXTR')['normalizada'] ?? 0;
            $asMoOriginal = collect($pontuacoes)->firstWhere('tag', 'ASMO')['normalizada'] ?? 0;
            
            $excessoFaixa = $this->obtenerFaixaNormalizada($exTrOriginal);
            $assedioFaixa = $this->obtenerFaixaNormalizada($asMoOriginal);
            
            return $this->interpretarEixo3($excessoFaixa, $assedioFaixa);
        }
    }

    /**
     * Interpreta el EJE 1 según las combinaciones
     */
    private function interpretarEixo1($exaustaoFaixa, $realizacaoFaixa): array
    {
        $interpretacoes = [
            'Exaustão Alta / Realização Baixa' => [
                'interpretacao' => '⚠️ Estado Crítico',
                'significado' => 'Alto risco de esgotamento. A sensação de impotência e perda de propósito indica necessidade de pausa e apoio.',
                'orientacao' => 'Reduza o ritmo, priorize descanso, converse com sua liderança e reflita sobre o que dá sentido ao seu trabalho.',
            ],
            'Exaustão Alta / Realização Moderada' => [
                'interpretacao' => 'Estado de Esforço Contínuo',
                'significado' => 'Há sobrecarga, mas o propósito ainda motiva. O risco é ultrapassar o limite sem perceber.',
                'orientacao' => 'Preserve seus espaços de recuperação e delegue tarefas. Sustente a motivação sem comprometer a saúde.',
            ],
            'Exaustão Alta / Realização Alta' => [
                'interpretacao' => 'Engajamento em Excesso',
                'significado' => 'Energia e propósito coexistem, mas o corpo pode estar pagando o preço.',
                'orientacao' => 'Valorize pausas, reconheça sinais de fadiga e equilibre ambição com autocuidado.',
            ],
            'Exaustão Moderada / Realização Alta' => [
                'interpretacao' => 'Equilíbrio Dinâmico',
                'significado' => 'Boa realização com cansaço controlado. Indica produtividade saudável.',
                'orientacao' => 'Mantenha rituais de descanso e reconheça conquistas. Esse é um ponto ótimo.',
            ],
            'Exaustão Moderada / Realização Baixa' => [
                'interpretacao' => 'Desânimo Progressivo',
                'significado' => 'Esforço emocional sem retorno de propósito. Pode evoluir para desmotivação.',
                'orientacao' => 'Busque feedbacks e alinhe expectativas. Reencontre significado nas atividades.',
            ],
            'Exaustão Moderada / Realização Moderada' => [
                'interpretacao' => 'Estado de Manutenção',
                'significado' => 'Equilíbrio funcional. Nem sobrecarregado, nem entediado.',
                'orientacao' => 'Continue cuidando do ritmo e do engajamento. Práticas de gratidão ajudam a fortalecer esse equilíbrio.',
            ],
            'Exaustão Baixa / Realização Alta' => [
                'interpretacao' => '💚 Zona de Vitalidade',
                'significado' => 'Estado ideal. Boa energia e satisfação no trabalho.',
                'orientacao' => 'Continue praticando hábitos saudáveis, compartilhando boas práticas e inspirando colegas.',
            ],
            'Exaustão Baixa / Realização Moderada' => [
                'interpretacao' => 'Tranquilidade Operacional',
                'significado' => 'Rotina estável, mas com espaço para mais propósito.',
                'orientacao' => 'Defina novos desafios e metas inspiradoras.',
            ],
            'Exaustão Baixa / Realização Baixa' => [
                'interpretacao' => 'Apatia Emocional',
                'significado' => 'Baixo estresse, mas também baixo envolvimento. Indica tédio ou falta de desafio.',
                'orientacao' => 'Reavalie seus objetivos e busque oportunidades que reativem seu entusiasmo.',
            ],
        ];

        $chave = "Exaustão {$exaustaoFaixa} / Realização {$realizacaoFaixa}";
        return $interpretacoes[$chave] ?? [
            'interpretacao' => 'Estado de Equilíbrio',
            'significado' => 'Equilíbrio entre as dimensões avaliadas.',
            'orientacao' => 'Continue mantendo práticas saudáveis.',
        ];
    }

    /**
     * Interpreta el EJE 2 según las combinaciones
     */
    private function interpretarEixo2($cinismoFaixa, $fatoresFaixa): array
    {
        $interpretacoes = [
            'Cinismo Alto / Fatores Baixos' => [
                'interpretacao' => '⚠️ Isolamento e Desconfiança',
                'significado' => 'Indica desgaste relacional e perda de vínculo com o ambiente. Pode haver sensação de injustiça ou frieza no time.',
                'orientacao' => 'Reabra canais de diálogo. Se possível, busque apoio em pessoas de confiança e em práticas colaborativas.',
            ],
            'Cinismo Alto / Fatores Moderados' => [
                'interpretacao' => 'Proteção Emocional',
                'significado' => 'Tentativa de se proteger de tensões. O ambiente oferece algum suporte, mas há barreiras emocionais.',
                'orientacao' => 'Trabalhe a empatia e reforce vínculos leves e sinceros.',
            ],
            'Cinismo Alto / Fatores Altos' => [
                'interpretacao' => 'Cansaço Relacional',
                'significado' => 'O ambiente é bom, mas há esgotamento pessoal. O cinismo pode vir de excesso de exposição ou idealismo frustrado.',
                'orientacao' => 'Tire pausas de interação, sem se isolar. Retome o propósito em pequenas vitórias.',
            ],
            'Cinismo Moderado / Fatores Altos' => [
                'interpretacao' => 'Conexão Consciente',
                'significado' => 'Relacionamento saudável com limites claros.',
                'orientacao' => 'Mantenha equilíbrio e evite absorver tensões alheias.',
            ],
            'Cinismo Moderado / Fatores Moderados' => [
                'interpretacao' => 'Relações Neutras',
                'significado' => 'Conexões estáveis, porém pouco afetivas.',
                'orientacao' => 'Estimule momentos de reconhecimento e humanização nas relações.',
            ],
            'Cinismo Moderado / Fatores Baixos' => [
                'interpretacao' => 'Desencanto',
                'significado' => 'Sensação de distância emocional e falta de suporte.',
                'orientacao' => 'Invista em comunicação e peça clareza sobre expectativas.',
            ],
            'Cinismo Baixo / Fatores Altos' => [
                'interpretacao' => '💚 Pertencimento Saudável',
                'significado' => 'Relações de confiança, empatia e apoio mútuo.',
                'orientacao' => 'Continue nutrindo o ambiente com colaboração e reconhecimento.',
            ],
            'Cinismo Baixo / Fatores Moderados' => [
                'interpretacao' => 'Equilíbrio Social',
                'significado' => 'Boa convivência, ainda que nem sempre profunda.',
                'orientacao' => 'Cultive pequenas atitudes de escuta e feedbacks positivos.',
            ],
            'Cinismo Baixo / Fatores Baixos' => [
                'interpretacao' => 'Engajamento Solitário',
                'significado' => 'Você se mantém aberto e positivo mesmo em contextos frios.',
                'orientacao' => 'Proteja sua energia e incentive práticas coletivas de cooperação.',
            ],
        ];

        $chave = "Cinismo {$cinismoFaixa} / Fatores {$fatoresFaixa}";
        return $interpretacoes[$chave] ?? [
            'interpretacao' => 'Relações Estáveis',
            'significado' => 'Relações profissionais equilibradas.',
            'orientacao' => 'Continue mantendo comunicação clara e respeitosa.',
        ];
    }

    /**
     * Interpreta el EJE 3 según las combinaciones
     */
    private function interpretarEixo3($excessoFaixa, $assedioFaixa): array
    {
        $interpretacoes = [
            'Excesso Alto / Assédio Alto' => [
                'interpretacao' => '⚠️ Risco Crítico',
                'significado' => 'Indica ambiente tóxico, com sobrecarga e desrespeito. Altíssimo risco psicossocial.',
                'orientacao' => 'Acione canais formais de apoio. Nenhum resultado justifica adoecimento.',
            ],
            'Excesso Alto / Assédio Moderado' => [
                'interpretacao' => 'Sobrecarga Controlada',
                'significado' => 'Alta pressão, mas ainda com algum nível de segurança emocional.',
                'orientacao' => 'Converse com a liderança sobre prazos e prioridades. Pratique pausas regenerativas.',
            ],
            'Excesso Alto / Assédio Baixo' => [
                'interpretacao' => 'Dedicação Intensa',
                'significado' => 'Carga alta em ambiente respeitoso. O risco é o corpo não acompanhar o ritmo.',
                'orientacao' => 'Estabeleça limites de jornada e celebre pausas.',
            ],
            'Excesso Moderado / Assédio Alto' => [
                'interpretacao' => 'Ambiente Desgastante',
                'significado' => 'As demandas são gerenciáveis, mas o clima é hostil ou tenso.',
                'orientacao' => 'Busque apoio institucional. Priorize relações seguras e comunicação assertiva.',
            ],
            'Excesso Moderado / Assédio Moderado' => [
                'interpretacao' => 'Zona de Atenção',
                'significado' => 'Indica ambiente exigente, com riscos pontuais de tensão.',
                'orientacao' => 'Monitore sinais de estresse e pratique pausas semanais.',
            ],
            'Excesso Moderado / Assédio Baixo' => [
                'interpretacao' => '💚 Sustentabilidade Saudável',
                'significado' => 'Boa produtividade com respeito mútuo.',
                'orientacao' => 'Mantenha práticas saudáveis e incentive o mesmo no grupo.',
            ],
            'Excesso Baixo / Assédio Alto' => [
                'interpretacao' => 'Ambiente Inseguro',
                'significado' => 'Baixa demanda, mas clima emocional ruim. O problema está nas relações, não na carga.',
                'orientacao' => 'Não se isole. Procure espaços seguros e promova conversas francas.',
            ],
            'Excesso Baixo / Assédio Moderado' => [
                'interpretacao' => 'Cautela Social',
                'significado' => 'Carga leve, mas interações sensíveis.',
                'orientacao' => 'Mantenha postura empática e evite conflitos desnecessários.',
            ],
            'Excesso Baixo / Assédio Baixo' => [
                'interpretacao' => 'Zona de Bem-Estar',
                'significado' => 'Ambiente saudável, equilibrado e ético.',
                'orientacao' => 'Valorize e proteja esse equilíbrio. Compartilhe práticas positivas.',
            ],
        ];

        $chave = "Excesso {$excessoFaixa} / Assédio {$assedioFaixa}";
        return $interpretacoes[$chave] ?? [
            'interpretacao' => 'Sustentabilidade Equilibrada',
            'significado' => 'Equilíbrio entre esforço e suporte.',
            'orientacao' => 'Continue mantendo práticas saudáveis.',
        ];
    }

    public function gerarPDF(Request $request)
    {
        $formularioId = $request->formulario;
        $usuarioId = $request->user;

        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formulario->id)
            ->get();

        // Calcular puntuaciones brutas y normalizadas
        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }

            $b = $variavel->B ?? 0;
            $m = $variavel->M ?? 0;
            $a = $variavel->A ?? ($m + ($m - $b));
            
            $pontuacaoNormalizada = $this->normalizarPuntuacion($pontuacao, $b, $m, $a);
            $faixa = $this->classificarPontuacao($pontuacao, $variavel);

            $pontuacoes[] = [
                'nome' => $variavel->nome,
                'tag' => strtoupper($variavel->tag),
                'pontuacao' => $pontuacao,
                'normalizada' => $pontuacaoNormalizada,
                'faixa' => $faixa,
                'b' => $b,
                'm' => $m,
                'a' => $a,
            ];
        }

        // Calcular ejes analíticos y IID
        $eixos = $this->calcularEixosAnaliticos($pontuacoes);

        // Obtener interpretaciones detalladas de cada eje
        $eixos['eixo1']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(1, $eixos['eixo1']['dimensoes'], $pontuacoes);
        $eixos['eixo2']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(2, $eixos['eixo2']['dimensoes'], $pontuacoes);
        $eixos['eixo3']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(3, $eixos['eixo3']['dimensoes'], $pontuacoes);

        // Generar gráfico radar con puntuaciones normalizadas (0-100)
        $labels = collect($pontuacoes)->pluck('tag');
        $dataValores = collect($pontuacoes)->pluck('normalizada');

        $graficosDir = storage_path('app/public/graficos');
        if (!file_exists($graficosDir)) {
            mkdir($graficosDir, 0755, true);
        }

        // GRÁFICO DE RADAR con escala 0-100
        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labels->toArray(),
                'datasets' => [[
                    'label' => 'Pontuação',
                    'data' => $dataValores->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'pointBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(54, 162, 235, 1)'
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Radar E.MO.TI.VE']
                ],
                'scales' => [
                    'r' => [
                        'angleLines' => ['display' => true],
                        'min' => 0,
                        'max' => 100,
                        'ticks' => [
                            'stepSize' => 20,
                            'min' => 0,
                            'max' => 100
                        ]
                    ]
                ]
            ]
        ];

        $urlGraficoRadar = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configRadar));
        $imagemRadarPath = $graficosDir . '/radar_' . uniqid() . '.png';
        file_put_contents($imagemRadarPath, file_get_contents($urlGraficoRadar));
        $imagemRadarPublicPath = 'storage/graficos/' . basename($imagemRadarPath);

        // ANALISE GERADA PELA IA
        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';

        // DADOS PARA A VIEW
        $data = [
            'user' => $user,
            'formulario' => $formulario,
            'respostasUsuario' => $respostasUsuario,
            'pontuacoes' => $pontuacoes,
            'variaveis' => $variaveis,
            'eixos' => $eixos,
            'hoje' => now()->format('d/m/Y'),
            'dataResposta' => $respostasUsuario->first()?->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'imagemRadar' => $imagemRadarPublicPath,
            'analiseTexto' => $analiseTexto,
        ];

        // GERA O PDF con la nueva vista
        $pdf = Pdf::loadView('pdf.relatorios.emotive', $data)->setPaper('a4', 'portrait');
        return $pdf->download("relatorio_emotive_{$user->name}.pdf");
    }

    /**
     * Muestra el relatorio en HTML
     */
    public function mostrarHTML(Request $request)
    {
        $formularioId = $request->formulario;
        $usuarioId = $request->user;

        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formulario->id)
            ->get();

        // Calcular puntuaciones brutas y normalizadas
        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }

            $b = $variavel->B ?? 0;
            $m = $variavel->M ?? 0;
            $a = $variavel->A ?? ($m + ($m - $b));
            
            $pontuacaoNormalizada = $this->normalizarPuntuacion($pontuacao, $b, $m, $a);
            $faixa = $this->classificarPontuacao($pontuacao, $variavel);

            $pontuacoes[] = [
                'nome' => $variavel->nome,
                'tag' => strtoupper($variavel->tag),
                'pontuacao' => $pontuacao,
                'normalizada' => $pontuacaoNormalizada,
                'faixa' => $faixa,
                'b' => $b,
                'm' => $m,
                'a' => $a,
            ];
        }

        // Calcular ejes analíticos y IID
        $eixos = $this->calcularEixosAnaliticos($pontuacoes);

        // Obtener interpretaciones detalladas de cada eje
        $eixos['eixo1']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(1, $eixos['eixo1']['dimensoes'], $pontuacoes);
        $eixos['eixo2']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(2, $eixos['eixo2']['dimensoes'], $pontuacoes);
        $eixos['eixo3']['interpretacao_detalhada'] = $this->obtenerInterpretacaoEixo(3, $eixos['eixo3']['dimensoes'], $pontuacoes);

        // Generar gráfico radar con puntuaciones normalizadas (0-100)
        $labels = collect($pontuacoes)->pluck('tag');
        $dataValores = collect($pontuacoes)->pluck('normalizada');

        $graficosDir = storage_path('app/public/graficos');
        if (!file_exists($graficosDir)) {
            mkdir($graficosDir, 0755, true);
        }

        // GRÁFICO DE RADAR con escala 0-100
        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labels->toArray(),
                'datasets' => [[
                    'label' => 'Pontuação',
                    'data' => $dataValores->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'pointBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(54, 162, 235, 1)'
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Radar E.MO.TI.VE']
                ],
                'scales' => [
                    'r' => [
                        'angleLines' => ['display' => true],
                        'min' => 0,
                        'max' => 100,
                        'ticks' => [
                            'stepSize' => 20,
                            'min' => 0,
                            'max' => 100
                        ]
                    ]
                ]
            ]
        ];

        $urlGraficoRadar = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configRadar));
        $imagemRadarPath = $graficosDir . '/radar_' . uniqid() . '.png';
        file_put_contents($imagemRadarPath, file_get_contents($urlGraficoRadar));
        $imagemRadarPublicPath = 'storage/graficos/' . basename($imagemRadarPath);

        // ANALISE GERADA PELA IA
        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';

        // DADOS PARA A VIEW
        $data = [
            'user' => $user,
            'formulario' => $formulario,
            'respostasUsuario' => $respostasUsuario,
            'pontuacoes' => $pontuacoes,
            'variaveis' => $variaveis,
            'eixos' => $eixos,
            'hoje' => now()->format('d/m/Y'),
            'dataResposta' => $respostasUsuario->first()?->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'imagemRadar' => $imagemRadarPublicPath,
            'analiseTexto' => $analiseTexto,
        ];

        // Retorna la vista HTML
        return view('relatorios.emotive', $data);
    }

    /**
     * Clasifica una puntuación según los límites de la variable
     */
    private function classificarPontuacao($pontuacao, $variavel): string
    {
        if ($pontuacao <= ($variavel->B ?? 0)) {
            return 'Baixa';
        } elseif ($pontuacao <= ($variavel->M ?? 0)) {
            return 'Moderada';
        } else {
            return 'Alta';
        }
    }

}
