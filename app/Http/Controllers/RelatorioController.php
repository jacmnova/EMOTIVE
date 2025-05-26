<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Resposta;
use App\Models\Variavel;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function gerarPDF(Request $request)
    {
        $formularioId = $request->formulario;
        $usuarioId = $request->user;

        // Busca dados do usuário e formulário
        $user = User::findOrFail($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        // Busca respostas do usuário
        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        // Monta pontuações por variável
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

        // Prepara dados para gráficos
        $labels = collect($pontuacoes)->pluck('tag');
        $dataValores = collect($pontuacoes)->pluck('pontuacao');

        // Garante que a pasta dos gráficos existe
        $graficosDir = storage_path('app/public/graficos');
        if (!file_exists($graficosDir)) {
            mkdir($graficosDir, 0755, true);
        }

        // =======================
        // Gráfico de Barras
        // =======================
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
                        'ticks' => [
                            'min' => 0
                        ]
                    ]
                ]
            ]
        ];

        $urlGraficoBarras = 'https://quickchart.io/chart?c=' . urlencode(json_encode($configBarras));
        $imagemBarrasPath = $graficosDir . '/grafico_' . uniqid() . '.png';
        file_put_contents($imagemBarrasPath, file_get_contents($urlGraficoBarras));
        $imagemBarrasPublicPath = 'storage/graficos/' . basename($imagemBarrasPath);

        // =======================
        // Gráfico de Radar
        // =======================
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

        // Monta os dados para a view do PDF
        $data = [
            'user' => $user,
            'formulario' => $formulario,
            'respostasUsuario' => $respostasUsuario,
            'pontuacoes' => $pontuacoes,
            'variaveis' => $variaveis,
            'hoje' => now()->format('d/m/Y'),
            'imagemGrafico' => $imagemBarrasPublicPath,
            'imagemRadar' => $imagemRadarPublicPath,
        ];

        // Gera o PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.relatorios.qrp36', $data)->setPaper('a4', 'portrait');
        return $pdf->download("relatorio_qrp36_{$user->name}.pdf");
    }
}
