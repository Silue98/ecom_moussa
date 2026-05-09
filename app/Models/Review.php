<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'reviewer_name', 'source',
        'rating', 'title', 'body', 'is_approved', 'is_verified_purchase',
    ];

    protected $casts = [
        'is_approved'          => 'boolean',
        'is_verified_purchase' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Accesseurs ─────────────────────────────────────────────────

    /**
     * Nom affiché en front : priorité user->name, sinon reviewer_name, sinon 'Client vérifié'.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->reviewer_name ?? 'Client vérifié';
    }

    /**
     * Label source lisible.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'whatsapp' => '💬 WhatsApp',
            'boutique' => '🏪 Boutique',
            'importe'  => '📥 Importé',
            default    => '🌐 Site',
        };
    }
}
