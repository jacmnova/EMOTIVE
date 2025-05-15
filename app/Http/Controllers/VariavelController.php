<?php

namespace App\Http\Controllers;

use App\Models\Variavel;
use App\Models\Formulario;
use Illuminate\Http\Request;

class VariavelController extends Controller
{
    public function index()
    {
        $variaveis = Variavel::all();
        return view('variaveis.index', compact('variaveis'));
    }

    public function create()
    {
        $formularios = Formulario::all();
        return view('variaveis.create', compact('formularios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tag' => 'required|string|max:255|unique:variaveis,tag',
            'B' => 'required|integer',
            'M' => 'required|integer',
            'A' => 'required|integer',
            'baixa' => 'nullable|string',
            'moderada' => 'nullable|string',
            'alta' => 'nullable|string',
        ]);

        Variavel::create($request->all());

        return redirect()->route('variaveis.index')->with('msgSuccess', 'Variável criada com sucesso!');
    }

    public function show(Variavel $variavel)
    {
        return view('variaveis.show', compact('variavel'));
    }

    public function edit($id)
    {
        $variavel = Variavel::findOrFail($id);
        $formularios = Formulario::all();
        return view('variaveis.edit', compact('variavel', 'formularios'));
    }

    public function update(Request $request, Variavel $variavel)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'B' => 'required|numeric',
            'M' => 'required|numeric',
            'A' => 'required|numeric',
            'baixa' => 'required|string',
            'moderada' => 'required|string',
            'alta' => 'required|string',
        ]);
    
        // Opcional: use somente os campos validados
        $updateFields = $request->only(['nome', 'descricao', 'B', 'M', 'A', 'baixa', 'moderada', 'alta']);
    
        try {
            $variavel->update($updateFields);
            return redirect()->route('variaveis.index')->with('msgSuccess', 'Variável atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('msgError', 'Erro ao atualizar Variável. Tente novamente.');
        }
    }

    public function destroy(Variavel $variavel)
    {
        $variavel->delete();

        return redirect()->route('variaveis.index')->with('msgSuccess', 'Variável excluída com sucesso!');
    }

    public function getPorFormulario($id)
    {
        $variaveis = Variavel::where('formulario_id', $id)->get();

        return response()->json($variaveis);
    }

}