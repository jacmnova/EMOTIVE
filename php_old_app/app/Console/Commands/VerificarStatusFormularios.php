<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsuarioFormulario;
use App\Models\Formulario;
use App\Models\Resposta;

class VerificarStatusFormularios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'formulario:verificar-status {--usuario_id= : ID do usuário específico} {--formulario_id= : ID do formulário específico} {--corrigir : Corrige automaticamente os status incorretos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e corrige o status dos formulários baseado nas respostas dos usuários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->option('usuario_id');
        $formularioId = $this->option('formulario_id');
        $corrigir = $this->option('corrigir');

        $query = UsuarioFormulario::query();

        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }

        if ($formularioId) {
            $query->where('formulario_id', $formularioId);
        }

        $usuarioFormularios = $query->get();

        if ($usuarioFormularios->isEmpty()) {
            $this->error('Nenhum formulário encontrado com os critérios especificados.');
            return 1;
        }

        $this->info("Verificando {$usuarioFormularios->count()} formulário(s)...\n");

        $corrigidos = 0;
        $incorretos = 0;

        foreach ($usuarioFormularios as $usuarioFormulario) {
            $formulario = Formulario::with('perguntas')->find($usuarioFormulario->formulario_id);
            
            if (!$formulario) {
                $this->warn("Formulário ID {$usuarioFormulario->formulario_id} não encontrado.");
                continue;
            }

            $totalPerguntas = $formulario->perguntas->count();
            $perguntasIds = $formulario->perguntas->pluck('id')->toArray();

            $respostasUsuario = Resposta::where('user_id', $usuarioFormulario->usuario_id)
                ->whereIn('pergunta_id', $perguntasIds)
                ->whereNotNull('valor_resposta')
                ->get()
                ->keyBy('pergunta_id');

            $todasRespondidas = true;
            $perguntasSemResposta = [];
            
            foreach ($formulario->perguntas as $pergunta) {
                if (!isset($respostasUsuario[$pergunta->id]) || $respostasUsuario[$pergunta->id]->valor_resposta === null) {
                    $todasRespondidas = false;
                    $perguntasSemResposta[] = $pergunta->numero_da_pergunta ?? $pergunta->id;
                }
            }

            $respondidas = $respostasUsuario->count();
            $deveriaEstarCompleto = $todasRespondidas && $totalPerguntas > 0 && $respondidas == $totalPerguntas;
            $statusAtual = $usuarioFormulario->status;

            if ($deveriaEstarCompleto && $statusAtual !== 'completo') {
                $incorretos++;
                $this->warn("❌ Usuário ID {$usuarioFormulario->usuario_id} - Formulário ID {$usuarioFormulario->formulario_id}");
                $this->line("   Status atual: {$statusAtual}");
                $this->line("   Status esperado: completo");
                $this->line("   Total perguntas: {$totalPerguntas}");
                $this->line("   Respostas encontradas: {$respondidas}");

                if ($corrigir) {
                    $usuarioFormulario->status = 'completo';
                    $usuarioFormulario->save();
                    $this->info("   ✅ Status corrigido para 'completo'");
                    $corrigidos++;
                }
                $this->line('');
            } elseif (!$deveriaEstarCompleto && $statusAtual === 'completo') {
                $incorretos++;
                $this->warn("⚠️  Usuário ID {$usuarioFormulario->usuario_id} - Formulário ID {$usuarioFormulario->formulario_id}");
                $this->line("   Status atual: {$statusAtual}");
                $this->line("   Status esperado: pendente");
                $this->line("   Total perguntas: {$totalPerguntas}");
                $this->line("   Respostas encontradas: {$respondidas}");
                $this->line("   Perguntas sem resposta: " . implode(', ', $perguntasSemResposta));

                if ($corrigir) {
                    $usuarioFormulario->status = 'pendente';
                    $usuarioFormulario->save();
                    $this->info("   ✅ Status corrigido para 'pendente'");
                    $corrigidos++;
                }
                $this->line('');
            } else {
                $this->line("✓ Usuário ID {$usuarioFormulario->usuario_id} - Formulário ID {$usuarioFormulario->formulario_id}: Status correto ({$statusAtual})");
            }
        }

        $this->line("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Total verificados: {$usuarioFormularios->count()}");
        $this->warn("Status incorretos encontrados: {$incorretos}");
        
        if ($corrigir) {
            $this->info("Status corrigidos: {$corrigidos}");
        } else {
            $this->comment("Use a opção --corrigir para corrigir automaticamente os status incorretos");
        }

        return 0;
    }
}
