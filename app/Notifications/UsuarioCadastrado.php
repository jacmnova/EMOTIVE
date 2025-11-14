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
            ->subject('E.MO.TI.VE | Acesso disponível')
            ->view('emails.acesso-disponivel', [
                'usuario' => $notifiable,
                'senhaTemporaria' => $this->senhaTemporaria,
            ]);
    }
}
