<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;
use App\Models\UsuarioFormulario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Notifications\UsuarioCadastrado;
use Illuminate\Support\Facades\Validator;

class GestorController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function usuariosCliente()
    {
        $user = Auth::user();
        $usuarios = User::where('cliente_id',$user->cliente_id)->get(); 
        return view('gestao.index', compact('user', 'usuarios'));
    }

    public function usuariosEditar($id)
    {
        $usuario = User::findOrFail($id);
        $formularios = ClienteFormulario::with(['cliente', 'formulario'])->where('cliente_id',$usuario->cliente_id)->get();
        $questionarios = UsuarioFormulario::with(['formulario'])->where('usuario_id',$id)->get();
        return view('gestao.edit', compact('usuario','formularios','questionarios'));
    }

    public function listaFormularios()
    {
        $formularios = ClienteFormulario::where('cliente_id', Auth::user()->cliente_id)->get();
        return view('gestao.formularios', compact('formularios'));
    }

    public function create_cliente()
    {
        $cliente_id = Auth::user()->cliente_id;
        return view('usuarios.create_cliente', compact('cliente_id'));
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

        return redirect()->route('usuarios.cliente')->with('success', 'Usuário criado com sucesso!');
    }

}
