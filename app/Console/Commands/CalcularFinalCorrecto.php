<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalcularFinalCorrecto extends Command
{
    protected $signature = 'emotive:calcular-final-correcto';
    protected $description = 'Calcula correctamente interpretando las columnas como pertenencia (1=sí, 0=no)';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Analizando CSV con interpretación: columnas = pertenencia (1=sí, 0=no)');
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
            
            // Leer columnas de dimensiones (interpretar como pertenencia: 1=sí, 0=no)
            $pertenece = [
                'EXEM' => isset($data[12]) && $data[12] !== '' && $data[12] !== '0' ? true : false,
                'REPR' => isset($data[13]) && $data[13] !== '' && $data[13] !== '0' ? true : false,
                'DECI' => isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? true : false,
                'FAPS' => isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? true : false,
                'EXTR' => isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? true : false,
                'ASMO' => isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? true : false,
            ];
            
            // Agregar a dimensiones según pertenencia
            foreach ($pertenece as $dim => $perteneceADim) {
                if ($perteneceADim) {
                    if (!in_array($numeroPregunta, $dimensiones[$dim])) {
                        $dimensiones[$dim][] = $numeroPregunta;
                    }
                }
            }
        }
        
        fclose($handle);
        
        $this->info('📝 Preguntas por dimensión (según pertenencia):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            if (count($preguntas) <= 30) {
                $this->line("      " . implode(', ', $preguntas));
            }
        }
        
        $this->info('');
        $this->info('🔄 Preguntas invertidas: ' . count($preguntasInvertidas));
        $this->line("   " . implode(', ', $preguntasInvertidas));
        
        // Calcular cuando User_Choice=0
        // Preguntas normales: User_Choice=0 → Score=0
        // Preguntas invertidas: User_Choice=0 → Score=6
        // El Score se suma directamente a las dimensiones a las que pertenece
        
        $this->info('');
        $this->info('🔢 Calculando scores cuando User_Choice=0:');
        $this->info('');
        $this->info('   Lógica:');
        $this->info('   - Pregunta NORMAL: User_Choice=0 → Score=0 → aporta 0');
        $this->info('   - Pregunta INVERTIDA: User_Choice=0 → Score=6');
        $this->info('     Si pertenece a múltiples dimensiones, el Score=6 se suma a TODAS');
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
            // Esta pregunta invertida aporta 6 cuando User_Choice=0
            // Se suma a TODAS las dimensiones a las que pertenece
            foreach ($dimensiones as $dim => $preguntas) {
                if (in_array($num, $preguntas)) {
                    $scoresCuandoCero[$dim] += 6;
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
        
        $this->info('📋 RESULTADOS (Score=6 se suma a todas las dimensiones a las que pertenece):');
        $this->info('');
        $this->table(
            ['Dimensión', 'Calculado', 'Esperado', 'Diferencia', 'Estado'],
            array_map(function($tag, $calculado, $esperado) {
                $diferencia = $calculado - $esperado;
                $estado = abs($diferencia) < 0.01 ? '✅' : '❌';
                return [$tag, $calculado, $esperado, $diferencia, $estado];
            }, 
            array_keys($scoresCuandoCero),
            array_values($scoresCuandoCero),
            array_values($scoresEsperados))
        );
        
        // Si no coincide, tal vez el Score NO se suma a todas, sino que se distribuye
        // O tal vez solo algunas preguntas invertidas pertenecen a cada dimensión
        
        return 0;
    }
}

