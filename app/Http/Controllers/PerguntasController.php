<?php

namespace App\Http\Controllers;

use App\Models\Pergunta;
use App\Models\Formulario;
use App\Models\Variavel;
use Illuminate\Http\Request;

class PerguntasController extends Controller
{
    public function index()
    {
        $formularios = Formulario::all();
        $perguntas = Pergunta::all();
        return view('perguntas.index', compact('perguntas', 'formularios'));
    }

    public function create()
    {
        $formularios = Formulario::all();
        $variaveis = Variavel::all();
        return view('perguntas.create', compact('formularios', 'variaveis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'formulario_id'      => 'required|integer|exists:formularios,id',
            'variavel_id'        => 'required|array',
            'variavel_id.*'      => 'integer|exists:variaveis,id',
            'numero_da_pergunta' => 'required|integer',
            'pergunta'           => 'required|string|max:500',
        ]);

        $pergunta = Pergunta::create($data);

        $pergunta->variaveis()->sync($data['variavel_id']);

        return redirect()->route('perguntas.index')
            ->with('sucesso', 'Pergunta criada com sucesso!');
    }

    public function show($id)
    {
        $pergunta = Pergunta::findOrFail($id);
        return view('perguntas.show', compact('pergunta'));
    }

    public function edit($id)
    {
        $formularios = Formulario::all();
        $pergunta = Pergunta::findOrFail($id);
        $variaveis = Variavel::all();

        return view('perguntas.edit', compact('pergunta', 'formularios', 'variaveis'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'formulario_id'      => 'required|integer|exists:formularios,id',
            'variavel_id'        => 'required|array',
            'variavel_id.*'      => 'integer|exists:variaveis,id',
            'numero_da_pergunta' => 'required|integer',
            'pergunta'           => 'required|string|max:500',
        ]);

        $pergunta = Pergunta::findOrFail($id);
        $pergunta->update($data);

        $pergunta->variaveis()->sync($data['variavel_id']);

        return redirect()->route('perguntas.index')
            ->with('sucesso', 'Pergunta atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $pergunta = Pergunta::findOrFail($id);
        $pergunta->variaveis()->detach();
        $pergunta->delete();

        return redirect()->route('perguntas.index')
            ->with('sucesso', 'Pergunta excluída com sucesso!');
    }
}
