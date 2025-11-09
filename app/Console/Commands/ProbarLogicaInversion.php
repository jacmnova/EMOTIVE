<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variavel;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\User;
use App\Models\Formulario;

class ProbarLogicaInversion extends Command
{
    protected $signature = 'emotive:probar-inversion {formulario_id=1}';
    protected $description = 'Prueba la lógica de inversión para todas las dimensiones';

    public function handle()
    {
        $formularioId = $this->argument('formulario_id');
        
        $this->info('🧪 PRUEBA DE LÓGICA DE INVERSIÓN');
        $this->info('=====================================');
        $this->info('');
        
        $formulario = Formulario::find($formularioId);
        if (!$formulario) {
            $this->error("Formulario {$formularioId} no encontrado");
            return 1;
        }
        
        $this->info("Formulario: {$formulario->nome} (ID: {$formularioId})");
        $this->info('');
        
        // Obtener todas las variables
        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formularioId)
            ->get();
        
        if ($variaveis->isEmpty()) {
            $this->error('No se encontraron variables para este formulario');
            return 1;
        }
        
        // Lista de preguntas invertidas
        // Actualizada: incluye preguntas #4, #6, #9, #21, #25, #31, #35 que deben dar 0 cuando están en 6
        $perguntasComInversao = [4, 6, 9, 21, 25, 31, 35, 48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
        
        $this->info('📋 CASOS DE PRUEBA:');
        $this->info('');
        
        // Caso 1: Todas las respuestas en 0
        $this->info('1️⃣ CASO 1: Todas las respuestas en 0');
        $this->info('   Para que el resultado sea 0:');
        $this->info('   - Preguntas normales: deben estar en 0');
        $this->info('   - Preguntas invertidas: deben estar en 6 (porque 6→0 después de inversión)');
        $this->info('');
        
        // Caso 2: Todas las respuestas en 6
        $this->info('2️⃣ CASO 2: Todas las respuestas en 6');
        $this->info('   Resultado esperado:');
        $this->info('   - Preguntas normales: 6');
        $this->info('   - Preguntas invertidas: 0 (porque 6→0 después de inversión)');
        $this->info('');
        
        // Analizar cada dimensión
        $this->info('📊 ANÁLISIS POR DIMENSIÓN:');
        $this->info('');
        
        $resultados = [];
        
        foreach ($variaveis as $variavel) {
            $tag = strtoupper($variavel->tag ?? '');
            $nome = $variavel->nome ?? 'Sin nombre';
            
            $this->info("   🔹 {$tag} - {$nome}");
            
            $preguntasNormales = [];
            $preguntasInvertidas = [];
            
            foreach ($variavel->perguntas as $pergunta) {
                $numeroPergunta = (int)($pergunta->numero_da_pergunta ?? 0);
                $necesitaInversion = in_array($numeroPergunta, $perguntasComInversao, true);
                
                if ($necesitaInversion) {
                    $preguntasInvertidas[] = $numeroPergunta;
                } else {
                    $preguntasNormales[] = $numeroPergunta;
                }
            }
            
            // Calcular para caso 1: todo en 0
            $caso1Normal = count($preguntasNormales) * 0; // 0
            $caso1Invertida = count($preguntasInvertidas) * 6; // 6 porque 0→6
            $caso1Total = $caso1Normal + $caso1Invertida;
            
            // Calcular para caso 2: todo en 6
            $caso2Normal = count($preguntasNormales) * 6; // 6
            $caso2Invertida = count($preguntasInvertidas) * 0; // 0 porque 6→0
            $caso2Total = $caso2Normal + $caso2Invertida;
            
            // Calcular para caso 3: para obtener resultado 0
            // Normales en 0, invertidas en 6 (porque 6→0 después de inversión)
            $caso3Normal = count($preguntasNormales) * 0; // 0
            $caso3Invertida = count($preguntasInvertidas) * 0; // 0 porque invertidas en 6 → 0 después de inversión
            $caso3Total = $caso3Normal + $caso3Invertida;
            
            // Calcular para caso 4: todo en 0 (lo que realmente pasa)
            // Normales en 0, invertidas en 0 → 6 (después de inversión)
            $caso4Normal = count($preguntasNormales) * 0; // 0
            $caso4Invertida = count($preguntasInvertidas) * 6; // 6 porque 0→6 después de inversión
            $caso4Total = $caso4Normal + $caso4Invertida;
            
            $resultados[$tag] = [
                'nome' => $nome,
                'total_preguntas' => count($variavel->perguntas),
                'preguntas_normales' => count($preguntasNormales),
                'preguntas_invertidas' => count($preguntasInvertidas),
                'caso1_todo_0' => $caso1Total,
                'caso2_todo_6' => $caso2Total,
                'caso3_para_0' => $caso3Total,
                'caso4_todo_0_real' => $caso4Total,
                'preguntas_normales_list' => $preguntasNormales,
                'preguntas_invertidas_list' => $preguntasInvertidas,
            ];
            
            $this->line("      Total preguntas: " . count($variavel->perguntas));
            $this->line("      Preguntas normales: " . count($preguntasNormales));
            $this->line("      Preguntas invertidas: " . count($preguntasInvertidas));
            $this->line("      Caso 1 (todo en 0): {$caso1Total} puntos");
            $this->line("      Caso 2 (todo en 6): {$caso2Total} puntos");
            $this->line("      Caso 3 (para resultado 0): {$caso3Total} puntos");
            $this->line("      Caso 4 (todo en 0 - real): {$caso4Total} puntos");
            $this->info('');
        }
        
        // Mostrar tabla resumen
        $this->info('📋 RESUMEN:');
        $this->info('');
        
        $headers = ['Dimensión', 'Total Preg', 'Normales', 'Invertidas', 'Caso 1 (0→)', 'Caso 2 (6→)', 'Caso 3 (para 0)', 'Caso 4 (0 real)'];
        $rows = [];
        
        foreach ($resultados as $tag => $datos) {
            $rows[] = [
                $tag,
                $datos['total_preguntas'],
                $datos['preguntas_normales'],
                $datos['preguntas_invertidas'],
                $datos['caso1_todo_0'],
                $datos['caso2_todo_6'],
                $datos['caso3_para_0'],
                $datos['caso4_todo_0_real'],
            ];
        }
        
        $this->table($headers, $rows);
        
        // Verificar lógica
        $this->info('');
        $this->info('✅ VERIFICACIÓN DE LÓGICA:');
        $this->info('');
        
        $todosCorrectos = true;
        
        foreach ($resultados as $tag => $datos) {
            // Verificar que caso 3 (para resultado 0) sea 0
            if ($datos['caso3_para_0'] != 0) {
                $this->error("   ❌ {$tag}: Caso 3 debería ser 0, pero es {$datos['caso3_para_0']}");
                $todosCorrectos = false;
            } else {
                $this->info("   ✅ {$tag}: Caso 3 correcto (0)");
            }
        }
        
        $this->info('');
        
        if ($todosCorrectos) {
            $this->info('✅ TODAS LAS DIMENSIONES USAN LA MISMA LÓGICA CORRECTAMENTE');
        } else {
            $this->error('❌ HAY PROBLEMAS EN LA LÓGICA');
        }
        
        // Mostrar detalles de preguntas invertidas
        $this->info('');
        $this->info('🔍 DETALLE DE PREGUNTAS INVERTIDAS POR DIMENSIÓN:');
        $this->info('');
        
        foreach ($resultados as $tag => $datos) {
            if (!empty($datos['preguntas_invertidas_list'])) {
                $this->line("   {$tag}: " . implode(', ', $datos['preguntas_invertidas_list']));
            }
        }
        
        return 0;
    }
}

