<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Formulario;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Traits\CalculaEjesAnaliticos;

class VerificarCalculoEmotive extends Command
{
    use CalculaEjesAnaliticos;
    
    protected $signature = 'emotive:verificar-calculo {user_id} {formulario_id}';
    protected $description = 'Verifica que el cálculo de dimensiones y ejes esté funcionando correctamente';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario no encontrado: {$userId}");
            return 1;
        }

        $formulario = Formulario::with('perguntas')->find($formularioId);
        if (!$formulario) {
            $this->error("Formulario no encontrado: {$formularioId}");
            return 1;
        }

        $this->info("🔍 VERIFICACIÓN DE CÁLCULO E.MO.TI.VE");
        $this->info("Usuario: {$user->name} (ID: {$user->id})");
        $this->info("Formulario: {$formulario->nome} (ID: {$formulario->id})");
        $this->info("");

        // Obtener respuestas
        $perguntaIds = $formulario->perguntas->pluck('id')->toArray();
        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');

        $this->info("📊 RESPUESTAS ENCONTRADAS: " . $respostasUsuario->count() . " de " . count($perguntaIds) . " preguntas");
        $this->info("");

        // Cargar variables
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();

        $this->info("📈 CÁLCULO DE DIMENSIONES:");
        $this->info(str_repeat("=", 80));

        $pontuacoes = [];
        $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];

        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            $preguntasProcesadas = [];
            $preguntasSinResposta = [];

            if ($variavel->perguntas->isEmpty()) {
                $this->warn("  ⚠️  {$variavel->tag}: Sin preguntas asociadas");
                continue;
            }

            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);

                if (!$resposta || $resposta->valor_resposta === null) {
                    $preguntasSinResposta[] = $pergunta->numero_da_pergunta ?? $pergunta->id;
                    continue;
                }

                $valorOriginal = (int)$resposta->valor_resposta;
                $numeroPergunta = (int)($pergunta->numero_da_pergunta ?? 0);
                $necesitaInversion = in_array($numeroPergunta, $perguntasComInversao, true);
                $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;

                $pontuacao += $valorUsado;
                $totalRespostas++;

                $preguntasProcesadas[] = [
                    'numero' => $numeroPergunta,
                    'valor_original' => $valorOriginal,
                    'valor_usado' => $valorUsado,
                    'invertida' => $necesitaInversion
                ];
            }

            if ($totalRespostas === 0) {
                $this->warn("  ⚠️  {$variavel->tag}: Sin respuestas");
                continue;
            }

            $b = is_numeric($variavel->B) ? (float)$variavel->B : 0;
            $m = is_numeric($variavel->M) ? (float)$variavel->M : 0;
            $a = is_numeric($variavel->A) ? (float)$variavel->A : 0;

            $faixa = 'Baixa';
            if ($pontuacao > $m) {
                $faixa = 'Alta';
            } elseif ($pontuacao > $b) {
                $faixa = 'Moderada';
            }

            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'nome' => $variavel->nome,
                'valor' => $pontuacao,
                'faixa' => $faixa,
                'b' => $b,
                'm' => $m,
                'a' => $a,
                'total_respostas' => $totalRespostas,
                'preguntas_procesadas' => count($preguntasProcesadas),
                'preguntas_sin_resposta' => count($preguntasSinResposta)
            ];

            $this->info("  {$variavel->tag} ({$variavel->nome}):");
            $this->line("    Puntuación: {$pontuacao}");
            $this->line("    Faixa: {$faixa} (B={$b}, M={$m}, A={$a})");
            $this->line("    Respuestas usadas: {$totalRespostas} de " . $variavel->perguntas->count());
            if (count($preguntasSinResposta) > 0) {
                $this->warn("    ⚠️  Preguntas sin respuesta: " . implode(', ', array_slice($preguntasSinResposta, 0, 10)));
            }
            
            // Mostrar primeras 3 preguntas procesadas
            if (count($preguntasProcesadas) > 0) {
                $this->line("    Primeras 3 preguntas:");
                foreach (array_slice($preguntasProcesadas, 0, 3) as $p) {
                    $inv = $p['invertida'] ? ' (INVERTIDA)' : '';
                    $this->line("      #{$p['numero']}: {$p['valor_original']} → {$p['valor_usado']}{$inv}");
                }
            }
            $this->info("");
        }

        // Calcular ejes
        $this->info("📊 CÁLCULO DE EJES (EE, PR, SO):");
        $this->info(str_repeat("=", 80));

        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        $this->info("  EE (Energia Emocional): {$indices['EE']}");
        $this->info("  PR (Propósito e Relações): {$indices['PR']}");
        $this->info("  SO (Sustentabilidade Ocupacional): {$indices['SO']}");
        $this->info("");

        // Calcular ejes analíticos
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }

        $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
        $iid = $this->calcularIID($ejesAnaliticos);
        $nivelRisco = $this->determinarNivelRisco($iid);

        $this->info("📊 EJES ANALÍTICOS:");
        $this->info(str_repeat("=", 80));
        $this->info("  Eje 1 (Energia Emocional): {$ejesAnaliticos['eixo1']['total']}");
        $this->info("  Eje 2 (Propósito e Relações): {$ejesAnaliticos['eixo2']['total']}");
        $this->info("  Eje 3 (Sustentabilidade Ocupacional): {$ejesAnaliticos['eixo3']['total']}");
        $this->info("");
        $this->info("📊 IID (Índice Integrado de Descarrilamento): {$iid}%");
        $this->info("  Nivel de Risco: {$nivelRisco['nivel']} - {$nivelRisco['zona']}");
        $this->info("");

        // Resumen
        $this->info("✅ RESUMEN:");
        $this->info(str_repeat("=", 80));
        $this->table(
            ['Dimensión', 'Puntuación', 'Faixa', 'Respuestas'],
            array_map(function($p) {
                return [
                    $p['tag'],
                    $p['valor'],
                    $p['faixa'],
                    "{$p['total_respostas']} de " . ($p['preguntas_procesadas'] + $p['preguntas_sin_resposta'])
                ];
            }, $pontuacoes)
        );

        return 0;
    }
}

