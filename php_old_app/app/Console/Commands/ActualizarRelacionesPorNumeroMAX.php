<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variavel;
use App\Models\Pergunta;

class ActualizarRelacionesPorNumeroMAX extends Command
{
    protected $signature = 'actualizar:relaciones-numero-max';
    protected $description = 'Actualiza las relaciones pregunta-variable usando numero_da_pergunta del CSV MAX';

    public function handle()
    {
        $this->info('Actualizando relaciones pregunta-variable usando numero_da_pergunta del CSV MAX...');

        // Mapeo según CSV MAX - usando numero_da_pergunta (columna ID_Quest)
        // Estos son los números que aparecen en la columna ID_Quest del CSV MAX
        $dimensionesCSV = [
            'ExEm' => [28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            'RePr' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'DeCi' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            'FaPs' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87],
            'ExTr' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'AsMo' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        ];

        // Obtener variables
        $variaveis = Variavel::where('formulario_id', 1)->get()->keyBy('tag');

        DB::beginTransaction();
        try {
            // Eliminar todas las relaciones existentes
            $variavelIds = $variaveis->pluck('id')->toArray();
            DB::table('pergunta_variavel')
                ->whereIn('variavel_id', $variavelIds)
                ->delete();

            $this->info('Relaciones antiguas eliminadas.');
            $this->info('');

            $total = 0;
            $noEncontradas = [];

            // Para cada dimensión, buscar preguntas por numero_da_pergunta
            foreach ($dimensionesCSV as $tag => $numeros) {
                if (!isset($variaveis[$tag])) {
                    $this->warn("Variable con tag '{$tag}' no encontrada.");
                    continue;
                }

                $variavelId = $variaveis[$tag]->id;
                $preguntasEncontradas = 0;
                
                foreach ($numeros as $numero) {
                    // Buscar pregunta por numero_da_pergunta
                    $perguntas = Pergunta::where('formulario_id', 1)
                        ->where('numero_da_pergunta', $numero)
                        ->get();
                    
                    if ($perguntas->isEmpty()) {
                        $noEncontradas[] = [
                            'tag' => $tag,
                            'numero' => $numero
                        ];
                        continue;
                    }
                    
                    // Puede haber múltiples preguntas con el mismo numero_da_pergunta
                    // Asociar todas
                    foreach ($perguntas as $pergunta) {
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
                            $preguntasEncontradas++;
                        }
                    }
                }

                $this->info("  {$tag}: {$variaveis[$tag]->nome} - {$preguntasEncontradas} preguntas encontradas de " . count($numeros) . " esperadas");
                
                // Actualizar rangos automáticamente
                $variavel = $variaveis[$tag];
                $variavel->load('perguntas', 'formulario');
                \App\Traits\CalculaRangosVariavel::actualizarRangosAutomaticamente($variavel);
            }

            DB::commit();
            
            $this->info("");
            $this->info("✅ Relaciones actualizadas correctamente. Total: {$total} relaciones.");
            $this->info("✅ Rangos B, M, A actualizados automáticamente para todas las variables.");
            
            if (!empty($noEncontradas)) {
                $this->warn("");
                $this->warn("⚠️  Preguntas no encontradas en BD (" . count($noEncontradas) . "):");
                foreach (array_slice($noEncontradas, 0, 20) as $no) {
                    $this->line("  {$no['tag']}: numero_da_pergunta {$no['numero']}");
                }
                if (count($noEncontradas) > 20) {
                    $this->line("  ... y " . (count($noEncontradas) - 20) . " más");
                }
            }

            // Verificar ejes después de la actualización
            $this->info("");
            $this->info("📊 Verificación de ejes:");
            
            $exEm = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'ExEm')->first();
            $rePr = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'RePr')->first();
            $deCi = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'DeCi')->first();
            $faPs = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'FaPs')->first();
            $exTr = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'ExTr')->first();
            $asMo = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'AsMo')->first();

            $eeIds = array_unique(array_merge(
                $exEm ? $exEm->perguntas->pluck('id')->toArray() : [],
                $rePr ? $rePr->perguntas->pluck('id')->toArray() : []
            ));
            
            $prIds = array_unique(array_merge(
                $deCi ? $deCi->perguntas->pluck('id')->toArray() : [],
                $faPs ? $faPs->perguntas->pluck('id')->toArray() : []
            ));
            
            $soIds = array_unique(array_merge(
                $exTr ? $exTr->perguntas->pluck('id')->toArray() : [],
                $asMo ? $asMo->perguntas->pluck('id')->toArray() : []
            ));

            $this->line("EE (EXEM ∪ REPR): " . count($eeIds) . " preguntas únicas (esperado: 46)");
            $this->line("PR (DECI ∪ FAPS): " . count($prIds) . " preguntas únicas (esperado: 39)");
            $this->line("SO (EXTR ∪ ASMO): " . count($soIds) . " preguntas únicas (esperado: 31)");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar relaciones: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}

