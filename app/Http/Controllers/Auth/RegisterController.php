<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NovoUsuarioCadastrado;
use App\Notifications\VerificarEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
     * Criação do usuário, envio de token de verificação e notificação ao admin.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $usuario = User::create([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'password'            => Hash::make($data['password']),
            'verification_token'  => Str::random(64),
        ]);

        // Envia e-mail de verificação ao próprio usuário
        $usuario->notify(new VerificarEmail($usuario->verification_token));

        // Notifica o admin
        User::find(1)?->notify(new NovoUsuarioCadastrado($usuario));

        return $usuario;
    }
}
