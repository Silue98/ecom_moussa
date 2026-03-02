<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'value', 'sku', 'price_modifier', 'quantity', 'is_active'];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Prix final = prix produit de base + modificateur de variante */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->product?->price ?? 0) + (float) $this->price_modifier;
    }
}
