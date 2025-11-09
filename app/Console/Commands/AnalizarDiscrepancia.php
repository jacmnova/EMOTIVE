<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Variavel;
use App\Models\Resposta;

class AnalizarDiscrepancia extends Command
{
    protected $signature = 'analizar:discrepancia {usuario_id} {formulario_id}';
    protected $description = 'Analiza en detalle las discrepancias entre los cálculos';

    public function handle()
    {
        $usuarioId = $this->argument('usuario_id');
        $formularioId = $this->argument('formulario_id');

        $user = User::find($usuarioId);
        $formulario = Formulario::find($formularioId);

        if (!$user || !$formulario) {
            $this->error('Usuario o formulario no encontrado');
            return 1;
        }

        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->orderBy('tag')
            ->get();

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];

        $this->info("=== ANÁLISIS DETALLADO DE DISCREPANCIAS ===");
        $this->info("Usuario: {$user->name} (ID: {$usuarioId})");
        $this->info("Formulario: {$formulario->nome} (ID: {$formularioId})");
        $this->info("");

        $resultados = [];

        foreach ($variaveis as $variavel) {
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Variable: {$variavel->tag} - {$variavel->nome}");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            $pontuacao = 0;
            $pontuacaoSinInversion = 0;
            $detalle = [];
            $preguntasInvertidas = [];
            $preguntasNormales = [];

            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }

                $valorOriginal = $resposta->valor_resposta;
                $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                
                $pontuacao += $valorUsado;
                $pontuacaoSinInversion += $valorOriginal;

                if ($necesitaInversion) {
                    $preguntasInvertidas[] = [
                        'id' => $pergunta->id,
                        'numero' => $pergunta->numero_da_pergunta,
                        'original' => $valorOriginal,
                        'invertido' => $valorUsado
                    ];
                } else {
                    $preguntasNormales[] = [
                        'id' => $pergunta->id,
                        'numero' => $pergunta->numero_da_pergunta,
                        'valor' => $valorOriginal
                    ];
                }
            }

            $resultados[$variavel->tag] = [
                'nome' => $variavel->nome,
                'con_inversion' => $pontuacao,
                'sin_inversion' => $pontuacaoSinInversion,
                'diferencia' => $pontuacaoSinInversion - $pontuacao,
                'total_preguntas' => count($preguntasNormales) + count($preguntasInvertidas),
                'preguntas_invertidas' => count($preguntasInvertidas),
                'preguntas_normales' => count($preguntasNormales)
            ];

            $this->info("Preguntas NORMALES ({$resultados[$variavel->tag]['preguntas_normales']}):");
            foreach ($preguntasNormales as $p) {
                $this->line("  ID {$p['id']} (Nº{$p['numero']}): {$p['valor']}");
            }

            $this->info("");
            $this->info("Preguntas INVERTIDAS ({$resultados[$variavel->tag]['preguntas_invertidas']}):");
            foreach ($preguntasInvertidas as $p) {
                $this->line("  ID {$p['id']} (Nº{$p['numero']}): {$p['original']} → {$p['invertido']}");
            }

            $this->info("");
            $this->info("📊 RESUMEN:");
            $this->info("  Sin inversión: {$pontuacaoSinInversion}");
            $this->info("  Con inversión: {$pontuacao}");
            $this->info("  Diferencia: " . ($pontuacaoSinInversion - $pontuacao));
            $this->info("");
        }

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📋 RESUMEN GENERAL");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $this->table(
            ['Variable', 'Con Inversión', 'Sin Inversión', 'Diferencia', 'Total Preguntas', 'Invertidas'],
            array_map(function($r, $tag) {
                return [
                    $tag,
                    $r['con_inversion'],
                    $r['sin_inversion'],
                    $r['diferencia'],
                    $r['total_preguntas'],
                    $r['preguntas_invertidas']
                ];
            }, $resultados, array_keys($resultados))
        );

        $this->info("");
        $this->warn("⚠️  Valores esperados según Excel:");
        $this->info("  EXEM: 98");
        $this->info("  REPR: 98");
        $this->info("  DECI: 113");
        $this->info("  FAPS: 30");
        $this->info("  ASMO: 75");
        $this->info("  EXTR: 80");
        $this->info("");

        return 0;
    }
}

