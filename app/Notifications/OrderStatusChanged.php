<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order  $order,
        public string $oldStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->order->status_label;
        $subject     = match ($this->order->status) {
            'processing' => '⚙️ Votre commande est en cours de traitement',
            'shipped'    => '🚚 Votre commande a été expédiée !',
            'delivered'  => '✅ Votre commande a été livrée !',
            'cancelled'  => '❌ Votre commande a été annulée',
            'refunded'   => '↩️ Votre commande a été remboursée',
            default      => 'Mise à jour de votre commande',
        };

        $mail = (new MailMessage)
            ->subject($subject . ' #' . $this->order->order_number)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Le statut de votre commande **#' . $this->order->order_number . '** a été mis à jour.')
            ->line('**Nouveau statut :** ' . $statusLabel);

        if ($this->order->status === 'shipped' && $this->order->tracking_number) {
            $mail->line('**Numéro de suivi :** ' . $this->order->tracking_number);
        }

        if ($this->order->status === 'delivered') {
            $mail->line('Nous espérons que vous êtes satisfait de votre achat ! N\'hésitez pas à laisser un avis.');
        }

        return $mail
            ->action('Voir ma commande', url('/compte/commandes/' . $this->order->id))
            ->salutation('L\'équipe E-Commerce');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_status_changed',
            'title'        => 'Commande mise à jour',
            'message'      => 'Votre commande #' . $this->order->order_number . ' est maintenant : ' . $this->order->status_label,
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->order->status,
            'url'          => '/compte/commandes/' . $this->order->id,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
