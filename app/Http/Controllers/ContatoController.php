<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContatoController extends Controller
{
    public function enviarMensagem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mensagem' => 'required|string|min:10',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $nome = $request->input('nome');
            $email = $request->input('email');
            $mensagem = $request->input('mensagem');
            $emailSoporte = 'instrumentos@fellipelli.com.br';

            // Enviar email
            Mail::send('emails.contato-soporte', [
                'nome' => $nome,
                'email' => $email,
                'mensagem' => $mensagem,
            ], function ($mail) use ($emailSoporte, $nome, $email) {
                $mail->to($emailSoporte)
                     ->subject('Nova mensagem de contato - FAQ E.MO.TI.VE')
                     ->replyTo($email, $nome);
            });

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar mensagem de contato: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem. Por favor, tente novamente mais tarde.'
            ], 500);
        }
    }
}

