<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Resposta;
use App\Models\Pergunta;
use App\Models\Variavel;
use App\Models\Formulario;
use App\Http\Controllers\DadosController;

class VerificarFapsUsuario extends Command
{
    protected $signature = 'emotive:verificar-faps {user_id} {formulario_id=1}';
    protected $description = 'Verifica el cálculo y clasificación de FAPS para un usuario';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $formularioId = $this->argument('formulario_id');
        
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario no encontrado: {$userId}");
            return 1;
        }
        
        // Simular el cálculo del controlador
        $controller = new DadosController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['formulario_id' => $formularioId]);
        
        try {
            // Usar reflexión para acceder al método protegido o crear un método público
            $this->info("🔍 Verificando cálculo de FAPS para usuario: {$user->name}");
            $this->info("");
            
            // Obtener datos directamente
            $formulario = Formulario::find($formularioId);
            $variaveis = Variavel::with('perguntas')
                ->where('formulario_id', $formularioId)
                ->get();
            
            $perguntaIds = Pergunta::where('formulario_id', $formularioId)->pluck('id');
            $respostasUsuario = Resposta::where('user_id', $userId)
                ->whereIn('pergunta_id', $perguntaIds)
                ->get()
                ->keyBy('pergunta_id');
            
            // Buscar FAPS
            $variavelFaps = $variaveis->firstWhere(function($v) {
                return in_array(strtoupper($v->tag), ['FAPS', 'FAPS']);
            });
            
            if (!$variavelFaps) {
                $this->error("Variable FAPS no encontrada");
                return 1;
            }
            
            // Calcular score
            $pontuacao = 0;
            $perguntasComInversao = [48, 49, 50, 51, 52, 53, 54, 55, 78, 79, 81, 82, 83, 88, 90, 92, 93, 94, 95, 96, 97];
            
            foreach ($variavelFaps->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $valorOriginal = (int)$resposta->valor_resposta;
                    $necesitaInversion = in_array($pergunta->id, $perguntasComInversao, true);
                    $valorUsado = $necesitaInversion ? (6 - $valorOriginal) : $valorOriginal;
                    $pontuacao += $valorUsado;
                }
            }
            
            // Clasificar
            $faixa = 'Baixa';
            if ($pontuacao > $variavelFaps->M) {
                $faixa = 'Alta';
            } elseif ($pontuacao > $variavelFaps->B) {
                $faixa = 'Moderada';
            }
            
            $this->info("📊 Resultado:");
            $this->info("   Score: {$pontuacao}");
            $this->info("   Rangos: B={$variavelFaps->B}, M={$variavelFaps->M}, A={$variavelFaps->A}");
            $this->info("   Clasificación: {$faixa}");
            $this->info("");
            
            // Verificar lógica
            $this->info("🔍 Verificación:");
            if ($pontuacao <= $variavelFaps->B) {
                $this->info("   {$pontuacao} ≤ {$variavelFaps->B} → Faixa Baixa");
            } elseif ($pontuacao <= $variavelFaps->M) {
                $this->info("   {$pontuacao} > {$variavelFaps->B} y {$pontuacao} ≤ {$variavelFaps->M} → Faixa Moderada ✅");
            } else {
                $this->info("   {$pontuacao} > {$variavelFaps->M} → Faixa Alta");
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}

