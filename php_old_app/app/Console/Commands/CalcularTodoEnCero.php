<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalcularTodoEnCero extends Command
{
    protected $signature = 'emotive:calcular-todo-cero';
    protected $description = 'Calcula los scores cuando todas las respuestas son 0, comparando con el Excel';

    public function handle()
    {
        $this->info('📊 Calculando scores cuando TODAS las respuestas son 0');
        $this->info('==================================================\n');
        
        // Según el Excel línea 9, cuando todo es 0, los scores esperados son:
        $scoresEsperados = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        // Preguntas por dimensión según el CSV (IDs de base de datos)
        $dimensiones = [
            'EXEM' => [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61],
            'REPR' => [28, 29, 30, 31, 32, 33, 34, 35, 56, 57, 58, 59, 60, 61, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            'DECI' => [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 48, 49, 50, 51, 52, 53, 54, 55, 56],
            'FAPS' => [78, 79, 80, 81, 82, 83, 84, 85, 86, 87],
            'EXTR' => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77],
            'ASMO' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
        ];
        
        // Preguntas que requieren inversión
        $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        $this->info('🔢 Cálculo cuando User_Choice = 0 para todas las preguntas:');
        $this->info('');
        $this->info('   Preguntas NORMALES (sin inversión): 0 → 0');
        $this->info('   Preguntas INVERTIDAS: 0 → 6 (inversión)');
        $this->info('');
        
        $resultados = [];
        
        foreach ($dimensiones as $tag => $preguntas) {
            $total = 0;
            $preguntasInvertidas = 0;
            $preguntasNormales = 0;
            
            foreach ($preguntas as $perguntaId) {
                $necesitaInversion = in_array($perguntaId, $perguntasComInversao, true);
                
                if ($necesitaInversion) {
                    // Si es 0 y necesita inversión: 0 → 6
                    $valorUsado = 6;
                    $preguntasInvertidas++;
                } else {
                    // Si es 0 y NO necesita inversión: 0 → 0
                    $valorUsado = 0;
                    $preguntasNormales++;
                }
                
                $total += $valorUsado;
            }
            
            $resultados[$tag] = [
                'calculado' => $total,
                'esperado' => $scoresEsperados[$tag] ?? 0,
                'preguntas_total' => count($preguntas),
                'preguntas_invertidas' => $preguntasInvertidas,
                'preguntas_normales' => $preguntasNormales
            ];
        }
        
        // Mostrar resultados
        $this->info('📋 RESULTADOS:');
        $this->info('');
        $this->table(
            ['Dimensión', 'Calculado', 'Esperado (Excel)', 'Diferencia', 'Estado'],
            array_map(function($tag, $datos) {
                $diferencia = $datos['calculado'] - $datos['esperado'];
                $estado = $diferencia == 0 ? '✅' : '❌';
                return [
                    $tag,
                    $datos['calculado'],
                    $datos['esperado'],
                    $diferencia,
                    $estado
                ];
            }, array_keys($resultados), $resultados)
        );
        
        $this->info('');
        $this->info('🔍 Detalle por dimensión:');
        $this->info('');
        
        foreach ($resultados as $tag => $datos) {
            $this->line("   {$tag}:");
            $this->line("      Total preguntas: {$datos['preguntas_total']}");
            $this->line("      Preguntas invertidas: {$datos['preguntas_invertidas']} (0→6 cada una)");
            $this->line("      Preguntas normales: {$datos['preguntas_normales']} (0→0 cada una)");
            $this->line("      Cálculo: ({$datos['preguntas_invertidas']} × 6) + ({$datos['preguntas_normales']} × 0) = {$datos['calculado']}");
            $this->line("      Esperado: {$datos['esperado']}");
            
            if ($datos['calculado'] == $datos['esperado']) {
                $this->info("      ✅ COINCIDE");
            } else {
                $this->error("      ❌ DIFERENCIA: " . ($datos['calculado'] - $datos['esperado']));
            }
            $this->info('');
        }
        
        // Verificar si hay problemas
        $problemas = array_filter($resultados, function($d) {
            return $d['calculado'] != $d['esperado'];
        });
        
        if (empty($problemas)) {
            $this->info('✅ Todos los cálculos coinciden con el Excel!');
        } else {
            $this->error('❌ Hay ' . count($problemas) . ' dimensión(es) con diferencias');
            $this->info('');
            $this->info('🔧 Posibles causas:');
            $this->info('   1. Las preguntas asociadas a cada dimensión no coinciden con el CSV');
            $this->info('   2. Las preguntas que requieren inversión no están correctas');
            $this->info('   3. Hay preguntas duplicadas o faltantes en las relaciones');
        }
        
        return 0;
    }
}

