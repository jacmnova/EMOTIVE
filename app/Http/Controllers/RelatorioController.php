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
                // 'analise' => $this->interpretarPontuacao($variavel->tag, $pontuacao),
            ];
        }

        $data = [
            'user' => $user,
            'dimensoes' => $pontuacoes,
            'hoje' => now()->format('d/m/Y'),
            'formulario' => $formulario,
            'respostasUsuario' => $respostasUsuario,
            'pontuacoes' => $pontuacoes,
            'variaveis' => $variaveis,
        ];

        $pdf = Pdf::loadView('pdf.relatorios.qrp36', $data)->setPaper('a4', 'portrait');
        return $pdf->download("relatorio_qrp36_{$user->name}.pdf");
    }

    // private function interpretarPontuacao($tag, $valor)
    // {
    //     $analises = [
    //         'EXEM' => 'Cansaço emocional crescente. É necessário atenção à recuperação psíquica.',
    //         'DECI' => 'Indiferença e distanciamento emocional. Pode indicar defesa frente ao estresse.',
    //         'REPR' => 'Indica insatisfação com desempenho e baixa autoestima profissional.',
    //         'FAPS' => 'Ambiente psicossocial crítico. Riscos evidentes à saúde mental.',
    //         'ASMO' => 'Presença forte de assédio moral. Exige apuração e intervenção imediata.',
    //         'EXTR' => 'Carga de trabalho excessiva. Alto risco de burnout e adoecimento.',
    //     ];

    //     $tag = strtoupper($tag);

    //     return $analises[$tag] ?? 'Sem análise disponível para essa variável.';
    // }



}
