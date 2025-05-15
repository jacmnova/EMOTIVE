<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function UploadImagemUsuario(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $user = User::findOrFail($request->id);
        if ($request->hasFile('image')) {
            if ($user->avatar && $user->avatar !== null && strpos($user->avatar, 'vendor/adminlte/dist/img/user.png') === false) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }
            $avatarPath = $request->file('image')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }
        $user->save();
        return redirect()->back()->with('msgSuccess', 'Perfil <strong>' . $user->email . '</strong> atualizado com sucesso!');
    }

    public function UploadImagemCliente(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $cliente = Cliente::findOrFail($request->id);

        if ($request->hasFile('image')) {
            if ($cliente->logo_url && $cliente->logo_url !== null && strpos($cliente->logo_url, 'vendor/adminlte/dist/img/client.png') === false) {
                if (Storage::disk('public')->exists($cliente->logo_url)) {
                    Storage::disk('public')->delete($cliente->logo_url);
                }
            }
            $avatarPath = $request->file('image')->store('avatars', 'public');
            $cliente->logo_url = $avatarPath;
        }
        $cliente->save();
        return redirect()->back()->with('msgSuccess', 'Perfil <strong>' . $cliente->nome . '</strong> atualizado com sucesso!');
    }

}