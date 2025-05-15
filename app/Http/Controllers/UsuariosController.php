<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\User;

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
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show($id)
    {
        $usuario = User::find($id);
        $cliente = Cliente::find($usuario->cliente_id);
        $quantidadeFormularios = $usuario->getQuantidadeFormulariosAttribute();
        return view('usuarios.show', compact('usuario', 'cliente', 'quantidadeFormularios'));
    }

    // Mostrar formulário de edição
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $clientes = Cliente::all();
        return view('usuarios.edit', compact('usuario','clientes'));
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
