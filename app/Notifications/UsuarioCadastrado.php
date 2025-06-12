<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UsuarioCadastrado extends Notification
{
    use Queueable;

    protected $senhaTemporaria;

    public function __construct($senhaTemporaria)
    {
        $this->senhaTemporaria = $senhaTemporaria;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cadastro no Sistema')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você foi cadastrado com sucesso no sistema.')
            ->line('Email: ' . $notifiable->email)
            ->line('Senha provisória: **' . $this->senhaTemporaria . '**')
            ->line('Recomendamos que você altere sua senha após o primeiro login.')
            ->action('Acessar o sistema', url('/login'))
            ->line('Seja bem-vindo!');
    }
}
