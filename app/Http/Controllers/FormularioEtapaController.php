<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\FormularioEtapa;
use Illuminate\Http\Request;

class FormularioEtapaController extends Controller
{
    public function adicionar(Request $request)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
            'etapa' => 'required|integer',
            'de' => 'required|integer',
            'ate' => 'required|integer'
        ]);

        FormularioEtapa::create([
            'formulario_id' => $request->formulario_id,
            'etapa' => $request->etapa,
            'de' => $request->de,
            'ate' => $request->ate
        ]);

        return redirect()->back()->with('msgSuccess', 'Etapa adicionada com sucesso!');
    }

    public function remover($id)
    {
        $etapa = FormularioEtapa::findOrFail($id);
        $etapa->delete();

        return redirect()->back()->with('msgSuccess', 'Etapa removida com sucesso!');
    }
}
