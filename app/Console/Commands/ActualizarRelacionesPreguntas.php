<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variavel;

class ActualizarRelacionesPreguntas extends Command
{
    protected $signature = 'actualizar:relaciones-preguntas';
    protected $description = 'Actualiza las relaciones pregunta-variable según el CSV';

    public function handle()
    {
        $this->info('Actualizando relaciones pregunta-variable según el CSV...');

        // Mapeo de tags a IDs de variables
        $variaveis = Variavel::where('formulario_id', 1)->get()->keyBy('tag');
        
        // Mapeo según el CSV
        $dimensiones_csv = [
            'ExEm' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61],
            'RePr' => [28, 29, 30, 31, 32, 33, 34, 35, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            'DeCi' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56],
            'FaPs' => [78, 79, 80, 81, 82, 83, 84, 85, 86, 87],
            'ExTr' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'AsMo' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        ];

        DB::beginTransaction();
        try {
            // Eliminar todas las relaciones existentes para el formulario 1
            $variavelIds = $variaveis->pluck('id')->toArray();
            DB::table('pergunta_variavel')
                ->whereIn('variavel_id', $variavelIds)
                ->delete();

            $this->info('Relaciones antiguas eliminadas.');

            // Insertar nuevas relaciones
            $total = 0;
            foreach ($dimensiones_csv as $tag => $preguntas) {
                if (!isset($variaveis[$tag])) {
                    $this->warn("Variable con tag '{$tag}' no encontrada.");
                    continue;
                }

                $variavelId = $variaveis[$tag]->id;
                
                foreach ($preguntas as $perguntaId) {
                    // IMPORTANTE: Los números en $dimensiones_csv son IDs de la base de datos, no numero_da_pergunta
                    // Buscar la pregunta directamente por ID
                    $pergunta = \App\Models\Pergunta::where('formulario_id', 1)
                        ->where('id', $perguntaId)
                        ->first();
                    
                    if (!$pergunta) {
                        $this->error("Pregunta con ID {$perguntaId} no encontrada para {$tag}");
                        continue;
                    }
                    
                    // Evitar duplicados
                    $existe = DB::table('pergunta_variavel')
                        ->where('pergunta_id', $pergunta->id)
                        ->where('variavel_id', $variavelId)
                        ->exists();
                    
                    if (!$existe) {
                        DB::table('pergunta_variavel')->insert([
                            'pergunta_id' => $pergunta->id,
                            'variavel_id' => $variavelId,
                        ]);
                        $total++;
                    }
                }

                $this->info("  {$tag}: {$variaveis[$tag]->nome} - " . count($preguntas) . " preguntas");
            }

            DB::commit();
            $this->info("\n✅ Relaciones actualizadas correctamente. Total: {$total} relaciones.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar relaciones: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

