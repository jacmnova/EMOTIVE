<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalcularDesdeCsvDirecto extends Command
{
    protected $signature = 'emotive:calcular-desde-csv';
    protected $description = 'Calcula los scores sumando directamente las columnas EXEM, REPR, etc. del CSV';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE ALE - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Calculando scores sumando columnas del CSV...');
        $this->info('');
        
        $handle = fopen($csvPath, 'r');
        
        // Leer hasta la línea 11 (encabezados)
        for ($i = 0; $i < 11; $i++) {
            $header = fgetcsv($handle);
        }
        
        // Acumuladores para cada dimensión
        $totales = [
            'EXEM' => 0,
            'REPR' => 0,
            'DECI' => 0,
            'FAPS' => 0,
            'EXTR' => 0,
            'ASMO' => 0,
        ];
        
        $preguntasPorDimension = [
            'EXEM' => [],
            'REPR' => [],
            'DECI' => [],
            'FAPS' => [],
            'EXTR' => [],
            'ASMO' => [],
        ];
        
        $preguntasInvertidas = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            $numeroPregunta = (int)str_replace('#', '', $data[0]);
            $userChoice = isset($data[9]) ? trim($data[9]) : '';
            $score = isset($data[10]) ? trim($data[10]) : '';
            
            // Leer valores de columnas de dimensiones
            $exem = isset($data[12]) && $data[12] !== '' ? (float)str_replace(',', '.', $data[12]) : 0;
            $repr = isset($data[13]) && $data[13] !== '' ? (float)str_replace(',', '.', $data[13]) : 0;
            $deci = isset($data[14]) && $data[14] !== '' ? (float)str_replace(',', '.', $data[14]) : 0;
            $faps = isset($data[15]) && $data[15] !== '' ? (float)str_replace(',', '.', $data[15]) : 0;
            $extr = isset($data[16]) && $data[16] !== '' ? (float)str_replace(',', '.', $data[16]) : 0;
            $asmo = isset($data[17]) && $data[17] !== '' ? (float)str_replace(',', '.', $data[17]) : 0;
            
            // Cuando User_Choice = 0, sumar los valores de cada columna
            if ($userChoice === '0') {
                $totales['EXEM'] += $exem;
                $totales['REPR'] += $repr;
                $totales['DECI'] += $deci;
                $totales['FAPS'] += $faps;
                $totales['EXTR'] += $extr;
                $totales['ASMO'] += $asmo;
                
                // Registrar qué preguntas contribuyen a cada dimensión
                if ($exem > 0) $preguntasPorDimension['EXEM'][] = $numeroPregunta;
                if ($repr > 0) $preguntasPorDimension['REPR'][] = $numeroPregunta;
                if ($deci > 0) $preguntasPorDimension['DECI'][] = $numeroPregunta;
                if ($faps > 0) $preguntasPorDimension['FAPS'][] = $numeroPregunta;
                if ($extr > 0) $preguntasPorDimension['EXTR'][] = $numeroPregunta;
                if ($asmo > 0) $preguntasPorDimension['ASMO'][] = $numeroPregunta;
            }
            
            // Detectar inversión: cuando User_Choice != Score
            if ($userChoice !== '' && $score !== '' && $userChoice !== $score) {
                $preguntasInvertidas[] = $numeroPregunta;
            }
        }
        
        fclose($handle);
        
        $scoresEsperados = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        $this->info('📋 RESULTADOS (sumando columnas cuando User_Choice=0):');
        $this->info('');
        $this->table(
            ['Dimensión', 'Calculado (CSV)', 'Esperado (Línea 9)', 'Diferencia', 'Estado'],
            array_map(function($tag, $calculado, $esperado) {
                $diferencia = $calculado - $esperado;
                $estado = abs($diferencia) < 0.01 ? '✅' : '❌';
                return [
                    $tag,
                    number_format($calculado, 2),
                    $esperado,
                    number_format($diferencia, 2),
                    $estado
                ];
            }, 
            array_keys($totales), 
            array_values($totales),
            array_values($scoresEsperados))
        );
        
        $this->info('');
        $this->info('📝 Preguntas que contribuyen a cada dimensión (cuando User_Choice=0):');
        $this->info('');
        
        foreach ($preguntasPorDimension as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            $this->line("      " . implode(', ', $preguntas));
        }
        
        $this->info('');
        $this->info('🔄 Preguntas que requieren inversión:');
        sort($preguntasInvertidas);
        $this->info("   Total: " . count($preguntasInvertidas));
        $this->line("   Números: " . implode(', ', array_slice($preguntasInvertidas, 0, 30)) . (count($preguntasInvertidas) > 30 ? '...' : ''));
        
        return 0;
    }
}

