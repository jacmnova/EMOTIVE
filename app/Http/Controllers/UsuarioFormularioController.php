<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;
use App\Models\UsuarioFormulario;

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

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'usuario_id' => 'nullable|integer',
    //         'formulario_id' => 'nullable|integer',
    //     ]);
    
    //     $exists = UsuarioFormulario::where('usuario_id', $validatedData['usuario_id'])
    //         ->where('formulario_id', $validatedData['formulario_id'])
    //         ->exists();
    
    //     if ($exists) {
    //         return redirect()->back()->with('msgError', 'Este formulário já foi incluído para este usuário.');
    //     }

    //     $usuario = User::find($validatedData['usuario_id']);
    //     $formulario = ClienteFormulario::where('formulario_id',$validatedData['formulario_id'])->where('cliente_id',$usuario->cliente_id);
    //     if($formulario->quantidade > 0 ){
    //         $formulario->quantidade = $formulario->quantidade - 1;
    //         $formulario->save();
    //         UsuarioFormulario::create($validatedData);
    //     }

    //     return redirect()->back()->with('msgSuccess', 'Formulário incluído com sucesso!');
    // }

    public function store(Request $request)
    {
        // 1. Validação obrigatória e clara
        $validatedData = $request->validate([
            'usuario_id' => 'required|integer|exists:users,id',
            'formulario_id' => 'required|integer',
        ]);

        // 2. Verifica se já existe vínculo
        $exists = UsuarioFormulario::where('usuario_id', $validatedData['usuario_id'])
            ->where('formulario_id', $validatedData['formulario_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('msgError', 'Este formulário já foi incluído para este usuário.');
        }

        // 3. Busca usuário e valida
        $usuario = User::find($validatedData['usuario_id']);
        if (!$usuario) {
            return redirect()->back()->with('msgError', 'Usuário não encontrado.');
        }

        // 4. Busca ClienteFormulario vinculado ao cliente do usuário
        $formulario = ClienteFormulario::where('formulario_id', $validatedData['formulario_id'])
            ->where('cliente_id', $usuario->cliente_id)
            ->first();

        if (!$formulario) {
            return redirect()->back()->with('msgError', 'Formulário não está disponível para este cliente.');
        }

        // 5. Verifica se ainda há formulários disponíveis
        if ($formulario->quantidade <= 0) {
            return redirect()->back()->with('msgError', 'Não há mais formulários "'. $formulario->formulario->nome .'" disponíveis.');
        }

        // 6. Decrementa, salva e cria o vínculo
        $formulario->decrement('quantidade');
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