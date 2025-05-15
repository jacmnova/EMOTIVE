<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;
use App\Models\Pergunta;

class QuestionariosController extends Controller
{
    // Exibir uma lista de questionários
    public function index()
    {
        $questionarios = Formulario::all();
        return view('questionarios.index', compact('questionarios'));
    }

    // Exibir o formulário para criar um novo questionário
    public function create()
    {
        return view('questionarios.create'); // ajuste a view
    }

    // Salvar um novo questionário
    public function store(Request $request)
    {
        // validar os dados
        $validated = $request->validate([
            // exemplo: 'nome' => 'required|string|max:255',
        ]);

        // criar o questionário
        // Questionario::create($validated);

        return redirect()->route('questionarios.index')->with('success', 'Questionário criado com sucesso!');
    }

    // Exibir um questionário específico
    public function show($id)
    {
        // $questionario = Questionario::findOrFail($id);
        $questionarios = Pergunta::all();
        return view('questionarios.show'); // ajuste a view
    }

    // Exibir o formulário para editar
    public function edit($id)
    {
        // $questionario = Questionario::findOrFail($id);
        return view('questionarios.edit'); // ajuste a view
    }

    // Atualizar o registro
    public function update(Request $request, $id)
    {
        // $questionario = Questionario::findOrFail($id);
        // validar os dados
        $validated = $request->validate([
            // exemplo: 'nome' => 'required|string|max:255',
        ]);

        // $questionario->update($validated);

        return redirect()->route('questionarios.index')->with('success', 'Questionário atualizado!');
    }

    // Excluir um questionário
    public function destroy($id)
    {
        // $questionario = Questionario::findOrFail($id);
        // $questionario->delete();

        return redirect()->route('questionarios.index')->with('success', 'Questionário excluído!');
    }
}