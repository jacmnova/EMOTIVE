<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Processa a troca de senha do usuário pelo admin.
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('usuarios.index')
                         ->with('status', 'Senha atualizada com sucesso para o usuário: ' . $user->name);
    }
}
