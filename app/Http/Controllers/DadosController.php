<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analise;
use App\Models\Cliente;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Formulario;
use App\Models\Pergunta;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;
use App\Models\UsuarioFormulario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Traits\CalculaEjesAnaliticos;


class DadosController extends Controller
{
    use CalculaEjesAnaliticos;
    public function index()
    {
        $user = Auth::user();
        $cliente = Cliente::find($user->cliente_id);
        return view('dados.index', compact('user', 'cliente'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = User::findOrFail($request->id);
        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {

            if ($user->avatar && $user->avatar !== 'img/user.png') {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        $mensagem = 'Perfil <strong>' . $user->email . '</strong> atualizado com sucesso!';
        return redirect()->route('dados.index')->with('msgSuccess', $mensagem);
    }

    public function updatePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('msgError', ' A "Nova Senha" está diferente da confirmação.');
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('msgError', ' A senha "Atual" está incorreta.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('msgSuccess', 'Senha alterada com sucesso!');
    }

    public function updateUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;

        $user->usuario = $request->usuario_hidden;
        $user->gestor = $request->gestor_hidden;
        $user->save();

        $mensagem = 'Usuário <strong>' . $user->email . '</strong> atualizado com sucesso!';
        return redirect()->route('usuarios.cliente')->with('msgSuccess', $mensagem);
    }

    public function questionariosUsuario()
    {
        $user = Auth::user();
        $questionarios = UsuarioFormulario::with(['formulario.perguntas', 'formulario.etapas', 'midia'])
            ->where('usuario_id', $user->id)
            ->get();

        foreach ($questionarios as $formulario) {
            $perguntas = $formulario->formulario->perguntas;
            $etapas = $formulario->formulario->etapas;

            $respostasUsuario = \App\Models\Resposta::where('user_id', $user->id)
                ->whereIn('pergunta_id', $perguntas->pluck('id'))
                ->get()
                ->keyBy('pergunta_id');

            $etapaAtual = null;
            foreach ($etapas as $etapa) {
                $perguntasEtapa = $perguntas->whereBetween('id', [$etapa->de, $etapa->ate]);
                $todasRespondidas = $perguntasEtapa->every(function ($pergunta) use ($respostasUsuario) {
                    return isset($respostasUsuario[$pergunta->id]);
                });
                if (!$todasRespondidas) {
                    $etapaAtual = $etapa;
                    break;
                }
            }

            if (!$etapaAtual) {
                $etapaAtual = $etapas->last();
            }

            $formulario->etapa_atual_numero = $etapaAtual ? $etapaAtual->etapa : null;
            $formulario->etapa_atual_nome = $etapaAtual ? 'Etapa ' . $etapaAtual->etapa : 'Sem Etapa';
        }

        return view('participante.index', compact('user', 'questionarios'));
    }

    public function questionarioEditar($id)
    {
        $user = Auth::user();
        $formulario = Formulario::with(['perguntas', 'etapas'])->find($id);

        $perguntas = $formulario->perguntas;

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        // Fluxo para encontrar a primeira etapa não respondida
        $etapaAtual = null;
        foreach ($formulario->etapas as $etapa) {
            $perguntasEtapa = $perguntas->whereBetween('id', [$etapa->de, $etapa->ate]);
            $todasRespondidas = $perguntasEtapa->every(function ($pergunta) use ($respostasUsuario) {
                return isset($respostasUsuario[$pergunta->id]);
            });
            if (!$todasRespondidas) {
                $etapaAtual = $etapa;
                break;
            }
        }

        // Se todas as etapas foram respondidas, mostra a última
        if (!$etapaAtual) {
            $etapaAtual = $formulario->etapas->last();
        }

        return view('participante.formulario', compact('user', 'formulario', 'respostasUsuario', 'etapaAtual'));
    }

    public function salvarRespostas(Request $request)
    {
        $userId = Auth::user()->id;
        $formularioId = $request->input('formulario_id');
        $respostas = $request->input('respostas', []);
        $etapaDe = (int)$request->input('etapa_de');
        $etapaAte = (int)$request->input('etapa_ate');
        $etapaAtualNumero = $request->input('etapa_atual');

        // Salvar ou atualizar respostas
        foreach ($respostas as $perguntaId => $valorResposta) {
            $resposta = Resposta::where('user_id', $userId)
                                ->where('pergunta_id', $perguntaId)
                                ->first();

            if ($resposta) {
                $resposta->valor_resposta = (int)$valorResposta;
                $resposta->save();
            } else {
                Resposta::create([
                    'user_id' => $userId,
                    'pergunta_id' => $perguntaId,
                    'valor_resposta' => (int)$valorResposta,
                ]);
            }
        }

        $formulario = Formulario::with('perguntas')->find($formularioId);
        $totalPerguntas = $formulario->perguntas->count();
        
        // Obtém IDs das perguntas do formulário
        $perguntasIds = $formulario->perguntas->pluck('id')->toArray();

        // Busca todas as respostas do usuário para este formulário
        $respostasUsuario = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $perguntasIds)
            ->whereNotNull('valor_resposta') // Só conta respostas com valor válido
            ->get()
            ->keyBy('pergunta_id');

        // Verifica se todas as perguntas têm respostas válidas
        $todasRespondidas = true;
        foreach ($formulario->perguntas as $pergunta) {
            if (!isset($respostasUsuario[$pergunta->id]) || $respostasUsuario[$pergunta->id]->valor_resposta === null) {
                $todasRespondidas = false;
                break;
            }
        }

        $respondidas = $respostasUsuario->count();
        $percentual = $totalPerguntas > 0 ? round(($respondidas / $totalPerguntas) * 100) : 0;

        // Atualizar status do formulario
        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $userId)
            ->where('formulario_id', $formularioId)
            ->first();

        if ($usuarioFormulario) {
            // Atualiza status baseado na verificação completa
            if ($todasRespondidas && $totalPerguntas > 0 && $respondidas == $totalPerguntas) {
                $usuarioFormulario->status = 'completo';
            } else {
                $usuarioFormulario->status = 'pendente';
            }
            $usuarioFormulario->updated_at = now();
            $usuarioFormulario->save();
        }

        // Verificar se todas as perguntas da etapa atual foram respondidas
        $perguntasEtapaAtual = $formulario->perguntas->whereBetween('id', [$etapaDe, $etapaAte]);
        $etapaRespondida = $perguntasEtapaAtual->every(function ($pergunta) use ($respostasUsuario) {
            return isset($respostasUsuario[$pergunta->id]);
        });

        if ($usuarioFormulario && $usuarioFormulario->status === 'completo') {
            return response()->json([
                'status' => 'formulario_concluido',
                'percentual' => 100
            ]);
        } elseif ($etapaRespondida) {
            return response()->json([
                'status' => 'etapa_concluida',
                'etapa' => $etapaAtualNumero,
                'percentual' => $percentual
            ]);
        } else {
            return response()->json([
                'status' => 'etapa_incompleta',
                'etapa' => $etapaAtualNumero,
                'percentual' => $percentual
            ]);
        }
    }

    public function relatorioShow(Request $request)
    {
        try {
            $validated = $request->validate([
                'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
                'usuario_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $formularioId = $validated['formulario_id'];
            $usuarioId = $validated['usuario_id'];

            $user = User::find($usuarioId);
            if (!$user) {
                \Log::error('Usuario no encontrado en relatorioShow', ['usuario_id' => $usuarioId]);
                return redirect()->back()->with('msgError', 'Usuario no encontrado.');
            }

            $formulario = Formulario::with('perguntas')->find($formularioId);
            if (!$formulario) {
                \Log::error('Formulario no encontrado en relatorioShow', ['formulario_id' => $formularioId]);
                return redirect()->back()->with('msgError', 'Formulario no encontrado.');
            }

            // Validar que el formulario tenga preguntas
            if ($formulario->perguntas->isEmpty()) {
                \Log::error('Formulario sin preguntas en relatorioShow', ['formulario_id' => $formularioId]);
                return redirect()->back()->with('msgError', 'El formulario no tiene preguntas asociadas.');
            }

            $perguntaIds = $formulario->perguntas->pluck('id')->toArray();
            
            // Validar que haya IDs de preguntas
            if (empty($perguntaIds)) {
                \Log::error('No se pudieron obtener IDs de preguntas', ['formulario_id' => $formularioId]);
                return redirect()->back()->with('msgError', 'Error al cargar las preguntas del formulario.');
            }

        // Obtener respuestas del usuario - VERIFICAR QUE SE OBTENGAN CORRECTAMENTE
        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $perguntaIds)
            ->get()
            ->keyBy('pergunta_id');

        // Log para verificar respuestas obtenidas
        \Log::info('🔍 RESPUESTAS OBTENIDAS PARA CÁLCULOS', [
            'usuario_id' => $user->id,
            'formulario_id' => $formularioId,
            'total_perguntas_formulario' => count($perguntaIds),
            'total_respostas_encontradas' => $respostasUsuario->count(),
            'respostas_con_valor' => $respostasUsuario->filter(function($r) {
                return $r->valor_resposta !== null;
            })->count(),
            'primeras_5_respostas' => $respostasUsuario->take(5)->map(function($r) {
                return [
                    'pergunta_id' => $r->pergunta_id,
                    'valor_resposta' => $r->valor_resposta
                ];
            })->values()->toArray()
        ]);

        // Cargar variables con preguntas, asegurando que se carguen todos los campos de las preguntas
        // IMPORTANTE: Cargar el campo 'id' es crítico para la inversión
        $variaveis = Variavel::with(['perguntas' => function($query) {
            $query->select('perguntas.id', 'perguntas.formulario_id', 'perguntas.numero_da_pergunta', 'perguntas.pergunta');
        }])
            ->where('formulario_id', $formulario->id)
            ->get();
        
        // Verificar que las preguntas tengan el campo 'id' cargado
        foreach ($variaveis as $variavel) {
            foreach ($variavel->perguntas as $pergunta) {
                if (!isset($pergunta->id)) {
                    \Log::error('Pregunta sin ID cargado', [
                        'variavel' => $variavel->tag,
                        'pergunta_numero' => $pergunta->numero_da_pergunta ?? 'N/A'
                    ]);
                }
            }
        }

        // Validar que haya variables
        if ($variaveis->isEmpty()) {
            \Log::error('Formulario sin variables en relatorioShow', ['formulario_id' => $formularioId]);
            return redirect()->back()->with('msgError', 'El formulario no tiene variables asociadas.');
        }

        \Log::info('Iniciando cálculo de puntuaciones', [
            'formulario_id' => $formularioId,
            'usuario_id' => $usuarioId,
            'total_variaveis' => $variaveis->count()
        ]);

        // Calcular puntuaciones para TODAS las dimensiones (EXEM, REPR, DECI, FAPS, EXTR, ASMO)
        // Todas usan la misma lógica de inversión basada en numero_da_pergunta
        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            $totalRespostas = 0;
            
            // Verificar si la variable tiene preguntas asociadas
            if ($variavel->perguntas->isEmpty()) {
                continue; // Saltar variables sin preguntas
            }
            
            // Calcular puntuación basada en las respuestas
            // IMPORTANTE: La misma lógica se aplica para todas las dimensiones
            $preguntasProcesadas = [];
            $preguntasSinResposta = [];
            
            foreach ($variavel->perguntas as $pergunta) {
                // IMPORTANTE: Verificar que la respuesta se obtenga correctamente
                $resposta = $respostasUsuario->get($pergunta->id);
                
                if (!$resposta) {
                    $preguntasSinResposta[] = [
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A'
                    ];
                    \Log::warning('⚠️ Pregunta sin respuesta encontrada', [
                        'variavel' => $variavel->tag,
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A',
                        'total_respostas_disponibles' => $respostasUsuario->count()
                    ]);
                    continue;
                }
                
                if ($resposta->valor_resposta === null) {
                    $preguntasSinResposta[] = [
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A',
                        'razon' => 'valor_resposta es NULL'
                    ];
                    \Log::warning('⚠️ Pregunta con respuesta NULL', [
                        'variavel' => $variavel->tag,
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A'
                    ]);
                    continue;
                }
                
                // Aplicar la misma lógica de inversión para todas las dimensiones
                // Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
                // Preguntas normales: valor sin cambios
                $valorResposta = $this->obterValorRespostaComInversao($resposta, $pergunta);
                if ($valorResposta !== null) {
                    $valorOriginal = (int)$resposta->valor_resposta;
                    $pontuacao += $valorResposta;
                    $totalRespostas++;
                    
                    $preguntasProcesadas[] = [
                        'pergunta_id' => $pergunta->id,
                        'numero_da_pergunta' => $pergunta->numero_da_pergunta ?? 'N/A',
                        'valor_original' => $valorOriginal,
                        'valor_usado' => $valorResposta
                    ];
                }
            }
            
            // Log detallado de la variable
            \Log::info('📊 Cálculo de variable completado', [
                'variavel' => $variavel->tag,
                'variavel_nome' => $variavel->nome,
                'total_preguntas_asociadas' => $variavel->perguntas->count(),
                'preguntas_procesadas' => count($preguntasProcesadas),
                'preguntas_sin_resposta' => count($preguntasSinResposta),
                'preguntas_sin_resposta_detalle' => $preguntasSinResposta,
                'pontuacao_final' => $pontuacao,
                'total_respostas_usadas' => $totalRespostas
            ]);
            
            // Solo agregar si hay al menos una respuesta válida
            if ($totalRespostas === 0) {
                continue; // Saltar variables sin respuestas
            }

            // Validar que la variable tenga los campos necesarios
            $b = is_numeric($variavel->B) ? (float)$variavel->B : 0;
            $m = is_numeric($variavel->M) ? (float)$variavel->M : 0;
            $a = is_numeric($variavel->A) ? (float)$variavel->A : ($m + max(0, ($m - $b)));
            
            // Asegurar que la puntuación sea numérica
            $pontuacaoNumerica = is_numeric($pontuacao) ? (float)$pontuacao : 0;
            
            $faixa = $this->classificarPontuacao($pontuacaoNumerica, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? 'Sem dados.';
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? 'Sem dados.';
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? 'Sem dados.';
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            // Mapear tags a formato estándar (mayúsculas)
            $tagMapeado = strtoupper($variavel->tag ?? '');
            // Asegurar que los tags coincidan con el CSV
            $tagMap = [
                'EXEM' => 'EXEM',
                'REPR' => 'REPR',
                'DECI' => 'DECI',
                'FAPS' => 'FAPS',
                'EXTR' => 'EXTR',
                'ASMO' => 'ASMO',
            ];
            $tagFinal = $tagMap[$tagMapeado] ?? $tagMapeado;
            
            $pontuacoes[] = [
                'tag' => $tagFinal,
                'nome' => $variavel->nome ?? 'Sin nombre',
                'valor' => $pontuacaoNumerica,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
                'b' => $b,
                'm' => $m,
                'a' => $a,
            ];
            
            \Log::info('Puntuación calculada para variable', [
                'variavel_tag_original' => $variavel->tag,
                'variavel_tag_mapeado' => $tagMapeado,
                'variavel_tag_final' => $tagFinal,
                'variavel_nome' => $variavel->nome,
                'pontuacao_final' => $pontuacaoNumerica,
                'total_respostas' => $totalRespostas,
                'faixa' => $faixa
            ]);
        }
        
        \Log::info('✅ CÁLCULO COMPLETADO - Puntuaciones finales', [
            'total_pontuacoes' => count($pontuacoes),
            'pontuacoes' => $pontuacoes
        ]);

        $analise = Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        if (!$analise) {
            $prompt = $this->gerarPrompt($user, $variaveis, $pontuacoes);

            try {
                $analiseTexto = $this->gerarAnaliseViaOpenAI($prompt);
            } catch (\Throwable $e) {
                $analiseTexto = null;
            }

            if ($analiseTexto) {
                $analise = Analise::create([
                    'user_id' => $usuarioId,
                    'formulario_id' => $formularioId,
                    'texto' => $analiseTexto,
                ]);
            } else {
                $analiseTexto = 'A análise está em processamento. Em breve ela será disponibilizada neste relatório.';
            }
        } else {
            $analiseTexto = $analise->texto;
        }

        $analiseData = $analise?->created_at;

        // === FORMATAÇÃO DE MARCAÇÕES BÁSICAS DO TEXTO GERADO ===
        $analiseHtml = nl2br(e($analiseTexto));
        $analiseHtml = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $analiseHtml);
        $analiseHtml = preg_replace('/###\s?(.*)/', '<h4>$1</h4>', $analiseHtml);

        // === CALCULAR ÍNDICES EE, PR, SO DIRECTAMENTE DESDE RESPOSTAS ===
        $indices = $this->calcularIndicesDesdeRespostas($respostasUsuario, $formulario->id);
        
        // Validar que los índices se calcularon correctamente
        if (!isset($indices['EE']) || !isset($indices['PR']) || !isset($indices['SO'])) {
            \Log::error('Error calculando índices', [
                'indices' => $indices,
                'formulario_id' => $formularioId,
                'usuario_id' => $usuarioId
            ]);
            $indices = [
                'EE' => $indices['EE'] ?? 0,
                'PR' => $indices['PR'] ?? 0,
                'SO' => $indices['SO'] ?? 0
            ];
        }
        
        // === CALCULAR EJES ANALÍTICOS Y IID ===
        // Preparar pontuacoes para o cálculo (garantir formato correto)
        $pontuacoesParaCalculo = [];
        foreach ($pontuacoes as $ponto) {
            $pontuacoesParaCalculo[] = [
                'tag' => $ponto['tag'],
                'valor' => $ponto['valor'],
                'faixa' => $ponto['faixa']
            ];
        }
        
        try {
            $ejesAnaliticos = $this->calcularEjesAnaliticos($pontuacoesParaCalculo, $indices);
            $iid = $this->calcularIID($ejesAnaliticos);
            $nivelRisco = $this->determinarNivelRisco($iid);
            $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        } catch (\Exception $e) {
            \Log::error('Error calculando ejes analíticos o IID', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'formulario_id' => $formularioId,
                'usuario_id' => $usuarioId
            ]);
            // Valores por defecto en caso de error
            $ejesAnaliticos = [
                'eixo1' => ['total' => 0],
                'eixo2' => ['total' => 0],
                'eixo3' => ['total' => 0]
            ];
            $iid = 0;
            $nivelRisco = $this->determinarNivelRisco(0);
            $planDesenvolvimento = $this->getPlanDesenvolvimento($nivelRisco);
        }
        
        // Calcular promedio de índices (sin porcentaje) para mostrar en la puntuación
        // Asegurar que los índices sean numéricos antes de calcular
        $ee = is_numeric($indices['EE']) ? (float)$indices['EE'] : 0;
        $pr = is_numeric($indices['PR']) ? (float)$indices['PR'] : 0;
        $so = is_numeric($indices['SO']) ? (float)$indices['SO'] : 0;
        $promedioIndices = ($ee + $pr + $so) / 3;

        // Usar la nueva vista E.MO.TI.VE si existe, sino la antigua
        if (view()->exists('participante.relatorio_emotive')) {
            return view('participante.relatorio_emotive', compact(
                'formulario',
                'respostasUsuario',
                'pontuacoes',
                'variaveis',
                'user',
                'analiseTexto',
                'analiseHtml',
                'analiseData',
                'ejesAnaliticos',
                'iid',
                'nivelRisco',
                'planDesenvolvimento',
                'promedioIndices'
            ));
        }
        
        return view('participante.relatorio', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData',
            'ejesAnaliticos',
            'iid',
            'nivelRisco',
            'planDesenvolvimento',
            'promedioIndices'
        ));
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación en relatorioShow', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error general en relatorioShow', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('msgError', 'Ocurrió un error al generar el relatório. Por favor, intente nuevamente.');
        }
    }


    private function gerarPrompt($user, $variaveis, $pontuacoes): string
    {
        $prompt = "Olá, {$user->name}. Você é um assistente especializado em saúde emocional, bem-estar no trabalho e aconselhamento positivo.  
    Escreva um texto motivacional (500 palavras), explicando seus resultados, destacando pontos fortes, apontando vulnerabilidades e oferecendo orientações práticas de autocuidado e crescimento.\n\n";

        foreach ($variaveis as $variavel) {
            $ponto = collect($pontuacoes)->firstWhere('tag', strtoupper($variavel->tag))['valor'] ?? null;
            if ($ponto !== null) {
                $faixa = $this->classificarPontuacao($ponto, $variavel);
                $prompt .= "{$variavel->nome} ({$variavel->tag}): {$ponto} pontos, Faixa {$faixa}.\n";
            }
        }

        return $prompt;
    }

    private function gerarAnaliseViaOpenAI(string $prompt): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post(env('OPENAI_API_URL'), [
            'model' => env('OPENAI_MODEL', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'Você é um assistente especializado em saúde emocional e aconselhamento motivacional.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1500,
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'] . "\n\nAssinado por FELLIPELLI";
        }

        return null;
    }

    private function classificarPontuacao($valor, $variavel): string
    {
        // Validar que el valor sea numérico
        $valorNumerico = is_numeric($valor) ? (float)$valor : 0;
        
        // Validar que la variable tenga los campos necesarios
        $b = is_numeric($variavel->B) ? (float)$variavel->B : 0;
        $m = is_numeric($variavel->M) ? (float)$variavel->M : ($b + 1);
        
        if ($valorNumerico <= $b) {
            return 'Baixa';
        } elseif ($valorNumerico <= $m) {
            return 'Moderada';
        } else {
            return 'Alta';
        }
    }

    /**
     * Obtiene el valor de respuesta aplicando inversión si la pregunta lo requiere
     * 
     * IMPORTANTE: Esta lógica se aplica uniformemente para TODAS las dimensiones:
     * EXEM, REPR, DECI, FAPS, EXTR, ASMO
     * 
     * Las preguntas invertidas se identifican por su TEXTO para evitar problemas con IDs
     * 
     * Lógica de inversión (igual para todas las dimensiones):
     * - Preguntas invertidas: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
     *   (En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo)
     * - Preguntas normales: valor sin cambios
     */
    private function obterValorRespostaComInversao($resposta, $pergunta): ?int
    {
        if (!$resposta || $resposta->valor_resposta === null) {
            return null;
        }

        $valor = $resposta->valor_resposta;
        
        // Asegurar que la pregunta existe
        if (!$pergunta) {
            \Log::warning('Pregunta es null en obterValorRespostaComInversao');
            return $valor;
        }
        
        // Verificar si esta pregunta requiere inversión (usando helper por texto)
        $necesitaInversion = \App\Helpers\PerguntasInvertidasHelper::precisaInversao($pergunta);
        
        if ($necesitaInversion) {
            // Invertir el valor: 0→6, 1→5, 2→4, 3→3, 4→2, 5→1, 6→0
            // En preguntas invertidas: 0 es el valor más alto, 6 es el valor más bajo
            $valorInvertido = 6 - $valor;
            \Log::info('✅ APLICANDO INVERSIÓN', [
                'pergunta_id' => $pergunta->id,
                'texto_pergunta' => substr(trim($pergunta->pergunta ?? ''), 0, 50) . '...',
                'valor_original' => $valor,
                'valor_invertido' => $valorInvertido
            ]);
            return $valorInvertido;
        }
        
        \Log::debug('No se aplica inversión', [
            'pergunta_id' => $pergunta->id,
            'numero_da_pergunta' => $numeroPergunta,
            'valor' => $valor
        ]);
        
        return $valor;
    }


    /**
     * Verifica e corrige o status de um formulário baseado nas respostas
     */
    public function verificarStatusFormulario(Request $request)
    {
        $validated = $request->validate([
            'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $formularioId = $validated['formulario_id'];
        $usuarioId = $validated['usuario_id'];

        $formulario = Formulario::with('perguntas')->findOrFail($formularioId);
        $totalPerguntas = $formulario->perguntas->count();
        
        $perguntasIds = $formulario->perguntas->pluck('id')->toArray();

        $respostasUsuario = Resposta::where('user_id', $usuarioId)
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

        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        if (!$usuarioFormulario) {
            return response()->json([
                'success' => false,
                'message' => 'Formulário não encontrado para este usuário.'
            ], 404);
        }

        $statusAnterior = $usuarioFormulario->status;
        $respondidas = $respostasUsuario->count();

        if ($todasRespondidas && $totalPerguntas > 0 && $respondidas == $totalPerguntas) {
            $usuarioFormulario->status = 'completo';
            $usuarioFormulario->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status atualizado para "completo".',
                'status_anterior' => $statusAnterior,
                'status_atual' => 'completo',
                'total_perguntas' => $totalPerguntas,
                'respostas_encontradas' => $respondidas
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Formulário não está completo.',
                'status_atual' => $statusAnterior,
                'total_perguntas' => $totalPerguntas,
                'respostas_encontradas' => $respondidas,
                'perguntas_sem_resposta' => $perguntasSemResposta,
                'faltam' => $totalPerguntas - $respondidas
            ]);
        }
    }

}
