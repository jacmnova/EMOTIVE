<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificarEmail extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('verificar.email', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Confirme seu e-mail - Sistema Burnout')
            ->greeting('Olá!')
            ->line('Clique no botão abaixo para confirmar seu endereço de e-mail.')
            ->action('Confirmar E-mail', $url)
            ->line('Se você não criou uma conta, ignore este e-mail.');
    }
}
