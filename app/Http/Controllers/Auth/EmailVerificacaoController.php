<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificacaoController extends Controller
{
    public function verificar($token)
    {
        $user = \App\Models\User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return redirect('/login')->with('status', 'E-mail confirmado com sucesso! Você já pode entrar no sistema.');
    }
}
