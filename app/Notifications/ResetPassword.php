<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * Token de redefinição de senha.
     */
    public $token;

    /**
     * Cria uma nova instância da notificação.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Define os canais de entrega da notificação.
     *
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Gera o e-mail de redefinição de senha.
     *
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Solicitação de Redefinição de Senha')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta.')
            ->line('Clique no botão abaixo para continuar com a redefinição de senha:')
            ->action('Redefinir Senha', $resetUrl)
            ->line('Este link de redefinição de senha expirará em 60 minutos.')
            ->line('Se você não solicitou essa redefinição, nenhuma ação adicional é necessária.')
            ->salutation('Atenciosamente, ' . config('app.name'));
    }

    /**
     * Retorna a representação em array da notificação.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reset_url' => url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->email,
            ], false)),
        ];
    }
}
