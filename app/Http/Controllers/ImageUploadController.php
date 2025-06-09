<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Imagick;

class ImageUploadController extends Controller
{
    public function UploadImagemUsuario(Request $request)
    {
        $request->validate([
            'imagem_base64' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);

        $data = $request->input('imagem_base64');

        if (preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
            $tipo = strtolower($matches[1]);

            if (!in_array($tipo, ['jpg', 'jpeg', 'png'])) {
                return back()->withErrors(['imagem_base64' => 'Formato de imagem inválido.']);
            }

            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);

            $nomeFinal = uniqid('avatar_') . '.jpg';
            $pasta = 'avatars';

            Storage::disk('public')->put("{$pasta}/{$nomeFinal}", $data);

            // Apaga o anterior, se necessário
            if ($user->avatar && strpos($user->avatar, 'vendor/adminlte/dist/img/user.png') === false) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            $user->avatar = "{$pasta}/{$nomeFinal}";
            $user->save();

            return back()->with('msgSuccess', 'Perfil <strong>' . $user->email . '</strong> atualizado com sucesso!');
        }

        return back()->withErrors(['imagem_base64' => 'Imagem inválida.']);
    }

    public function UploadImagemCliente(Request $request)
    {
        $request->validate([
            'imagem_base64' => 'required|string',
        ]);

        $cliente = Cliente::findOrFail($request->id);
        $data = $request->input('imagem_base64');

        if (preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
            $tipo = strtolower($matches[1]);

            if (!in_array($tipo, ['jpg', 'jpeg', 'png'])) {
                return back()->withErrors(['imagem_base64' => 'Formato de imagem inválido.']);
            }

            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);

            $nomeFinal = uniqid('logo_') . '.jpg';
            $pasta = 'avatars';

            Storage::disk('public')->put("{$pasta}/{$nomeFinal}", $data);

            if ($cliente->logo_url && strpos($cliente->logo_url, 'vendor/adminlte/dist/img/client.png') === false) {
                if (Storage::disk('public')->exists($cliente->logo_url)) {
                    Storage::disk('public')->delete($cliente->logo_url);
                }
            }

            $cliente->logo_url = "{$pasta}/{$nomeFinal}";
            $cliente->save();

            return back()->with('msgSuccess', 'Perfil <strong>' . $cliente->nome . '</strong> atualizado com sucesso!');
        }

        return back()->withErrors(['imagem_base64' => 'Imagem inválida.']);
    }

}
