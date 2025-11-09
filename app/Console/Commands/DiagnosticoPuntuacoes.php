<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Variavel;
use App\Models\Resposta;
use App\Models\Pergunta;

class DiagnosticoPuntuacoes extends Command
{
    protected $signature = 'diagnostico:puntuacoes {usuario_id} {formulario_id}';
    protected $description = 'Diagnostica las puntuaciones calculadas vs esperadas';

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
            ->get();

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];

        $this->info("=== DIAGNÓSTICO DE PUNTUACIONES ===");
        $this->info("Usuario: {$user->name} (ID: {$usuarioId})");
        $this->info("Formulario: {$formulario->nome} (ID: {$formularioId})");
        $this->info("");

        foreach ($variaveis as $variavel) {
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Variable: {$variavel->tag} - {$variavel->nome}");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            $pontuacao = 0;
            $detalle = [];

            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }

                $valorOriginal = $resposta->valor_resposta;
                $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                
                $pontuacao += $valorUsado;

                $detalle[] = [
                    'id' => $pergunta->id,
                    'numero' => $pergunta->numero_da_pergunta,
                    'original' => $valorOriginal,
                    'invertir' => $necesitaInversion ? 'SÍ' : 'NO',
                    'usado' => $valorUsado,
                    'acumulado' => $pontuacao
                ];
            }

            // Mostrar detalle
            $this->table(
                ['ID', 'Número', 'Original', 'Invertir', 'Valor Usado', 'Acumulado'],
                array_map(function($d) {
                    return [
                        $d['id'],
                        $d['numero'],
                        $d['original'],
                        $d['invertir'],
                        $d['usado'],
                        $d['acumulado']
                    ];
                }, $detalle)
            );

            $this->info("PUNTUACIÓN FINAL: {$pontuacao}");
            $this->info("");
        }

        return 0;
    }
}

