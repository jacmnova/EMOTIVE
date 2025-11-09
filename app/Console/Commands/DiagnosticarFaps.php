<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Resposta;
use App\Models\Pergunta;
use App\Models\Variavel;
use App\Models\Formulario;

class DiagnosticarFaps extends Command
{
    protected $signature = 'emotive:diagnosticar-faps {user_id} {formulario_id=1}';
    protected $description = 'Diagnostica el cálculo de FAPS para un usuario específico';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');
        
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario no encontrado: {$userId}");
            return 1;
        }
        
        $formulario = Formulario::find($formularioId);
        if (!$formulario) {
            $this->error("Formulario no encontrado: {$formularioId}");
            return 1;
        }
        
        $this->info("🔍 Diagnóstico de FAPS para usuario: {$user->name} (ID: {$userId})");
        $this->info("   Formulario: {$formulario->nome} (ID: {$formularioId})");
        $this->info("");
        
        // Obtener variable FAPS
        $variavelFaps = Variavel::where('formulario_id', $formularioId)
            ->where(function($q) {
                $q->where('tag', 'FaPs')
                  ->orWhere('tag', 'FAPS')
                  ->orWhere('tag', 'faps');
            })
            ->first();
        
        if (!$variavelFaps) {
            $this->error("Variable FAPS no encontrada");
            return 1;
        }
        
        $this->info("📊 Variable FAPS:");
        $this->info("   ID: {$variavelFaps->id}");
        $this->info("   Nombre: {$variavelFaps->nome}");
        $this->info("   Tag: {$variavelFaps->tag}");
        $this->info("   Rangos: B={$variavelFaps->B}, M={$variavelFaps->M}, A={$variavelFaps->A}");
        $this->info("");
        
        // Obtener preguntas de FAPS
        $variavelFaps->load('perguntas');
        $perguntasFaps = $variavelFaps->perguntas;
        
        $this->info("📝 Preguntas asociadas: {$perguntasFaps->count()}");
        
        // Preguntas que requieren inversión
        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        // Obtener respuestas
        $perguntaIds = $perguntasFaps->pluck('id');
        $respostas = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');
        
        $this->info("   Respuestas encontradas: {$respostas->count()}");
        $this->info("");
        
        // Calcular manualmente
        $this->info("🔢 Cálculo detallado:");
        $this->info("--------------------------------");
        
        $total = 0;
        $detalles = [];
        
        foreach ($perguntasFaps as $pergunta) {
            $resposta = $respostas->get($pergunta->id);
            
            if (!$resposta) {
                $this->warn("   Pregunta #{$pergunta->id} (nº{$pergunta->numero_da_pergunta}): Sin respuesta");
                continue;
            }
            
            $valorOriginal = (int)$resposta->valor_resposta;
            $perguntaId = $pergunta->id;
            $necesitaInversion = in_array($perguntaId, $perguntasComInversao, true);
            $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
            
            $total += $valorUsado;
            
            $inversionStr = $necesitaInversion ? " (INV: {$valorOriginal}→{$valorUsado})" : "";
            $this->line("   Pregunta #{$pergunta->id} (nº{$pergunta->numero_da_pergunta}): {$valorOriginal} → {$valorUsado}{$inversionStr}");
            
            $detalles[] = [
                'pergunta_id' => $pergunta->id,
                'numero' => $pergunta->numero_da_pergunta,
                'valor_original' => $valorOriginal,
                'inversion' => $necesitaInversion,
                'valor_usado' => $valorUsado
            ];
        }
        
        $this->info("");
        $this->info("✅ Total calculado: {$total}");
        $this->info("");
        
        // Clasificar
        $this->info("🎯 Clasificación:");
        $this->info("   B = {$variavelFaps->B}, M = {$variavelFaps->M}, A = {$variavelFaps->A}");
        
        if ($total <= $variavelFaps->B) {
            $faixa = 'Baixa';
        } elseif ($total <= $variavelFaps->M) {
            $faixa = 'Moderada';
        } else {
            $faixa = 'Alta';
        }
        
        $this->info("   Score {$total} → Faixa {$faixa}");
        $this->info("");
        
        // Verificar qué está mostrando el sistema
        $this->info("🔍 Verificando qué muestra el sistema...");
        
        // Simular el cálculo del controlador
        $pontuacao = 0;
        foreach ($variavelFaps->perguntas as $pergunta) {
            $resposta = $respostas->get($pergunta->id);
            if ($resposta) {
                $valorOriginal = (int)$resposta->valor_resposta;
                $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                $pontuacao += $valorUsado;
            }
        }
        
        $this->info("   Score calculado por sistema: {$pontuacao}");
        
        if ($pontuacao != $total) {
            $this->error("   ❌ DIFERENCIA: Manual={$total}, Sistema={$pontuacao}");
        } else {
            $this->info("   ✅ Coincide con cálculo manual");
        }
        
        return 0;
    }
}

