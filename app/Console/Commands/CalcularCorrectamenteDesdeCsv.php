<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalcularCorrectamenteDesdeCsv extends Command
{
    protected $signature = 'emotive:calcular-correcto-csv';
    protected $description = 'Calcula correctamente desde el CSV entendiendo la relación User_Choice → Score → Dimensiones';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Analizando CSV para entender la lógica correcta...');
        $this->info('');
        
        $handle = fopen($csvPath, 'r');
        
        // Leer hasta la línea 11
        for ($i = 0; $i < 11; $i++) {
            $header = fgetcsv($handle);
        }
        
        // Analizar cada pregunta
        $dimensiones = [
            'EXEM' => [],
            'REPR' => [],
            'DECI' => [],
            'FAPS' => [],
            'EXTR' => [],
            'ASMO' => [],
        ];
        
        $preguntasInvertidas = [];
        $detallePreguntas = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            $numeroPregunta = (int)str_replace('#', '', $data[0]);
            
            // Detectar si es invertida
            $escalaInicio = isset($data[2]) ? trim($data[2]) : '';
            $esInvertida = ($escalaInicio === '6');
            
            if ($esInvertida) {
                $preguntasInvertidas[] = $numeroPregunta;
            }
            
            // Leer User_Choice y Score del CSV (cuando User_Choice=5)
            $userChoice = isset($data[9]) ? trim($data[9]) : '';
            $score = isset($data[10]) ? trim($data[10]) : '';
            
            // Leer valores de dimensiones cuando User_Choice=5
            $valoresCuando5 = [
                'EXEM' => isset($data[12]) && $data[12] !== '' && $data[12] !== '0' ? (float)str_replace(',', '.', $data[12]) : 0,
                'REPR' => isset($data[13]) && $data[13] !== '' && $data[13] !== '0' ? (float)str_replace(',', '.', $data[13]) : 0,
                'DECI' => isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? (float)str_replace(',', '.', $data[14]) : 0,
                'FAPS' => isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? (float)str_replace(',', '.', $data[15]) : 0,
                'EXTR' => isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? (float)str_replace(',', '.', $data[16]) : 0,
                'ASMO' => isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? (float)str_replace(',', '.', $data[17]) : 0,
            ];
            
            // Si tiene valor > 0 cuando User_Choice=5, pertenece a esa dimensión
            foreach ($valoresCuando5 as $dim => $valor) {
                if ($valor > 0) {
                    if (!in_array($numeroPregunta, $dimensiones[$dim])) {
                        $dimensiones[$dim][] = $numeroPregunta;
                    }
                }
            }
            
            $detallePreguntas[$numeroPregunta] = [
                'invertida' => $esInvertida,
                'userChoice' => $userChoice,
                'score' => $score,
                'valoresCuando5' => $valoresCuando5
            ];
        }
        
        fclose($handle);
        
        $this->info('📝 Preguntas por dimensión (cuando tienen valor > 0 cuando User_Choice=5):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
        }
        
        $this->info('');
        $this->info('🔄 Preguntas invertidas: ' . count($preguntasInvertidas));
        
        // Ahora calcular cuando User_Choice=0
        // Para preguntas normales: User_Choice=0 → Score=0 → aporta 0 a todas las dimensiones
        // Para preguntas invertidas: User_Choice=0 → Score=6
        // Pero el Score se distribuye entre las dimensiones a las que pertenece
        
        $this->info('');
        $this->info('🔢 Calculando scores cuando User_Choice=0:');
        $this->info('');
        $this->info('   Lógica:');
        $this->info('   - Pregunta NORMAL: User_Choice=0 → Score=0 → aporta 0 a todas las dimensiones');
        $this->info('   - Pregunta INVERTIDA: User_Choice=0 → Score=6');
        $this->info('     El Score=6 se distribuye entre las dimensiones según la proporción');
        $this->info('     que tenía cuando User_Choice=5');
        $this->info('');
        
        $scoresCuandoCero = [
            'EXEM' => 0,
            'REPR' => 0,
            'DECI' => 0,
            'FAPS' => 0,
            'EXTR' => 0,
            'ASMO' => 0,
        ];
        
        foreach ($preguntasInvertidas as $num) {
            $detalle = $detallePreguntas[$num];
            $valoresCuando5 = $detalle['valoresCuando5'];
            
            // Calcular total cuando User_Choice=5
            $totalCuando5 = array_sum($valoresCuando5);
            
            if ($totalCuando5 > 0) {
                // Cuando User_Choice=0, Score=6
                // Distribuir este 6 según la proporción que tenía cuando User_Choice=5
                foreach ($valoresCuando5 as $dim => $valorCuando5) {
                    if ($valorCuando5 > 0) {
                        // Proporción: valorCuando5 / totalCuando5
                        // Aporta: 6 * (valorCuando5 / totalCuando5)
                        $proporcion = $valorCuando5 / $totalCuando5;
                        $aporteCuandoCero = 6 * $proporcion;
                        $scoresCuandoCero[$dim] += $aporteCuandoCero;
                    }
                }
            } else {
                // Si no tenía valores cuando User_Choice=5, cuando User_Choice=0 aporta 6
                // Pero necesito saber a qué dimensión(es) pertenece
                // Por ahora, si está en la lista de dimensiones, aporta 6
                foreach ($dimensiones as $dim => $preguntas) {
                    if (in_array($num, $preguntas)) {
                        $scoresCuandoCero[$dim] += 6;
                    }
                }
            }
        }
        
        $scoresEsperados = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        $this->info('📋 RESULTADOS:');
        $this->info('');
        $this->table(
            ['Dimensión', 'Calculado', 'Esperado', 'Diferencia', 'Estado'],
            array_map(function($tag, $calculado, $esperado) {
                $diferencia = round($calculado - $esperado, 2);
                $estado = abs($diferencia) < 0.5 ? '✅' : '❌';
                return [$tag, round($calculado, 2), $esperado, $diferencia, $estado];
            }, 
            array_keys($scoresCuandoCero),
            array_values($scoresCuandoCero),
            array_values($scoresEsperados))
        );
        
        // Si no coincide, probar otra lógica: tal vez el Score se usa directamente
        // sin distribución proporcional
        
        return 0;
    }
}

