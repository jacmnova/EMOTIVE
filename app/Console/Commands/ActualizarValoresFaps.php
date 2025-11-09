<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActualizarValoresFaps extends Command
{
    protected $signature = 'emotive:actualizar-rangos';
    protected $description = 'Actualiza los valores B, M, A para TODAS las dimensiones según el CSV del emulador';

    public function handle()
    {
        $this->info('🔄 Actualizando valores B, M, A según CSV del Emulador...');
        $this->info('');
        
        // Valores según el CSV (líneas 2-5)
        $valoresCsv = [
            'ExEm' => ['B' => 52, 'M' => 104, 'A' => 105],
            'RePr' => ['B' => 52, 'M' => 104, 'A' => 105],
            'DeCi' => ['B' => 58, 'M' => 116, 'A' => 117],
            'FaPs' => ['B' => 20, 'M' => 40, 'A' => 41],
            'AsMo' => ['B' => 30, 'M' => 60, 'A' => 61],
            'ExTr' => ['B' => 32, 'M' => 64, 'A' => 65],
        ];
        
        $totalActualizados = 0;
        
        foreach ($valoresCsv as $tag => $valores) {
            $this->info("📝 Actualizando {$tag}...");
            
            // Actualizar registros (sin importar formulario_id)
            $actualizados = DB::table('variaveis')
                ->where(function($query) use ($tag) {
                    $query->where('tag', $tag)
                          ->orWhere('tag', strtoupper($tag))
                          ->orWhere('tag', strtolower($tag));
                })
                ->update([
                    'B' => $valores['B'],
                    'M' => $valores['M'],
                    'A' => $valores['A'],
                    'updated_at' => now()
                ]);
            
            if ($actualizados > 0) {
                $this->info("   ✅ {$actualizados} registro(s) actualizado(s)");
                $this->line("      B={$valores['B']}, M={$valores['M']}, A={$valores['A']}");
                $totalActualizados += $actualizados;
            } else {
                $this->warn("   ⚠️  No se encontraron registros para {$tag}");
            }
        }
        
        $this->info('');
        $this->info("✨ Proceso completado! Total: {$totalActualizados} registro(s) actualizado(s)");
        
        return 0;
    }
}
