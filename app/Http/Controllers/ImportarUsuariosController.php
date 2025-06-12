<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UsuarioCadastrado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class ImportarUsuariosController extends Controller
{
    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt',
        ]);

        $senhaPadrao = '12345678';
        $arquivo = fopen($request->file('arquivo')->getRealPath(), 'r');
        $cabecalho = fgetcsv($arquivo); // pula a primeira linha (cabeçalho)

        $cadastrados = 0;
        while (($linha = fgetcsv($arquivo)) !== false) {
            $email = trim($linha[0]);
            $nome = trim($linha[1]);

            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'name' => $nome,
                    'email' => $email,
                    'password' => Hash::make($senhaPadrao),
                ]);

                $user->notify(new UsuarioCadastrado($senhaPadrao));
                $cadastrados++;
            }
        }

        fclose($arquivo);

        return back()->with('success', "{$cadastrados} usuários importados com sucesso.");
    }

    public function form()
    {
        return view('usuarios.importar');
    }
}
