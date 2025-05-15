<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Formulario;
use Illuminate\Http\Request;
use App\Models\ClienteFormulario;

class ClienteController extends Controller
{

    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $usuarios = User::all();
        return view('clientes.create', compact('usuarios'));
    }

    public function edit($id)
    {
        $usuarios = User::all();
        $clientes = Cliente::findOrFail($id);
        $formularios = Formulario::all();
        $questionarios = ClienteFormulario::with(['cliente', 'formulario'])->where('cliente_id',$id)->get();
        return view('clientes.edit', compact('clientes','usuarios','formularios','questionarios'));
    }

    public function show($id)
    {
        $dadosCliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('dadosCliente'));
    }

    public function store(Request $request)
    {
        try {
            $cliente = Cliente::create($request->all());

            if (!empty($cliente->usuario_id)) {
                $user = User::find($cliente->usuario_id);
                if ($user) {
                    $user->cliente_id = $cliente->id;
                    $user->gestor = 1;
                    $user->save();
                }
            }

            return redirect()->route('clientes.index')->with('msgSuccess', 'Cliente criado com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msgError' => 'Ocorreu um erro ao criar o cliente.']);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_fantasia' => 'required|string',
            'razao_social' => 'required|string',
            'reg' => 'nullable|string',
            'email' => 'required|email',
            'contato' => 'nullable|string',
            'telefone' => 'nullable|string',
            'ativo' => 'required|boolean',
        ]);

        $clientes = Cliente::findOrFail($request->id);

        $clientes->update([
            'nome_fantasia' => $request->nome_fantasia,
            'razao_social' => $request->razao_social,
            'reg' => $request->reg,
            'email' => $request->email,
            'contato' => $request->contato,
            'telefone' => $request->telefone,
            'ativo' => $request->ativo,
            'usuario_id' => $request->usuario_id,
        ]);

        $user = User::find($request->usuario_id);
        $user->cliente_id = $request->id;
        $user->gestor = 1;
        $user->save();

        $mensagem = 'Cliente <strong>' . $clientes->nome_fantasia . '</strong> atualizado com sucesso!';
        return redirect()->route('clientes.index')->with('msgSuccess', $mensagem);
    }

    public function status(Request $request)
    {
        $id = $request->input('status_id');
        $clientes = Cliente::findOrFail($id);
        $statusAtualizado = $clientes->ativo == 1 ? 0 : 1;
        $clientes->update(['ativo' => $statusAtualizado]);
        $mensagem = 'Status do cliente <strong>' . $clientes->nome_fantasia . '</strong> alterado com sucesso!';
        return redirect()->route('clientes.index')->with('msgSuccess', $mensagem);
    }


    public function destroy(Request $request)
    {
        $id = $request->input('destroy_id');
        $clientes = Cliente::findOrFail($id);
        $clientes->delete();
        $mensagem = 'Cliente <strong>' . $clientes->nome_fantasia . '</strong> excluído com sucesso!';
        return redirect()->route('clientes.index')->with('msgSuccess', $mensagem);
    }

    public function incluir(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required',
            'formulario_id' => 'required|exists:formularios,id',
            'quantidade' => 'required|integer|min:1'
        ]);

        ClienteFormulario::create([
            'cliente_id' => $request->input('cliente_id'),
            'formulario_id' => $request->input('formulario_id'),
            'ativo' => true,
            'deleted_by' => null,
            'quantidade' => $request->input('quantidade')
        ]);

        return redirect()->back()->with('msgSuccess', 'Questionário incluído com sucesso!');
    }

}
