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
