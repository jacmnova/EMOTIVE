<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Formulario;
use Illuminate\Http\Request;
use App\Models\UsuarioFormulario;
use App\Notifications\UsuarioCadastrado;

use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index()
    {
        $perfisArray = User::with('cliente')->get();
        $perfis = $perfisArray->toArray();
        return view('usuarios.index', compact('perfis'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('usuarios.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $senhaPadrao = 'mudar@123';

        $dados = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($senhaPadrao),
            'email_verified_at' => now(),
            'usuario' => $request->boolean('usuario'),
            'gestor' => $request->boolean('gestor'),
        ];

        // Adiciona cliente_id somente se preenchido
        if ($request->filled('cliente_id')) {
            $dados['cliente_id'] = $request->cliente_id;
        }

        $user = User::create($dados);

        $user->notify(new UsuarioCadastrado($senhaPadrao));

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show($id)
    {
        $usuario = User::find($id);
        $cliente = Cliente::find($usuario->cliente_id);
        $quantidadeFormularios = $usuario->getQuantidadeFormulariosAttribute();
        $quantidadePendente = $usuario->getQuantidadeFormulariosPendentesAttribute();
        $quantidadeFinalizado = $usuario->getQuantidadeFormulariosFinalizadosAttribute();
        $questionarios = UsuarioFormulario::with(['formulario'])->where('usuario_id',$usuario->id)->get();
        return view('usuarios.show', compact('usuario', 'cliente', 'quantidadeFormularios','quantidadePendente','quantidadeFinalizado','questionarios'));
    }

    // Mostrar formulário de edição
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $clientes = Cliente::all();
        $formularios = Formulario::all();
        $questionarios = UsuarioFormulario::with(['formulario'])->where('usuario_id',$usuario->id)->get();
        return view('usuarios.edit', compact('usuario','clientes','formularios','questionarios'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;

        $user->sa = $request->sa_hidden;
        $user->admin = $request->admin_hidden;
        $user->usuario = $request->usuario_hidden;
        $user->gestor = $request->gestor_hidden;

        $user->cliente_id = $request->cliente_id;
        $user->save();

        $mensagem = 'Usuário <strong>' . $user->email . '</strong> atualizado com sucesso!';
        return redirect()->route('usuarios.index')->with('msgSuccess', $mensagem);
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function status(Request $request)
    {
        try {
            $id = $request->input('status_id');
            $perfil = User::findOrFail($id);

            if($perfil->ativo === true){
                $perfil->ativo = false;
            }else{
                $perfil->ativo = true;
            }
            $perfil->save();

            $mensagem = 'Status do Usuário <strong>' . $perfil->email . '</strong> foi alterado com sucesso!';
            return redirect()->route('usuarios.index')->with('msgSuccess', $mensagem);

        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('msgError', $e->getMessage());
        }
    }
}
