<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtraerDimensionesCorrectas extends Command
{
    protected $signature = 'emotive:extraer-dimensiones-correctas';
    protected $description = 'Extrae las dimensiones correctas analizando el CSV línea por línea';

    public function handle()
    {
        $csvPath = '/Users/novadesck/Downloads/EMULADOR - EMOTIVE (2) - perguntas_completas_99.csv';
        
        if (!file_exists($csvPath)) {
            $this->error("CSV no encontrado");
            return 1;
        }
        
        $this->info('📊 Analizando CSV para extraer dimensiones correctas...');
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
            
            // Detectar si es invertida (escala comienza con 6)
            $escalaInicio = isset($data[2]) ? trim($data[2]) : '';
            $esInvertida = ($escalaInicio === '6');
            
            if ($esInvertida) {
                $preguntasInvertidas[] = $numeroPregunta;
            }
            
            // Leer valores de dimensiones (columna 12-17)
            $valores = [
                'EXEM' => isset($data[12]) && $data[12] !== '' && $data[12] !== '0' ? (float)str_replace(',', '.', $data[12]) : 0,
                'REPR' => isset($data[13]) && $data[13] !== '' && $data[13] !== '0' ? (float)str_replace(',', '.', $data[13]) : 0,
                'DECI' => isset($data[14]) && $data[14] !== '' && $data[14] !== '0' ? (float)str_replace(',', '.', $data[14]) : 0,
                'FAPS' => isset($data[15]) && $data[15] !== '' && $data[15] !== '0' ? (float)str_replace(',', '.', $data[15]) : 0,
                'EXTR' => isset($data[16]) && $data[16] !== '' && $data[16] !== '0' ? (float)str_replace(',', '.', $data[16]) : 0,
                'ASMO' => isset($data[17]) && $data[17] !== '' && $data[17] !== '0' ? (float)str_replace(',', '.', $data[17]) : 0,
            ];
            
            // Si tiene valor > 0, pertenece a esa dimensión
            foreach ($valores as $dim => $valor) {
                if ($valor > 0) {
                    $dimensiones[$dim][] = $numeroPregunta;
                }
            }
            
            $detallePreguntas[$numeroPregunta] = [
                'invertida' => $esInvertida,
                'valores' => $valores
            ];
        }
        
        fclose($handle);
        
        // Ahora calcular cuando todo es 0
        // Para preguntas normales: User_Choice=0 → Score=0
        // Para preguntas invertidas: User_Choice=0 → Score=6
        // Pero necesito saber qué valor aporta a cada dimensión
        
        $this->info('📝 Preguntas por dimensión (cuando tienen valor > 0 en la columna):');
        $this->info('');
        
        foreach ($dimensiones as $tag => $preguntas) {
            sort($preguntas);
            $this->info("   {$tag}: " . count($preguntas) . " preguntas");
            if (count($preguntas) <= 30) {
                $this->line("      " . implode(', ', $preguntas));
            } else {
                $this->line("      " . implode(', ', array_slice($preguntas, 0, 30)) . '...');
            }
        }
        
        $this->info('');
        $this->info('🔄 Preguntas invertidas: ' . count($preguntasInvertidas));
        $this->line("   " . implode(', ', $preguntasInvertidas));
        
        // Calcular scores cuando todo es 0
        // El problema es que el CSV muestra valores cuando User_Choice=5
        // Necesito calcular qué valores tendrían cuando User_Choice=0
        
        $this->info('');
        $this->info('🔢 Calculando scores cuando User_Choice=0:');
        $this->info('');
        $this->info('   Lógica:');
        $this->info('   - Preguntas NORMALES: User_Choice=0 → Score=0 → aporta 0 a todas las dimensiones');
        $this->info('   - Preguntas INVERTIDAS: User_Choice=0 → Score=6');
        $this->info('     Pero necesito saber qué dimensión(es) pertenece cada pregunta invertida');
        $this->info('');
        
        // Para preguntas invertidas, cuando User_Choice=0 → Score=6
        // Necesito saber a qué dimensión(es) pertenece cada pregunta invertida
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
            // Pero solo a las dimensiones a las que pertenece
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
        
        $this->info('📋 RESULTADOS (sumando 6 por cada pregunta invertida en cada dimensión):');
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
        
        // El problema es que esto no coincide. Necesito entender mejor.
        // Tal vez las preguntas invertidas no aportan 6 a todas las dimensiones a las que pertenecen
        // O tal vez la lógica es diferente
        
        return 0;
    }
}

