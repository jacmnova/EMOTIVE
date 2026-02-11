<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start($id)
    {
        $user = User::find($id);
        if ($user) {
            Auth::login($user);
            return redirect()->route('home');
        }
        return redirect()->route('home')->with('error', 'Usuário não encontrado.');
    }    
}
