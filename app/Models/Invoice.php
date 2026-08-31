<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'tripay_reference',
        'buyer_email',
        'buyer_phone',
        'raw_response',
        'merchant_ref',
        'amount',
        'payment_method',
        'checkout_url',
        'status',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Produk yang dibeli pada invoice ini.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
