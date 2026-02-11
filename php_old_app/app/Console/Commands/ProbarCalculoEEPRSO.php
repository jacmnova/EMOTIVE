<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsuarioFormulario;
use App\Models\Resposta;
use App\Traits\CalculaEjesAnaliticos;

class ProbarCalculoEEPRSO extends Command
{
    use CalculaEjesAnaliticos;

    protected $signature = 'emotive:probar-ee-pr-so {usuario_id}';
    protected $description = 'Prueba el cálculo de EE, PR, SO y IID para un usuario';

    public function handle()
    {
        $usuarioId = $this->argument('usuario_id');
        
        $this->info("🔍 Probando cálculo de EE, PR, SO para usuario ID: {$usuarioId}");
        $this->info("");
        
        // Obtener formulario del usuario
        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $usuarioId)
            ->where('formulario_id', 1)
            ->first();
        
        if (!$usuarioFormulario) {
            $this->error("❌ No se encontró formulario para el usuario {$usuarioId}");
            return 1;
        }
        
        $formulario = $usuarioFormulario->formulario;
        $respostasUsuario = Resposta::where('usuario_formulario_id', $usuarioFormulario->id)
            ->get()
            ->keyBy('pergunta_id');
        
        $this->info("📊 Total respuestas: " . $respostasUsuario->count());
        $this->info("");
        
        // Calcular índices EE, PR, SO
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $this->info("📈 ÍNDICES CALCULADOS:");
        $this->info("====================");
        $this->info("  EE (Energia Emocional): {$indices['EE']}");
        $this->info("  PR (Propósito e Relações): {$indices['PR']}");
        $this->info("  SO (Sustentabilidade Ocupacional): {$indices['SO']}");
        $this->info("");
        
        // Calcular dimensiones primero (necesarias para los ejes analíticos)
        $variaveis = \App\Models\Variavel::with('perguntas')->where('formulario_id', 1)->get();
        $pontuacoes = [];
        
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if (!$resposta || $resposta->valor_resposta === null) {
                    continue;
                }
                
                $valor = (int)$resposta->valor_resposta;
                $necesitaInversion = \App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta);
                $valorUsado = $necesitaInversion ? (6 - $valor) : $valor;
                
                $pontuacao += $valorUsado;
                $totalRespostas++;
            }
            
            if ($totalRespostas > 0) {
                $faixa = $this->classificarPontuacao($pontuacao, $variavel);
                $pontuacoes[] = [
                    'tag' => strtoupper($variavel->tag ?? ''),
                    'valor' => $pontuacao,
                    'faixa' => $faixa
                ];
            }
        }
        
        // Calcular ejes analíticos
        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoes, $indices);
        
        $this->info("📊 EJES ANALÍTICOS:");
        $this->info("===================");
        $this->info("  Eixo 1 (EE): {$ejesAnaliticos['eixo1']['total']}");
        $this->info("  Eixo 2 (PR): {$ejesAnaliticos['eixo2']['total']}");
        $this->info("  Eixo 3 (SO): {$ejesAnaliticos['eixo3']['total']}");
        $this->info("");
        
        // Calcular IID
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);
        
        $this->info("🎯 ÍNDICE INTEGRADO DE DESCARRILAMENTO (IID):");
        $this->info("=============================================");
        $this->info("  IID: {$iid}%");
        $this->info("  Nivel: {$nivelRisco['nivel']}");
        $this->info("  Zona: {$nivelRisco['zona']}");
        $this->info("");
        
        // Mostrar máximos esperados
        $this->info("📏 MÁXIMOS ESPERADOS (según CSV ALE):");
        $this->info("=====================================");
        $this->info("  EE: 114 puntos (19 preguntas × 6)");
        $this->info("  PR: 72 puntos (12 preguntas × 6)");
        $this->info("  SO: 84 puntos (14 preguntas × 6)");
        $this->info("  Promedio máximos: 90");
        $this->info("");
        
        return 0;
    }
}

