<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Confirmation de votre commande #' . $this->order->order_number)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Nous avons bien reçu votre commande et elle est en cours de traitement.')
            ->line('**N° de commande :** ' . $this->order->order_number)
            ->line('**Total :** ' . number_format($this->order->total, 2) . ' FCFA')
            ->line('**Mode de paiement :** ' . $this->order->payment_method_label)
            ->action('Voir ma commande', url('/compte/commandes/' . $this->order->id))
            ->line('Merci pour votre confiance ! 🙏')
            ->salutation('L\'équipe E-Commerce');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_confirmed',
            'title'        => 'Commande confirmée',
            'message'      => 'Votre commande #' . $this->order->order_number . ' a été confirmée.',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
            'url'          => '/compte/commandes/' . $this->order->id,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
