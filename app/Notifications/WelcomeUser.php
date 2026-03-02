<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUser extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Bienvenue sur E-Commerce !')
            ->greeting('Bienvenue ' . $notifiable->name . ' !')
            ->line('Votre compte a été créé avec succès.')
            ->line('Vous pouvez maintenant profiter de toutes nos offres et suivre vos commandes.')
            ->action('Commencer mes achats', url('/'))
            ->line('Utilisez le code **BIENVENUE10** pour -10% sur votre première commande ! 🎁')
            ->salutation('L\'équipe E-Commerce');
    }
}
