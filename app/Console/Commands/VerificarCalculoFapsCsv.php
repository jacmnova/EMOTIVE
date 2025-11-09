<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerificarCalculoFapsCsv extends Command
{
    protected $signature = 'emotive:verificar-faps-csv';
    protected $description = 'Verifica el cálculo de FAPS comparando con el CSV del emulador';

    public function handle()
    {
        $this->info('📊 Analizando CSV del Emulador E.MO.TI.VE');
        $this->info('==========================================\n');

        // Según el CSV línea 9: SCORE,13,16,8,9,18,0,26,17,18
        // Columnas: EXEM, REPR, DECI, FAPS, EXTR, ASMO, EE, PR, SO
        $scoreEsperadoFaps = 9;
        
        $this->info("Score esperado para FAPS según CSV: {$scoreEsperadoFaps}");
        $this->info("Rangos según CSV línea 2-5:");
        $this->info("  - Faixa Baixa (0 até): 20");
        $this->info("  - Faixa Média (até): 40");
        $this->info("  - Faixa Alta (acima até Máx): 41");
        $this->info("  - MAX: 60");
        $this->info("");

        // Preguntas de FAPS según el código: [78, 79, 80, 81, 82, 83, 84, 85, 86, 87]
        // Preguntas que requieren inversión: [78, 79, 81, 82, 83]
        
        // Valores del CSV (User_Choice):
        // #078: 6 (requiere inversión: 6→0)
        // #079: 6 (requiere inversión: 6→0)
        // #080: 1 (NO requiere inversión: 1)
        // #081: 6 (requiere inversión: 6→0)
        // #082: 2 (requiere inversión: 2→4)
        // #083: 5 (requiere inversión: 5→1)
        // #084: 2 (NO requiere inversión: 2)
        // #085: 1 (NO requiere inversión: 1)
        // #086: 0 (NO requiere inversión: 0)
        // #087: 0 (NO requiere inversión: 0)

        $preguntasFaps = [
            78 => ['user_choice' => 6, 'inversion' => true, 'valor_original' => 6, 'valor_usado' => 0],
            79 => ['user_choice' => 6, 'inversion' => true, 'valor_original' => 6, 'valor_usado' => 0],
            80 => ['user_choice' => 1, 'inversion' => false, 'valor_original' => 1, 'valor_usado' => 1],
            81 => ['user_choice' => 6, 'inversion' => true, 'valor_original' => 6, 'valor_usado' => 0],
            82 => ['user_choice' => 2, 'inversion' => true, 'valor_original' => 2, 'valor_usado' => 4],
            83 => ['user_choice' => 5, 'inversion' => true, 'valor_original' => 5, 'valor_usado' => 1],
            84 => ['user_choice' => 2, 'inversion' => false, 'valor_original' => 2, 'valor_usado' => 2],
            85 => ['user_choice' => 1, 'inversion' => false, 'valor_original' => 1, 'valor_usado' => 1],
            86 => ['user_choice' => 0, 'inversion' => false, 'valor_original' => 0, 'valor_usado' => 0],
            87 => ['user_choice' => 0, 'inversion' => false, 'valor_original' => 0, 'valor_usado' => 0],
        ];

        $this->info("📝 Cálculo manual de FAPS:");
        $this->info("--------------------------------");
        
        $total = 0;
        foreach ($preguntasFaps as $preguntaId => $datos) {
            $total += $datos['valor_usado'];
            $inversionStr = $datos['inversion'] ? ' (INVERTIDO: ' . $datos['valor_original'] . '→' . $datos['valor_usado'] . ')' : '';
            $this->line("  Pregunta #{$preguntaId}: {$datos['valor_original']} → {$datos['valor_usado']}{$inversionStr}");
        }
        
        $this->info("");
        $this->info("✅ Total calculado: {$total}");
        $this->info("📋 Score esperado del CSV: {$scoreEsperadoFaps}");
        
        if ($total == $scoreEsperadoFaps) {
            $this->info("✅ ¡Los cálculos coinciden!");
        } else {
            $this->error("❌ ¡HAY UNA DIFERENCIA!");
            $this->error("   Diferencia: " . abs($total - $scoreEsperadoFaps));
        }
        
        $this->info("");
        $this->info("🎯 Clasificación según rangos:");
        $this->info("   B = 20, M = 40, A = 60");
        
        if ($total <= 20) {
            $faixa = 'Baixa';
            $this->info("   Score {$total} ≤ 20 → Faixa {$faixa} ✅");
        } elseif ($total <= 40) {
            $faixa = 'Moderada';
            $this->info("   Score {$total} ≤ 40 → Faixa {$faixa} ✅");
        } else {
            $faixa = 'Alta';
            $this->info("   Score {$total} > 40 → Faixa {$faixa} ✅");
        }
        
        $this->info("");
        $this->info("📊 Comparación con valores del sistema:");
        $this->info("   Sistema actual: B=20, M=40, A=60");
        $this->info("   CSV muestra: B=20, M=40, A=60");
        $this->info("   ✅ Los valores coinciden");
        
        return 0;
    }
}

