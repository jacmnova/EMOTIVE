<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AnalizarCsvCompletoCorrecto extends Command
{
    protected $signature = 'emotive:analizar-csv-correcto';
    protected $description = 'Analiza el CSV completo para extraer las relaciones correctas pregunta-dimensión';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Analizando CSV completo...');
        $this->info('');
        
        $handle = fopen($csvPath, 'r');
        
        // Leer hasta la línea 11 (encabezados)
        for ($i = 0; $i < 11; $i++) {
            $header = fgetcsv($handle);
        }
        
        // Índices de columnas: ID_Quest=0, escala=2-8, User_Choice=9, Score=10, EXEM=12, REPR=13, DECI=14, FAPS=15, EXTR=16, ASMO=17
        $dimensiones = [
            'EXEM' => [],
            'REPR' => [],
            'DECI' => [],
            'FAPS' => [],
            'EXTR' => [],
            'ASMO' => [],
        ];
        
        $preguntasInvertidas = [];
        $preguntasNormales = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0]) || strpos($data[0], '#') !== 0) {
                continue;
            }
            
            // Extraer número de pregunta (#001 → 1)
            $numeroPregunta = (int)str_replace('#', '', $data[0]);
            
            // Detectar si es pregunta invertida: escala comienza con 6 (columna 2)
            $escalaInicio = isset($data[2]) ? trim($data[2]) : '';
            $esInvertida = ($escalaInicio === '6');
            
            if ($esInvertida) {
                $preguntasInvertidas[] = $numeroPregunta;
            } else {
                $preguntasNormales[] = $numeroPregunta;
            }
            
            // Leer valores de columnas de dimensiones (cuando User_Choice=5 en este CSV)
            $exem = isset($data[12]) && $data[12] !== '' && $data[12] !== '0' ? (float)str_replace(',', '.', $data[12]) : 0;
            $repr = isset($data[13]) && $data[13] !== '' && $data[13] !== '0' ? (float)str_replace(',', '.', $data[13]) : 0;
            $deci = isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? (float)str_replace(',', '.', $data[14]) : 0;
            $faps = isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? (float)str_replace(',', '.', $data[15]) : 0;
            $extr = isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? (float)str_replace(',', '.', $data[16]) : 0;
            $asmo = isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? (float)str_replace(',', '.', $data[17]) : 0;
            
            // Si tiene valor > 0 en la columna, pertenece a esa dimensión
            if ($exem > 0) $dimensiones['EXEM'][] = $numeroPregunta;
            if ($repr > 0) $dimensiones['REPR'][] = $numeroPregunta;
            if ($deci > 0) $dimensiones['DECI'][] = $numeroPregunta;
            if ($faps > 0) $dimensiones['FAPS'][] = $numeroPregunta;
            if ($extr > 0) $dimensiones['EXTR'][] = $numeroPregunta;
            if ($asmo > 0) $dimensiones['ASMO'][] = $numeroPregunta;
        }
        
        fclose($handle);
        
        $this->info('📝 Preguntas por dimensión (según columnas del CSV):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            $this->line("      " . implode(', ', $preguntas));
        }
        
        $this->info('');
        $this->info('🔄 Preguntas invertidas (escala 6,5,4,3,2,1,0):');
        sort($preguntasInvertidas);
        $this->info("   Total: " . count($preguntasInvertidas));
        $this->line("   Números: " . implode(', ', $preguntasInvertidas));
        
        $this->info('');
        $this->info('📊 Preguntas normales (escala 0,1,2,3,4,5,6):');
        $this->info("   Total: " . count($preguntasNormales));
        
        // Calcular scores cuando todo es 0
        $this->info('');
        $this->info('🔢 Calculando scores cuando User_Choice=0 para todas las preguntas:');
        $this->info('');
        $this->info('   Preguntas NORMALES: User_Choice=0 → Score=0');
        $this->info('   Preguntas INVERTIDAS: User_Choice=0 → Score=6 (inversión)');
        $this->info('');
        
        $scoresEsperados = [
            'EXEM' => 13,
            'REPR' => 16,
            'DECI' => 8,
            'FAPS' => 9,
            'EXTR' => 18,
            'ASMO' => 0,
        ];
        
        $resultados = [];
        
        foreach ($dimensiones as $tag => $preguntas) {
            $total = 0;
            $preguntasInvertidasEnDimension = 0;
            $preguntasNormalesEnDimension = 0;
            
            foreach ($preguntas as $num) {
                if (in_array($num, $preguntasInvertidas)) {
                    // Pregunta invertida: cuando User_Choice=0 → Score=6
                    $total += 6;
                    $preguntasInvertidasEnDimension++;
                } else {
                    // Pregunta normal: cuando User_Choice=0 → Score=0
                    $total += 0;
                    $preguntasNormalesEnDimension++;
                }
            }
            
            $esperado = $scoresEsperados[$tag] ?? 0;
            $diferencia = $total - $esperado;
            $estado = abs($diferencia) < 0.01 ? '✅' : '❌';
            
            $resultados[$tag] = [
                'calculado' => $total,
                'esperado' => $esperado,
                'diferencia' => $diferencia,
                'estado' => $estado,
                'invertidas' => $preguntasInvertidasEnDimension,
                'normales' => $preguntasNormalesEnDimension
            ];
        }
        
        $this->info('📋 RESULTADOS:');
        $this->info('');
        $this->table(
            ['Dimensión', 'Calculado', 'Esperado', 'Diferencia', 'Estado', 'Invertidas', 'Normales'],
            array_map(function($tag, $datos) {
                return [
                    $tag,
                    $datos['calculado'],
                    $datos['esperado'],
                    $datos['diferencia'],
                    $datos['estado'],
                    $datos['invertidas'],
                    $datos['normales']
                ];
            }, array_keys($resultados), $resultados)
        );
        
        // Mostrar detalle
        $this->info('');
        $this->info('🔍 Detalle por dimensión:');
        $this->info('');
        
        foreach ($resultados as $tag => $datos) {
            $this->line("   {$tag}:");
            $this->line("      Preguntas invertidas: {$datos['invertidas']} (0→6 cada una)");
            $this->line("      Preguntas normales: {$datos['normales']} (0→0 cada una)");
            $this->line("      Cálculo: ({$datos['invertidas']} × 6) + ({$datos['normales']} × 0) = {$datos['calculado']}");
            $this->line("      Esperado: {$datos['esperado']}");
            
            if ($datos['estado'] === '✅') {
                $this->info("      ✅ COINCIDE");
            } else {
                $this->error("      ❌ DIFERENCIA: {$datos['diferencia']}");
            }
            $this->info('');
        }
        
        // Generar código para ActualizarRelacionesPreguntas.php
        $this->info('');
        $this->info('💻 Código para ActualizarRelacionesPreguntas.php:');
        $this->info('');
        $this->line('$dimensiones_csv = [');
        foreach ($dimensiones as $tag => $preguntas) {
            $tagCodigo = ucfirst(strtolower($tag));
            $this->line("    '{$tagCodigo}' => [" . implode(', ', $preguntas) . '],');
        }
        $this->line('];');
        
        $this->info('');
        $this->info('💻 Preguntas invertidas:');
        $this->line('$perguntasComInversao = [' . implode(', ', $preguntasInvertidas) . '];');
        
        return 0;
    }
}

