<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variavel;
use App\Models\Pergunta;

class ActualizarRelacionesPorTextoMAX extends Command
{
    protected $signature = 'actualizar:relaciones-max';
    protected $description = 'Actualiza las relaciones pregunta-variable usando el texto del CSV MAX';

    public function handle()
    {
        $this->info('Actualizando relaciones pregunta-variable usando texto del CSV MAX...');

        // Buscar el CSV MAX
        $csvPath = base_path('EMULADOR - EMOTIVE ID II - perguntas_completas_99 MAX.csv');
        if (!file_exists($csvPath)) {
            $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE ID II - perguntas_completas_99 MAX.csv';
        }
        
        if (!file_exists($csvPath)) {
            $this->error("CSV MAX no encontrado. Buscado en:");
            $this->line("  - " . base_path('EMULADOR - EMOTIVE ID II - perguntas_completas_99 MAX.csv'));
            $this->line("  - /Users/novadesck/Downloads/EMULADOR - EMOTIVE ID II - perguntas_completas_99 MAX.csv");
            return 1;
        }

        $handle = fopen($csvPath, 'r');
        // Saltar hasta línea 12 (datos)
        for ($i = 0; $i < 12; $i++) {
            $header = fgetcsv($handle);
        }

        $mapeoCSV = [];
        $dimensionesCSV = [
            'ExEm' => [],
            'RePr' => [],
            'DeCi' => [],
            'FaPs' => [],
            'ExTr' => [],
            'AsMo' => [],
        ];

        $preguntasVistas = [];

        // Leer CSV MAX y crear mapeo texto -> dimensiones
        // Col 15: EXTR, Col 16: ASMO, Col 19: SO
        // Col 14: EXEM, Col 17: REPR, Col 18: DECI, Col 20: FAPS
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            $idQuest = isset($data[0]) ? trim($data[0]) : '';
            $textoPergunta = isset($data[2]) ? trim($data[2]) : '';
            
            if (empty($textoPergunta)) {
                continue;
            }
            
            // Solo procesar una vez por pregunta
            if (isset($preguntasVistas[$idQuest])) {
                continue;
            }
            $preguntasVistas[$idQuest] = true;
            
            $mapeoCSV[$textoPergunta] = [];
            
            // Ver a qué dimensiones pertenece según CSV MAX
            // IMPORTANTE: Usar las columnas correctas del CSV MAX
            // Col 14: Exaustão Emocional (EXEM)
            // Col 17: Realização Profissional (REPR) 
            // Col 18: Cinismo (DECI)
            // Col 20: Fatores Psicossociais (FAPS)
            // Col 15: Excesso de Trabalho (EXTR)
            // Col 16: Assédio Moral (ASMO)
            // Col 19: EE (Energia Emocional) - para verificar
            // Col 20: PR (Propósito e Relações) - para verificar
            // Col 21: SO (Sustentabilidade Ocupacional) - para verificar
            $exem = isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? (float)str_replace(',', '.', $data[14]) : 0;
            $repr = isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? (float)str_replace(',', '.', $data[17]) : 0;
            $deci = isset($data[18]) && $data[18] !== '' && $data[18] !== '0' ? (float)str_replace(',', '.', $data[18]) : 0;
            $faps = isset($data[20]) && $data[20] !== '' && $data[20] !== '0' ? (float)str_replace(',', '.', $data[20]) : 0;
            $extr = isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? (float)str_replace(',', '.', $data[15]) : 0;
            $asmo = isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? (float)str_replace(',', '.', $data[16]) : 0;
            
            // Asociar a dimensiones según CSV MAX (solo usar columnas de dimensiones, no de ejes)
            // Los ejes (EE, PR, SO) son la unión de dimensiones, no se usan directamente
            if ($exem > 0) {
                $dimensionesCSV['ExEm'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'ExEm';
            }
            if ($repr > 0) {
                $dimensionesCSV['RePr'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'RePr';
            }
            if ($deci > 0) {
                $dimensionesCSV['DeCi'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'DeCi';
            }
            if ($faps > 0) {
                $dimensionesCSV['FaPs'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'FaPs';
            }
            if ($extr > 0) {
                $dimensionesCSV['ExTr'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'ExTr';
            }
            if ($asmo > 0) {
                $dimensionesCSV['AsMo'][] = $textoPergunta;
                $mapeoCSV[$textoPergunta][] = 'AsMo';
            }
        }

        fclose($handle);

        $this->info('CSV MAX leído. Total preguntas en CSV: ' . count($mapeoCSV));
        $this->info('');

        // Mostrar resumen
        $this->info('Resumen de dimensiones en CSV MAX:');
        foreach ($dimensionesCSV as $tag => $textos) {
            $this->line("  {$tag}: " . count($textos) . " preguntas");
        }
        $this->info('');

        // Obtener variables
        $variaveis = Variavel::where('formulario_id', 1)->get()->keyBy('tag');
        
        // Obtener todas las preguntas de la BD
        $preguntasBD = Pergunta::where('formulario_id', 1)->get();

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

            // Para cada dimensión, buscar preguntas por texto
            foreach ($dimensionesCSV as $tag => $textosCSV) {
                if (!isset($variaveis[$tag])) {
                    $this->warn("Variable con tag '{$tag}' no encontrada.");
                    continue;
                }

                $variavelId = $variaveis[$tag]->id;
                $preguntasEncontradas = 0;
                
                foreach ($textosCSV as $textoCSV) {
                    // Buscar pregunta en BD por texto (comparación flexible)
                    $perguntaEncontrada = null;
                    
                    foreach ($preguntasBD as $pBD) {
                        $textoBD = trim($pBD->pergunta ?? '');
                        
                        // Comparación flexible
                        if (stripos($textoBD, $textoCSV) !== false || 
                            stripos($textoCSV, $textoBD) !== false ||
                            $textoBD === $textoCSV) {
                            $perguntaEncontrada = $pBD;
                            break;
                        }
                    }
                    
                    if ($perguntaEncontrada) {
                        // Evitar duplicados
                        $existe = DB::table('pergunta_variavel')
                            ->where('pergunta_id', $perguntaEncontrada->id)
                            ->where('variavel_id', $variavelId)
                            ->exists();
                        
                        if (!$existe) {
                            DB::table('pergunta_variavel')->insert([
                                'pergunta_id' => $perguntaEncontrada->id,
                                'variavel_id' => $variavelId,
                            ]);
                            $total++;
                            $preguntasEncontradas++;
                        }
                    } else {
                        $noEncontradas[] = [
                            'tag' => $tag,
                            'texto' => substr($textoCSV, 0, 60) . '...'
                        ];
                    }
                }

                $this->info("  {$tag}: {$variaveis[$tag]->nome} - {$preguntasEncontradas} preguntas encontradas de " . count($textosCSV) . " esperadas");
                
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
                foreach (array_slice($noEncontradas, 0, 10) as $no) {
                    $this->line("  {$no['tag']}: {$no['texto']}");
                }
                if (count($noEncontradas) > 10) {
                    $this->line("  ... y " . (count($noEncontradas) - 10) . " más");
                }
            }

            // Verificar SO después de la actualización
            $this->info("");
            $this->info("📊 Verificación de SO (EXTR + ASMO):");
            $exTr = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'ExTr')->first();
            $asMo = Variavel::with('perguntas')->where('formulario_id', 1)->where('tag', 'AsMo')->first();
            
            if ($exTr && $asMo) {
                $extrIds = $exTr->perguntas->pluck('id')->toArray();
                $asmoIds = $asMo->perguntas->pluck('id')->toArray();
                $unionIds = array_unique(array_merge($extrIds, $asmoIds));
                
                $this->info("  EXTR: " . count($extrIds) . " preguntas");
                $this->info("  ASMO: " . count($asmoIds) . " preguntas");
                $this->info("  SO (unión): " . count($unionIds) . " preguntas únicas");
                $this->info("  Máximo teórico: " . (count($unionIds) * 6) . " puntos");
                $this->info("  Máximo esperado según CSV: 186 puntos (31 preguntas × 6)");
                
                if (count($unionIds) == 31) {
                    $this->info("  ✅ SO tiene las 31 preguntas correctas");
                } else {
                    $this->warn("  ⚠️  SO tiene " . count($unionIds) . " preguntas, se esperaban 31");
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar relaciones: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}

