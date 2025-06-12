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
            ->subject('Confirmação de E-mail - Sistema Burnout')
            ->view('emails.verificacao', [
                'usuario' => $notifiable,
                'url' => $url
            ]);
    }
}
