<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Analise;
use App\Models\Variavel;

class AnaliseController extends Controller
{
    public function gerarAnalise(Request $request, $usuarioId)
    {
        $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
        ]);

        $formularioId = $request->input('formulario_id');

        $analiseExistente = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        if ($analiseExistente) {
            return redirect()->back()->with('analiseTexto', $analiseExistente->texto);
        }

        $formulario = \App\Models\Formulario::with('perguntas.variaveis')->findOrFail($formularioId);
        // Cargar variables con preguntas, asegurando que se carguen todos los campos de las preguntas
        $variaveis = \App\Models\Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])->where('formulario_id', $formulario->id)->get();
        $respostasUsuario = \App\Models\Resposta::where('user_id', $usuarioId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $pontuacao += $valorResposta;
                }
            }
            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'valor' => $pontuacao,
            ];
        }

        $prompt = "Você é um assistente especializado em saúde emocional, bem-estar no trabalho e aconselhamento motivacional.

Escreva um relatório analítico detalhado direcionado à pessoa que respondeu este questionário, com **no mínimo 1200 palavras** (mínimo 7500 caracteres).

O relatório deve obrigatoriamente conter:
- Um Resumo Geral Motivacional (mínimo 8 parágrafos).
- Uma Análise Profunda das Respostas (mínimo 8 parágrafos), detalhando padrões, vulnerabilidades, pontos fortes e inconsistências.
- Sugestões Personalizadas (mínimo 8 parágrafos), com recomendações práticas.
- Uma Conclusão Inspiradora.

Evite frases curtas. Expanda cada seção com exemplos, detalhes, reflexões. Não resuma. Não termine cedo. Gere um texto robusto e humano, sem economizar palavras.

Aqui estão os resultados por dimensão:\n";

        foreach ($variaveis as $variavel) {
            $ponto = collect($pontuacoes)->firstWhere('tag', strtoupper($variavel->tag))['valor'] ?? null;
            if ($ponto !== null) {
                if ($ponto <= $variavel->B) {
                    $faixa = 'Baixa';
                } elseif ($ponto <= $variavel->M) {
                    $faixa = 'Moderada';
                } else {
                    $faixa = 'Alta';
                }
                $prompt .= "{$variavel->nome} ({$variavel->tag}): {$ponto} pontos, Faixa {$faixa}.\n";
            }
        }

        $prompt .= "\nAqui estão as respostas individuais fornecidas:\n";
        foreach ($formulario->perguntas as $pergunta) {
            $resposta = $respostasUsuario->get($pergunta->id);
            $valor = $resposta ? $resposta->valor_resposta : 'não respondida';

            $prompt .= "Pergunta {$pergunta->id} ({$pergunta->texto}): Resposta = {$valor}\n";
        }

        $inconsistencias = [];
        foreach ($variaveis as $variavel) {
            $respostasNaVariavel = [];

            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $respostasNaVariavel[$pergunta->id] = $resposta->valor_resposta;
                }
            }

            $perguntaIds = array_keys($respostasNaVariavel);
            for ($i = 0; $i < count($perguntaIds); $i++) {
                for ($j = $i + 1; $j < count($perguntaIds); $j++) {
                    $p1 = $perguntaIds[$i];
                    $p2 = $perguntaIds[$j];
                    $diff = abs($respostasNaVariavel[$p1] - $respostasNaVariavel[$p2]);

                    if ($diff >= 3) {
                        $inconsistencias[] = "Na variável {$variavel->nome}, a Pergunta {$p1} teve resposta {$respostasNaVariavel[$p1]} e a Pergunta {$p2} teve resposta {$respostasNaVariavel[$p2]} (diferença de {$diff}).";
                    }
                }
            }
        }

        if (!empty($inconsistencias)) {
            $prompt .= "\n\nDetectamos as seguintes inconsistências:\n";
            foreach ($inconsistencias as $item) {
                $prompt .= "- {$item}\n";
            }
        } else {
            $prompt .= "\n\nNenhuma inconsistência grande foi detectada.";
        }

        $prompt .= "\n\n⚠️ Gere no mínimo 1200 palavras. Cada bloco deve ter no mínimo 8 parágrafos. Seja completo, detalhado e profundo.";

        $analiseTexto = '';
        $maxTries = 3;
        $tries = 0;

        do {
            $tries++;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente especializado em saúde emocional e aconselhamento motivacional.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ]);

            if ($response->successful()) {
                $analiseTexto = $response->json()['choices'][0]['message']['content'];
                $wordCount = str_word_count(strip_tags($analiseTexto));

                if ($wordCount >= 1200) {
                    break; // texto suficiente
                }

            } else {
                $analiseTexto = 'Erro ao gerar análise: ' . $response->body();
                break;
            }

        } while ($tries < $maxTries);

        if ($analiseTexto && !str_contains($analiseTexto, 'Erro ao gerar análise')) {
            Analise::create([
                'user_id' => $usuarioId,
                'formulario_id' => $formularioId,
                'texto' => $analiseTexto,
            ]);
        }

        return redirect()->back()->with('analiseTexto', $analiseTexto);
    }

    /**
     * Obtiene el valor de respuesta aplicando inversión si la pregunta lo requiere
     * Las preguntas que requieren inversión son las que tienen estos IDs: 4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
     * Inversión: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
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
        
        // Usar el ID de la pregunta (ID de la base de datos) para identificar cuáles requieren inversión
        // Las preguntas que requieren inversión son las que tienen estos IDs: 4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97
        $perguntaId = (int)$pergunta->id;
        
        // Lista de IDs de preguntas que requieren inversión (según el CSV)
        // Actualizada: incluye preguntas #4, #6, #9, #21, #25, #31, #35 que deben dar 0 cuando están en 6
        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        // Verificar si esta pregunta requiere inversión (usando el ID de la base de datos)
        if (in_array($perguntaId, $perguntasComInversao, true)) {
            // Invertir el valor: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
            // En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo
            $valorInvertido = 6 - $valor;
            \Log::info('✅ APLICANDO INVERSIÓN', [
                'pergunta_id' => $perguntaId,
                'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A',
                'valor_original' => $valor,
                'valor_invertido' => $valorInvertido
            ]);
            return $valorInvertido;
        }
        
        return $valor;
    }
}
