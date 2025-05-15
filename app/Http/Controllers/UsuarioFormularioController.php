<?php

namespace App\Http\Controllers;

use App\Models\UsuarioFormulario;
use Illuminate\Http\Request;

class UsuarioFormularioController extends Controller
{
    public function index()
    {
        $registros = UsuarioFormulario::all();
        return view('usuario_formulario.index', compact('registros'));
    }

    public function create()
    {
        return view('usuario_formulario.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'usuario_id' => 'nullable|integer',
            'formulario_id' => 'nullable|integer',
        ]);
    
        $exists = UsuarioFormulario::where('usuario_id', $validatedData['usuario_id'])
            ->where('formulario_id', $validatedData['formulario_id'])
            ->exists();
    
        if ($exists) {
            return redirect()->back()->with('msgError', 'Este formulário já foi incluído para este usuário.');
        }
    
        UsuarioFormulario::create($validatedData);
    
        return redirect()->back()->with('msgSuccess', 'Formulário incluído com sucesso!');
    }

    public function show($id)
    {
        $registro = UsuarioFormulario::findOrFail($id);
        return view('usuario_formulario.show', compact('registro'));
    }

    public function edit($id)
    {
        $registro = UsuarioFormulario::findOrFail($id);
        return view('usuario_formulario.edit', compact('registro'));
    }

    public function update(Request $request, $id)
    {
        $registro = UsuarioFormulario::findOrFail($id);

        $request->validate([
            'usuario_id' => 'nullable|integer',
            'formulario_id' => 'nullable|integer',
            'status' => 'required|in:novo,pendente,completo',
            'data_limite' => 'nullable|date',
            'deleted_by' => 'nullable|integer',
        ]);

        $registro->update($request->all());

        return redirect()->route('usuario_formulario.index')->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $registro = UsuarioFormulario::findOrFail($id);
        $registro->delete();

        return redirect()->route('usuario_formulario.index')->with('success', 'Registro excluído com sucesso!');
    }
}