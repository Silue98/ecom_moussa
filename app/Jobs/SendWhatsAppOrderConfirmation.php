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

class SendWhatsAppOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function __construct(public int $orderId) {}

    public function handle(GreenApiService $greenApi): void
    {
        $order = Order::with('items.product')->find($this->orderId);

        if (! $order) {
            Log::warning("[WhatsApp] Commande #{$this->orderId} introuvable.");
            return;
        }

        // Numéro du client (saisi au checkout)
        $phone = $order->shipping_phone ?? $order->billing_phone ?? null;

        if (empty($phone)) {
            Log::info("[WhatsApp] Pas de numéro de téléphone pour la commande #{$order->order_number}.");
            return;
        }

        $message = $this->buildMessage($order);

        $sent = $greenApi->sendMessage($phone, $message);

        if ($sent) {
            Log::info("[WhatsApp] Confirmation envoyée pour commande #{$order->order_number} au {$phone}.");
        }
    }

    private function buildMessage(Order $order): string
    {
        $shopName    = setting('shop_name', 'TrustPhone CI');
        $shopPhone   = setting('shop_phone', '');
        $shopAddress = setting('shop_address', '');
        $shopHours   = setting('shop_hours', '');

        // Construire la liste des articles
        $itemLines = '';
        foreach ($order->items as $item) {
            $name      = $item->product?->name ?? $item->product_name ?? 'Produit';
            $qty       = $item->quantity;
            $unitPrice = number_format($item->price, 0, ',', ' ');
            $itemLines .= "  • {$name} x{$qty} — {$unitPrice} FCFA\n";
        }

        $total    = number_format($order->total, 0, ',', ' ');
        $shipping = $order->shipping_amount > 0
            ? number_format($order->shipping_amount, 0, ',', ' ') . ' FCFA'
            : 'Gratuite';

        $paymentLabel = $order->payment_method_label ?? $order->payment_method;

        $message  = "✅ *Commande confirmée !*\n\n";
        $message .= "Bonjour {$order->shipping_name} 👋\n\n";
        $message .= "Merci pour votre commande chez *{$shopName}* !\n\n";
        $message .= "📦 *N° de commande :* #{$order->order_number}\n\n";
        $message .= "🛍️ *Articles commandés :*\n{$itemLines}\n";

        $message .= "🚚 *Livraison :* {$shipping}\n";
        $message .= "💰 *Total :* *{$total} FCFA*\n";
        $message .= "💳 *Paiement :* {$paymentLabel}\n\n";
        $message .= "📍 *Retrait en boutique :*\n";

        if (! empty($shopAddress)) {
            $message .= "  {$shopAddress}\n";
        }
        if (! empty($shopHours)) {
            $message .= "  ⏰ {$shopHours}\n";
        }
        if (! empty($shopPhone)) {
            $message .= "  📞 {$shopPhone}\n";
        }

        // Lien de suivi pour clients connectés
        if ($order->user_id) {
            $trackingUrl = url('/compte/commandes/' . $order->id . '/suivi');
            $message .= "\n📍 *Suivre votre commande :*\n  " . $trackingUrl;
        }

        $message .= "\n\nMerci pour votre confiance ! 🙏";

        return $message;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("[WhatsApp] Échec définitif pour commande #{$this->orderId}.", [
            'error' => $exception->getMessage(),
        ]);
    }
}
