<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Shipping\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\Shipping\Database\Factories\ShippingFactory;

/**
 * Class Shipping
 *
 * @property int $id
 * @property string $type
 * @property float $price
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Order> $orders
 * @property-read int|null               $orders_count
 *
 * @method static Builder<static>|Shipping newModelQuery()
 * @method static Builder<static>|Shipping newQuery()
 * @method static Builder<static>|Shipping query()
 * @method static Builder<static>|Shipping whereCreatedAt($value)
 * @method static Builder<static>|Shipping whereId($value)
 * @method static Builder<static>|Shipping wherePrice($value)
 * @method static Builder<static>|Shipping whereStatus($value)
 * @method static Builder<static>|Shipping whereType($value)
 * @method static Builder<static>|Shipping whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Shipping extends Core
{
    use HasFactory;

    protected $table = 'shipping';

    protected $casts
        = [
            'price' => 'float',
        ];

    protected $fillable
        = [
            'type',
            'price',
            'status',
        ];

    public static function Factory(): ShippingFactory
    {
        return ShippingFactory::new();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
