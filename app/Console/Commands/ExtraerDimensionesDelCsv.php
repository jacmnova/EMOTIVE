<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtraerDimensionesDelCsv extends Command
{
    protected $signature = 'emotive:extraer-dimensiones-csv';
    protected $description = 'Extrae las relaciones pregunta-dimensión directamente del CSV analizando las columnas de scores';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Extrayendo dimensiones del CSV...');
        $this->info('');
        
        $handle = fopen($csvPath, 'r');
        
        // Leer hasta la línea 11 (encabezados)
        for ($i = 0; $i < 11; $i++) {
            $header = fgetcsv($handle);
        }
        
        // Índices de columnas según el CSV
        // ID_Quest=0, User_Choice=9, Score=10, EXEM=12, REPR=13, DECI=14, FAPS=15, EXTR=16, ASMO=17
        $dimensiones = [
            'EXEM' => [],
            'REPR' => [],
            'DECI' => [],
            'FAPS' => [],
            'EXTR' => [],
            'ASMO' => [],
        ];
        
        $preguntasInvertidas = [];
        $lineaNum = 0;
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineaNum++;
            
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            // Extraer número de pregunta (#001 → 1)
            $numeroPregunta = (int)str_replace('#', '', $data[0]);
            
            // User_Choice y Score
            $userChoice = isset($data[9]) ? trim($data[9]) : '';
            $score = isset($data[10]) ? trim($data[10]) : '';
            
            // Valores en columnas de dimensiones (cuando User_Choice = 0)
            $exem = isset($data[12]) ? trim($data[12]) : '';
            $repr = isset($data[13]) ? trim($data[13]) : '';
            $deci = isset($data[14]) ? trim($data[14]) : '';
            $faps = isset($data[15]) ? trim($data[15]) : '';
            $extr = isset($data[16]) ? trim($data[16]) : '';
            $asmo = isset($data[17]) ? trim($data[17]) : '';
            
            // Si la pregunta tiene un valor NO CERO en la columna de la dimensión cuando User_Choice=0,
            // significa que pertenece a esa dimensión
            // Si User_Choice=0 y el valor en la columna es diferente de 0, la pregunta requiere inversión
            
            if ($userChoice === '0') {
                // Detectar inversión: si Score != User_Choice cuando User_Choice=0
                if ($score !== '' && $score !== '0' && $score !== $userChoice) {
                    $preguntasInvertidas[] = $numeroPregunta;
                }
                
                // Agregar a dimensiones según el valor en cada columna
                if ($exem !== '' && $exem !== '0') {
                    $dimensiones['EXEM'][] = $numeroPregunta;
                }
                if ($repr !== '' && $repr !== '0') {
                    $dimensiones['REPR'][] = $numeroPregunta;
                }
                if ($deci !== '' && $deci !== '0') {
                    $dimensiones['DECI'][] = $numeroPregunta;
                }
                if ($faps !== '' && $faps !== '0') {
                    $dimensiones['FAPS'][] = $numeroPregunta;
                }
                if ($extr !== '' && $extr !== '0') {
                    $dimensiones['EXTR'][] = $numeroPregunta;
                }
                if ($asmo !== '' && $asmo !== '0') {
                    $dimensiones['ASMO'][] = $numeroPregunta;
                }
            }
        }
        
        fclose($handle);
        
        $this->info('📝 Preguntas por dimensión (cuando User_Choice=0, valor en columna != 0):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            $this->line("      Números: " . implode(', ', $preguntas));
        }
        
        $this->info('');
        $this->info('🔄 Preguntas que requieren inversión (User_Choice=0 pero Score != 0):');
        sort($preguntasInvertidas);
        $this->info("   Total: " . count($preguntasInvertidas));
        $this->line("   Números: " . implode(', ', $preguntasInvertidas));
        
        // Ahora calcular los scores cuando todo es 0
        $this->info('');
        $this->info('🔢 Calculando scores cuando todo es 0:');
        $this->info('');
        
        $scoresEsperados = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        foreach ($dimensiones as $tag => $preguntas) {
            $total = 0;
            foreach ($preguntas as $num) {
                // Si está en preguntas invertidas, cuando User_Choice=0 → Score=6
                // Si no está invertida, cuando User_Choice=0 → Score=0
                if (in_array($num, $preguntasInvertidas)) {
                    $total += 6; // Inversión: 0 → 6
                } else {
                    $total += 0; // Normal: 0 → 0
                }
            }
            
            $esperado = $scoresEsperados[$tag] ?? 0;
            $estado = $total == $esperado ? '✅' : '❌';
            
            $this->line("   {$tag}: Calculado={$total}, Esperado={$esperado} {$estado}");
        }
        
        return 0;
    }
}

