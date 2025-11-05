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

        $urlGraficoBarras = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configBarras));
        $imagemBarrasPath = $graficosDir . '/grafico_' . uniqid() . '.png';
        file_put_contents($imagemBarrasPath, file_get_contents($urlGraficoBarras));
        $imagemBarrasPublicPath = 'storage/graficos/' . basename($imagemBarrasPath);

        // GRÁFICO DE RADAR E.MO.TI.VE
        $coresPorTag = [
            'EXEM' => 'rgba(139, 69, 19, 0.8)',
            'REPR' => 'rgba(210, 105, 30, 0.8)',
            'DECI' => 'rgba(255, 140, 0, 0.8)',
            'FAPS' => 'rgba(144, 238, 144, 0.8)',
            'EXTR' => 'rgba(34, 139, 34, 0.8)',
            'ASMO' => 'rgba(65, 105, 225, 0.8)'
        ];
        
        $datasetsRadar = [];
        
        // Dataset principal (linha geral)
        $datasetsRadar[] = [
            'label' => 'Pontuação Geral',
            'data' => $dataValores,
            'backgroundColor' => 'rgba(192, 192, 192, 0.1)',
            'borderColor' => 'rgba(192, 192, 192, 1)',
            'borderWidth' => 2,
            'pointBackgroundColor' => 'rgba(192, 192, 192, 1)',
            'pointBorderColor' => '#fff',
            'pointRadius' => 4
        ];
        
        // Datasets para cada dimensão
        foreach ($pontuacoes as $index => $ponto) {
            $data = array_fill(0, count($labels), 0);
            $data[$index] = $ponto['pontuacao'];
            $cor = $coresPorTag[$ponto['tag']] ?? 'rgba(128, 128, 128, 0.8)';
            
            $datasetsRadar[] = [
                'label' => $ponto['nome'],
                'data' => $data,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'borderWidth' => 2,
                'pointBackgroundColor' => $cor,
                'pointBorderColor' => '#fff',
                'pointRadius' => 5,
                'fill' => true
            ];
        }
        
        $configRadar = [
            'type' => 'radar',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasetsRadar
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => true, 'position' => 'right'],
                    'title' => ['display' => true, 'text' => 'Radar E.MO.TI.VE']
                ],
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'angleLines' => ['display' => true],
                        'suggestedMin' => 0,
                        'suggestedMax' => max(collect($dataValores)->max() + 20, 100)
                    ]
                ]
            ]
        ];

        $urlGraficoRadar = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configRadar));
        $imagemRadarPath = $graficosDir . '/radar_' . uniqid() . '.png';
        file_put_contents($imagemRadarPath, file_get_contents($urlGraficoRadar));
        $imagemRadarPublicPath = 'storage/graficos/' . basename($imagemRadarPath);
        
        // GRÁFICO DE RISCO DE DESCARRILAMENTO (IID) - Barra horizontal
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes);
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
        
        $urlGraficoIID = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configIID));
        $imagemIIDPath = $graficosDir . '/iid_' . uniqid() . '.png';
        file_put_contents($imagemIIDPath, file_get_contents($urlGraficoIID));
        $imagemIIDPublicPath = 'storage/graficos/' . basename($imagemIIDPath);

        // ANALISE GERADA PELA IA
        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        $analiseTexto = $analise?->texto ?? 'Análise não disponível.';

        // === CALCULAR EJES ANALÍTICOS Y IID ===
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);

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
            'imagemIID' => $imagemIIDPublicPath,
            'analiseTexto' => $analiseTexto,
            'ejesAnaliticos' => $ejesAnaliticos,
            'iid' => $iid,
            'nivelRisco' => $nivelRisco,
            'planDesenvolvimento' => $planDesenvolvimento,
        ];

        // GERA O PDF - Usar nova vista E.MO.TI.VE se existir
        $viewPDF = view()->exists('pdf.relatorios.emotive') ? 'pdf.relatorios.emotive' : 'pdf.relatorios.qrp36';
        $pdf = Pdf::loadView($viewPDF, $data)->setPaper('a4', 'portrait');
        return $pdf->download("relatorio_emotive_{$user->name}.pdf");
    }

}
