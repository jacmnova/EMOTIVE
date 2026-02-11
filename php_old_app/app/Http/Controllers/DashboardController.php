<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Formulario;
use App\Models\UsuarioFormulario;
use App\Models\Resposta;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarios = User::count();
        $clientes = Cliente::count();
        $formularios = Formulario::count();
        $formulariosCompletados = UsuarioFormulario::where('status', 'completo')->count();
        $formulariosPendentes = UsuarioFormulario::where('status', 'pendente')->count();
        $formulariosNovos = UsuarioFormulario::where('status', 'novo')->count();

        return view('dashboard.index', compact(
            'usuarios',
            'clientes',
            'formularios',
            'formulariosCompletados',
            'formulariosPendentes',
            'formulariosNovos'
        ));
    }
}
