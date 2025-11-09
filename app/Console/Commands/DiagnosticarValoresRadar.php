<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Variavel;
use App\Models\Resposta;

class DiagnosticarValoresRadar extends Command
{
    protected $signature = 'emotive:diagnosticar-radar {usuario_id} {formulario_id=1} {--todas-respuestas}';
    protected $description = 'Diagnostica por qué el radar muestra valores cuando debería ser 0';

    public function handle()
    {
        $usuarioId = $this->argument('usuario_id');
        $formularioId = $this->argument('formulario_id');
        
        $user = User::find($usuarioId);
        if (!$user) {
            $this->error("Usuario {$usuarioId} no encontrado");
            return 1;
        }
        
        $formulario = Formulario::with('perguntas')->find($formularioId);
        if (!$formulario) {
            $this->error("Formulario {$formularioId} no encontrado");
            return 1;
        }
        
        $this->info("🔍 DIAGNÓSTICO DE VALORES DEL RADAR");
        $this->info("Usuario: {$user->name} (ID: {$usuarioId})");
        $this->info("Formulario: {$formulario->nome} (ID: {$formularioId})");
        $this->info("=====================================");
        $this->info('');
        
        // Obtener respuestas del usuario
        $respostasUsuario = Resposta::where('user_id', $usuarioId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');
        
        $this->info("Total respuestas encontradas: " . $respostasUsuario->count());
        
        // Verificar si todas las respuestas están en 0
        $respuestasEnCero = $respostasUsuario->filter(function($r) {
            return (int)($r->valor_resposta ?? -1) === 0;
        })->count();
        
        $respuestasConValor = $respostasUsuario->filter(function($r) {
            return (int)($r->valor_resposta ?? 0) > 0;
        });
        
        $this->info("Respuestas en 0: {$respuestasEnCero}");
        $this->info("Respuestas con valor > 0: " . $respuestasConValor->count());
        
        if ($respuestasConValor->count() > 0 && $this->option('todas-respuestas')) {
            $this->info('');
            $this->warn("⚠️  Respuestas que NO están en 0:");
            foreach ($respuestasConValor->take(10) as $r) {
                $p = $formulario->perguntas->firstWhere('id', $r->pergunta_id);
                $num = $p ? $p->numero_da_pergunta : 'N/A';
                $this->line("  Pregunta #{$num} (ID: {$r->pergunta_id}): valor = {$r->valor_resposta}");
            }
            if ($respuestasConValor->count() > 10) {
                $this->line("  ... y " . ($respuestasConValor->count() - 10) . " más");
            }
        }
        
        $this->info('');
        
        // Obtener variables
        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formularioId)
            ->get();
        
        // Usar helper para identificar preguntas invertidas por texto
        
        $this->info("📊 ANÁLISIS POR DIMENSIÓN:");
        $this->info('');
        
        foreach ($variaveis as $variavel) {
            $tag = strtoupper($variavel->tag ?? '');
            if (!in_array($tag, ['ASMO', 'REPR', 'DECI', 'EXEM', 'FAPS', 'EXTR'])) {
                continue;
            }
            
            $pontuacao = 0;
            $detalle = [];
            $preguntasConValor = 0;
            $preguntasEnCero = 0;
            $preguntasInvertidasEnCero = 0;
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta) {
                    continue;
                }
                
                $valorOriginal = (int)($resposta->valor_resposta ?? 0);
                $numeroPergunta = (int)($pergunta->numero_da_pergunta ?? 0);
                $necesitaInversion = \App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta);
                
                if ($necesitaInversion) {
                    $valorUsado = 6 - $valorOriginal;
                } else {
                    $valorUsado = $valorOriginal;
                }
                
                $pontuacao += $valorUsado;
                
                if ($valorUsado > 0) {
                    $preguntasConValor++;
                }
                
                if ($valorOriginal == 0) {
                    $preguntasEnCero++;
                    if ($necesitaInversion && $valorUsado > 0) {
                        $preguntasInvertidasEnCero++;
                    }
                }
                
                if ($valorUsado > 0) {
                    $detalle[] = [
                        'pregunta' => $numeroPergunta,
                        'original' => $valorOriginal,
                        'invertida' => $necesitaInversion,
                        'usado' => $valorUsado,
                        'pergunta_id' => $pergunta->id
                    ];
                }
            }
            
            $this->info("{$tag} - {$variavel->nome}");
            $this->line("  Puntuación total: {$pontuacao}");
            $this->line("  Total preguntas asociadas: " . $variavel->perguntas->count());
            $this->line("  Preguntas con valor > 0: {$preguntasConValor}");
            $this->line("  Preguntas en 0: {$preguntasEnCero}");
            $this->line("  Preguntas invertidas en 0 que dan valor: {$preguntasInvertidasEnCero}");
            
            if (!empty($detalle)) {
                $this->line("  Detalle de preguntas con valor:");
                foreach ($detalle as $d) {
                    $tipo = $d['invertida'] ? 'INVERTIDA' : 'NORMAL';
                    $this->line("    Pregunta #{$d['pregunta']} (ID: {$d['pergunta_id']}): {$d['original']} → {$d['usado']} ({$tipo})");
                }
            }
            
            $this->info('');
        }
        
        return 0;
    }
}

