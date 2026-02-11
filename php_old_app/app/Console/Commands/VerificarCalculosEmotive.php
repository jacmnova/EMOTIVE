<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variavel;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\User;

class VerificarCalculosEmotive extends Command
{
    protected $signature = 'emotive:verificar-calculos {user_id} {formulario_id=1}';
    protected $description = 'Verifica que los cálculos coincidan con el CSV del emulador';

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
        $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        // Mapeo según el CSV
        $dimensionesEsperadas = [
            'EXEM' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61],
            'REPR' => [28, 29, 30, 31, 32, 33, 34, 35, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            'DECI' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56],
            'FAPS' => [78, 79, 80, 81, 82, 83, 84, 85, 86, 87],
            'EXTR' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'ASMO' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        ];
        
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
        foreach ($dimensionesEsperadas as $tag => $numerosEsperados) {
            $variavel = $variaveis->firstWhere('tag', $tag) ?? $variaveis->firstWhere('tag', ucfirst(strtolower($tag)));
            
            if (!$variavel) {
                $this->warn("⚠️  Variable {$tag} no encontrada");
                continue;
            }
            
            $this->info("--- {$tag} ({$variavel->nome}) ---");
            $this->info("Preguntas esperadas según CSV: " . count($numerosEsperados));
            $this->info("Preguntas en BD: " . $variavel->perguntas->count());
            
            // Calcular manualmente
            $totalManual = 0;
            $preguntasEncontradas = [];
            $preguntasFaltantes = [];
            
            foreach ($numerosEsperados as $numeroEsperado) {
                $pergunta = $variavel->perguntas->first(function($p) use ($numeroEsperado) {
                    return $p->numero_da_pergunta == $numeroEsperado;
                });
                
                if (!$pergunta) {
                    $preguntasFaltantes[] = $numeroEsperado;
                    continue;
                }
                
                $resposta = $respostas->get($pergunta->id);
                if (!$resposta) {
                    $preguntasFaltantes[] = $numeroEsperado;
                    continue;
                }
                
                $valorOriginal = $resposta->valor_resposta;
                $necesitaInversion = in_array($numeroEsperado, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                
                $totalManual += $valorUsado;
                $preguntasEncontradas[] = [
                    'numero' => $numeroEsperado,
                    'valor_original' => $valorOriginal,
                    'invertida' => $necesitaInversion,
                    'valor_usado' => $valorUsado
                ];
            }
            
            $this->info("Preguntas encontradas: " . count($preguntasEncontradas));
            if (!empty($preguntasFaltantes)) {
                $this->warn("Preguntas faltantes: " . implode(', ', $preguntasFaltantes));
            }
            
            // Calcular con el método del controlador
            $totalControlador = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostas->get($pergunta->id);
                if ($resposta) {
                    $valorOriginal = $resposta->valor_resposta;
                    $necesitaInversion = in_array($pergunta->numero_da_pergunta, $perguntasComInversao, true);
                    $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                    $totalControlador += $valorUsado;
                }
            }
            
            $this->info("Total manual (según CSV): {$totalManual}");
            $this->info("Total controlador (según BD): {$totalControlador}");
            
            if ($totalManual != $totalControlador) {
                $this->error("❌ DIFERENCIA DETECTADA!");
            } else {
                $this->info("✅ Valores coinciden");
            }
            $this->info("");
        }
        
        return 0;
    }
}

