<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderAdmin;
use App\Notifications\OrderConfirmed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre maximum de tentatives si le mail échoue.
     */
    public int $tries = 5;

    /**
     * Délai entre chaque tentative (en secondes) : 1min, 5min, 15min, 30min, 1h
     */
    public function backoff(): array
    {
        return [60, 300, 900, 1800, 3600];
    }

    /**
     * Timeout max pour l'envoi d'un mail (secondes).
     */
    public int $timeout = 60;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::with(['items', 'user'])->find($this->orderId);

        if (! $order) {
            Log::warning("[SendOrderNotifications] Commande #{$this->orderId} introuvable — job annulé.");
            return;
        }

        // ── 1. Notification client ────────────────────────────────────────
        if ($order->user) {
            $order->user->notify(new OrderConfirmed($order));
            Log::info("[SendOrderNotifications] Email confirmation envoyé au client {$order->user->email} pour commande #{$order->order_number}.");
        }

        // ── 2. Notifications admins / managers ────────────────────────────
        $admins = User::whereIn('role', ['admin', 'manager'])->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewOrderAdmin($order));
        }

        Log::info("[SendOrderNotifications] Notifications admin envoyées pour commande #{$order->order_number} ({$admins->count()} destinataire(s)).");
    }

    /**
     * Appelé quand toutes les tentatives ont échoué.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error(
            "[SendOrderNotifications] Échec définitif de l'envoi des notifications pour commande #{$this->orderId}.",
            ['error' => $exception->getMessage()]
        );
    }
}
