<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\FormularioEtapa;
use App\Models\TipoCalculo;
use App\Models\Variavel;
use Illuminate\Http\Request;

class FormulariosController extends Controller
{
    public function index()
    {
        $formularios = Formulario::all();
        return view('formularios.index', compact('formularios'));
    }

    public function create()
    {
        $calculos = TipoCalculo::all();
        return view('formularios.create', compact('calculos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'descricao' => 'required',
            'instrucoes' => 'required',
            'score_ini' => 'required',
            'score_fim' => 'required',
            'calculo_id' => 'required',
        ]);

        try {
            Formulario::create($request->all());
            return redirect()->route('formularios.index')->with('msgSuccess', 'Formulário criado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('msgError', 'Erro ao criar formulário. Tente novamente.');
        }
    }

    public function show(Formulario $formulario)
    {
        $variaveis = $formulario->variaveisDetalhadas();
        return view('formularios.show', compact('formulario','variaveis'));
    }

    public function edit(Formulario $formulario)
    {
        $calculos = TipoCalculo::all();
        // $etapas = FormularioEtapa::find($formulario->id);
        $formulario->load('etapas'); 
        return view('formularios.edit', compact('formulario','calculos'));
    }

    public function update(Request $request, Formulario $formulario)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'descricao' => 'required',
            'instrucoes' => 'required',
            'score_ini' => 'required',
            'score_fim' => 'required',
            'calculo_id' => 'required',
        ]);

        try {
            $formulario->update($request->all());
            return redirect()->route('formularios.index')->with('msgSuccess', 'Formulário atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('msgError', 'Erro ao atualizar formulário. Tente novamente.');
        }
    }

    public function destroy(Formulario $formulario)
    {
        try {
            $formulario->delete();
            return redirect()->route('formularios.index')->with('msgSuccess', 'Formulário excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('msgError', 'Erro ao excluir formulário. Tente novamente.');
        }
    }
    
}
