<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Pergunta;

class DiagnosticarCalculosRespostas extends Command
{
    protected $signature = 'emotive:diagnosticar-respostas {user_id} {formulario_id}';
    protected $description = 'Diagnostica cómo se están obteniendo y usando las respuestas en los cálculos';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario no encontrado: {$userId}");
            return 1;
        }

        $formulario = Formulario::with('perguntas')->find($formularioId);
        if (!$formulario) {
            $this->error("Formulario no encontrado: {$formularioId}");
            return 1;
        }

        $this->info("🔍 DIAGNÓSTICO DE RESPUESTAS Y CÁLCULOS");
        $this->info("Usuario: {$user->name} (ID: {$user->id})");
        $this->info("Formulario: {$formulario->nome} (ID: {$formulario->id})");
        $this->info("");

        // 1. Obtener respuestas como lo hace el controlador
        $perguntaIds = $formulario->perguntas->pluck('id')->toArray();
        $this->info("📋 Total de preguntas en formulario: " . count($perguntaIds));
        
        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');

        $this->info("✅ Respuestas encontradas: " . $respostasUsuario->count());
        $this->info("");

        // 2. Verificar respuestas por pregunta
        $this->info("📊 DETALLE DE RESPUESTAS:");
        $this->info(str_repeat("-", 80));
        
        $respostasConValor = 0;
        $respostasSinValor = 0;
        
        foreach ($formulario->perguntas->take(10) as $pergunta) {
            $resposta = $respostasUsuario->get($pergunta->id);
            if ($resposta) {
                $valor = $resposta->valor_resposta;
                if ($valor !== null) {
                    $respostasConValor++;
                    $this->line("Pregunta ID {$pergunta->id} (#{$pergunta->numero_da_pergunta}): Valor = {$valor}");
                } else {
                    $respostasSinValor++;
                    $this->warn("Pregunta ID {$pergunta->id} (#{$pergunta->numero_da_pergunta}): Sin valor (NULL)");
                }
            } else {
                $respostasSinValor++;
                $this->error("Pregunta ID {$pergunta->id} (#{$pergunta->numero_da_pergunta}): Sin respuesta");
            }
        }
        
        if ($formulario->perguntas->count() > 10) {
            $this->info("... (mostrando solo las primeras 10)");
        }
        
        $this->info("");
        $this->info("Resumen: {$respostasConValor} con valor, {$respostasSinValor} sin valor/ausentes");
        $this->info("");

        // 3. Verificar variables y sus preguntas
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        $this->info("📈 VARIABLES Y SUS CÁLCULOS:");
        $this->info(str_repeat("=", 80));

        foreach ($variaveis as $variavel) {
            $this->info("");
            $this->info("Variable: {$variavel->nome} ({$variavel->tag})");
            $this->info("Total de preguntas asociadas: " . $variavel->perguntas->count());
            
            if ($variavel->perguntas->isEmpty()) {
                $this->warn("⚠️  Variable sin preguntas asociadas!");
                continue;
            }

            $pontuacao = 0;
            $totalRespostas = 0;
            $preguntasConResposta = 0;
            $preguntasSinResposta = 0;
            
            $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
            
            $this->info("  Detalle de preguntas:");
            
            foreach ($variavel->perguntas->take(5) as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if ($resposta && $resposta->valor_resposta !== null) {
                    $valorOriginal = (int)$resposta->valor_resposta;
                    $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                    $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                    
                    $pontuacao += $valorUsado;
                    $totalRespostas++;
                    $preguntasConResposta++;
                    
                    $inversion = $necesitaInversion ? " (INVERTIDO: {$valorOriginal} → {$valorUsado})" : "";
                    $this->line("    ✓ Pregunta ID {$pergunta->id} (#{$pergunta->numero_da_pergunta}): {$valorOriginal} → {$valorUsado}{$inversion}");
                } else {
                    $preguntasSinResposta++;
                    $this->warn("    ✗ Pregunta ID {$pergunta->id} (#{$pergunta->numero_da_pergunta}): Sin respuesta");
                }
            }
            
            if ($variavel->perguntas->count() > 5) {
                $this->info("    ... (mostrando solo las primeras 5)");
            }
            
            $this->info("");
            $this->info("  📊 Resultado:");
            $this->info("    - Preguntas con respuesta: {$preguntasConResposta}");
            $this->info("    - Preguntas sin respuesta: {$preguntasSinResposta}");
            $this->info("    - Puntuación total: {$pontuacao}");
            $this->info("    - Total respuestas usadas: {$totalRespostas}");
            
            // Clasificar
            $b = is_numeric($variavel->B) ? (float)$variavel->B : 0;
            $m = is_numeric($variavel->M) ? (float)$variavel->M : 0;
            
            $faixa = 'Baixa';
            if ($pontuacao > $m) {
                $faixa = 'Alta';
            } elseif ($pontuacao > $b) {
                $faixa = 'Moderada';
            }
            
            $this->info("    - Faixa: {$faixa} (B={$b}, M={$m}, Puntuación={$pontuacao})");
        }

        // 4. Verificar directamente en la base de datos
        $this->info("");
        $this->info("🔍 VERIFICACIÓN DIRECTA EN BASE DE DATOS:");
        $this->info(str_repeat("=", 80));
        
        $totalRespostasBD = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->count();
        
        $respostasConValorBD = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->whereNotNull('valor_resposta')
            ->count();
        
        $this->info("Total respuestas en BD: {$totalRespostasBD}");
        $this->info("Respuestas con valor no nulo: {$respostasConValorBD}");
        
        // Mostrar algunas respuestas directamente
        $this->info("");
        $this->info("Primeras 5 respuestas en BD:");
        $respostasBD = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->with('pergunta')
            ->take(5)
            ->get();
        
        foreach ($respostasBD as $resposta) {
            $pergunta = $resposta->pergunta;
            $numero = $pergunta ? $pergunta->numero_da_pergunta : 'N/A';
            $valor = $resposta->valor_resposta ?? 'NULL';
            $this->line("  Pregunta ID {$resposta->pergunta_id} (#{$numero}): valor_resposta = {$valor}");
        }

        return 0;
    }
}

