<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variavel;
use App\Models\Pergunta;

class ActualizarRelacionesPorTextoALE extends Command
{
    protected $signature = 'actualizar:relaciones-ale';
    protected $description = 'Actualiza las relaciones pregunta-variable usando el texto del CSV ALE';

    public function handle()
    {
        $this->info('Actualizando relaciones pregunta-variable usando texto del CSV ALE...');

        // Buscar el CSV en la raíz del proyecto o en Downloads
        $csvPath = base_path('EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv');
        if (!file_exists($csvPath)) {
            $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE ALE - perguntas_completas_99 (1).csv';
        }
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado. Buscado en:");
            $this->line("  - " . base_path('EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv'));
            $this->line("  - /Users/novadesck/Downloads/EMULADOR - EMOTIVE ALE - perguntas_completas_99 (1).csv");
            return 1;
        }

        $handle = fopen($csvPath, 'r');
        for ($i = 0; $i < 11; $i++) {
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

        // Leer CSV y crear mapeo texto -> dimensiones
        // Col 14: EXEM, Col 15: REPR, Col 16: DECI, Col 17: FAPS, Col 18: EXTR, Col 19: ASMO
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            $idQuest = isset($data[0]) ? trim($data[0]) : '';
            $textoPergunta = isset($data[1]) ? trim($data[1]) : '';
            
            if (empty($textoPergunta)) {
                continue;
            }
            
            // Solo procesar una vez por pregunta
            if (isset($preguntasVistas[$idQuest])) {
                continue;
            }
            $preguntasVistas[$idQuest] = true;
            
            $mapeoCSV[$textoPergunta] = [];
            
            // Ver a qué dimensiones pertenece (Col 27-32)
            // Col 27: Exaustão Emocional (EXEM)
            // Col 28: Realização Profissional (REPR)
            // Col 29: Cinismo (DECI)
            // Col 30: Fatores Psicossociais (FAPS)
            // Col 31: Excesso de Trabalho (EXTR)
            // Col 32: Assédio Moral (ASMO)
            $exem = isset($data[27]) && $data[27] !== '' && $data[27] !== '0' ? (int)$data[27] : 0;
            $repr = isset($data[28]) && $data[28] !== '' && $data[28] !== '0' ? (int)$data[28] : 0;
            $deci = isset($data[29]) && $data[29] !== '' && $data[29] !== '0' ? (int)$data[29] : 0;
            $faps = isset($data[30]) && $data[30] !== '' && $data[30] !== '0' ? (int)$data[30] : 0;
            $extr = isset($data[31]) && $data[31] !== '' && $data[31] !== '0' ? (int)$data[31] : 0;
            $asmo = isset($data[32]) && $data[32] !== '' && $data[32] !== '0' ? (int)$data[32] : 0;
            
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

        $this->info('CSV leído. Total preguntas en CSV: ' . count($mapeoCSV));
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

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al actualizar relaciones: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

