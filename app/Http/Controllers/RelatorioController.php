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

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }
            $pontuacoes[] = [
                'nome' => $variavel->nome,
                'tag' => strtoupper($variavel->tag),
                'pontuacao' => $pontuacao,
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

        $urlGraficoBarras = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configBarras));
        $imagemBarrasPath = $graficosDir . '/grafico_' . uniqid() . '.png';
        file_put_contents($imagemBarrasPath, file_get_contents($urlGraficoBarras));
        $imagemBarrasPublicPath = 'storage/graficos/' . basename($imagemBarrasPath);

        // GRÁFICO DE RADAR
        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Pontuação',
                    'data' => $dataValores,
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
                    'title' => ['display' => true, 'text' => 'Radar das Dimensões']
                ],
                'scales' => [
                    'r' => [
                        'angleLines' => ['display' => true],
                        'suggestedMin' => 0,
                        'suggestedMax' => collect($dataValores)->max() + 5
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
            'hoje' => now()->format('d/m/Y'),
            'imagemGrafico' => $imagemBarrasPublicPath,
            'imagemRadar' => $imagemRadarPublicPath,
            'analiseTexto' => $analiseTexto,
        ];

        // GERA O PDF
        $pdf = Pdf::loadView('pdf.relatorios.qrp36', $data)->setPaper('a4', 'portrait');
        return $pdf->download("relatorio_qrp36_{$user->name}.pdf");
    }

}
