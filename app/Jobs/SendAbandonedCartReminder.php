<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Services\GreenApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 30;

    public function __construct(public int $cartId) {}

    public function handle(GreenApiService $greenApi): void
    {
        $cart = Cart::with(['user', 'items.product'])->find($this->cartId);

        if (!$cart || $cart->items->isEmpty()) {
            return; // Panier vidé ou inexistant
        }

        // Si l'utilisateur a commandé depuis, on abandonne
        if ($cart->user && $cart->user->orders()->where('created_at', '>', now()->subHours(3))->exists()) {
            return;
        }

        $user = $cart->user;
        if (!$user) return;

        $shopName   = setting('shop_name', 'Notre boutique');
        $shopPhone  = setting('shop_phone', '');
        $total      = number_format($cart->total, 0, ',', ' ');
        $firstName  = explode(' ', $user->name)[0];

        // Construire la liste des articles
        $itemLines = $cart->items->take(3)->map(function($item) {
            $name = $item->product?->name ?? 'Produit';
            return "  • {$name}";
        })->implode("\n");

        $remaining = $cart->items->count() - 3;
        if ($remaining > 0) $itemLines .= "\n  • ...et {$remaining} autre(s)";

        $message = "😊 Bonjour {$firstName} !\n\n"
            . "Vous avez laissé des articles dans votre panier chez *{$shopName}* :\n\n"
            . "{$itemLines}\n\n"
            . "💰 *Total : {$total} FCFA*\n\n"
            . "Votre panier vous attend ! Finalisez votre commande avant qu'un article parte en rupture 😉\n\n"
            . "👉 " . url('/panier');

        if (!empty($shopPhone) && $user->phone) {
            $sent = $greenApi->sendMessage($user->phone, $message);
            if ($sent) {
                Log::info("[AbandonedCart] WhatsApp envoyé à {$user->email} pour panier #{$cart->id}");
                return;
            }
        }

        // Fallback email
        try {
            Mail::raw(
                strip_tags(str_replace(['*', '#'], '', $message)),
                function($mail) use ($user, $shopName) {
                    $mail->to($user->email, $user->name)
                         ->subject("😊 Vous avez oublié quelque chose — {$shopName}");
                }
            );
            Log::info("[AbandonedCart] Email envoyé à {$user->email}");
        } catch (\Throwable $e) {
            Log::warning("[AbandonedCart] Échec notification pour panier #{$this->cartId}: " . $e->getMessage());
        }
    }
}
