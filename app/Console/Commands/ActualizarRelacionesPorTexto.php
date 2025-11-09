<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variavel;
use App\Models\Pergunta;

class ActualizarRelacionesPorTexto extends Command
{
    protected $signature = 'actualizar:relaciones-por-texto';
    protected $description = 'Actualiza las relaciones pregunta-variable usando el texto de las preguntas del CSV';

    public function handle()
    {
        $this->info('Actualizando relaciones pregunta-variable usando texto del CSV...');

        // Buscar el CSV en la raíz del proyecto
        $csvPath = base_path('EMULADOR - EMOTIVE ID II - perguntas_completas_99 MAX.csv');
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado en: {$csvPath}");
            $this->info("Buscando en la raíz del proyecto...");
            return 1;
        }

        $handle = fopen($csvPath, 'r');
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

        // Leer CSV y crear mapeo texto -> dimensiones
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            $idQuest = isset($data[1]) ? trim($data[1]) : '';
            $textoPergunta = isset($data[2]) ? trim($data[2]) : '';
            
            if (empty($textoPergunta)) {
                continue;
            }
            
            $mapeoCSV[$textoPergunta] = [];
            
            // Ver a qué dimensiones pertenece
            $exem = isset($data[12]) && $data[12] !== '' && $data[12] !== '0' ? (float)str_replace(',', '.', $data[12]) : 0;
            $repr = isset($data[13]) && $data[13] !== '' && $data[13] !== '0' ? (float)str_replace(',', '.', $data[13]) : 0;
            $deci = isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? (float)str_replace(',', '.', $data[14]) : 0;
            $faps = isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? (float)str_replace(',', '.', $data[15]) : 0;
            $extr = isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? (float)str_replace(',', '.', $data[16]) : 0;
            $asmo = isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? (float)str_replace(',', '.', $data[17]) : 0;
            
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

