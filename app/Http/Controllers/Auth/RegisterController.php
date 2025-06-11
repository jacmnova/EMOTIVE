<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NovoUsuarioCadastrado;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Para onde redirecionar após o registro.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Construtor do controller.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validação dos dados de registro.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Criação do usuário e envio da notificação.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $usuario = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Notificar o admin (ajuste o ID se necessário)
        User::find(1)?->notify(new NovoUsuarioCadastrado($usuario));

        return $usuario;
    }
}
