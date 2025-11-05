<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analise;
use App\Models\Cliente;
use App\Models\Resposta;
use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;
use App\Models\UsuarioFormulario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class DadosController extends Controller
{
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

        $respostasUsuario = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $respondidas = $respostasUsuario->count();
        $percentual = $totalPerguntas > 0 ? round(($respondidas / $totalPerguntas) * 100) : 0;

        // Atualizar status do formulario
        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $userId)
            ->where('formulario_id', $formularioId)
            ->first();

        if ($usuarioFormulario) {
            // Solo actualizar status si no está completo ya
            if ($usuarioFormulario->status !== 'completo') {
                $usuarioFormulario->status = 'pendente';
                $usuarioFormulario->updated_at = now();

                if ($respondidas >= $totalPerguntas && $totalPerguntas > 0) {
                    $usuarioFormulario->status = 'completo';
                }

                $usuarioFormulario->save();
            }
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
        $validated = $request->validate([
            'formulario_id' => ['required', 'integer', 'exists:formularios,id'],
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $formularioId = $validated['formulario_id'];
        $usuarioId = $validated['usuario_id'];

        $user = User::find($usuarioId);
        $formulario = Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formulario->id)
            ->get();

        $pontuacoes = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->get($pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }

            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa;
                    $badge = 'info';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada;
                    $badge = 'warning';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta;
                    $badge = 'danger';
                    break;
                default:
                    $recomendacao = 'Sem dados.';
                    $badge = 'secondary';
                    break;
            }

            $b = $variavel->B ?? 0;
            $m = $variavel->M ?? 0;
            $a = $variavel->A ?? ($m + ($m - $b));

            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'nome' => $variavel->nome,
                'valor' => $pontuacao,
                'faixa' => $faixa,
                'recomendacao' => $recomendacao,
                'badge' => $badge,
                'b' => $b,
                'm' => $m,
                'a' => $a,
            ];
        }

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

        return view('participante.relatorio', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto',
            'analiseHtml',
            'analiseData'
        ));
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
        if ($valor <= $variavel->B) {
            return 'Baixa';
        } elseif ($valor <= $variavel->M) {
            return 'Moderada';
        } else {
            return 'Alta';
        }
    }

    /**
     * Prepara los datos del reporte en formato JSON simplificado para enviar a la API de Python
     */
    private function prepararDatosParaRelatorio($userId, $formularioId): array
    {
        $user = User::find($userId);
        $formulario = Formulario::with('perguntas')->findOrFail($formularioId);
        
        // Obtener respuestas del usuario
        $respostasUsuario = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get();
        
        // Obtener variables con sus límites
        $variaveis = Variavel::with('perguntas')
            ->where('formulario_id', $formularioId)
            ->get();
        
        // Calcular puntuaciones y organizar por secciones para la API de Python
        $sections = [];
        foreach ($variaveis as $variavel) {
            $pontuacao = 0;
            foreach ($variavel->perguntas as $pergunta) {
                $resposta = $respostasUsuario->firstWhere('pergunta_id', $pergunta->id);
                if ($resposta) {
                    $pontuacao += $resposta->valor_resposta ?? 0;
                }
            }
            $faixa = $this->classificarPontuacao($pontuacao, $variavel);
            
            // Determinar recomendación según la faixa
            $recomendacao = '';
            switch ($faixa) {
                case 'Baixa':
                    $recomendacao = $variavel->r_baixa ?? '';
                    break;
                case 'Moderada':
                    $recomendacao = $variavel->r_moderada ?? '';
                    break;
                case 'Alta':
                    $recomendacao = $variavel->r_alta ?? '';
                    break;
            }
            
            // Construir el body de la sección con información detallada
            $body = "<h4>{$variavel->nome} ({$variavel->tag})</h4>";
            $body .= "<p><strong>Puntuación:</strong> {$pontuacao} puntos</p>";
            $body .= "<p><strong>Clasificación:</strong> <span class='badge badge-" . ($faixa == 'Baixa' ? 'info' : ($faixa == 'Moderada' ? 'warning' : 'danger')) . "'>{$faixa}</span></p>";
            $body .= "<p><strong>Límites:</strong> Baixa (≤{$variavel->B}), Moderada (≤{$variavel->M}), Alta (>{$variavel->M})</p>";
            if ($recomendacao) {
                $body .= "<div class='mt-3'><strong>Recomendación:</strong><br><p>{$recomendacao}</p></div>";
            }
            
            $sections[] = [
                'title' => $variavel->nome . " ({$variavel->tag})",
                'body' => $body
            ];
        }
        
        // Formato compatible con la API de Python de generación de documentos
        return [
            'template_id' => str_pad($formularioId, 3, '0', STR_PAD_LEFT), // Template ID basado en el ID del formulario (001, 002, etc.)
            'data' => [
                'header' => [
                    'title' => $formulario->nome . ' - ' . $formulario->label
                ],
                'welcome_screen' => [
                    'title' => 'Bienvenido, ' . $user->name,
                    'body' => '<p>Este es tu reporte personalizado del formulario <strong>' . $formulario->nome . '</strong>.</p><p>Fecha de generación: ' . now()->format('d/m/Y H:i') . '</p>',
                    'show_btn' => false,
                    'text_btn' => '',
                    'link_btn' => ''
                ],
                'explanation_screen' => [
                    'title' => 'Sobre este Reporte',
                    'body' => $formulario->descricao ?? '<p>Este reporte presenta el análisis de las dimensiones evaluadas.</p>',
                    'show_img' => false,
                    'img_link' => ''
                ],
                'respuestas' => [
                    'sections' => $sections
                ]
            ],
            'output_format' => 'both' // Genera tanto HTML como PDF
        ];
    }

    /**
     * Envía los datos del reporte a la API de Python
     * Retorna array con ['success' => bool, 'error' => string|null, 'datos' => array|null]
     */
    private function enviarDatosAPython($datos): array
    {
        $apiUrl = env('PYTHON_RELATORIO_API_URL', 'http://localhost:5000/generate');
        
        try {
            // Log del payload que se va a enviar
            \Log::info('Enviando datos a la API de Python', [
                'url' => $apiUrl,
                'payload' => $datos
            ]);
            
            // Enviar como JSON con headers correctos
            $response = Http::timeout(30)
                ->acceptJson()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($apiUrl, $datos);
            
            if ($response->successful()) {
                \Log::info('Datos enviados exitosamente a la API de Python', [
                    'template_id' => $datos['template_id'] ?? 'N/A',
                    'response' => $response->json()
                ]);
                return ['success' => true, 'error' => null, 'datos' => null];
            } else {
                $error = "Error HTTP {$response->status()}: " . $response->body();
                \Log::error('Error al enviar datos a la API de Python', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload_enviado' => $datos
                ]);
                return [
                    'success' => false,
                    'error' => $error,
                    'datos' => $datos
                ];
            }
        } catch (\Exception $e) {
            $error = "Excepción: " . $e->getMessage();
            \Log::error('Excepción al enviar datos a la API de Python', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload_intentado' => $datos
            ]);
            return [
                'success' => false,
                'error' => $error,
                'datos' => $datos
            ];
        }
    }

    /**
     * Genera el relatorio vía API de Python
     * Endpoint público para ser llamado desde el frontend
     */
    public function generarRelatorioAPI(Request $request)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
            'usuario_id' => 'required|integer|exists:users,id',
        ]);

        $userId = $request->input('usuario_id');
        $formularioId = $request->input('formulario_id');

        // Verificar que el usuario tiene permiso (solo puede generar su propio relatorio o ser admin)
        if (Auth::id() != $userId && !Auth::user()->admin) {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permiso para generar este relatorio.'
            ], 403);
        }

        try {
            // Preparar datos para la API
            $datosRelatorio = $this->prepararDatosParaRelatorio($userId, $formularioId);
            
            // Log para debug
            \Log::info('Datos preparados para la API de Python', [
                'user_id' => $userId,
                'formulario_id' => $formularioId,
                'datos_preparados' => $datosRelatorio
            ]);
            
            // Enviar a la API de Python
            $resultado = $this->enviarDatosAPython($datosRelatorio);
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Relatorio generado exitosamente vía API de Python.',
                    'data' => null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $resultado['error'],
                    'data' => $resultado['datos']
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error al generar relatorio vía API', [
                'user_id' => $userId,
                'formulario_id' => $formularioId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al generar relatorio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finalizar(Request $request)
    {
        $userId = Auth::user()->id;
        $formularioId = $request->input('f_formulario_id') ?? $request->input('formulario_id');
        
        if (!$formularioId) {
            return redirect()->back()->with('msgError', 'Formulário não identificado.');
        }

        // Verificar se todas as perguntas foram respondidas
        $formulario = Formulario::with('perguntas')->findOrFail($formularioId);
        $totalPerguntas = $formulario->perguntas->count();
        
        $respostasUsuario = Resposta::where('user_id', $userId)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');
        
        $respondidas = $respostasUsuario->count();
        
        // Atualizar status do formulario
        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $userId)
            ->where('formulario_id', $formularioId)
            ->first();
        
        if (!$usuarioFormulario) {
            return redirect()->back()->with('msgError', 'Formulário não encontrado.');
        }
        
        // Marcar como completo
        $usuarioFormulario->status = 'completo';
        $usuarioFormulario->save();
        
        // Generar JSON simplificado y enviar a la API de Python
        try {
            $datosRelatorio = $this->prepararDatosParaRelatorio($userId, $formularioId);
            $resultado = $this->enviarDadosAPython($datosRelatorio);
            
            // Si hay error, guardar los datos en la sesión para mostrar en el modal
            if (!$resultado['success']) {
                session()->flash('pythonApiError', true);
                session()->flash('pythonApiErrorData', json_encode($resultado['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                session()->flash('pythonApiErrorMessage', $resultado['error']);
            }
        } catch (\Exception $e) {
            // Log del error pero no interrumpir el flujo
            \Log::error('Error al preparar/enviar datos a la API de Python', [
                'user_id' => $userId,
                'formulario_id' => $formularioId,
                'error' => $e->getMessage(),
            ]);
            
            // Guardar error en sesión para mostrar en modal
            session()->flash('pythonApiError', true);
            session()->flash('pythonApiErrorMessage', 'Error al preparar datos: ' . $e->getMessage());
        }
        
        // Tentar gerar analise automaticamente (se não existir)
        $analise = Analise::where('user_id', $userId)
            ->where('formulario_id', $formularioId)
            ->first();
        
        if (!$analise) {
            // Gerar analise em background (puede tardar)
            try {
                $user = User::find($userId);
                $variaveis = Variavel::with('perguntas')
                    ->where('formulario_id', $formularioId)
                    ->get();
                
                $pontuacoes = [];
                foreach ($variaveis as $variavel) {
                    $pontuacao = 0;
                    foreach ($variavel->perguntas as $pergunta) {
                        $resposta = $respostasUsuario->get($pergunta->id);
                        if ($resposta) {
                            $pontuacao += $resposta->valor_resposta ?? 0;
                        }
                    }
                    $faixa = $this->classificarPontuacao($pontuacao, $variavel);
                    $pontuacoes[] = [
                        'tag' => strtoupper($variavel->tag),
                        'valor' => $pontuacao,
                        'faixa' => $faixa,
                    ];
                }
                
                $prompt = $this->gerarPrompt($user, $variaveis, $pontuacoes);
                $analiseTexto = $this->gerarAnaliseViaOpenAI($prompt);
                
                if ($analiseTexto && !str_contains($analiseTexto, 'Erro')) {
                    Analise::create([
                        'user_id' => $userId,
                        'formulario_id' => $formularioId,
                        'texto' => $analiseTexto,
                    ]);
                }
            } catch (\Exception $e) {
                // Si falla, la analise se generará cuando acceda al relatorio
                \Log::error('Error generando analise: ' . $e->getMessage());
            }
        }
        
        // Redirigir al relatorio
        return redirect()->route('relatorio.show', [
            'formulario_id' => $formularioId,
            'usuario_id' => $userId,
        ])->with('msgSuccess', 'Formulário finalizado com sucesso!');
    }

}
