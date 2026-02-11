<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AnalizarLogicaCalculoCSV extends Command
{
    protected $signature = 'emotive:analizar-logica-csv {csv_path}';
    protected $description = 'Analiza la lógica de cálculo del CSV para entender cómo se calculan EXEM, REPR, DECI, FAPS, EXTR, ASMO, EE, PR, SO';

    public function handle()
    {
        $csvPath = $this->argument('csv_path');
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado: {$csvPath}");
            return 1;
        }

        $this->info('📊 Analizando lógica de cálculo del CSV...');
        $this->info('');

        $handle = fopen($csvPath, 'r');
        
        // Leer hasta la línea 11 (encabezados)
        $header = null;
        for ($i = 0; $i < 11; $i++) {
            $header = fgetcsv($handle);
        }
        
        // Encontrar índices de columnas en el header
        $indices = [];
        if ($header) {
            foreach ($header as $idx => $col) {
                $col = trim($col);
                if ($col === 'User_Choice') $indices['user_choice'] = $idx;
                if ($col === 'Score') $indices['score'] = $idx;
                if ($col === 'EXEM') $indices['exem'] = $idx;
                if ($col === 'REPR') $indices['repr'] = $idx;
                if ($col === 'DECI') $indices['deci'] = $idx;
                if ($col === 'FAPS') $indices['faps'] = $idx;
                if ($col === 'EXTR') $indices['extr'] = $idx;
                if ($col === 'ASMO') $indices['asmo'] = $idx;
                if ($col === 'EE') $indices['ee'] = $idx;
                if ($col === 'PR') $indices['pr'] = $idx;
                if ($col === 'SO') $indices['so'] = $idx;
            }
        }
        
        $this->info('Índices encontrados: ' . json_encode($indices));
        $this->info('');

        // Estructura para almacenar información
        $dimensiones = [
            'EXEM' => [],
            'REPR' => [],
            'DECI' => [],
            'FAPS' => [],
            'EXTR' => [],
            'ASMO' => [],
        ];
        
        $ejes = [
            'EE' => [],
            'PR' => [],
            'SO' => [],
        ];
        
        $preguntasInvertidas = [];
        $preguntasNormales = [];
        
        $lineaNum = 11;
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineaNum++;
            
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            // Extraer número de pregunta (#001 → 1)
            $numeroPregunta = (int)str_replace('#', '', $data[0]);
            
            // Detectar si es invertida (escala comienza con 6)
            $escalaInicio = isset($data[2]) ? trim($data[2]) : '';
            $esInvertida = ($escalaInicio === '6');
            
            if ($esInvertida) {
                $preguntasInvertidas[] = $numeroPregunta;
            } else {
                $preguntasNormales[] = $numeroPregunta;
            }
            
            // Usar los índices encontrados en el header
            $userChoice = isset($indices['user_choice']) && isset($data[$indices['user_choice']]) ? trim($data[$indices['user_choice']]) : '';
            $score = isset($indices['score']) && isset($data[$indices['score']]) ? trim($data[$indices['score']]) : '';
            
            $exem = isset($indices['exem']) && isset($data[$indices['exem']]) ? trim($data[$indices['exem']]) : '0';
            $repr = isset($indices['repr']) && isset($data[$indices['repr']]) ? trim($data[$indices['repr']]) : '0';
            $deci = isset($indices['deci']) && isset($data[$indices['deci']]) ? trim($data[$indices['deci']]) : '0';
            $faps = isset($indices['faps']) && isset($data[$indices['faps']]) ? trim($data[$indices['faps']]) : '0';
            $extr = isset($indices['extr']) && isset($data[$indices['extr']]) ? trim($data[$indices['extr']]) : '0';
            $asmo = isset($indices['asmo']) && isset($data[$indices['asmo']]) ? trim($data[$indices['asmo']]) : '0';
            
            $ee = isset($indices['ee']) && isset($data[$indices['ee']]) ? trim($data[$indices['ee']]) : '0';
            $pr = isset($indices['pr']) && isset($data[$indices['pr']]) ? trim($data[$indices['pr']]) : '0';
            $so = isset($indices['so']) && isset($data[$indices['so']]) ? trim($data[$indices['so']]) : '0';
            
            // Convertir valores a números
            $exemVal = $exem !== '' && $exem !== '0' ? (float)str_replace(',', '.', $exem) : 0;
            $reprVal = $repr !== '' && $repr !== '0' ? (float)str_replace(',', '.', $repr) : 0;
            $deciVal = $deci !== '' && $deci !== '0' ? (float)str_replace(',', '.', $deci) : 0;
            $fapsVal = $faps !== '' && $faps !== '0' ? (float)str_replace(',', '.', $faps) : 0;
            $extrVal = $extr !== '' && $extr !== '0' ? (float)str_replace(',', '.', $extr) : 0;
            $asmoVal = $asmo !== '' && $asmo !== '0' ? (float)str_replace(',', '.', $asmo) : 0;
            
            $eeVal = $ee !== '' && $ee !== '0' ? (float)str_replace(',', '.', $ee) : 0;
            $prVal = $pr !== '' && $pr !== '0' ? (float)str_replace(',', '.', $pr) : 0;
            $soVal = $so !== '' && $so !== '0' ? (float)str_replace(',', '.', $so) : 0;
            
            // Debug: mostrar primeras 3 preguntas
            if ($numeroPregunta <= 3) {
                $this->line("DEBUG Pregunta #{$numeroPregunta}: User_Choice='{$userChoice}', Score='{$score}', EXEM='{$exem}', REPR='{$repr}', DECI='{$deci}', FAPS='{$faps}', EXTR='{$extr}', ASMO='{$asmo}', EE='{$ee}', PR='{$pr}', SO='{$so}'");
            }
            
            // Solo procesar cuando User_Choice=5 (que es el caso en este CSV)
            if ($userChoice === '5') {
                // Si tiene valor > 0 en la columna de dimensión, pertenece a esa dimensión
                if ($exemVal > 0) {
                    $dimensiones['EXEM'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $exemVal
                    ];
                }
                if ($reprVal > 0) {
                    $dimensiones['REPR'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $reprVal
                    ];
                }
                if ($deciVal > 0) {
                    $dimensiones['DECI'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $deciVal
                    ];
                }
                if ($fapsVal > 0) {
                    $dimensiones['FAPS'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $fapsVal
                    ];
                }
                if ($extrVal > 0) {
                    $dimensiones['EXTR'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $extrVal
                    ];
                }
                if ($asmoVal > 0) {
                    $dimensiones['ASMO'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $asmoVal
                    ];
                }
                
                // Si tiene valor > 0 en la columna de eje, pertenece a ese eje
                if ($eeVal > 0) {
                    $ejes['EE'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $eeVal
                    ];
                }
                if ($prVal > 0) {
                    $ejes['PR'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $prVal
                    ];
                }
                if ($soVal > 0) {
                    $ejes['SO'][] = [
                        'pregunta' => $numeroPregunta,
                        'invertida' => $esInvertida,
                        'user_choice' => $userChoice,
                        'score' => $score,
                        'valor' => $soVal
                    ];
                }
            }
            
            // También registrar si pertenece a la dimensión (independientemente del User_Choice)
            // para saber qué preguntas pertenecen a cada dimensión
            if ($exemVal > 0 || $reprVal > 0 || $deciVal > 0 || $fapsVal > 0 || $extrVal > 0 || $asmoVal > 0) {
                // Ya registrado arriba cuando User_Choice=5
            }
        }
        
        fclose($handle);
        
        // Mostrar resumen
        $this->info('📊 RESUMEN DE DIMENSIONES:');
        $this->info(str_repeat('=', 80));
        
        foreach ($dimensiones as $dim => $preguntas) {
            $this->info("");
            $this->info("{$dim}: " . count($preguntas) . " preguntas");
            
            // Calcular total cuando User_Choice=5
            $total = 0;
            foreach ($preguntas as $p) {
                if ($p['user_choice'] === '5') {
                    $total += $p['valor'];
                }
            }
            
            $this->info("  Total cuando User_Choice=5: {$total}");
            
            // Mostrar primeras 5 preguntas
            $this->info("  Primeras 5 preguntas:");
            foreach (array_slice($preguntas, 0, 5) as $p) {
                $tipo = $p['invertida'] ? 'INVERTIDA' : 'NORMAL';
                $this->line("    Pregunta #{$p['pregunta']} ({$tipo}): User_Choice={$p['user_choice']}, Score={$p['score']}, Valor={$p['valor']}");
            }
        }
        
        $this->info("");
        $this->info('📊 RESUMEN DE EJES:');
        $this->info(str_repeat('=', 80));
        
        foreach ($ejes as $eje => $preguntas) {
            $this->info("");
            $this->info("{$eje}: " . count($preguntas) . " preguntas");
            
            // Calcular total cuando User_Choice=5
            $total = 0;
            foreach ($preguntas as $p) {
                if ($p['user_choice'] === '5') {
                    $total += $p['valor'];
                }
            }
            
            $this->info("  Total cuando User_Choice=5: {$total}");
        }
        
        // Verificar la línea 9 del CSV (SCORE cuando todo es 5)
        $this->info("");
        $this->info('📊 VERIFICACIÓN DE LÍNEA 9 (SCORE cuando User_Choice=5):');
        $this->info(str_repeat('=', 80));
        
        $handle = fopen($csvPath, 'r');
        for ($i = 0; $i < 8; $i++) {
            fgetcsv($handle);
        }
        $linea9 = fgetcsv($handle);
        fclose($handle);
        
        if (isset($linea9[24])) {
            $this->info("EXEM esperado: " . ($linea9[24] ?? 'N/A'));
            $this->info("REPR esperado: " . ($linea9[25] ?? 'N/A'));
            $this->info("DECI esperado: " . ($linea9[26] ?? 'N/A'));
            $this->info("FAPS esperado: " . ($linea9[27] ?? 'N/A'));
            $this->info("EXTR esperado: " . ($linea9[28] ?? 'N/A'));
            $this->info("ASMO esperado: " . ($linea9[29] ?? 'N/A'));
            $this->info("EE esperado: " . ($linea9[30] ?? 'N/A'));
            $this->info("PR esperado: " . ($linea9[31] ?? 'N/A'));
            $this->info("SO esperado: " . ($linea9[32] ?? 'N/A'));
        }
        
        // Calcular sumas de dimensiones (todas ya tienen User_Choice=5)
        $this->info("");
        $this->info('📊 CÁLCULO DE SUMAS (cuando User_Choice=5):');
        $this->info(str_repeat('=', 80));
        
        $sumaEXEM = array_sum(array_column($dimensiones['EXEM'], 'valor'));
        $sumaREPR = array_sum(array_column($dimensiones['REPR'], 'valor'));
        $sumaDECI = array_sum(array_column($dimensiones['DECI'], 'valor'));
        $sumaFAPS = array_sum(array_column($dimensiones['FAPS'], 'valor'));
        $sumaEXTR = array_sum(array_column($dimensiones['EXTR'], 'valor'));
        $sumaASMO = array_sum(array_column($dimensiones['ASMO'], 'valor'));
        
        $this->info("Suma EXEM: {$sumaEXEM} (esperado: 26)");
        $this->info("Suma REPR: {$sumaREPR} (esperado: 26)");
        $this->info("Suma DECI: {$sumaDECI} (esperado: 29)");
        $this->info("Suma FAPS: {$sumaFAPS} (esperado: 10)");
        $this->info("Suma EXTR: {$sumaEXTR} (esperado: 16)");
        $this->info("Suma ASMO: {$sumaASMO} (esperado: 15)");
        
        $sumaEE = array_sum(array_column($ejes['EE'], 'valor'));
        $sumaPR = array_sum(array_column($ejes['PR'], 'valor'));
        $sumaSO = array_sum(array_column($ejes['SO'], 'valor'));
        
        $this->info("");
        $this->info("Suma EE: {$sumaEE} (esperado: 46)");
        $this->info("Suma PR: {$sumaPR} (esperado: 39)");
        $this->info("Suma SO: {$sumaSO} (esperado: 31)");
        
        // Verificar si EE = EXEM + REPR
        $this->info("");
        $this->info("Verificación:");
        $this->info("  EXEM + REPR = " . ($sumaEXEM + $sumaREPR) . " (EE esperado: 46)");
        $this->info("  DECI + FAPS = " . ($sumaDECI + $sumaFAPS) . " (PR esperado: 39)");
        $this->info("  EXTR + ASMO = " . ($sumaEXTR + $sumaASMO) . " (SO esperado: 31)");
        
        // Generar lista de preguntas por dimensión para actualizar relaciones
        $this->info("");
        $this->info('📋 LISTA DE PREGUNTAS POR DIMENSIÓN (para actualizar relaciones):');
        $this->info(str_repeat('=', 80));
        
        foreach ($dimensiones as $dim => $preguntas) {
            $ids = array_unique(array_column($preguntas, 'pregunta'));
            sort($ids);
            $this->info("{$dim}: [" . implode(', ', $ids) . "]");
        }
        
        $this->info("");
        $this->info('📋 LISTA DE PREGUNTAS POR EJE:');
        $this->info(str_repeat('=', 80));
        
        foreach ($ejes as $eje => $preguntas) {
            $ids = array_unique(array_column($preguntas, 'pregunta'));
            sort($ids);
            $this->info("{$eje}: [" . implode(', ', $ids) . "]");
        }
        
        $this->info("");
        $this->info("✅ Preguntas invertidas: [" . implode(', ', $preguntasInvertidas) . "]");
        $this->info("✅ Total preguntas invertidas: " . count($preguntasInvertidas));
        
        return 0;
    }
}

