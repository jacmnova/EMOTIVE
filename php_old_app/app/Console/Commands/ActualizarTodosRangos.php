<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Support\Facades\DB;

class ActualizarTodosRangos extends Command
{
    protected $signature = 'emotive:actualizar-todos-rangos';
    protected $description = 'Actualiza los rangos B, M, A para TODAS las variables de TODOS los formularios usando la fórmula general';

    public function handle()
    {
        $this->info('🔄 Actualizando rangos B, M, A para todas las encuestas...');
        $this->info('');
        
        $formularios = Formulario::all();
        $totalActualizados = 0;
        
        foreach ($formularios as $formulario) {
            $this->info("📋 Formulario: {$formulario->nome} (ID: {$formulario->id})");
            $this->info("   Score máximo: {$formulario->score_fim}");
            
            $variaveis = Variavel::where('formulario_id', $formulario->id)->get();
            
            foreach ($variaveis as $variavel) {
                $variavel->load('perguntas', 'formulario');
                $totalPerguntas = $variavel->perguntas()->count();
                $scoreFim = $formulario->score_fim ?? 6;
                
                $rangos = \App\Traits\CalculaRangosVariavel::calcularRangosGenerales($totalPerguntas, $scoreFim);
                
                // Solo actualizar si son diferentes
                if ($variavel->B != $rangos['B'] || $variavel->M != $rangos['M'] || $variavel->A != $rangos['A']) {
                    $this->line("   ✅ {$variavel->tag}: B={$rangos['B']}, M={$rangos['M']}, A={$rangos['A']} ({$totalPerguntas} preguntas)");
                    
                    Variavel::withoutEvents(function() use ($variavel, $rangos) {
                        $variavel->update([
                            'B' => $rangos['B'],
                            'M' => $rangos['M'],
                            'A' => $rangos['A']
                        ]);
                    });
                    
                    $totalActualizados++;
                }
            }
            $this->info('');
        }
        
        $this->info("✨ Proceso completado! Total: {$totalActualizados} variable(s) actualizada(s)");
        
        return 0;
    }
}

