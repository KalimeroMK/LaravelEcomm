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
            'expires_at' => 'datetime',
        ];

    protected $fillable
        = [
            'code',
            'type',
            'value',
            'status',
            'expires_at',
        ];

    public static function Factory(): CouponFactory
    {
        return CouponFactory::new();
    }

    /**
     * Get the expires_at attribute with fallback.
     * This handles cases where the column might not exist in the database yet.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon|null
     */
    public function getExpiresAtAttribute($value)
    {
        // Check if the attribute exists in the model's attributes array
        // This prevents MissingAttributeException when column doesn't exist
        if (! array_key_exists('expires_at', $this->attributes)) {
            return null;
        }

        // If value is null, return null
        if ($value === null) {
            return null;
        }

        // Let Laravel handle the casting through $casts
        // Just return the value as-is, Laravel will cast it
        return $value;
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
