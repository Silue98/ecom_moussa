<?php

namespace App\Console\Commands;

use App\Jobs\SendAbandonedCartReminder;
use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAbandonedCartReminders extends Command
{
    protected $signature   = 'shop:abandoned-carts';
    protected $description = 'Envoie des rappels WhatsApp/email pour les paniers abandonnés (2h–24h)';

    public function handle(): int
    {
        $carts = Cart::with(['user', 'items'])
            ->whereNotNull('user_id')
            ->whereHas('items')
            ->whereBetween('updated_at', [now()->subHours(24), now()->subHours(2)])
            ->get();

        $count = 0;
        foreach ($carts as $cart) {
            if ($cart->user && $cart->user->orders()->where('created_at', '>', now()->subHours(3))->exists()) {
                continue;
            }
            SendAbandonedCartReminder::dispatch($cart->id)->onQueue('default');
            $count++;
        }

        Log::info("[AbandonedCart] {$count} relance(s) dispatchée(s).");
        $this->info("✅ {$count} relance(s) de panier abandonné envoyée(s).");
        return self::SUCCESS;
    }
}
