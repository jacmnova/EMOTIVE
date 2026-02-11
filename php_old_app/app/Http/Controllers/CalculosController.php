<?php

namespace App\Http\Controllers;

use App\Models\Calculo;
use Illuminate\Http\Request;

class CalculosController extends Controller
{
    public function index()
    {
        $calculos = Calculo::all();
        return view('calculos.index', compact('calculos'));
    }

    public function create()
    {
        return view('calculos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:50',
            'descricao' => 'required|string|max:200',
            'operador' => 'required|string|max:20',
            'formula' => 'required|string',
        ]);

        Calculo::create($validated);

        return redirect()->route('calculos.index')->with('msgSuccess', 'Cálculo criado com sucesso!');
    }

    public function show($id)
    {
        $calculo = Calculo::findOrFail($id);
        return view('calculos.show', compact('calculo'));
    }

    public function edit($id)
    {
        $calculo = Calculo::findOrFail($id);
        return view('calculos.edit', compact('calculo'));
    }

    public function update(Request $request, $id)
    {
        $calculo = Calculo::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:50',
            'descricao' => 'required|string|max:200',
            'operador' => 'required|string|max:20',
            'formula' => 'required|string',
        ]);

        $calculo->update($validated);

        return redirect()->route('calculos.index')->with('msgSuccess', 'Cálculo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $calculo = Calculo::findOrFail($id);
        $calculo->delete();

        return redirect()->route('calculos.index')->with('msgSuccess', 'Cálculo removido com sucesso!');
    }

    public function status($id)
    {
        $calculo = Calculo::findOrFail($id);
        $calculo->ativo = !$calculo->ativo;
        $calculo->save();

        return redirect()->route('calculos.index')->with('msgSuccess', 'Status do cálculo alterado com sucesso!');
    }
}