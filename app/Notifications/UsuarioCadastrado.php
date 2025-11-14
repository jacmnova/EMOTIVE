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
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seja muito bem-vindo(a) ao EMOTIVE!')
            ->line('Estamos felizes em ter você por aqui para uma jornada de autoconhecimento e bem-estar. Nosso objetivo é investir em um ambiente de trabalho mais saudável, e sua participação é muito importante. ')
            ->line('Para começar, preparamos seu acesso à plataforma:')
            ->line('Email: ' . $notifiable->email)
            ->line('Senha provisória: **' . $this->senhaTemporaria . '**')
            ->line('Recomendamos que você altere sua senha após o primeiro login.')
            ->action('Acessar o sistema', url('/login'))
            ->line('Importante: ')
            ->line('- Sua senha é pessoal e intransferível. ')
            ->line('- Recomendamos utilizar letras maiúsculas, minúsculas, números e símbolos para maior segurança. ')
            ->line('- Em caso de dúvidas, entre em contato com nosso time de suporte: instrumentos@fellipelli.com.br ')
            ->line('- Qualquer dúvida, estamos à disposição! ')
            ->line('Abraços, ')
            ->line('Equipe Fellipelli Consultoria');
    }
}
