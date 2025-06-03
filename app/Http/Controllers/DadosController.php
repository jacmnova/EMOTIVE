<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        // Salvar ou atualizar respostas
        foreach ($respostas as $perguntaId => $valorResposta) {
            $resposta = Resposta::where('user_id', $userId)
                                ->where('pergunta_id', $perguntaId)
                                ->first();

            if ($resposta) {
                $resposta->valor_resposta = (int) $valorResposta;
                $resposta->save();
            } else {
                Resposta::create([
                    'user_id' => $userId,
                    'pergunta_id' => $perguntaId,
                    'valor_resposta' => (int) $valorResposta,
                ]);
            }
        }

        // Atualizar status do formulário
        $usuarioFormulario = UsuarioFormulario::where('usuario_id', $userId)
            ->where('formulario_id', $formularioId)
            ->first();

        if ($usuarioFormulario) {
            $usuarioFormulario->status = 'pendente';
            $usuarioFormulario->updated_at = now();

            // Checar se todas as perguntas foram respondidas
            $formulario = \App\Models\Formulario::with('perguntas')->find($formularioId);
            $totalPerguntas = $formulario->perguntas->count();

            $respostasUsuario = \App\Models\Resposta::where('user_id', $userId)
                ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
                ->count();

            if ($respostasUsuario >= $totalPerguntas && $totalPerguntas > 0) {
                $usuarioFormulario->status = 'completo';
            }

            $usuarioFormulario->save();
        }

        $mensagem = ($usuarioFormulario->status === 'completo') 
            ? 'Formulário finalizado automaticamente com sucesso!'
            : 'Respostas salvas com sucesso!';

        return redirect()->route('questionarios.usuario')->with('msgSuccess', $mensagem);
    }

    public function relatorioShow(Request $request)
    {
        $formularioId = $request->query('formulario_id');
        $usuarioId = $request->query('usuario_id');

        $user = \App\Models\User::find($usuarioId);
        $formulario = \App\Models\Formulario::with('perguntas.variaveis')->findOrFail($formularioId);

        $respostasUsuario = \App\Models\Resposta::where('user_id', $user->id)
            ->whereIn('pergunta_id', $formulario->perguntas->pluck('id'))
            ->get()
            ->keyBy('pergunta_id');

        $variaveis = \App\Models\Variavel::with('perguntas')
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
            $pontuacoes[] = [
                'tag' => strtoupper($variavel->tag),
                'valor' => $pontuacao,
            ];
        }

        // 🔍 Verifica se já existe análise salva
        $analise = \App\Models\Analise::where('user_id', $usuarioId)
            ->where('formulario_id', $formularioId)
            ->first();

        if (!$analise) {
            // Não existe → gera com API e salva
            $prompt = "Você é um assistente especializado em saúde emocional, bem-estar no trabalho e aconselhamento positivo.  
    Escreva um texto motivacional (500 palavras), diretamente para a pessoa que respondeu este questionário, explicando seus resultados, destacando pontos fortes, apontando vulnerabilidades e oferecendo orientações práticas de autocuidado e crescimento.\n\n";

            foreach ($variaveis as $variavel) {
                $ponto = collect($pontuacoes)->firstWhere('tag', strtoupper($variavel->tag))['valor'] ?? null;
                if ($ponto !== null) {
                    if ($ponto <= $variavel->B) {
                        $faixa = 'Baixa';
                    } elseif ($ponto <= $variavel->M) {
                        $faixa = 'Moderada';
                    } else {
                        $faixa = 'Alta';
                    }
                    $prompt .= "{$variavel->nome} ({$variavel->tag}): {$ponto} pontos, Faixa {$faixa}.\n";
                }
            }

            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente especializado em saúde emocional e aconselhamento motivacional.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            if ($response->successful()) {
                $analiseTexto = $response->json()['choices'][0]['message']['content'];

                // 💾 Salva no banco
                $analise = \App\Models\Analise::create([
                    'user_id' => $usuarioId,
                    'formulario_id' => $formularioId,
                    'texto' => $analiseTexto,
                ]);
            } else {
                $analiseTexto = 'Erro ao gerar análise: ' . $response->body();
            }
        } else {
            // Já existe no banco
            $analiseTexto = $analise->texto;
        }

        return view('participante.relatorio', compact(
            'formulario',
            'respostasUsuario',
            'pontuacoes',
            'variaveis',
            'user',
            'analiseTexto' // passa para a view
        ));
    }



}
