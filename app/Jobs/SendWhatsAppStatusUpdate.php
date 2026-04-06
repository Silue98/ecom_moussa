<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\GreenApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function backoff(): array { return [60, 300, 900]; }

    public function __construct(
        public int    $orderId,
        public string $oldStatus
    ) {}

    public function handle(GreenApiService $greenApi): void
    {
        $order = Order::find($this->orderId);
        if (!$order) return;

        $phone    = $order->shipping_phone ?? $order->billing_phone ?? null;
        if (!$phone) return;

        $shopName = setting('shop_name', 'Notre boutique');
        $name     = $order->shipping_name;
        $num      = $order->order_number;

        $message = match($order->status) {
            'processing' =>
                "⚙️ *Commande en préparation !*\n\n"
                . "Bonjour {$name} 👋\n"
                . "Votre commande *#{$num}* chez *{$shopName}* est en cours de préparation.\n\n"
                . "Nous vous informerons dès l'expédition.",

            'shipped' =>
                "🚚 *Votre commande est en route !*\n\n"
                . "Bonjour {$name} 👋\n"
                . "Votre commande *#{$num}* a été expédiée !\n"
                . ($order->tracking_number ? "📦 N° de suivi : *{$order->tracking_number}*\n" : "")
                . "\nPréparez-vous à la réceptionner. 😊",

            'delivered' =>
                "✅ *Commande livrée !*\n\n"
                . "Bonjour {$name} 👋\n"
                . "Votre commande *#{$num}* a bien été livrée.\n\n"
                . "Nous espérons que vous êtes satisfait(e) de votre achat ! 🙏\n"
                . "N'hésitez pas à laisser un avis sur notre site.",

            'cancelled' =>
                "❌ *Commande annulée*\n\n"
                . "Bonjour {$name},\n"
                . "Votre commande *#{$num}* a été annulée.\n\n"
                . "Pour toute question, contactez-nous directement.",

            default => null,
        };

        if (!$message) return;

        $sent = $greenApi->sendMessage($phone, $message);

        if ($sent) {
            Log::info("[WhatsAppStatus] Envoyé pour commande #{$num} → {$order->status}");
        } else {
            Log::warning("[WhatsAppStatus] Échec pour commande #{$num} → {$order->status}");
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[WhatsAppStatus] Échec définitif commande #{$this->orderId}: " . $e->getMessage());
    }
}
