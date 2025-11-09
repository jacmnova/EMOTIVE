<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Support\Facades\DB;

class CalcularRangosGenerales extends Command
{
    protected $signature = 'emotive:calcular-rangos-generales {formulario_id=1}';
    protected $description = 'Calcula los rangos B, M, A de forma general basado en número de preguntas y score máximo';

    public function handle()
    {
        $formularioId = $this->argument('formulario_id');
        
        $formulario = Formulario::find($formularioId);
        if (!$formulario) {
            $this->error("Formulario no encontrado: {$formularioId}");
            return 1;
        }
        
        $scoreFim = $formulario->score_fim ?? 6; // Por defecto 6 si no está definido
        
        $this->info("📊 Calculando rangos generales para Formulario ID: {$formularioId}");
        $this->info("   Score máximo por pregunta: {$scoreFim}");
        $this->info("");
        
        // Analizar el CSV para encontrar el patrón
        // Según el CSV, los porcentajes aproximados son:
        // B ≈ 33.3% del MAX
        // M ≈ 66.7% del MAX  
        // A ≈ M + 1 (o sea, justo después de M)
        
        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formularioId)
            ->get();
        
        $this->info("🔍 Analizando cada dimensión:");
        $this->info("");
        
        $actualizaciones = [];
        
        foreach ($variaveis as $variavel) {
            $totalPerguntas = $variavel->perguntas->count();
            
            if ($totalPerguntas == 0) {
                $this->warn("   ⚠️  {$variavel->tag} ({$variavel->nome}): Sin preguntas asociadas");
                continue;
            }
            
            $max = $totalPerguntas * $scoreFim;
            
            // Calcular rangos basados en porcentajes del CSV
            // B = 33.3% del máximo (redondeado)
            // M = 66.7% del máximo (redondeado)
            // A = M + 1 (inicio de faixa alta)
            $b = round($max * 0.333);
            $m = round($max * 0.667);
            $a = $m + 1;
            
            // Asegurar que A no exceda el máximo
            if ($a > $max) {
                $a = $max;
            }
            
            $actualizaciones[] = [
                'variavel' => $variavel,
                'total_perguntas' => $totalPerguntas,
                'max' => $max,
                'B' => $b,
                'M' => $m,
                'A' => $a
            ];
            
            $this->line("   {$variavel->tag} ({$variavel->nome}):");
            $this->line("      Preguntas: {$totalPerguntas}");
            $this->line("      MAX: {$max} ({$totalPerguntas} × {$scoreFim})");
            $this->line("      Calculado: B={$b} (33.3%), M={$m} (66.7%), A={$a}");
            $this->line("      Actual:     B={$variavel->B}, M={$variavel->M}, A={$variavel->A}");
            
            if ($variavel->B != $b || $variavel->M != $m || $variavel->A != $a) {
                $this->warn("      ⚠️  Necesita actualización");
            } else {
                $this->info("      ✅ Ya está correcto");
            }
            $this->info("");
        }
        
        if (empty($actualizaciones)) {
            $this->warn("No hay variables para actualizar");
            return 0;
        }
        
        // Preguntar si desea actualizar
        if ($this->confirm('¿Desea actualizar los valores en la base de datos?', true)) {
            DB::beginTransaction();
            try {
                foreach ($actualizaciones as $item) {
                    $variavel = $item['variavel'];
                    $variavel->B = $item['B'];
                    $variavel->M = $item['M'];
                    $variavel->A = $item['A'];
                    $variavel->save();
                }
                
                DB::commit();
                $this->info("✅ Valores actualizados correctamente");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("❌ Error al actualizar: " . $e->getMessage());
                return 1;
            }
        } else {
            $this->info("Operación cancelada");
        }
        
        return 0;
    }
}

