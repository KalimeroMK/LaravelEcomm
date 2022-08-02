<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Shipping\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property Collection|Order[] $orders
 * @package App\Models
 * @property-read int|null $orders_count
 * @method static Builder|Shipping newModelQuery()
 * @method static Builder|Shipping newQuery()
 * @method static Builder|Shipping query()
 * @method static Builder|Shipping whereCreatedAt($value)
 * @method static Builder|Shipping whereId($value)
 * @method static Builder|Shipping wherePrice($value)
 * @method static Builder|Shipping whereStatus($value)
 * @method static Builder|Shipping whereType($value)
 * @method static Builder|Shipping whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Shipping extends Core
{
    use HasFactory;
    
    protected $table = 'shipping';
    
    protected $casts = [
        'price' => 'float',
    ];
    
    protected $fillable = [
        'type',
        'price',
        'status',
    ];
    
    /**
     * @return ShippingFactory
     */
    public static function Factory(): ShippingFactory
    {
        return ShippingFactory::new();
    }
    
    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
