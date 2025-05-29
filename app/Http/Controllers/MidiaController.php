<?php

namespace App\Http\Controllers;

use App\Models\Midia;
use App\Models\Formulario;
use Illuminate\Http\Request;

class MidiaController extends Controller
{
    public function index()
    {
        $midias = Midia::all();
        $formularios = Formulario::all();
        
        return view('midias.index', compact('midias', 'formularios'));
    }

    public function create()
    {
        $formularios = Formulario::all();
        return view('midias.create', compact('formularios'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:video,url',
            'formulario_id' => 'required|integer',
            'url' => 'nullable|url',
            'arquivo' => 'nullable|file|mimetypes:video/*'
        ]);

        $data = [
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'formulario_id' => $request->formulario_id,
        ];

        if ($request->tipo === 'url') {
            $data['url'] = $request->url;
            $data['arquivo'] = null;
        } elseif ($request->tipo === 'video' && $request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo')->store('videos', 'public');
            $data['arquivo'] = $arquivo;
            $data['url'] = null;
        } else {
            return redirect()->back()->with('error', 'Arquivo de vídeo obrigatório para o tipo vídeo.');
        }

        Midia::create($data);

        return redirect()->route('midias.index')->with('success', 'Mídia cadastrada com sucesso!');
    }



    public function edit(Midia $midia)
    {
        $formularios = Formulario::all();
        return view('midias.edit', compact('midia', 'formularios'));
    }

    public function update(Request $request, Midia $midia)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:video,url',
            'formulario_id' => 'required|integer',
            'url' => 'nullable|url',
            'arquivo' => 'nullable|file|mimetypes:video/*'
        ]);

        $data = [
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'formulario_id' => $request->formulario_id,
        ];

        if ($request->tipo === 'url') {
            $data['url'] = $request->url;
            $data['arquivo'] = null;
        } elseif ($request->tipo === 'video' && $request->hasFile('arquivo')) {
            // Se já tinha um arquivo antigo, poderia removê-lo aqui com Storage::delete()
            $arquivo = $request->file('arquivo')->store('videos', 'public');
            $data['arquivo'] = $arquivo;
            $data['url'] = null;
        }

        $midia->update($data);

        return redirect()->route('midias.index')->with('success', 'Mídia atualizada com sucesso!');
    }

    public function destroy(Midia $midia)
    {
        $midia->delete();
        return redirect()->route('midias.index')->with('success', 'Mídia removida com sucesso!');
    }
}
