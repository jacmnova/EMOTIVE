<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NovoUsuarioCadastrado extends Notification implements ShouldQueue
{
    use Queueable;

    protected $usuario;

    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => 'Novo usuário cadastrado',
            'mensagem' => 'Nome: ' . $this->usuario->name . ' | Email: ' . $this->usuario->email,
            'user_id' => $this->usuario->id,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Novo Usuário Cadastrado no Sistema Burnout')
            ->greeting('Olá!')
            ->line('Um novo usuário foi registrado no sistema:')
            ->line('Nome: ' . $this->usuario->name)
            ->line('Email: ' . $this->usuario->email)
            ->line('Data e Hora: ' . now()->format('d/m/Y H:i'))
            ->line('---')
            ->line('Mensagem automática do Sistema Burnout');
    }
}
