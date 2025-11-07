<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActualizarValoresFaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emotive:actualizar-faps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los valores B, M, A para Fatores Psicossociais (FaPs) en todos los formularios según el Excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Actualizando valores de FaPs en todos los formularios...');
        
        // Valores correctos según el Excel:
        // Faixa Baixa: 0 hasta 20 → B = 20
        // Faixa Média: hasta 40 → M = 40
        // Faixa Alta: arriba hasta 60 → A = 60
        $nuevoB = 20;
        $nuevoM = 40;
        $nuevoA = 60;
        
        // Actualizar todos los registros de FaPs sin importar el formulario_id
        $actualizados = DB::table('variaveis')
            ->where('tag', 'FaPs')
            ->orWhere('tag', 'FAPS')
            ->orWhere('tag', 'faps')
            ->update([
                'B' => $nuevoB,
                'M' => $nuevoM,
                'A' => $nuevoA,
                'updated_at' => now()
            ]);
        
        if ($actualizados > 0) {
            $this->info("✅ Se actualizaron {$actualizados} registro(s) de FaPs");
            $this->info("   - B (Faixa Baixa): {$nuevoB}");
            $this->info("   - M (Faixa Média): {$nuevoM}");
            $this->info("   - A (Faixa Alta): {$nuevoA}");
            
            // Mostrar los registros actualizados
            $registros = DB::table('variaveis')
                ->where('tag', 'FaPs')
                ->orWhere('tag', 'FAPS')
                ->orWhere('tag', 'faps')
                ->select('id', 'formulario_id', 'nome', 'tag', 'B', 'M', 'A')
                ->get();
            
            $this->info("\n📋 Registros actualizados:");
            foreach ($registros as $registro) {
                $this->line("   - ID: {$registro->id} | Formulario: {$registro->formulario_id} | {$registro->nome} ({$registro->tag})");
                $this->line("     B={$registro->B}, M={$registro->M}, A={$registro->A}");
            }
        } else {
            $this->warn('⚠️  No se encontraron registros de FaPs para actualizar');
        }
        
        $this->info("\n✨ Proceso completado!");
        
        return 0;
    }
}

