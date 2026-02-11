<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    // Tempo máximo de inatividade (em segundos)
    protected $timeout = 15 * 60;

    public function handle($request, Closure $next)
    {
        if (!Session::has('lastActivityTime')) {
            Session::put('lastActivityTime', now()->timestamp);
        } elseif (now()->timestamp - Session::get('lastActivityTime') > $this->timeout) {
            Session::forget('lastActivityTime');
            Auth::logout();
            return redirect()->route('login')->withErrors(['message' => 'Sessão expirada por inatividade.']);
        }

        Session::put('lastActivityTime', now()->timestamp);

        return $next($request);
    }
}
