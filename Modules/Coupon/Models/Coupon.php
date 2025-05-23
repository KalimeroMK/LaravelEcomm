<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Coupon\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Coupon\Database\Factories\CouponFactory;

/**
 * Class Coupon
 *
 * @property int $id
 * @property string $code
 * @property string $type
 * @property float $value
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|Coupon newModelQuery()
 * @method static Builder<static>|Coupon newQuery()
 * @method static Builder<static>|Coupon query()
 * @method static Builder<static>|Coupon whereCode($value)
 * @method static Builder<static>|Coupon whereCreatedAt($value)
 * @method static Builder<static>|Coupon whereId($value)
 * @method static Builder<static>|Coupon whereStatus($value)
 * @method static Builder<static>|Coupon whereType($value)
 * @method static Builder<static>|Coupon whereUpdatedAt($value)
 * @method static Builder<static>|Coupon whereValue($value)
 *
 * @mixin Eloquent
 */
class Coupon extends Core
{
    use HasFactory;

    protected $table = 'coupons';

    protected $casts
        = [
            'value' => 'float',
        ];

    protected $fillable
        = [
            'code',
            'type',
            'value',
            'status',
        ];

    public static function Factory(): CouponFactory
    {
        return CouponFactory::new();
    }

    /**
     * Calculates the discount based on the total amount and the coupon type.
     *
     * @param  float  $total  The total amount on which the discount is to be applied.
     * @return float The amount of discount.
     */
    public function discount(float $total): float
    {
        if ($this->type === 'fixed') {
            // Assuming 'value' is the fixed discount amount.
            return min($this->value, $total); // Ensure discount does not exceed the total.
        }
        if ($this->type === 'percent') {
            // Assuming 'value' is the percentage discount.
            return $total * ($this->value / 100);
        }

        return 0; // No discount applicable.
    }
}
