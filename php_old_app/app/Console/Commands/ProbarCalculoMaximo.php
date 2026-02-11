<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pergunta;
use App\Models\Variavel;
use App\Traits\CalculaEjesAnaliticos;

class ProbarCalculoMaximo extends Command
{
    use CalculaEjesAnaliticos;

    protected $signature = 'probar:calculo-maximo';
    protected $description = 'Prueba el cálculo de EE, PR, SO e IID con todas las respuestas en máximo (6)';

    public function handle()
    {
        $this->info('📊 PRUEBA CON TODAS LAS RESPUESTAS EN MÁXIMO (6)');
        $this->line(str_repeat('=', 70));
        $this->line('');

        $formularioId = 1;

        // Obtener todas las preguntas del formulario
        $preguntas = Pergunta::where('formulario_id', $formularioId)->get();

        $this->info("Total preguntas en formulario: {$preguntas->count()}");
        $this->line('');

        // Crear respuestas simuladas (todas en 6)
        $respostasSimuladas = collect();
        foreach ($preguntas as $pergunta) {
            $resposta = new \stdClass();
            $resposta->pergunta_id = $pergunta->id;
            $resposta->valor_resposta = 6;
            $respostasSimuladas->put($pergunta->id, $resposta);
        }

        // Calcular índices
        $indices = $this->calcularIndicesDesdeRespostas($respostasSimuladas, $formularioId);

        $this->info('📊 RESULTADOS DEL CÁLCULO:');
        $this->line(str_repeat('=', 70));
        $this->line("EE (Energia Emocional): {$indices['EE']} puntos");
        $this->line("PR (Propósito e Relações): {$indices['PR']} puntos");
        $this->line("SO (Sustentabilidade Ocupacional): {$indices['SO']} puntos");
        $this->line('');

        // Calcular IID
        $ejesAnaliticos = [
            'eixo1' => ['total' => $indices['EE']],
            'eixo2' => ['total' => $indices['PR']],
            'eixo3' => ['total' => $indices['SO']],
        ];

        $iid = $this->calcularIID($ejesAnaliticos);
        $this->line("IID (Índice Integrado de Descarrilamento): {$iid}%");
        $this->line('');

        // Verificar máximos esperados
        $this->info('📊 MÁXIMOS ESPERADOS (CSV MAX):');
        $this->line(str_repeat('=', 70));
        $this->line('EE máximo: 276 puntos');
        $this->line('PR máximo: 234 puntos');
        $this->line('SO máximo: 186 puntos');
        $this->line('IID máximo: 100% (cuando EE=276, PR=234, SO=186)');
        $this->line('');

        // Verificar si coinciden
        $eeOk = $indices['EE'] == 276;
        $prOk = $indices['PR'] == 234;
        $soOk = $indices['SO'] == 186;

        $this->info('📊 VERIFICACIÓN:');
        $this->line(str_repeat('=', 70));
        
        if ($eeOk) {
            $this->info('✅ EE: CORRECTO');
        } else {
            $this->error("❌ EE: INCORRECTO (esperado: 276, obtenido: {$indices['EE']})");
            $this->line("   Diferencia: " . (276 - $indices['EE']) . " puntos");
        }
        
        if ($prOk) {
            $this->info('✅ PR: CORRECTO');
        } else {
            $this->error("❌ PR: INCORRECTO (esperado: 234, obtenido: {$indices['PR']})");
            $this->line("   Diferencia: " . (234 - $indices['PR']) . " puntos");
        }
        
        if ($soOk) {
            $this->info('✅ SO: CORRECTO');
        } else {
            $this->error("❌ SO: INCORRECTO (esperado: 186, obtenido: {$indices['SO']})");
            $this->line("   Diferencia: " . (186 - $indices['SO']) . " puntos");
        }

        $this->line('');

        // Verificar número de preguntas por eje
        $this->info('📊 VERIFICACIÓN DE PREGUNTAS POR EJE:');
        $this->line(str_repeat('=', 70));
        
        $exEm = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'ExEm')->first();
        $rePr = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'RePr')->first();
        $deCi = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'DeCi')->first();
        $faPs = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'FaPs')->first();
        $exTr = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'ExTr')->first();
        $asMo = Variavel::with('perguntas')->where('formulario_id', $formularioId)->where('tag', 'AsMo')->first();

        $eeIds = array_unique(array_merge(
            $exEm ? $exEm->perguntas->pluck('id')->toArray() : [],
            $rePr ? $rePr->perguntas->pluck('id')->toArray() : []
        ));
        
        $prIds = array_unique(array_merge(
            $deCi ? $deCi->perguntas->pluck('id')->toArray() : [],
            $faPs ? $faPs->perguntas->pluck('id')->toArray() : []
        ));
        
        $soIds = array_unique(array_merge(
            $exTr ? $exTr->perguntas->pluck('id')->toArray() : [],
            $asMo ? $asMo->perguntas->pluck('id')->toArray() : []
        ));

        $this->line("EE: " . count($eeIds) . " preguntas únicas (EXEM: " . ($exEm ? $exEm->perguntas->count() : 0) . ", REPR: " . ($rePr ? $rePr->perguntas->count() : 0) . ")");
        $this->line("PR: " . count($prIds) . " preguntas únicas (DECI: " . ($deCi ? $deCi->perguntas->count() : 0) . ", FAPS: " . ($faPs ? $faPs->perguntas->count() : 0) . ")");
        $this->line("SO: " . count($soIds) . " preguntas únicas (EXTR: " . ($exTr ? $exTr->perguntas->count() : 0) . ", ASMO: " . ($asMo ? $asMo->perguntas->count() : 0) . ")");
        $this->line('');

        // Calcular máximos teóricos
        $maxEE = count($eeIds) * 6;
        $maxPR = count($prIds) * 6;
        $maxSO = count($soIds) * 6;

        $this->line("Máximo teórico EE: {$maxEE} puntos (si todas las respuestas son 6)");
        $this->line("Máximo teórico PR: {$maxPR} puntos (si todas las respuestas son 6)");
        $this->line("Máximo teórico SO: {$maxSO} puntos (si todas las respuestas son 6)");
        $this->line('');

        // Verificar si hay preguntas invertidas
        $this->info('📊 VERIFICACIÓN DE PREGUNTAS INVERTIDAS:');
        $this->line(str_repeat('=', 70));
        
        $preguntasInvertidas = 0;
        foreach ($preguntas as $pergunta) {
            if (\App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta)) {
                $preguntasInvertidas++;
            }
        }
        
        $this->line("Total preguntas invertidas: {$preguntasInvertidas}");
        $this->line('');
        $this->line("NOTA: Las preguntas invertidas con respuesta 6 se convierten en 0 (6-6=0)");
        $this->line("Por eso el máximo real puede ser menor que el teórico.");

        return 0;
    }
}

