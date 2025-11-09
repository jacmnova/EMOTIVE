<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analise;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\CalculaEjesAnaliticos;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RelatorioController extends Controller
{
    use CalculaEjesAnaliticos;
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

        return redirect()->route('relatorio.show', [
            'formulario_id' => $request->formulario_id,
            'usuario_id' => $request->usuario_id,
        ])->with('msgSuccess', 'Análise regenerada com sucesso.');
    }

    /**
     * Mostrar relatorio con token temporal para PDF
     */
    public function relatorioPDFTemp(Request $request, $token)
    {
        // Validar token desde cache
        $cacheKey = "pdf_token_{$token}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            abort(404, 'Token inválido ou expirado');
        }
        
        $formularioId = $data['formulario_id'];
        $usuarioId = $data['usuario_id'];
        
        // Usar el mismo método que relatorioShow pero sin autenticación
        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        // Cargar variables con preguntas, asegurando que se carguen todos los campos de las preguntas
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            if ($variavel->perguntas->isEmpty()) {
                continue;
            }
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $pontuacao += $valorResposta;
                    $totalRespostas++;
                }
            }
            
            if ($totalRespostas === 0) {
                continue;
            }

            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa;
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada;
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta;
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'nome' => $variavel->nome,
                'valor' => $pontuacao,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
            ];
        }

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
        $analiseData = $analise?->created_at;
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        
        // Calcular promedio de índices (sin porcentaje) para mostrar en la puntuación
        $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;

        return view('participante.relatorio_emotive', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData',
            'ejesAnaliticos',
            'iid',
            'promedioIndices',
            'nivelRisco',
            'planDesenvolvimento'
        ));
    }

    public function gerarPDF(Request $request)
    {
        // Validar que los parámetros estén presentes
        if (!$request->has('formulario') || !$request->has('user')) {
            return redirect()->back()->with('msgError', 'Parámetros faltantes para generar el PDF.');
        }
        
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

        // Calcular puntuaciones para TODAS las dimensiones (EXEM, REPR, DECI, FAPS, EXTR, ASMO)
        // Todas usan la misma lógica de inversión basada en numero_da_pergunta
        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            // Verificar si la variable tiene preguntas asociadas
            if ($variavel->perguntas->isEmpty()) {
                continue; // Saltar variables sin preguntas
            }
            
            // Calcular puntuación basada en las respuestas
            // IMPORTANTE: La misma lógica se aplica para todas las dimensiones
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                // Aplicar la misma lógica de inversión para todas las dimensiones
                // Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
                // Preguntas normales: valor sin cambios
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $pontuacao += $valorResposta;
                    $totalRespostas++;
                }
            }
            
            // Solo agregar si hay al menos una respuesta válida
            if ($totalRespostas === 0) {
                continue; // Saltar variables sin respuestas
            }
            
            // Classificar faixa
            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            
            // Determinar recomendação
            $recomendacao = 'Sem recomendação disponível.';
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? $recomendacao;
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? $recomendacao;
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? $recomendacao;
                    break;
            }
            
            $pontuacoes[] = [
                'nome' => $variavel->nome,
                'tag' => strtoupper($variavel->tag),
                'pontuacao' => $pontuacao,
                'valor' => $pontuacao,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
            ];
        }

        $labels = collect($pontuacoes)->pluck('tag');
        $dataValores = collect($pontuacoes)->pluck('pontuacao');

        $graficosDir = storage_path('app/public/graficos');
        if (!file_exists($graficosDir)) {
            mkdir($graficosDir, 0755, true);
        }

        // GRÁFICO DE BARRAS
        $dataValores[] = 0;

        $configBarras = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Pontuação',
                    'data' => $dataValores,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Pontuação por Dimensão']
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'suggestedMin' => 0,
                        'min' => 0,
                        'ticks' => ['min' => 0]
                    ]
                ]
            ]
        ];

        // Generar gráfico de barras con timeout
        $urlGraficoBarras = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configBarras));
        $imagemBarrasPath = $graficosDir . '/grafico_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemBarrasContent = @file_get_contents($urlGraficoBarras, false, $context);
        if ($imagemBarrasContent !== false) {
            file_put_contents($imagemBarrasPath, $imagemBarrasContent);
            $imagemBarrasPublicPath = 'storage/graficos/' . basename($imagemBarrasPath);
        } else {
            $imagemBarrasPublicPath = null;
        }

        // GRÁFICO DE RADAR E.MO.TI.VE - Igual a la web
        $ordemDimensoes = ['EXEM', 'REPR', 'DECI', 'FAPS', 'EXTR', 'ASMO'];
        $pontuacoesOrdenadas = [];
        foreach ($ordemDimensoes as $tag) {
            $ponto = collect($pontuacoes)->firstWhere('tag', $tag);
            if ($ponto) {
                $pontuacoesOrdenadas[] = $ponto;
            }
        }
        $labelsRadar = collect($pontuacoesOrdenadas)->pluck('tag')->map(function($tag) {
            $nomesCompletos = [
                'EXEM' => 'Exaustão Emocional',
                'REPR' => 'Realização Profissional',
                'DECI' => 'Despersonalização / Cinismo',
                'FAPS' => 'Fatores Psicossociais',
                'EXTR' => 'Excesso de Trabalho',
                'ASMO' => 'Assédio Moral'
            ];
            return $nomesCompletos[$tag] ?? $tag;
        });
        $dataRadar = collect($pontuacoesOrdenadas)->pluck('valor');

        $coresPorTag = [
            'EXEM' => '#8B4513',
            'REPR' => '#8B4513',
            'DECI' => '#FF8C00',
            'FAPS' => '#90EE90',
            'EXTR' => '#9370DB',
            'ASMO' => '#4169E1'
        ];
        $coresTransparentes = [
            'EXEM' => 'rgba(139, 69, 19, 0.25)',
            'REPR' => 'rgba(139, 69, 19, 0.25)',
            'DECI' => 'rgba(255, 140, 0, 0.25)',
            'FAPS' => 'rgba(144, 238, 144, 0.25)',
            'EXTR' => 'rgba(147, 112, 219, 0.25)',
            'ASMO' => 'rgba(65, 105, 225, 0.25)'
        ];

        $datasetsRadar = [];
        // Dataset principal (línea azul claro)
        $datasetsRadar[] = [
            'label' => 'Pontuação Geral',
            'data' => $dataRadar,
            'backgroundColor' => 'rgba(173, 216, 230, 0.4)',
            'borderColor' => 'rgba(173, 216, 230, 1)',
            'borderWidth' => 2.5,
            'pointBackgroundColor' => 'rgba(173, 216, 230, 1)',
            'pointBorderColor' => '#fff',
            'pointRadius' => 0,
            'fill' => true,
            'order' => 2
        ];

        // Datasets para cada dimensão (segmentos de colores)
        foreach ($pontuacoesOrdenadas as $index => $ponto) {
            $data = array_fill(0, count($labelsRadar), 0);
            $data[$index] = $ponto['valor'];

            $cor = $coresPorTag[$ponto['tag']] ?? '#808080';
            $corTransparente = $coresTransparentes[$ponto['tag']] ?? 'rgba(128, 128, 128, 0.25)';

            $datasetsRadar[] = [
                'label' => $ponto['nome'],
                'data' => $data,
                'backgroundColor' => $corTransparente,
                'borderColor' => 'transparent',
                'borderWidth' => 0,
                'pointBackgroundColor' => $cor,
                'pointBorderColor' => '#fff',
                'pointRadius' => 0,
                'fill' => true,
                'order' => 1
            ];
        }

        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labelsRadar,
                'datasets' => $datasetsRadar
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'tooltip' => ['enabled' => true],
                ],
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'min' => 0,
                        'ticks' => [
                            'stepSize' => 20,
                            'font' => ['size' => 10, 'family' => 'Quicksand'],
                            'color' => '#666',
                            'backdropColor' => 'transparent'
                        ],
                        'grid' => ['color' => 'rgba(0, 0, 0, 0.15)', 'lineWidth' => 1],
                        'angleLines' => ['color' => 'rgba(0, 0, 0, 0.15)', 'lineWidth' => 1],
                        'pointLabels' => [
                            'font' => ['size' => 11, 'weight' => 'bold', 'family' => 'Quicksand'],
                            'color' => '#000',
                            'padding' => 15
                        ]
                    ]
                ]
            ]
        ];

        // Generar gráfico de radar con timeout
        $urlGraficoRadar = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configRadar));
        $imagemRadarPath = $graficosDir . '/radar_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemRadarContent = @file_get_contents($urlGraficoRadar, false, $context);
        if ($imagemRadarContent !== false) {
            file_put_contents($imagemRadarPath, $imagemRadarContent);
            $imagemRadarPublicPath = 'storage/graficos/' . basename($imagemRadarPath);
        } else {
            $imagemRadarPublicPath = null;
        }
        
        // GRÁFICO DE RISCO DE DESCARRILAMENTO (IID) - Barra horizontal
        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        
        $configIID = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Risco de Descarrilamento'],
                'datasets' => [[
                    'label' => 'IID',
                    'data' => [$iid],
                    'backgroundColor' => $nivelRisco['cor_hex'],
                    'borderColor' => $nivelRisco['cor_hex'],
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'indexAxis' => 'y',
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Índice Integrado de Descarrilamento (IID)']
                ],
                'scales' => [
                    'x' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => ['stepSize' => 20]
                    ]
                ]
            ]
        ];
        
        // Generar gráfico IID con timeout
        $urlGraficoIID = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configIID));
        $imagemIIDPath = $graficosDir . '/iid_' . uniqid() . '.png';
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'method' => 'GET',
                'header' => 'User-Agent: PHP'
            ]
        ]);
        $imagemIIDContent = @file_get_contents($urlGraficoIID, false, $context);
        if ($imagemIIDContent !== false) {
            file_put_contents($imagemIIDPath, $imagemIIDContent);
            $imagemIIDPublicPath = 'storage/graficos/' . basename($imagemIIDPath);
        } else {
            $imagemIIDPublicPath = null;
        }

        // Generar token temporal para acceder al relatorio sin autenticación
        $token = Str::random(64);
        $cacheKey = "pdf_token_{$token}";
        
        // Guardar datos en cache por 10 minutos
        Cache::put($cacheKey, [
            'formulario_id' => $formularioId,
            'usuario_id' => $usuarioId,
        ], now()->addMinutes(10));
        
        // Generar URL completa del relatorio temporal
        $url = route('relatorio.pdf.temp', ['token' => $token]);
        
        // Asegurar que la URL sea absoluta usando APP_URL o la URL actual
        if (!str_starts_with($url, 'http')) {
            $baseUrl = config('app.url', request()->getSchemeAndHttpHost());
            $url = rtrim($baseUrl, '/') . '/' . ltrim(parse_url($url, PHP_URL_PATH), '/');
        }
        
        // Si APP_URL es localhost, usar 127.0.0.1 para mejor compatibilidad
        $url = str_replace('localhost', '127.0.0.1', $url);
        
        // Log para debugging
        \Log::info('Generando PDF con Browsershot', [
            'url' => $url,
            'formulario_id' => $formularioId,
            'usuario_id' => $usuarioId,
            'app_url' => config('app.url'),
            'request_host' => request()->getSchemeAndHttpHost()
        ]);
        
        // Aumentar tiempo de ejecución para PDFs complejos
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        
        try {
            // Verificar que la URL sea accesible antes de intentar generar el PDF
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new \Exception("La URL del relatorio no es accesible (HTTP $httpCode). Asegúrese de que el servidor esté corriendo y accesible.");
            }
            
            // Usar Browsershot para capturar la página web tal como se ve
            $browsershot = Browsershot::url($url)
                ->waitUntilNetworkIdle()
                ->timeout(120)
                ->format('A4')
                ->margins(0, 0, 0, 0)
                ->showBackground()
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']);
            
            $pdf = $browsershot->pdf();
            
            if (empty($pdf)) {
                throw new \Exception('Browsershot retornó un PDF vacío. Verifique que Puppeteer esté instalado correctamente.');
            }
            
            // Limpiar token después de usar
            Cache::forget($cacheKey);
            
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"relatorio_emotive_{$user->name}.pdf\"",
            ]);
        } catch (\Exception $e) {
            // Limpiar token en caso de error
            Cache::forget($cacheKey);
            
            \Log::error('Error generando PDF con Browsershot: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('URL intentada: ' . $url);
            
            // Intentar usar DomPDF como fallback
            \Log::info('Intentando usar DomPDF como fallback');
            try {
                // Preparar datos para DomPDF
                $analise = Analise::where('user_id', $usuarioId)
                    ->where('formulario_id', $formularioId)
                    ->first();
                $analiseTexto = $analise?->texto ?? 'Análise não disponível.';
                // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
                $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
                $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes, $indices);
                $iid = $this->calcularIID($ejesAnaliticos);
                $nivelRisco = $this->determinarNivelRisco($iid);
                $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
                
                // Calcular promedio de índices (sin porcentaje) para mostrar en la puntuación
                $promedioIndices = ($indices['EE'] + $indices['PR'] + $indices['SO']) / 3;
                
                $data = [
                    'user' => $user,
                    'formulario' => $formulario,
                    'respostasUsuario' => $respostasUsuario,
                    'pontuacoes' => $pontuacoes,
                    'variaveis' => $variaveis,
                    'hoje' => now()->format('d/m/Y'),
                    'imagemGrafico' => $imagemBarrasPublicPath ?? null,
                    'imagemRadar' => $imagemRadarPublicPath ?? null,
                    'imagemIID' => $imagemIIDPublicPath ?? null,
                    'analiseTexto' => $analiseTexto,
                    'ejesAnaliticos' => $ejesAnaliticos,
                    'iid' => $iid,
                    'promedioIndices' => $promedioIndices,
                    'nivelRisco' => $nivelRisco,
                    'planDesenvolvimento' => $planDesenvolvimento,
                    'isPdf' => true,
                ];
                
                $viewPDF = view()->exists('pdf.relatorios.emotive') ? 'pdf.relatorios.emotive' : 'pdf.relatorios.qrp36';
                
                $pdf = Pdf::loadView($viewPDF, $data)
                    ->setPaper('a4', 'portrait')
                    ->setOption('enable-local-file-access', true)
                    ->setOption('isRemoteEnabled', false)
                    ->setOption('isHtml5ParserEnabled', true)
                    ->setOption('isPhpEnabled', false)
                    ->setOption('enable-javascript', false)
                    ->setOption('dpi', 96)
                    ->setOption('defaultFont', 'DejaVu Sans')
                    ->setOption('enable-font-subsetting', false);
                
                return $pdf->download("relatorio_emotive_{$user->name}.pdf");
            } catch (\Exception $fallbackException) {
                \Log::error('Error en fallback DomPDF: ' . $fallbackException->getMessage());
                
                // Mensaje de error más amigable
                $errorMessage = 'Error al generar el PDF. ';
                if (str_contains($e->getMessage(), 'Puppeteer') || str_contains($e->getMessage(), 'node')) {
                    $errorMessage .= 'Puppeteer no está instalado. Ejecute: npm install puppeteer. Se intentó usar DomPDF como alternativa pero también falló.';
                } elseif (str_contains($e->getMessage(), 'HTTP')) {
                    $errorMessage .= 'El servidor no es accesible desde la URL generada. Verifique la configuración de APP_URL en .env';
                } else {
                    $errorMessage .= $e->getMessage();
                }
                
                return redirect()->back()->with('msgError', $errorMessage);
            }
        }
    }

    /**
     * Obtiene el valor de respuesta aplicando inversión si la pregunta lo requiere
     * 
     * IMPORTANTE: Esta lógica se aplica uniformemente para TODAS las dimensiones:
     * EXEM, REPR, DECI, FAPS, EXTR, ASMO
     * 
     * Las preguntas que requieren inversión son las que tienen estos numero_da_pergunta: 
     * 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
     * 
     * Lógica de inversión (igual para todas las dimensiones):
     * - Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
     *   (En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo)
     * - Preguntas normales: valor sin cambios
     */
    private function obterValorRespostaComInversao($resposta, $pergunta): ?int
    {
        if (!$resposta || $resposta->valor_resposta === null) {
            return null;
        }

        $valor = $resposta->valor_resposta;
        
        // Asegurar que la pregunta existe
        if (!$pergunta) {
            \Log::warning('Pregunta es null en obterValorRespostaComInversao');
            return $valor;
        }
        
        // Usar numero_da_pergunta para identificar cuáles requieren inversión
        // Las preguntas que requieren inversión son las que tienen estos numero_da_pergunta: 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
        $numeroPergunta = (int)($pergunta->numero_da_pergunta ?? 0);
        
        // Lista de numero_da_pergunta de preguntas que requieren inversión (según el CSV)
        $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        // Verificar si esta pregunta requiere inversión (usando numero_da_pergunta)
        if (in_array($numeroPergunta, $perguntasComInversao, true)) {
            // Invertir el valor: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
            // En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo
            $valorInvertido = 6 - $valor;
            \Log::info('✅ APLICANDO INVERSIÓN', [
                'pergunta_id' => $pergunta->id,
                'numero_da_pergunta' => $numeroPergunta,
                'valor_original' => $valor,
                'valor_invertido' => $valorInvertido
            ]);
            return $valorInvertido;
        }
        
        return $valor;
    }

}
