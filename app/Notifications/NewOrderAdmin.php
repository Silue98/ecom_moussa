<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdmin extends Notification implements ShouldQueue
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
            ->subject('🛒 Nouvelle commande #' . $this->order->order_number)
            ->greeting('Nouvelle commande reçue !')
            ->line('**Client :** ' . $this->order->shipping_name)
            ->line('**Email :** ' . $this->order->shipping_email)
            ->line('**Montant :** ' . number_format($this->order->total, 2) . ' FCFA')
            ->line('**Articles :** ' . $this->order->items->count() . ' article(s)')
            ->line('**Paiement :** ' . $this->order->payment_method_label)
            ->action('Traiter la commande', url('/admin/orders/' . $this->order->id))
            ->salutation('Système de notification automatique');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'new_order',
            'title'        => 'Nouvelle commande',
            'message'      => 'Commande #' . $this->order->order_number . ' de ' . $this->order->shipping_name . ' — ' . number_format($this->order->total, 2) . ' FCFA',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
            'url'          => '/admin/orders/' . $this->order->id,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
