<?php

declare(strict_types=1);

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\Shipping\Database\Factories\ShippingZoneMethodFactory;

class ShippingZoneMethod extends Core
{
    use HasFactory;

    protected $table = 'shipping_zone_methods';

    protected $fillable = [
        'shipping_zone_id',
        'shipping_id',
        'price',
        'free_shipping_threshold',
        'estimated_days',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * Calculate shipping cost based on order total
     */
    public function calculateCost(float $orderTotal): float
    {
        if ($this->free_shipping_threshold && $orderTotal >= $this->free_shipping_threshold) {
            return 0;
        }

        return (float) $this->price;
    }

    protected static function newFactory(): ShippingZoneMethodFactory
    {
        return ShippingZoneMethodFactory::new();
    }
}
