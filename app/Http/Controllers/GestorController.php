<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;
use App\Models\UsuarioFormulario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
}
