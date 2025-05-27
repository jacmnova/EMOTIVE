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
            'image' => 'required|image|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        $user = User::findOrFail($request->id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Nome base para a imagem convertida
            $nomeFinal = uniqid('avatar_') . '.jpg';

            // Pasta onde vai salvar no storage (público)
            $pasta = 'avatars';

            // Se for .heic, converte para .jpg
            if ($file->getClientOriginalExtension() === 'heic') {
                $caminhoTemporario = $file->getRealPath();
                $imagem = new Imagick();
                $imagem->readImage($caminhoTemporario);
                $imagem->setImageFormat('jpeg');
                $caminhoFinal = storage_path('app/public/' . $pasta . '/' . $nomeFinal);
                $imagem->writeImage($caminhoFinal);
                $imagem->clear();
                $imagem->destroy();
            } else {
                // Se for jpg, jpeg ou png, só move pro storage direto
                $caminhoFinal = $file->storeAs($pasta, $nomeFinal, 'public');
                $caminhoFinal = storage_path('app/public/' . $caminhoFinal);
            }

            // Apaga avatar antigo se não for o padrão
            if ($user->avatar && $user->avatar !== null && strpos($user->avatar, 'vendor/adminlte/dist/img/user.png') === false) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            // Salva caminho relativo no banco
            $user->avatar = $pasta . '/' . $nomeFinal;
            $user->save();
        }

        return redirect()->back()->with('msgSuccess', 'Perfil <strong>' . $user->email . '</strong> atualizado com sucesso!');
    }

    public function UploadImagemCliente(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        $cliente = Cliente::findOrFail($request->id);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $nomeFinal = uniqid('logo_') . '.jpg';
            $pasta = 'avatars';

            if ($file->getClientOriginalExtension() === 'heic') {
                $caminhoTemporario = $file->getRealPath();
                $imagem = new Imagick();
                $imagem->readImage($caminhoTemporario);
                $imagem->setImageFormat('jpeg');
                $caminhoFinal = storage_path('app/public/' . $pasta . '/' . $nomeFinal);
                $imagem->writeImage($caminhoFinal);
                $imagem->clear();
                $imagem->destroy();
            } else {
                $caminhoFinal = $file->storeAs($pasta, $nomeFinal, 'public');
                $caminhoFinal = storage_path('app/public/' . $caminhoFinal);
            }

            if ($cliente->logo_url && $cliente->logo_url !== null && strpos($cliente->logo_url, 'vendor/adminlte/dist/img/client.png') === false) {
                if (Storage::disk('public')->exists($cliente->logo_url)) {
                    Storage::disk('public')->delete($cliente->logo_url);
                }
            }

            $cliente->logo_url = $pasta . '/' . $nomeFinal;
            $cliente->save();
        }

        return redirect()->back()->with('msgSuccess', 'Perfil <strong>' . $cliente->nome . '</strong> atualizado com sucesso!');
    }
}
