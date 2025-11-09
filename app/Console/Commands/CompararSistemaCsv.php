<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variavel;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\User;

class CompararSistemaCsv extends Command
{
    protected $signature = 'emotive:comparar-csv {user_id?} {formulario_id=1}';
    protected $description = 'Compara los cálculos del sistema con el CSV del emulador';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');
        
        $this->info('🔍 Comparando Sistema vs CSV del Emulador');
        $this->info('==========================================\n');
        
        // Valores esperados del CSV (línea 9)
        $csvEsperado = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        // Rangos del CSV (líneas 2-5)
        $rangosCsv = [
            'EXEM' => ['B' => 52, 'M' => 104, 'A' => 105, 'MAX' => 156],
            'REPR' => ['B' => 52, 'M' => 104, 'A' => 105, 'MAX' => 156],
            'DECI' => ['B' => 58, 'M' => 116, 'A' => 117, 'MAX' => 174],
            'FAPS' => ['B' => 20, 'M' => 40, 'A' => 41, 'MAX' => 60],
            'EXTR' => ['B' => 32, 'M' => 64, 'A' => 65, 'MAX' => 96],
            'ASMO' => ['B' => 30, 'M' => 60, 'A' => 61, 'MAX' => 90],
        ];
        
        $this->info('📊 Valores esperados del CSV:');
        foreach ($csvEsperado as $tag => $valor) {
            $this->line("   {$tag}: {$valor}");
        }
        $this->info('');
        
        // Verificar valores B, M, A en la base de datos
        $this->info('🔍 Verificando valores B, M, A en la base de datos:');
        $variaveis = Variavel::where('formulario_id', $formularioId)->get();
        
        $problemas = [];
        foreach ($variaveis as $variavel) {
            $tag = strtoupper($variavel->tag);
            if (isset($rangosCsv[$tag])) {
                $csvRango = $rangosCsv[$tag];
                $dbRango = ['B' => $variavel->B, 'M' => $variavel->M, 'A' => $variavel->A];
                
                $this->line("   {$tag} ({$variavel->nome}):");
                $this->line("      CSV: B={$csvRango['B']}, M={$csvRango['M']}, A={$csvRango['A']}");
                $this->line("      DB:  B={$dbRango['B']}, M={$dbRango['M']}, A={$dbRango['A']}");
                
                if ($csvRango['B'] != $dbRango['B'] || $csvRango['M'] != $dbRango['M'] || $csvRango['A'] != $dbRango['A']) {
                    $problemas[] = "{$tag}: Los valores B, M, A no coinciden con el CSV";
                    $this->error("      ❌ NO COINCIDEN");
                } else {
                    $this->info("      ✅ Coinciden");
                }
            }
        }
        $this->info('');
        
        // Si hay un user_id, verificar cálculos reales
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Usuario no encontrado: {$userId}");
                return 1;
            }
            
            $this->info("📝 Verificando cálculos para usuario: {$user->name} (ID: {$userId})");
            
            // Obtener respuestas del usuario
            $perguntaIds = Pergunta::where('formulario_id', $formularioId)->pluck('id');
            $respostas = Resposta::where('user_id', $userId)
                ->whereIn('pergunta_id', $perguntaIds)
                ->get()
                ->keyBy('pergunta_id');
            
            // Preguntas que requieren inversión
            $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
            
            // Calcular FAPS manualmente
            $preguntasFaps = [78, 79, 80, 81, 82, 83, 84, 85, 86, 87];
            $totalFaps = 0;
            
            $this->info('   Cálculo de FAPS:');
            foreach ($preguntasFaps as $perguntaId) {
                $resposta = $respostas->get($perguntaId);
                if ($resposta) {
                    $valorOriginal = (int)$resposta->valor_resposta;
                    $necesitaInversion = in_array($perguntaId, $perguntasComInversao, true);
                    $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                    $totalFaps += $valorUsado;
                    
                    $inversionStr = $necesitaInversion ? " (INV: {$valorOriginal}→{$valorUsado})" : "";
                    $this->line("      Pregunta #{$perguntaId}: {$valorOriginal} → {$valorUsado}{$inversionStr}");
                } else {
                    $this->warn("      Pregunta #{$perguntaId}: Sin respuesta");
                }
            }
            
            $this->info("   Total FAPS calculado: {$totalFaps}");
            $this->info("   Total FAPS esperado (CSV): {$csvEsperado['FAPS']}");
            
            if ($totalFaps == $csvEsperado['FAPS']) {
                $this->info("   ✅ Los cálculos coinciden");
            } else {
                $this->error("   ❌ Diferencia: " . abs($totalFaps - $csvEsperado['FAPS']));
            }
        }
        
        $this->info('');
        
        if (empty($problemas)) {
            $this->info('✅ Todos los valores B, M, A coinciden con el CSV');
        } else {
            $this->error('❌ Problemas encontrados:');
            foreach ($problemas as $problema) {
                $this->error("   - {$problema}");
            }
        }
        
        return 0;
    }
}

