<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordLinkNotification extends Notification
{
    public function __construct(
        public string $token,
        public bool $isInvitation = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $broker = $this->isInvitation ? 'invitations' : 'users';
        $route = $this->isInvitation ? 'invitation.accept' : 'password.reset';

        $url = route($route, [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutes = (int) config("auth.passwords.{$broker}.expire");
        $validity = $minutes >= 1440 ? intdiv($minutes, 1440).' jours' : $minutes.' minutes';

        $message = (new MailMessage)->greeting('Bonjour,');

        if ($this->isInvitation) {
            $message->subject('Invitation au portail des membres')
                ->line('Un compte a été créé pour vous sur le portail des membres.')
                ->action('Définir mon mot de passe', $url)
                ->line("Ce lien est valide pendant {$validity}.");
        } else {
            $message->subject('Réinitialisation de votre mot de passe')
                ->line('Vous avez demandé la réinitialisation de votre mot de passe.')
                ->action('Choisir un nouveau mot de passe', $url)
                ->line("Ce lien est valide pendant {$validity}.")
                ->line("Si vous n'avez pas fait cette demande, ignorez ce message.");
        }

        return $message->salutation('Cordialement,');
    }
}