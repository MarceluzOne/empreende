<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Recuperação de Senha — Empreende Vitória')
            ->greeting('Olá!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta.')
            ->action('Redefinir Senha', $url)
            ->line('Este link expirará em ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação é necessária.')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
