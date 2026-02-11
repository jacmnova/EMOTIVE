<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Variavel;
use App\Models\Resposta;

class ExportarParaComparar extends Command
{
    protected $signature = 'exportar:comparar {usuario_id} {formulario_id}';
    protected $description = 'Exporta los datos en formato CSV para comparar con Excel';

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

        $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];

        $output = [];
        $output[] = ['Variable', 'Tag', 'Pregunta ID', 'Número Pregunta', 'Valor Original', 'Necesita Inversión', 'Valor Usado', 'Puntuación Acumulada'];

        foreach ($variaveis as $variavel) {
            $pontuacao = 0;

            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }

                $valorOriginal = $resposta->valor_resposta;
                $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                
                $pontuacao += $valorUsado;

                $output[] = [
                    $variavel->nome,
                    $variavel->tag,
                    $pergunta->id,
                    $pergunta->numero_da_pergunta,
                    $valorOriginal,
                    $necesitaInversion ? 'SÍ' : 'NO',
                    $valorUsado,
                    $pontuacao
                ];
            }

            // Línea separadora con total
            $output[] = [
                $variavel->nome,
                $variavel->tag,
                'TOTAL',
                '',
                '',
                '',
                '',
                $pontuacao
            ];
            $output[] = []; // Línea vacía
        }

        $filename = storage_path("app/comparacion_usuario_{$usuarioId}_formulario_{$formularioId}.csv");
        $file = fopen($filename, 'w');
        
        foreach ($output as $row) {
            fputcsv($file, $row, ';');
        }
        
        fclose($file);

        $this->info("✅ Archivo exportado: {$filename}");
        $this->info("Puedes abrirlo en Excel para comparar con tus cálculos manuales.");

        return 0;
    }
}

