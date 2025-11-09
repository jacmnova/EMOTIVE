<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AnalizarCsvCompleto extends Command
{
    protected $signature = 'emotive:analizar-csv';
    protected $description = 'Analiza el CSV completo para extraer las relaciones exactas pregunta-dimensión';

    public function handle()
    {
        $csvPath = storage_path('app/EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv');
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado en: {$csvPath}");
            $this->info("Por favor, coloca el CSV en: storage/app/");
            return 1;
        }
        
        $this->info('📊 Analizando CSV completo...');
        $this->info('');
        
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->error("No se pudo abrir el CSV");
            return 1;
        }
        
        // Leer encabezados
        $header = fgetcsv($handle);
        
        // Leer línea de rangos (línea 2)
        $rangos = fgetcsv($handle);
        
        // Leer línea de scores esperados (línea 9)
        for ($i = 0; $i < 7; $i++) {
            $linea = fgetcsv($handle);
        }
        $scoresLine = fgetcsv($handle); // Esta es la línea 9 con los scores
        
        $this->info('📋 Scores esperados cuando todo es 0:');
        $this->info("   EXEM: " . ($scoresLine[13] ?? 'N/A'));
        $this->info("   REPR: " . ($scoresLine[14] ?? 'N/A'));
        $this->info("   DECI: " . ($scoresLine[15] ?? 'N/A'));
        $this->info("   FAPS: " . ($scoresLine[16] ?? 'N/A'));
        $this->info("   EXTR: " . ($scoresLine[17] ?? 'N/A'));
        $this->info("   ASMO: " . ($scoresLine[18] ?? 'N/A'));
        $this->info('');
        
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
        $lineaNum = 0;
        
        while (($data = fgetcsv($handle)) !== false) {
            $lineaNum++;
            
            // Saltar líneas vacías o de encabezados
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            // Extraer ID de pregunta (ej: #001 → 1)
            $idPregunta = str_replace('#', '', $data[0]);
            $idPregunta = ltrim($idPregunta, '0');
            if (empty($idPregunta)) $idPregunta = '0';
            $idPregunta = (int)$idPregunta;
            
            // User_Choice (columna 9, índice 9)
            $userChoice = isset($data[9]) ? trim($data[9]) : '';
            
            // Score (columna 10, índice 10)
            $score = isset($data[10]) ? trim($data[10]) : '';
            
            // Columnas de dimensiones (índices 12-17)
            $exem = isset($data[12]) ? trim($data[12]) : '';
            $repr = isset($data[13]) ? trim($data[13]) : '';
            $deci = isset($data[14]) ? trim($data[14]) : '';
            $faps = isset($data[15]) ? trim($data[15]) : '';
            $extr = isset($data[16]) ? trim($data[16]) : '';
            $asmo = isset($data[17]) ? trim($data[17]) : '';
            
            // Si tiene valor en la columna de la dimensión, pertenece a esa dimensión
            if ($exem !== '' && $exem !== '0') {
                $dimensiones['EXEM'][] = $idPregunta;
            }
            if ($repr !== '' && $repr !== '0') {
                $dimensiones['REPR'][] = $idPregunta;
            }
            if ($deci !== '' && $deci !== '0') {
                $dimensiones['DECI'][] = $idPregunta;
            }
            if ($faps !== '' && $faps !== '0') {
                $dimensiones['FAPS'][] = $idPregunta;
            }
            if ($extr !== '' && $extr !== '0') {
                $dimensiones['EXTR'][] = $idPregunta;
            }
            if ($asmo !== '' && $asmo !== '0') {
                $dimensiones['ASMO'][] = $idPregunta;
            }
            
            // Detectar inversión: si User_Choice != Score y ambos tienen valores
            if ($userChoice !== '' && $score !== '' && $userChoice !== $score) {
                $preguntasInvertidas[] = $idPregunta;
            }
        }
        
        fclose($handle);
        
        $this->info('📝 Preguntas por dimensión (según CSV):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            $this->line("      IDs: " . implode(', ', $preguntas));
        }
        
        $this->info('');
        $this->info('🔄 Preguntas que requieren inversión (según CSV):');
        sort($preguntasInvertidas);
        $this->info("   Total: " . count($preguntasInvertidas));
        $this->line("   IDs: " . implode(', ', $preguntasInvertidas));
        
        // Comparar con lo que tenemos en el código
        $this->info('');
        $this->info('🔍 Comparación con el código actual:');
        
        $dimensionesCodigo = [
            'EXEM' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61],
            'REPR' => [28, 29, 30, 31, 32, 33, 34, 35, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            'DECI' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56],
            'FAPS' => [78, 79, 80, 81, 82, 83, 84, 85, 86, 87],
            'EXTR' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'ASMO' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        ];
        
        $perguntasInvertidasCodigo = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        foreach ($dimensiones as $tag => $preguntasCsv) {
            $preguntasCodigo = $dimensionesCodigo[$tag] ?? [];
            sort($preguntasCsv);
            sort($preguntasCodigo);
            
            $diferencias = array_diff($preguntasCsv, $preguntasCodigo);
            $faltantes = array_diff($preguntasCodigo, $preguntasCsv);
            
            if (empty($diferencias) && empty($faltantes)) {
                $this->info("   {$tag}: ✅ Coincide");
            } else {
                $this->error("   {$tag}: ❌ DIFERENCIAS");
                if (!empty($diferencias)) {
                    $this->warn("      En CSV pero no en código: " . implode(', ', $diferencias));
                }
                if (!empty($faltantes)) {
                    $this->warn("      En código pero no en CSV: " . implode(', ', $faltantes));
                }
            }
        }
        
        return 0;
    }
}

