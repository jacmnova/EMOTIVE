<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Variavel;
use App\Models\Resposta;
use App\Models\Pergunta;

class DiagnosticoCalculos extends Command
{
    protected $signature = 'emotive:diagnostico {user_id} {formulario_id=1}';
    protected $description = 'Diagnostica los cálculos del radar E.MO.TI.VE';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');
        
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario no encontrado: {$userId}");
            return 1;
        }
        
        // Preguntas que requieren inversión
        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        // Cargar variables
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta');
        }])->where('formulario_id', $formularioId)->get();
        
        // Cargar respuestas
        $perguntaIds = Pergunta::where('formulario_id', $formularioId)->pluck('id');
        $respostas = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');
        
        $this->info("=== DIAGNÓSTICO DE CÁLCULOS EMOTIVE ===\n");
        $this->info("Usuario: {$user->name} (ID: {$userId})");
        $this->info("Formulario ID: {$formularioId}\n");
        
        // Verificar cada dimensión
        foreach ($variaveis as $variavel) {
            $this->info("--- {$variavel->tag} ({$variavel->nome}) ---");
            
            $pontuacao = 0;
            $totalRespostas = 0;
            $preguntasInvertidas = 0;
            $preguntasNormales = 0;
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostas->get($pergunta->id);
                
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }
                
                $valorOriginal = (int)$resposta->valor_resposta;
                $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                
                if ($necesitaInversion) {
                    $valorUsado = 6 - $valorOriginal;
                    $preguntasInvertidas++;
                    $this->line("  Pregunta ID {$pergunta->id} (num: {$pergunta->numero_da_pergunta}): {$valorOriginal} → {$valorUsado} [INVERTIDA]");
                } else {
                    $valorUsado = $valorOriginal;
                    $preguntasNormales++;
                    $this->line("  Pregunta ID {$pergunta->id} (num: {$pergunta->numero_da_pergunta}): {$valorOriginal} [NORMAL]");
                }
                
                $pontuacao += $valorUsado;
                $totalRespostas++;
            }
            
            $this->info("Total: {$pontuacao} (Normales: {$preguntasNormales}, Invertidas: {$preguntasInvertidas}, Respuestas: {$totalRespostas})");
            $this->info("");
        }
        
        return 0;
    }
}

