<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Stock bas : ' . $this->product->name)
            ->greeting('Alerte stock !')
            ->line('Le produit **' . $this->product->name . '** est presque en rupture de stock.')
            ->line('**Stock actuel :** ' . $this->product->quantity . ' unités')
            ->line('**Seuil d\'alerte :** ' . $this->product->low_stock_threshold . ' unités')
            ->action('Gérer le stock', url('/admin/products/' . $this->product->id . '/edit'))
            ->salutation('Système de notification automatique');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'low_stock',
            'title'      => 'Stock bas',
            'message'    => $this->product->name . ' — ' . $this->product->quantity . ' unités restantes',
            'product_id' => $this->product->id,
            'quantity'   => $this->product->quantity,
            'url'        => '/admin/products/' . $this->product->id . '/edit',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
