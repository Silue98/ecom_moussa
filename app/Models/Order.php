<?php

namespace App\Models;

use App\Notifications\OrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_status', 'payment_method',
        'subtotal', 'tax_amount', 'shipping_amount', 'discount_amount', 'total',
        'coupon_id', 'coupon_code',
        'shipping_name', 'shipping_email', 'shipping_phone',
        'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_zip', 'shipping_country',
        'billing_name', 'billing_email', 'billing_phone',
        'billing_address', 'billing_city', 'billing_state',
        'billing_zip', 'billing_country',
        'notes', 'admin_notes', 'tracking_number', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'shipped_at'      => 'datetime',
        'delivered_at'    => 'datetime',
    ];

    // ─── Constantes ───────────────────────────────────────────
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
    const STATUS_REFUNDED   = 'refunded';

    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_PAID     = 'paid';
    const PAYMENT_FAILED   = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // ─── Relations ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // ─── Accesseurs labels ────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '⏳ En attente',
            'processing' => '⚙️ En traitement',
            'shipped'    => '🚚 Expédié',
            'delivered'  => '✅ Livré',
            'cancelled'  => '❌ Annulé',
            'refunded'   => '↩️ Remboursé',
            default      => ucfirst($this->status),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod'           => '💵 Paiement à la livraison',
            'card'          => '💳 Carte bancaire',
            'bank_transfer' => '🏦 Virement bancaire',
            default         => ucfirst($this->payment_method ?? ''),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'shipped'    => 'primary',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            'refunded'   => 'gray',
            default      => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending'  => '⏳ En attente',
            'paid'     => '✅ Payé',
            'failed'   => '❌ Échoué',
            'refunded' => '↩️ Remboursé',
            default    => ucfirst($this->payment_status ?? ''),
        };
    }

    // ─── Boot : numéro auto + notification statut ─────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });

        // Notification automatique quand le statut change
        static::updated(function ($order) {
            if ($order->isDirty('status') && $order->user) {
                $oldStatus = $order->getOriginal('status');
                $order->user->notify(new OrderStatusChanged($order, $oldStatus));
            }
        });
    }
}
