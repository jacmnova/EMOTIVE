<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UsuarioCadastrado;

class ImportarUsuariosController extends Controller
{
    public function form()
    {
        return view('usuarios.importar');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:csv,txt',
        ]);

        $clienteId = Auth::user()->cliente_id;
        $senhaPadrao = 'mudar@123';
        $cadastrados = 0;

        if (($handle = fopen($request->file('arquivo')->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);

            while (($linha = fgetcsv($handle, 1000, ',')) !== false) {
                $email = trim($linha[0]);
                $nome  = trim($linha[1]);

                if (!User::where('email', $email)->exists()) {
                    $user = User::create([
                        'name' => $nome,
                        'email' => $email,
                        'password' => Hash::make($senhaPadrao),
                        'email_verified_at' => now(),
                        'cliente_id' => $clienteId,
                        'usuario' => true,
                        'gestor' => false,
                    ]);

                    $user->notify(new UsuarioCadastrado($senhaPadrao));
                    $cadastrados++;
                }
            }

            fclose($handle);
        }

        return back()->with('success', "{$cadastrados} usuários importados com sucesso.");
    }
}
